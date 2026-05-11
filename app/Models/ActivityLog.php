<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

#[Fillable(['user_id', 'exam_id', 'exam_session_id', 'activity_type', 'description', 'ip_address', 'user_agent'])]
class ActivityLog extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function examSession(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class);
    }

    public static function record(
        string $activityType,
        ?string $description = null,
        ?User $user = null,
        ?Exam $exam = null,
        ?ExamSession $session = null,
        ?Request $request = null
    ): self {
        $request ??= request();
        $user ??= $request->user();

        return self::create([
            'user_id' => $user?->id,
            'exam_id' => $exam?->id ?? $session?->exam_id,
            'exam_session_id' => $session?->id,
            'activity_type' => $activityType,
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
