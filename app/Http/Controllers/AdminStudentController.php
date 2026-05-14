<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Exam;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminStudentController extends Controller
{
    public function index(Request $request): View
    {
        $students = User::query()
            ->with('participantExams:id,title,code')
            ->where('role', 'siswa')
            ->when($request->search, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('class', 'like', "%{$search}%");
                });
            })
            ->orderBy('class')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.students.index', compact('students'));
    }

    public function create(): View
    {
        return view('admin.students.form', ['student' => new User]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['role'] = 'siswa';

        User::create($validated);
        ActivityLog::record('student_created', 'Admin menambahkan siswa.', request: $request);

        return redirect()->route('admin.students.index')->with('status', 'Data siswa berhasil ditambahkan.');
    }

    public function show(User $student): RedirectResponse
    {
        return redirect()->route('admin.students.edit', $student);
    }

    public function edit(User $student): View
    {
        abort_unless($student->isStudent(), 404);

        return view('admin.students.form', compact('student'));
    }

    public function update(Request $request, User $student): RedirectResponse
    {
        abort_unless($student->isStudent(), 404);

        $validated = $request->validate($this->rules($student));

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $student->update($validated);
        ActivityLog::record('student_updated', 'Admin mengubah data siswa.', request: $request);

        return redirect()->route('admin.students.index')->with('status', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(User $student): RedirectResponse
    {
        abort_unless($student->isStudent(), 404);

        $student->delete();

        return redirect()->route('admin.students.index')->with('status', 'Data siswa berhasil dihapus.');
    }

    public function resetPassword(Request $request, User $student): RedirectResponse
    {
        abort_unless($student->isStudent(), 404);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6'],
        ]);

        $student->update(['password' => $validated['password']]);
        ActivityLog::record('student_password_reset', 'Admin reset password/token siswa.', request: $request);

        return back()->with('status', 'Password/token siswa berhasil direset.');
    }

    public function importCsv(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:5120'],
        ]);

        $extension = strtolower($validated['file']->getClientOriginalExtension());

        if (! in_array($extension, ['csv', 'txt'], true)) {
            return back()->withErrors([
                'file' => 'File siswa harus berformat CSV. Jika data masih Excel, simpan sebagai CSV terlebih dahulu.',
            ]);
        }

        $rows = $this->readCsvRows($validated['file']->getRealPath());
        $exams = Exam::query()
            ->whereNotNull('code')
            ->get()
            ->keyBy(fn (Exam $exam) => str($exam->code)->upper()->trim()->toString());
        $imported = 0;
        $registered = 0;
        $skipped = 0;
        $header = null;

        foreach ($rows as $row) {
            if (! is_array($row) || count(array_filter($row, fn ($value) => filled($value))) === 0) {
                continue;
            }

            if ($header === null) {
                $header = array_map(fn ($value) => $this->normalizeCsvHeader((string) $value), $row);

                $hasExamCode = in_array('kode_ujian', $header, true) || in_array('exam_code', $header, true);

                if (! in_array('username', $header, true) || ! $hasExamCode) {
                    return back()->withErrors([
                        'file' => 'Header CSV wajib memiliki kolom username dan kode_ujian. Gunakan template CSV siswa terbaru.',
                    ]);
                }

                continue;
            }

            $data = array_combine($header, array_slice(array_pad($row, count($header), null), 0, count($header)));

            if (! $data || blank($data['username'] ?? null)) {
                continue;
            }

            $examCodes = collect(preg_split('/[;,|\r\n]+/', (string) ($data['kode_ujian'] ?? $data['exam_code'] ?? ''), -1, PREG_SPLIT_NO_EMPTY))
                ->map(fn ($code) => str($code)->upper()->trim()->toString())
                ->filter()
                ->unique();
            $matchedExams = $examCodes->map(fn ($code) => $exams->get($code))->filter();

            if ($matchedExams->isEmpty()) {
                $skipped++;

                continue;
            }

            $student = User::updateOrCreate(
                ['username' => trim((string) $data['username'])],
                [
                    'name' => filled($data['name'] ?? $data['nama'] ?? null) ? trim((string) ($data['name'] ?? $data['nama'])) : trim((string) $data['username']),
                    'nisn' => filled($data['nisn'] ?? $data['nis'] ?? null) ? trim((string) ($data['nisn'] ?? $data['nis'])) : null,
                    'class' => filled($data['class'] ?? $data['kelas'] ?? null) ? trim((string) ($data['class'] ?? $data['kelas'])) : $matchedExams->first()->getAttribute('class'),
                    'email' => filled($data['email'] ?? null) ? trim((string) $data['email']) : null,
                    'password' => filled($data['password'] ?? null) ? trim((string) $data['password']) : 'password',
                    'role' => 'siswa',
                ]
            );

            $matchedExams->each(fn (Exam $exam) => $exam->participants()->syncWithoutDetaching([$student->id]));

            $imported++;
            $registered += $matchedExams->count();
        }

        ActivityLog::record('student_imported', "Admin import {$imported} siswa dari CSV dan mendaftarkan {$registered} peserta ujian.", request: $request);

        $message = "{$imported} siswa berhasil diimport dan {$registered} peserta berhasil didaftarkan ke ujian.";

        if ($skipped > 0) {
            $message .= " {$skipped} baris dilewati karena kode ujian tidak ditemukan.";
        }

        return back()->with('status', $message);
    }

    public function downloadTemplate(): Response
    {
        $examCodes = Exam::query()
            ->whereNotNull('code')
            ->latest()
            ->limit(2)
            ->pluck('code');
        $sampleCodes = $examCodes->isNotEmpty() ? $examCodes->implode(';') : 'ISI_KODE_UJIAN_1;ISI_KODE_UJIAN_2';

        $rows = [
            ['name', 'nisn', 'class', 'username', 'email', 'password', 'kode_ujian'],
            ['Contoh Siswa 1', '9001', 'XII IPA 1', 'siswa9001', 'siswa9001@example.com', 'password123', $sampleCodes],
            ['Contoh Siswa 2', '9002', 'XII IPA 1', 'siswa9002', 'siswa9002@example.com', 'password123', $sampleCodes],
        ];

        $csv = collect($rows)
            ->map(fn ($row) => collect($row)->map(fn ($value) => '"'.str_replace('"', '""', $value).'"')->implode(','))
            ->implode("\r\n");

        return response($csv."\r\n", 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template-upload-siswa.csv"',
        ]);
    }

    private function rules(?User $student = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'nisn' => ['required', 'string', 'max:255', Rule::unique('users', 'nisn')->ignore($student)],
            'class' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($student)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($student)],
            'password' => [$student ? 'nullable' : 'required', 'string', 'min:6'],
        ];
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
}
