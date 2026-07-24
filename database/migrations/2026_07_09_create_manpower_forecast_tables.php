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
        // Manpower Forecast
        if (!Schema::hasTable('manpower_forecasts')) {
            Schema::create('manpower_forecasts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
                $table->date('week_starting')->comment('Start date of the week for forecast');
                $table->foreignId('designation_id')->constrained('designations')->restrictOnDelete();
                $table->decimal('forecasted_headcount', 6, 2)->comment('Number of people needed');
                $table->decimal('forecasted_hours', 10, 2)->comment('Total hours forecasted for the week');
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
                $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft')->index();
                $table->timestamps();

                // Use explicit shorter index name to avoid MySQL identifier length limit (64 chars)
                $table->unique(['project_id', 'week_starting', 'designation_id'], 'mp_forecast_unique');
                $table->index(['project_id', 'week_starting'], 'mp_forecast_proj_week');
            });
        }

        // Manpower Assignment (Match employees to forecasts)
        if (!Schema::hasTable('manpower_assignments')) {
            Schema::create('manpower_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('manpower_forecast_id')->constrained('manpower_forecasts')->cascadeOnDelete();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->decimal('hours_assigned', 10, 2)->default(40)->comment('Hours assigned for the week');
                $table->boolean('billable')->default(true)->comment('Whether hours are billable to client');
                $table->text('notes')->nullable();
                $table->enum('status', ['assigned', 'confirmed', 'completed'])->default('assigned')->index();
                $table->timestamps();

                $table->index(['manpower_forecast_id', 'employee_id']);
            });
        }

        // Skill Matrix (Employee Skills)
        if (!Schema::hasTable('employee_skills')) {
            Schema::create('employee_skills', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->string('skill_name');
                $table->enum('proficiency', ['beginner', 'intermediate', 'expert'])->default('beginner');
                $table->integer('years_of_experience')->default(0);
                $table->timestamp('last_updated')->nullable();
                $table->timestamps();

                $table->unique(['employee_id', 'skill_name']);
                $table->index('employee_id');
            });
        }

        // Resource Availability (Track days/times employees are available)
        if (!Schema::hasTable('resource_availability')) {
            Schema::create('resource_availability', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->date('available_from');
                $table->date('available_until');
                $table->decimal('available_hours_per_week', 6, 2)->default(40);
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();

                $table->index(['employee_id', 'available_from']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_availability');
        Schema::dropIfExists('employee_skills');
        Schema::dropIfExists('manpower_assignments');
        Schema::dropIfExists('manpower_forecasts');
    }
};
