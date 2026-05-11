<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'exam_id',
    'started_at',
    'finished_at',
    'status',
    'tab_switch_count',
    'fullscreen_exit_count',
    'ip_address',
    'user_agent',
])]
class ExamSession extends Model
{
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isFinished(): bool
    {
        return in_array($this->status, ['selesai', 'waktu_habis'], true);
    }

    public function expiresAt()
    {
        return $this->started_at?->copy()->addMinutes($this->exam->duration_minutes);
    }

    public function remainingSeconds(): int
    {
        if (! $this->started_at) {
            return $this->exam->duration_minutes * 60;
        }

        return (int) max(0, now()->diffInSeconds($this->expiresAt(), false));
    }

    public function markFinished(string $status = 'selesai'): self
    {
        if (! $this->isFinished()) {
            $this->forceFill([
                'status' => $status,
                'finished_at' => now(),
            ])->save();
        }

        return $this->refresh();
    }
}
