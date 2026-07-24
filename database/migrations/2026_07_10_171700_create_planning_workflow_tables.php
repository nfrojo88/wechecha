<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. project_plan_workflows ─────────────────────────────────────
        Schema::create('project_plan_workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->enum('plan_type', ['initial', 'revision'])->default('initial');

            // State machine
            $table->enum('status', [
                'draft',
                'submitted',
                'planning_manager_approved',
                'coordinator_approved',
                'technical_manager_approved',
                'gm_approved',
                'rejected',
            ])->default('draft');

            // Step 1 — Planning Manager
            $table->foreignId('planning_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('planning_manager_at')->nullable();
            $table->text('planning_manager_note')->nullable();

            // Step 2 — Coordinator
            $table->foreignId('coordinator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('coordinator_at')->nullable();
            $table->text('coordinator_note')->nullable();

            // Step 3 — Technical Manager
            $table->foreignId('tech_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tech_manager_at')->nullable();
            $table->text('tech_manager_note')->nullable();

            // Step 4 — GM (also allocates budget)
            $table->foreignId('gm_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('gm_at')->nullable();
            $table->text('gm_note')->nullable();
            $table->decimal('budget_allocated', 18, 2)->nullable();

            // Rejection tracking
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('rejected_at_step')->nullable(); // which step was rejected

            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['project_id', 'status']);
        });

        // ── 2. project_budget_allocations (audit trail) ──────────────────
        Schema::create('project_budget_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workflow_id')->nullable()->constrained('project_plan_workflows')->nullOnDelete();
            $table->decimal('amount', 18, 2);
            $table->string('allocation_type')->default('initial'); // initial | supplement
            $table->text('reason')->nullable();
            $table->foreignId('allocated_by')->constrained('users');
            $table->timestamp('allocated_at');
            $table->timestamps();
        });

        // ── 3. Add planning_phase_status to projects ──────────────────────
        if (!Schema::hasColumn('projects', 'planning_phase_status')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->string('planning_phase_status', 60)->default('draft')->after('status');
            });
        }

        // ── 4. Add budget_status to expenses ──────────────────────────────
        if (!Schema::hasColumn('expenses', 'budget_status')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->string('budget_status', 20)->nullable()->after('status');
                // safe | at_risk | blocked
            });
        }

        // ── 5. Add budget_status to purchase_orders ───────────────────────
        if (!Schema::hasColumn('purchase_orders', 'budget_status')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->string('budget_status', 20)->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_budget_allocations');
        Schema::dropIfExists('project_plan_workflows');

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('planning_phase_status');
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('budget_status');
        });
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('budget_status');
        });
    }
};
