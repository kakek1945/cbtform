<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function __invoke(Request $request): View
    {
        $logs = ActivityLog::query()
            ->with(['user', 'exam', 'examSession'])
            ->when($request->activity_type, fn ($query, string $type) => $query->where('activity_type', $type))
            ->when($request->exam_id, fn ($query, string $examId) => $query->where('exam_id', $examId))
            ->when($request->date, fn ($query, string $date) => $query->whereDate('created_at', $date))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $exams = Exam::orderBy('title')->get();
        $activityTypes = ActivityLog::query()->select('activity_type')->distinct()->orderBy('activity_type')->pluck('activity_type');

        return view('admin.activity-logs.index', compact('logs', 'exams', 'activityTypes'));
    }
}
