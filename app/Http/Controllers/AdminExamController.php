<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Exam;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AdminExamController extends Controller
{
    public function index(Request $request): View
    {
        $exams = Exam::query()
            ->when($request->class, fn ($query, string $class) => $query->where('class', $class))
            ->withCount('participants')
            ->latest('start_time')
            ->paginate(12)
            ->withQueryString();

        return view('admin.exams.index', compact('exams'));
    }

    public function create(): View
    {
        return view('admin.exams.form', ['exam' => new Exam]);
    }

    public function store(Request $request): RedirectResponse
    {
        $exam = Exam::create($this->validated($request));
        ActivityLog::record('exam_created', "Admin membuat ujian {$exam->title}.", exam: $exam, request: $request);

        return redirect()->route('admin.exams.index')->with('status', 'Ujian berhasil dibuat.');
    }

    public function show(Exam $exam): RedirectResponse
    {
        return redirect()->route('admin.exams.edit', $exam);
    }

    public function edit(Exam $exam): View
    {
        $exam->loadCount('participants');

        return view('admin.exams.form', compact('exam'));
    }

    public function update(Request $request, Exam $exam): RedirectResponse
    {
        $exam->update($this->validated($request));
        ActivityLog::record('exam_updated', "Admin mengubah ujian {$exam->title}.", exam: $exam, request: $request);

        return redirect()->route('admin.exams.index')->with('status', 'Ujian berhasil diperbarui.');
    }

    public function destroy(Exam $exam): RedirectResponse
    {
        $exam->delete();

        return redirect()->route('admin.exams.index')->with('status', 'Ujian berhasil dihapus.');
    }

    public function importParticipants(Request $request, Exam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:5120'],
        ]);

        $extension = strtolower($validated['file']->getClientOriginalExtension());

        if (! in_array($extension, ['csv', 'txt'], true)) {
            return back()->withErrors([
                'file' => 'File peserta harus berformat CSV. Jika data masih Excel, simpan sebagai CSV terlebih dahulu.',
            ]);
        }

        $rows = $this->readCsvRows($validated['file']->getRealPath());

        $participantIds = [];
        $imported = 0;
        $header = null;

        foreach ($rows as $row) {
            if (! is_array($row) || count(array_filter($row, fn ($value) => filled($value))) === 0) {
                continue;
            }

            if ($header === null) {
                $header = array_map(fn ($value) => $this->normalizeCsvHeader((string) $value), $row);

                if (! in_array('username', $header, true)) {
                    return back()->withErrors([
                        'file' => 'Header CSV wajib memiliki kolom username. Gunakan template CSV dari tombol Download Template CSV.',
                    ]);
                }

                continue;
            }

            $data = array_combine($header, array_slice(array_pad($row, count($header), null), 0, count($header)));

            if (! $data || blank($data['username'] ?? null)) {
                continue;
            }

            $student = User::updateOrCreate(
                ['username' => trim((string) $data['username'])],
                [
                    'name' => trim((string) ($data['name'] ?? $data['nama'] ?? '')),
                    'nis' => trim((string) ($data['nis'] ?? '')),
                    'class' => trim((string) ($data['class'] ?? $data['kelas'] ?? $exam->getAttribute('class'))),
                    'email' => filled($data['email'] ?? null) ? trim((string) $data['email']) : null,
                    'password' => filled($data['password'] ?? null) ? trim((string) $data['password']) : 'password',
                    'role' => 'siswa',
                ]
            );

            $participantIds[] = $student->id;
            $imported++;
        }

        if ($participantIds !== []) {
            $exam->participants()->syncWithoutDetaching($participantIds);
        }

        ActivityLog::record('exam_participants_imported', "Admin import {$imported} peserta untuk ujian {$exam->title}.", exam: $exam, request: $request);

        return back()->with('status', "{$imported} peserta berhasil diimport untuk ujian {$exam->title}.");
    }

    public function downloadParticipantTemplate(Exam $exam): Response
    {
        $class = $exam->getAttribute('class');
        $safeClass = str($class)->replace(',', ' ')->toString();
        $rows = [
            ['name', 'nis', 'class', 'username', 'email', 'password'],
            ['Contoh Peserta 1', '9001', $safeClass, 'peserta9001', 'peserta9001@example.com', 'password123'],
            ['Contoh Peserta 2', '9002', $safeClass, 'peserta9002', 'peserta9002@example.com', 'password123'],
        ];

        $csv = collect($rows)
            ->map(fn ($row) => collect($row)->map(fn ($value) => '"'.str_replace('"', '""', $value).'"')->implode(','))
            ->implode("\r\n");

        return response($csv."\r\n", 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template-peserta-ujian-'.$exam->id.'.csv"',
        ]);
    }

    private function readCsvRows(string $path): array
    {
        $content = file_get_contents($path) ?: '';
        $firstLine = strtok($content, "\r\n") ?: '';
        $delimiter = collect([',', ';', "\t"])
            ->sortByDesc(fn ($delimiter) => substr_count($firstLine, $delimiter))
            ->first();

        $rows = [];
        $handle = fopen($path, 'r');

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    private function normalizeCsvHeader(string $value): string
    {
        $value = preg_replace('/^\xEF\xBB\xBF/', '', $value) ?? $value;

        return str($value)->lower()->trim()->replace([' ', '-'], '_')->toString();
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'class' => ['required', 'string', 'max:255'],
            'google_form_url' => ['required', 'url'],
            'result_spreadsheet_id' => ['nullable', 'string', 'max:255'],
            'result_sheet_name' => ['nullable', 'string', 'max:255'],
            'prefill_name_field' => ['nullable', 'string', 'max:255'],
            'prefill_nis_field' => ['nullable', 'string', 'max:255'],
            'prefill_class_field' => ['nullable', 'string', 'max:255'],
            'prefill_exam_field' => ['nullable', 'string', 'max:255'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'instructions' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['allow_retake'] = $request->boolean('allow_retake');

        return $validated;
    }
}
