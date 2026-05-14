<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

#[Fillable([
    'title',
    'code',
    'subject',
    'class',
    'google_form_url',
    'result_spreadsheet_id',
    'result_sheet_name',
    'prefill_name_field',
    'prefill_username_field',
    'prefill_nisn_field',
    'prefill_class_field',
    'prefill_exam_field',
    'start_time',
    'end_time',
    'duration_minutes',
    'is_active',
    'allow_retake',
    'show_score',
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
            'show_score' => 'boolean',
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
        $usernameField = filled($this->prefill_username_field)
            ? $this->prefill_username_field
            : $this->detectGoogleFormUsernameField();

        $params = array_filter([
            $this->prefill_name_field => $student->name,
            $usernameField => $student->username,
            $this->prefill_nisn_field => $student->nisn,
            $this->prefill_class_field => $student->getAttribute('class'),
            $this->prefill_exam_field => $this->title,
        ], fn ($value, $key) => filled($key) && filled($value), ARRAY_FILTER_USE_BOTH);

        if ($params === []) {
            return $this->google_form_url;
        }

        return $this->urlWithPrefilledParams($this->resolvedGoogleFormUrl(), $params);
    }

    private function urlWithPrefilledParams(string $url, array $params): string
    {
        $fragment = '';

        if (str_contains($url, '#')) {
            [$url, $fragment] = explode('#', $url, 2);
            $fragment = '#'.$fragment;
        }

        [$baseUrl, $queryString] = array_pad(explode('?', $url, 2), 2, '');
        $query = $this->parseQueryString($queryString);

        foreach ($params as $key => $value) {
            $query[$key] = $value;
        }

        return $baseUrl.'?'.$this->buildQueryString($query).$fragment;
    }

    private function parseQueryString(string $queryString): array
    {
        if ($queryString === '') {
            return [];
        }

        $query = [];

        foreach (explode('&', $queryString) as $part) {
            if ($part === '') {
                continue;
            }

            [$key, $value] = array_pad(explode('=', $part, 2), 2, '');
            $query[urldecode($key)] = urldecode($value);
        }

        return $query;
    }

    private function buildQueryString(array $query): string
    {
        return collect($query)
            ->map(fn ($value, $key) => rawurlencode((string) $key).'='.rawurlencode((string) $value))
            ->implode('&');
    }

    private function detectGoogleFormUsernameField(): ?string
    {
        if (blank($this->google_form_url)) {
            return null;
        }

        return Cache::remember('google-form-username-field:'.sha1($this->google_form_url), now()->addDay(), function (): ?string {
            try {
                $html = Http::timeout(15)->get($this->google_form_url)->body();
            } catch (\Throwable) {
                return null;
            }

            $decoded = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            if (preg_match('/%\.\@\.\[\d+,\s*"([^"]*(?:username|nama pengguna)[^"]*)".*?\[\[(\d+),/is', $decoded, $matches)) {
                return 'entry.'.$matches[2];
            }

            return null;
        });
    }

    private function resolvedGoogleFormUrl(): string
    {
        if (! str_contains($this->google_form_url, 'forms.gle')) {
            return $this->google_form_url;
        }

        return Cache::remember('google-form-resolved-url:'.sha1($this->google_form_url), now()->addDay(), function (): string {
            try {
                $response = Http::timeout(15)->get($this->google_form_url);

                return (string) $response->effectiveUri();
            } catch (\Throwable) {
                return $this->google_form_url;
            }
        });
    }
}
