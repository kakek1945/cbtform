<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class ExamResultSyncer
{
    public function sync(Exam $exam, ?string $spreadsheetId = null, ?string $sheetName = null): array
    {
        $spreadsheetId = filled($spreadsheetId)
            ? $this->extractSpreadsheetId((string) $spreadsheetId)
            : $exam->result_spreadsheet_id;

        $sheetName = filled($sheetName)
            ? trim((string) $sheetName)
            : ($exam->result_sheet_name ?: 'Form Responses 1');

        if (blank($spreadsheetId)) {
            throw ValidationException::withMessages([
                'result_spreadsheet_id' => 'ID Google Sheets hasil ujian belum diisi.',
            ]);
        }

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
            throw ValidationException::withMessages([
                'result_spreadsheet_id' => 'Google Sheets belum bisa dibaca. Pastikan akses sheet disetel Anyone with the link can view atau sheet sudah dipublish.',
            ]);
        }

        return $this->importRows($this->readCsvString($response->body()), $exam);
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

            $rawIdentifier = $this->firstFilled($data, ['username', 'nis', 'email', 'email_address', 'alamat_email']);
            $rawName = $this->firstFilled($data, ['name', 'nama', 'nama_lengkap']);
            $rawNis = $this->firstFilled($data, ['nis']);
            $rawClass = $this->firstFilled($data, ['class', 'kelas']);
            $student = $this->findStudent($rawIdentifier)
                ?: $this->findStudentByProfile($rawName, $rawNis, $rawClass);
            $identifier = $rawIdentifier
                ?: $student?->username
                ?: $student?->nis
                ?: md5(json_encode($data));

            if (! $student) {
                $unmatched++;
            }

            [$score, $maxScore, $percentage] = $this->parseScore($this->firstFilled($data, ['score', 'skor', 'nilai', 'total_score', 'total_skor']));

            $payload = [
                'exam_id' => $exam->id,
                'user_id' => $student?->id,
                'identifier' => $identifier,
                'student_name' => $student?->name ?? $rawName,
                'nis' => $student?->nis ?? $rawNis,
                'class' => $student?->getAttribute('class') ?? $rawClass,
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

    private function findStudentByProfile(?string $name, ?string $nis, ?string $class): ?User
    {
        if (filled($nis)) {
            return $this->findStudent($nis);
        }

        if (blank($name) || blank($class)) {
            return null;
        }

        return User::query()
            ->where('role', 'siswa')
            ->whereRaw('LOWER(name) = ?', [str((string) $name)->lower()->toString()])
            ->whereRaw('LOWER(class) = ?', [str((string) $class)->lower()->toString()])
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
            return [(float) $matches[0], null, null];
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
