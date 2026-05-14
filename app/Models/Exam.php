<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

#[Fillable([
    'title',
    'code',
    'subject',
    'class',
    'google_form_url',
    'result_spreadsheet_id',
    'result_sheet_name',
    'prefill_name_field',
    'prefill_nisn_field',
    'prefill_class_field',
    'prefill_exam_field',
    'start_time',
    'end_time',
    'duration_minutes',
    'is_active',
    'allow_retake',
    'instructions',
])]
class Exam extends Model
{
    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'is_active' => 'boolean',
            'allow_retake' => 'boolean',
        ];
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'exam_participants')
            ->withTimestamps();
    }

    public function hasParticipant(User $student): bool
    {
        return $this->participants instanceof Collection
            ? $this->participants->contains('id', $student->id)
            : $this->participants()->whereKey($student->id)->exists();
    }

    public function isAvailable(?Carbon $now = null): bool
    {
        $now ??= now();

        return $this->is_active
            && $this->start_time->lessThanOrEqualTo($now)
            && $this->end_time->greaterThanOrEqualTo($now);
    }

    public function statusFor(User $student, ?ExamSession $session = null): string
    {
        $session ??= $this->sessions instanceof Collection
            ? $this->sessions->firstWhere('user_id', $student->id)
            : $this->sessions()->where('user_id', $student->id)->first();

        if ($session) {
            return $session->status;
        }

        if (! $this->is_active || now()->lt($this->start_time)) {
            return 'belum_mulai';
        }

        if (now()->gt($this->end_time)) {
            return 'waktu_habis';
        }

        return 'tersedia';
    }

    public function prefilledUrlFor(User $student): string
    {
        $params = array_filter([
            $this->prefill_name_field => $student->name,
            $this->prefill_nisn_field => $student->nisn,
            $this->prefill_class_field => $student->getAttribute('class'),
            $this->prefill_exam_field => $this->title,
        ], fn ($value, $key) => filled($key) && filled($value), ARRAY_FILTER_USE_BOTH);

        if ($params === []) {
            return $this->google_form_url;
        }

        $separator = str_contains($this->google_form_url, '?') ? '&' : '?';

        return $this->google_form_url.$separator.Arr::query($params);
    }
}
