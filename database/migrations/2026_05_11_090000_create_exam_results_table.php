<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('identifier')->nullable();
            $table->string('student_name')->nullable();
            $table->string('nis')->nullable();
            $table->string('class')->nullable();
            $table->decimal('score', 8, 2)->nullable();
            $table->decimal('max_score', 8, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->index(['exam_id', 'user_id']);
            $table->index(['exam_id', 'identifier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
