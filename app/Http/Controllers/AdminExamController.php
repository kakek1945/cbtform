<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Exam;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

    private function validated(Request $request): array
    {
        $request->merge([
            'code' => str((string) $request->input('code'))->upper()->trim()->toString(),
        ]);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('exams', 'code')->ignore($request->route('exam'))],
            'subject' => ['required', 'string', 'max:255'],
            'class' => ['required', 'string', 'max:255'],
            'google_form_url' => ['required', 'url'],
            'result_spreadsheet_id' => ['nullable', 'string', 'max:255'],
            'result_sheet_name' => ['nullable', 'string', 'max:255'],
            'prefill_name_field' => ['nullable', 'string', 'max:255'],
            'prefill_username_field' => ['nullable', 'string', 'max:255'],
            'prefill_nisn_field' => ['nullable', 'string', 'max:255'],
            'prefill_class_field' => ['nullable', 'string', 'max:255'],
            'prefill_exam_field' => ['nullable', 'string', 'max:255'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'instructions' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['allow_retake'] = $request->boolean('allow_retake');
        $validated['show_score'] = $request->boolean('show_score');

        return $validated;
    }
}
