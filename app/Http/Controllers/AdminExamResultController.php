<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminExamResultController extends Controller
{
    public function index(Request $request): View
    {
        $exams = Exam::query()
            ->withCount('results')
            ->latest('start_time')
            ->get();

        $selectedExam = $request->filled('exam_id')
            ? $exams->firstWhere('id', (int) $request->exam_id)
            : $exams->first();

        $results = ExamResult::query()
            ->with(['exam', 'user'])
            ->when($selectedExam, fn ($query) => $query->where('exam_id', $selectedExam->id))
            ->latest('submitted_at')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.results.index', compact('exams', 'selectedExam', 'results'));
    }

    public function sync(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'exam_id' => ['required', 'exists:exams,id'],
            'result_spreadsheet_id' => ['required', 'string', 'max:255'],
            'result_sheet_name' => ['nullable', 'string', 'max:255'],
        ]);

        $exam = Exam::findOrFail($validated['exam_id']);
        $spreadsheetId = $this->extractSpreadsheetId($validated['result_spreadsheet_id']);
        $sheetName = filled($validated['result_sheet_name'] ?? null)
            ? trim((string) $validated['result_sheet_name'])
            : 'Form Responses 1';

        $exam->forceFill([
            'result_spreadsheet_id' => $spreadsheetId,
            'result_sheet_name' => $sheetName,
        ])->save();

        $url = 'https://docs.google.com/spreadsheets/d/'.$spreadsheetId.'/gviz/tq?'.Arr::query([
            'tqx' => 'out:csv',
            'sheet' => $sheetName,
        ]);

        $response = Http::timeout(20)->get($url);

        if (! $response->successful() || blank($response->body())) {
            return back()->withErrors([
                'result_spreadsheet_id' => 'Google Sheets belum bisa dibaca. Pastikan akses sheet disetel Anyone with the link can view atau sheet sudah dipublish.',
            ]);
        }

        $rows = $this->readCsvString($response->body());
        [$imported, $unmatched] = $this->importRows($rows, $exam);

        ActivityLog::record('exam_results_synced', "Admin sinkron {$imported} hasil Google Sheets untuk ujian {$exam->title}.", exam: $exam, request: $request);

        return redirect()
            ->route('admin.results.index', ['exam_id' => $exam->id])
            ->with('status', "{$imported} hasil berhasil disinkronkan dari Google Sheets. {$unmatched} baris belum cocok dengan data siswa.");
    }

    public function download(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'exam_id' => ['required', 'exists:exams,id'],
        ]);

        $exam = Exam::findOrFail($validated['exam_id']);
        $filename = 'hasil-ujian-'.$exam->id.'-'.str($exam->title)->slug()->toString().'.csv';

        ActivityLog::record('exam_results_downloaded', "Admin download hasil ujian {$exam->title}.", exam: $exam, request: $request);

        return response()->streamDownload(function () use ($exam): void {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Ujian', $exam->title]);
            fputcsv($handle, ['Mata Pelajaran', $exam->subject]);
            fputcsv($handle, ['Kelas Ujian', $exam->getAttribute('class')]);
            fputcsv($handle, ['Tanggal Download', now()->format('Y-m-d H:i:s')]);
            fputcsv($handle, []);

            fputcsv($handle, [
                'No',
                'Nama',
                'Kelas',
                'Nilai',
            ]);

            ExamResult::query()
                ->with('user')
                ->where('exam_id', $exam->id)
                ->oldest('submitted_at')
                ->oldest('id')
                ->chunk(200, function ($results) use ($handle, $exam): void {
                    static $number = 1;

                    foreach ($results as $result) {
                        fputcsv($handle, [
                            $number++,
                            $result->user?->name ?? $result->student_name ?? '',
                            $result->class ?: $result->user?->getAttribute('class'),
                            filled($result->max_score) ? $result->score.' / '.$result->max_score : $result->score,
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function importRows(array $rows, Exam $exam): array
    {
        $header = null;
        $imported = 0;
        $unmatched = 0;

        foreach ($rows as $row) {
            if (! is_array($row) || count(array_filter($row, fn ($value) => filled($value))) === 0) {
                continue;
            }

            if ($header === null) {
                $header = array_map(fn ($value) => $this->normalizeCsvHeader((string) $value), $row);

                if (! $this->hasAnyHeader($header, ['score', 'skor', 'nilai', 'total_score', 'total_skor'])) {
                    throw ValidationException::withMessages([
                        'file' => 'CSV wajib memiliki kolom nilai, misalnya Score, Skor, atau Nilai.',
                    ]);
                }

                continue;
            }

            $data = array_combine($header, array_slice(array_pad($row, count($header), null), 0, count($header)));

            if (! $data) {
                continue;
            }

            $identifier = $this->firstFilled($data, ['username', 'nis', 'email', 'email_address', 'alamat_email'])
                ?: md5(json_encode($data));
            $student = $this->findStudent($identifier);

            if (! $student) {
                $unmatched++;
            }

            [$score, $maxScore, $percentage] = $this->parseScore($this->firstFilled($data, ['score', 'skor', 'nilai', 'total_score', 'total_skor']));

            $payload = [
                'exam_id' => $exam->id,
                'user_id' => $student?->id,
                'identifier' => $identifier,
                'student_name' => $student?->name ?? $this->firstFilled($data, ['name', 'nama', 'nama_lengkap']),
                'nis' => $student?->nis ?? $this->firstFilled($data, ['nis']),
                'class' => $student?->getAttribute('class') ?? $this->firstFilled($data, ['class', 'kelas']),
                'score' => $score,
                'max_score' => $maxScore,
                'percentage' => $percentage,
                'submitted_at' => $this->parseSubmittedAt($this->firstFilled($data, ['timestamp', 'time_stamp', 'submitted_at', 'waktu_pengiriman', 'stempel_waktu'])),
                'imported_at' => now(),
                'raw_payload' => $data,
            ];

            $lookup = $student
                ? ['exam_id' => $exam->id, 'user_id' => $student->id]
                : ['exam_id' => $exam->id, 'identifier' => $identifier];

            ExamResult::updateOrCreate($lookup, $payload);
            $imported++;
        }

        return [$imported, $unmatched];
    }

    private function findStudent(?string $identifier): ?User
    {
        if (blank($identifier)) {
            return null;
        }

        $identifier = trim($identifier);

        return User::query()
            ->where('role', 'siswa')
            ->where(function ($query) use ($identifier) {
                $query
                    ->where('username', $identifier)
                    ->orWhere('nis', $identifier)
                    ->orWhere('email', $identifier);
            })
            ->first();
    }

    private function firstFilled(array $data, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (filled($data[$key] ?? null)) {
                return trim((string) $data[$key]);
            }
        }

        return null;
    }

    private function hasAnyHeader(array $header, array $keys): bool
    {
        return collect($keys)->contains(fn ($key) => in_array($key, $header, true));
    }

    private function parseScore(?string $value): array
    {
        if (blank($value)) {
            return [null, null, null];
        }

        $value = str_replace(',', '.', trim($value));

        if (preg_match('/([\d.]+)\s*\/\s*([\d.]+)/', $value, $matches)) {
            $score = (float) $matches[1];
            $maxScore = (float) $matches[2];

            return [$score, $maxScore, $maxScore > 0 ? round(($score / $maxScore) * 100, 2) : null];
        }

        if (preg_match('/-?\d+(?:\.\d+)?/', $value, $matches)) {
            $score = (float) $matches[0];

            return [$score, null, null];
        }

        return [null, null, null];
    }

    private function parseSubmittedAt(?string $value): ?Carbon
    {
        if (blank($value)) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function readCsvString(string $content): array
    {
        $firstLine = strtok($content, "\r\n") ?: '';
        $delimiter = collect([',', ';', "\t"])
            ->sortByDesc(fn ($delimiter) => substr_count($firstLine, $delimiter))
            ->first();

        $rows = [];
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $content);
        rewind($stream);

        while (($row = fgetcsv($stream, 0, $delimiter)) !== false) {
            $rows[] = $row;
        }

        fclose($stream);

        return $rows;
    }

    private function extractSpreadsheetId(string $value): string
    {
        if (preg_match('~/spreadsheets/d/([^/]+)~', $value, $matches)) {
            return $matches[1];
        }

        return trim($value);
    }

    private function normalizeCsvHeader(string $value): string
    {
        $value = preg_replace('/^\xEF\xBB\xBF/', '', $value) ?? $value;

        return str($value)
            ->lower()
            ->trim()
            ->replace([' ', '-', '.', '/', '(', ')'], '_')
            ->replace('__', '_')
            ->trim('_')
            ->toString();
    }
}
