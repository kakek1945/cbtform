<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subject');
            $table->string('class');
            $table->text('google_form_url');
            $table->string('prefill_name_field')->nullable();
            $table->string('prefill_nis_field')->nullable();
            $table->string('prefill_class_field')->nullable();
            $table->string('prefill_exam_field')->nullable();
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->unsignedInteger('duration_minutes');
            $table->boolean('is_active')->default(true);
            $table->boolean('allow_retake')->default(false);
            $table->text('instructions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
