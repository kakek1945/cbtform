<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $summary = [
            'students' => User::where('role', 'siswa')->count(),
            'exams' => Exam::count(),
            'active' => ExamSession::where('status', 'berlangsung')->count(),
            'finished' => ExamSession::where('status', 'selesai')->count(),
            'expired' => ExamSession::where('status', 'waktu_habis')->count(),
        ];

        $recentSessions = ExamSession::with(['user', 'exam'])
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact('summary', 'recentSessions'));
    }
}
