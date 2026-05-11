<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Exam;
use App\Models\ExamSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminMonitoringController extends Controller
{
    public function __invoke(Request $request): View
    {
        $sessions = ExamSession::query()
            ->with(['user', 'exam'])
            ->when($request->exam_id, fn ($query, string $examId) => $query->where('exam_id', $examId))
            ->when($request->class, fn ($query, string $class) => $query->whereHas('user', fn ($query) => $query->where('class', $class)))
            ->when($request->status, fn ($query, string $status) => $query->where('status', $status))
            ->when($request->date, fn ($query, string $date) => $query->whereDate('created_at', $date))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $exams = Exam::orderBy('title')->get();
        $classes = Exam::query()->select('class')->distinct()->orderBy('class')->pluck('class');

        return view('admin.monitoring.index', compact('sessions', 'exams', 'classes'));
    }

    public function reset(Request $request, ExamSession $session): RedirectResponse
    {
        $session->load(['user', 'exam']);

        ActivityLog::record(
            'exam_session_reset',
            "Admin mereset ujian {$session->exam->title} untuk siswa {$session->user->name}.",
            user: $request->user(),
            exam: $session->exam,
            session: $session,
            request: $request
        );

        $studentName = $session->user->name;
        $examTitle = $session->exam->title;

        $session->delete();

        return back()->with('status', "Ujian {$examTitle} untuk {$studentName} berhasil direset.");
    }
}
