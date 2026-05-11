<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Exam;
use App\Models\ExamSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamSessionController extends Controller
{
    public function start(Request $request, Exam $exam): RedirectResponse
    {
        $student = $request->user();
        abort_unless($exam->getAttribute('class') === $student->getAttribute('class'), 403);
        abort_unless($exam->hasParticipant($student), 403);

        if (! $exam->isAvailable()) {
            return back()->withErrors(['exam' => 'Ujian belum tersedia atau sudah melewati jadwal.']);
        }

        $existing = $student->examSessions()->where('exam_id', $exam->id)->first();

        if ($existing?->isFinished() && ! $exam->allow_retake) {
            return redirect()->route('exam.session.finished', $existing)
                ->with('status', 'Ujian ini sudah selesai dan tidak dapat diulang.');
        }

        $session = $existing && ! $existing->isFinished()
            ? $existing
            : $student->examSessions()->create([
                'exam_id' => $exam->id,
                'started_at' => now(),
                'status' => 'berlangsung',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

        if (! $existing) {
            ActivityLog::record('exam_started', 'Siswa mulai ujian.', exam: $exam, session: $session, request: $request);
        }

        return redirect()->route('exam.session.show', [$exam, $session]);
    }

    public function show(Request $request, Exam $exam, ExamSession $session): View|RedirectResponse
    {
        $this->authorizeSession($request, $exam, $session);

        if ($session->isFinished()) {
            return redirect()->route('exam.session.finished', $session);
        }

        if ($session->remainingSeconds() <= 0) {
            return $this->expire($request, $session);
        }

        ActivityLog::record('exam_page_opened', 'Siswa membuka halaman ujian.', exam: $exam, session: $session, request: $request);

        $formUrl = $exam->prefilledUrlFor($request->user());

        return view('student.exam', compact('exam', 'session', 'formUrl'));
    }

    public function finish(Request $request, ExamSession $session): RedirectResponse|JsonResponse
    {
        abort_unless($session->user_id === $request->user()->id, 403);

        $session->markFinished('selesai');
        ActivityLog::record('exam_finished', 'Siswa klik selesai ujian.', session: $session, request: $request);

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Ujian berhasil dikumpulkan.',
                'redirect' => route('exam.session.finished', $session),
            ]);
        }

        return redirect()->route('exam.session.finished', $session)
            ->with('status', 'Ujian berhasil dikumpulkan.');
    }

    public function tabSwitch(Request $request, ExamSession $session): JsonResponse
    {
        abort_unless($session->user_id === $request->user()->id, 403);

        $session->increment('tab_switch_count');
        ActivityLog::record('tab_switch', 'Siswa pindah tab atau aplikasi.', session: $session, request: $request);

        return response()->json(['count' => $session->refresh()->tab_switch_count]);
    }

    public function fullscreenExit(Request $request, ExamSession $session): JsonResponse
    {
        abort_unless($session->user_id === $request->user()->id, 403);

        $session->increment('fullscreen_exit_count');
        ActivityLog::record('fullscreen_exit', 'Siswa keluar dari fullscreen.', session: $session, request: $request);

        return response()->json(['count' => $session->refresh()->fullscreen_exit_count]);
    }

    public function finished(Request $request, ExamSession $session): View
    {
        abort_unless($session->user_id === $request->user()->id, 403);

        $session->load('exam', 'user');

        return view('student.finished', compact('session'));
    }

    private function authorizeSession(Request $request, Exam $exam, ExamSession $session): void
    {
        abort_unless($session->user_id === $request->user()->id, 403);
        abort_unless($session->exam_id === $exam->id, 404);
        abort_unless($exam->hasParticipant($request->user()), 403);
    }

    private function expire(Request $request, ExamSession $session): RedirectResponse
    {
        $session->markFinished('waktu_habis');
        ActivityLog::record('time_expired', 'Waktu ujian habis.', session: $session, request: $request);

        return redirect()->route('exam.session.finished', $session)
            ->with('status', 'Waktu ujian habis. Ujian otomatis diselesaikan.');
    }
}
