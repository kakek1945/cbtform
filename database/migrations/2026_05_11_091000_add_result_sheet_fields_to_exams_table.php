<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->string('result_spreadsheet_id')->nullable()->after('google_form_url');
            $table->string('result_sheet_name')->nullable()->after('result_spreadsheet_id');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['result_spreadsheet_id', 'result_sheet_name']);
        });
    }
};
