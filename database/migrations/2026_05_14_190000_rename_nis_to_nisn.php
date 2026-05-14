<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('nis', 'nisn');
        });

        Schema::table('exam_results', function (Blueprint $table) {
            $table->renameColumn('nis', 'nisn');
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->renameColumn('prefill_nis_field', 'prefill_nisn_field');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->renameColumn('prefill_nisn_field', 'prefill_nis_field');
        });

        Schema::table('exam_results', function (Blueprint $table) {
            $table->renameColumn('nisn', 'nis');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('nisn', 'nis');
        });
    }
};
