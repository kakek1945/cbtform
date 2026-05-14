<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'exam_id',
    'user_id',
    'identifier',
    'student_name',
    'nisn',
    'class',
    'score',
    'max_score',
    'percentage',
    'submitted_at',
    'imported_at',
    'raw_payload',
])]
class ExamResult extends Model
{
    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'percentage' => 'decimal:2',
            'submitted_at' => 'datetime',
            'imported_at' => 'datetime',
            'raw_payload' => 'array',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
