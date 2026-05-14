<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->string('code')->nullable()->unique()->after('title');
        });

        DB::table('exams')
            ->whereNull('code')
            ->orderBy('id')
            ->get()
            ->each(function ($exam): void {
                DB::table('exams')
                    ->where('id', $exam->id)
                    ->update(['code' => 'UJIAN-'.$exam->id]);
            });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
    }
};
