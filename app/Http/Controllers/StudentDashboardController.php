<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $student = $request->user();

        $exams = Exam::query()
            ->where('class', $student->getAttribute('class'))
            ->whereHas('participants', fn ($query) => $query->whereKey($student->id))
            ->with(['sessions' => fn ($query) => $query->where('user_id', $student->id)])
            ->withCount('participants')
            ->orderBy('start_time')
            ->get();

        return view('student.dashboard', compact('student', 'exams'));
    }
}
