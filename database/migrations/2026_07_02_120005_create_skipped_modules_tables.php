<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkippedModulesTables extends Migration
{
    public function up()
    {
        // Guard: skip entirely if already migrated (tables exist from prior run)
        if (Schema::hasTable('weekly_plan_dispatches')) {
            return;
        }

        // 1. Task Dispatching (Phase 3)
        if (!Schema::hasTable('weekly_plan_dispatches'))
        Schema::create('weekly_plan_dispatches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('dispatch_no', 50)->unique(); // WPD-YYYYMMDD-XXXX
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->date('week_start');
            $table->date('week_end');
            $table->foreignId('dispatched_to')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'active', 'completed'])->default('draft');
            $table->timestamps();
        });

        Schema::create('weekly_plan_dispatch_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('dispatch_id')->constrained('weekly_plan_dispatches')->cascadeOnDelete();
            $table->foreignId('schedule_task_id')->constrained('schedule_tasks')->cascadeOnDelete();
            $table->string('task_name', 255);
            $table->decimal('planned_quantity', 15, 3)->default(0);
            $table->string('unit', 20)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 2. Subcontractors & IPCs (Phase 7)
        Schema::create('subcon_agreements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('agreement_no', 50)->unique(); // SA-YYYYMMDD-XXXX
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('subcontractor_name', 255);
            $table->string('subcontractor_contact', 255)->nullable();
            $table->text('scope_of_work');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('contract_value', 18, 2)->default(0);
            $table->decimal('paid_to_date', 18, 2)->default(0);
            $table->decimal('retention_percent', 5, 2)->default(10);
            $table->decimal('retention_amount', 18, 2)->default(0); // storedAs contract_value * retention_percent / 100
            $table->enum('status', ['draft', 'active', 'completed', 'terminated', 'cancelled'])->default('draft')->index();
            $table->text('terms_conditions')->nullable();
            $table->string('agreement_file', 500)->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('subcon_agreement_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('agreement_id')->constrained('subcon_agreements')->cascadeOnDelete();
            $table->foreignId('boq_item_id')->nullable()->constrained('boq_items')->nullOnDelete();
            $table->text('description');
            $table->string('unit', 20)->nullable();
            $table->decimal('quantity', 15, 3);
            $table->decimal('unit_rate', 15, 2);
            $table->decimal('total_amount', 18, 2)->default(0); // quantity * unit_rate
            $table->decimal('completed_percent', 5, 2)->default(0);
            $table->decimal('earned_value', 18, 2)->default(0); // total_amount * completed_percent / 100
            $table->decimal('certified_amount', 18, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('ipc_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ipc_no', 50)->unique(); // IPC-XXXX-NN
            $table->foreignId('agreement_id')->constrained('subcon_agreements')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->integer('ipc_number'); // 1st, 2nd, etc.
            $table->date('period_from');
            $table->date('period_to');
            $table->decimal('previous_certified', 18, 2)->default(0);
            $table->decimal('current_work_value', 18, 2)->default(0);
            $table->decimal('current_retention', 18, 2)->default(0);
            $table->decimal('current_certified', 18, 2)->default(0);
            $table->decimal('cumulative_certified', 18, 2)->default(0);
            $table->decimal('balance_to_certify', 18, 2)->default(0);
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'paid', 'rejected'])->default('draft')->index();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ipc_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('ipc_record_id')->constrained('ipc_records')->cascadeOnDelete();
            $table->foreignId('agreement_item_id')->constrained('subcon_agreement_items')->cascadeOnDelete();
            $table->decimal('previous_percent', 5, 2)->default(0);
            $table->decimal('current_percent', 5, 2)->default(0);
            $table->decimal('boq_value', 18, 2);
            $table->decimal('current_value', 18, 2)->default(0); // boq_value * (current_percent - previous_percent) / 100
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // 3. Field Reporting (Phase 8 Extension)
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
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('daily_report_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('daily_report_id')->constrained('daily_reports')->cascadeOnDelete();
            $table->foreignId('schedule_task_id')->nullable()->constrained('schedule_tasks')->nullOnDelete();
            $table->text('work_description');
            $table->decimal('qty_completed', 15, 3)->default(0);
            $table->integer('workers_count')->default(0);
            $table->string('equipment_used', 255)->nullable();
            $table->text('issues')->nullable();
            $table->timestamps();
        });

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

    public function down()
    {
        Schema::dropIfExists('weekly_reports');
        Schema::dropIfExists('daily_report_items');
        Schema::dropIfExists('daily_reports');
        Schema::dropIfExists('ipc_items');
        Schema::dropIfExists('ipc_records');
        Schema::dropIfExists('subcon_agreement_items');
        Schema::dropIfExists('subcon_agreements');
        Schema::dropIfExists('weekly_plan_dispatch_tasks');
        Schema::dropIfExists('weekly_plan_dispatches');
    }
}
