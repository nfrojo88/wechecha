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
        // Performance Metrics
        if (!Schema::hasTable('performance_metrics')) {
            Schema::create('performance_metrics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->date('period_start');
                $table->date('period_end');
                $table->string('metric_name')->comment('e.g., Tasks Completed, Quality Score, Safety Incidents');
                $table->decimal('metric_value', 10, 2);
                $table->decimal('target_value', 10, 2);
                $table->decimal('weight', 4, 2)->default(1)->comment('Weight in overall score');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['employee_id', 'period_start']);
            });
        }

        // Performance Reviews
        if (!Schema::hasTable('performance_reviews')) {
            Schema::create('performance_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('reviewer_id')->constrained('users')->restrictOnDelete();
                $table->date('review_period')->comment('Date of review period end');
                $table->decimal('overall_score', 3, 1)->comment('1-5 scale');
                $table->decimal('technical_skills_score', 3, 1);
                $table->decimal('soft_skills_score', 3, 1);
                $table->decimal('attendance_score', 3, 1);
                $table->decimal('productivity_score', 3, 1);
                $table->decimal('communication_score', 3, 1);
                $table->decimal('teamwork_score', 3, 1);
                $table->text('comments')->nullable();
                $table->text('strengths')->nullable();
                $table->text('areas_for_improvement')->nullable();
                $table->text('development_plan')->nullable();
                $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft')->index();
                $table->dateTime('reviewed_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['employee_id', 'review_period']);
            });
        }

        // Performance Goals
        if (!Schema::hasTable('performance_goals')) {
            Schema::create('performance_goals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->string('goal_title');
                $table->text('description');
                $table->date('start_date');
                $table->date('target_date');
                $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
                $table->decimal('target_value', 10, 2)->nullable()->comment('Quantifiable target');
                $table->decimal('current_value', 10, 2)->default(0);
                $table->enum('status', ['not_started', 'in_progress', 'completed', 'on_hold'])->default('not_started')->index();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['employee_id', 'status']);
            });
        }

        // Competencies (Skills Framework)
        if (!Schema::hasTable('competencies')) {
            Schema::create('competencies', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->enum('category', ['technical', 'soft', 'leadership', 'specialized'])->default('technical');
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
            });
        }

        // Employee Competency Assessment
        if (!Schema::hasTable('competency_assessments')) {
            Schema::create('competency_assessments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('competency_id')->constrained('competencies')->cascadeOnDelete();
                $table->integer('current_level')->comment('1-5 scale');
                $table->integer('target_level')->comment('Desired level');
                $table->date('assessed_date');
                $table->foreignId('assessed_by')->constrained('users')->restrictOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['employee_id', 'competency_id'], 'comp_assess_emp_comp_unique');
                $table->index('employee_id');
            });
        }

        // Recognition/Achievements
        if (!Schema::hasTable('employee_achievements')) {
            Schema::create('employee_achievements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->string('achievement_type')->comment('Award, Certification, Project Completion, etc.');
                $table->string('title');
                $table->text('description')->nullable();
                $table->date('achievement_date');
                $table->string('issuing_authority')->nullable();
                $table->decimal('award_amount', 10, 2)->nullable()->comment('Bonus amount if applicable');
                $table->timestamps();

                $table->index(['employee_id', 'achievement_date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_achievements');
        Schema::dropIfExists('competency_assessments');
        Schema::dropIfExists('competencies');
        Schema::dropIfExists('performance_goals');
        Schema::dropIfExists('performance_reviews');
        Schema::dropIfExists('performance_metrics');
    }
};
