<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function instruction(Request $request, Exam $exam): View
    {
        $this->authorizeStudentExam($request, $exam);

        $session = $request->user()
            ->examSessions()
            ->where('exam_id', $exam->id)
            ->first();

        return view('student.instruction', compact('exam', 'session'));
    }

    private function authorizeStudentExam(Request $request, Exam $exam): void
    {
        abort_unless($exam->getAttribute('class') === $request->user()->getAttribute('class'), 403);
        abort_unless($exam->hasParticipant($request->user()), 403);
    }
}
