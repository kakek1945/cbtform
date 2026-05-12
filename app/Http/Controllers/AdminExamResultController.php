<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Services\ExamResultSyncer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function sync(Request $request, ExamResultSyncer $syncer): RedirectResponse
    {
        $validated = $request->validate([
            'exam_id' => ['required', 'exists:exams,id'],
            'result_spreadsheet_id' => ['required', 'string', 'max:255'],
            'result_sheet_name' => ['nullable', 'string', 'max:255'],
        ]);

        $exam = Exam::findOrFail($validated['exam_id']);
        $sheetName = filled($validated['result_sheet_name'] ?? null)
            ? trim((string) $validated['result_sheet_name'])
            : 'Form Responses 1';

        [$imported, $unmatched] = $syncer->sync($exam, $validated['result_spreadsheet_id'], $sheetName);

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
                ->chunk(200, function ($results) use ($handle): void {
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

    public function destroy(ExamResult $result): RedirectResponse
    {
        if ($result->user_id !== null) {
            return back()->withErrors([
                'result' => 'Hanya hasil yang belum cocok dengan data siswa yang dapat dihapus.',
            ]);
        }

        $exam = $result->exam;
        $studentName = $result->student_name ?: $result->identifier ?: 'hasil tidak cocok';

        $result->delete();

        ActivityLog::record('exam_result_deleted', "Admin menghapus hasil tidak cocok {$studentName}.", exam: $exam, request: request());

        return redirect()
            ->route('admin.results.index', ['exam_id' => $exam?->id])
            ->with('status', 'Hasil tidak cocok berhasil dihapus.');
    }
}
