<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates daily_reports table if missing (fallback for servers where
     * the original migration in 2026_07_02_120005 failed to run).
     */
    public function up(): void
    {
        if (!Schema::hasTable('daily_reports')) {
            Schema::create('daily_reports', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
                $table->date('report_date');
                $table->string('weather_conditions', 255)->nullable();
                $table->integer('temperature')->nullable();
                $table->integer('total_manpower')->default(0);
                $table->text('general_notes')->nullable();
                $table->text('safety_incidents')->nullable();
                $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft')->index();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->text('site_diary_remark')->nullable();
                $table->string('site_book_pic')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        } else {
            // Table already exists — just ensure the new columns are present
            Schema::table('daily_reports', function (Blueprint $table) {
                if (!Schema::hasColumn('daily_reports', 'site_diary_remark')) {
                    $table->text('site_diary_remark')->nullable()->after('status');
                }
                if (!Schema::hasColumn('daily_reports', 'site_book_pic')) {
                    $table->string('site_book_pic')->nullable()->after('site_diary_remark');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
