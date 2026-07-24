<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Handled by 2026_07_07_130001_ensure_daily_reports_table migration
        // This migration is kept as a no-op to avoid breaking existing installs
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropColumn(['site_diary_remark', 'site_book_pic']);
        });
    }
};
