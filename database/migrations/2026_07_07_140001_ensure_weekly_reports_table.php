<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Standalone migration to ensure weekly_reports table exists.
     * This is independent of other tables so it cannot be blocked by FK errors.
     */
    public function up(): void
    {
        if (!Schema::hasTable('weekly_reports')) {
            Schema::create('weekly_reports', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
                $table->date('week_start');
                $table->date('week_end');
                $table->text('executive_summary')->nullable();
                $table->decimal('planned_progress_percent', 5, 2)->default(0);
                $table->decimal('actual_progress_percent', 5, 2)->default(0);
                $table->text('critical_issues')->nullable();
                $table->text('next_week_plan')->nullable();
                $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft')->index();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_reports');
    }
};
