<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use SplFileObject;

class AdminStudentController extends Controller
{
    public function index(Request $request): View
    {
        $students = User::query()
            ->where('role', 'siswa')
            ->when($request->search, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
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
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = new SplFileObject($validated['file']->getRealPath());
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);

        $imported = 0;
        $header = null;

        foreach ($file as $row) {
            if (! is_array($row) || count(array_filter($row)) === 0) {
                continue;
            }

            if ($header === null) {
                $header = array_map(fn ($value) => str($value)->lower()->trim()->toString(), $row);

                continue;
            }

            $data = array_combine($header, array_slice(array_pad($row, count($header), null), 0, count($header)));

            if (! $data || blank($data['username'] ?? null)) {
                continue;
            }

            User::updateOrCreate(
                ['username' => trim($data['username'])],
                [
                    'name' => trim((string) ($data['name'] ?? $data['nama'] ?? '')),
                    'nis' => trim((string) ($data['nis'] ?? '')),
                    'class' => trim((string) ($data['class'] ?? $data['kelas'] ?? '')),
                    'email' => filled($data['email'] ?? null) ? trim((string) $data['email']) : null,
                    'password' => filled($data['password'] ?? null) ? trim((string) $data['password']) : 'password',
                    'role' => 'siswa',
                ]
            );

            $imported++;
        }

        ActivityLog::record('student_imported', "Admin import {$imported} siswa dari CSV.", request: $request);

        return back()->with('status', "{$imported} siswa berhasil diimport.");
    }

    private function rules(?User $student = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:255', Rule::unique('users', 'nis')->ignore($student)],
            'class' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($student)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($student)],
            'password' => [$student ? 'nullable' : 'required', 'string', 'min:6'],
        ];
    }
}
