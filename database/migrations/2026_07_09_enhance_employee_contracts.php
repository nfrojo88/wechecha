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
        // Add columns to existing employee_contracts table
        if (Schema::hasTable('employee_contracts')) {
            Schema::table('employee_contracts', function (Blueprint $table) {
                // Check if columns don't already exist before adding
                if (!Schema::hasColumn('employee_contracts', 'contract_number')) {
                    $table->string('contract_number')->nullable()->unique()->after('id');
                }
                if (!Schema::hasColumn('employee_contracts', 'approved_by')) {
                    $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('created_by');
                }
                if (!Schema::hasColumn('employee_contracts', 'approved_at')) {
                    $table->dateTime('approved_at')->nullable()->after('approved_by');
                }
                if (!Schema::hasColumn('employee_contracts', 'termination_reason')) {
                    $table->text('termination_reason')->nullable()->after('approved_at');
                }
                if (!Schema::hasColumn('employee_contracts', 'renewal_date')) {
                    $table->date('renewal_date')->nullable()->after('termination_reason');
                }
                if (!Schema::hasColumn('employee_contracts', 'is_renewable')) {
                    $table->boolean('is_renewable')->default(true)->after('renewal_date');
                }
                if (!Schema::hasColumn('employee_contracts', 'renewal_count')) {
                    $table->integer('renewal_count')->default(0)->after('is_renewable');
                }
                if (!Schema::hasColumn('employee_contracts', 'benefits_amount')) {
                    $table->decimal('benefits_amount', 12, 2)->nullable()->after('renewal_count');
                }
                if (!Schema::hasColumn('employee_contracts', 'special_terms')) {
                    $table->text('special_terms')->nullable()->after('benefits_amount');
                }
            });
        }

        // Contract Milestones (important dates/events)
        if (!Schema::hasTable('contract_milestones')) {
            Schema::create('contract_milestones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_contract_id')->constrained('employee_contracts')->cascadeOnDelete();
                $table->string('milestone_name')->comment('e.g., Probation End, Annual Review, Renewal');
                $table->date('milestone_date');
                $table->text('description')->nullable();
                $table->enum('status', ['pending', 'completed', 'missed'])->default('pending')->index();
                $table->timestamps();

                $table->index(['employee_contract_id', 'milestone_date']);
            });
        }

        // Contract Amendments (modifications to contract)
        if (!Schema::hasTable('contract_amendments')) {
            Schema::create('contract_amendments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_contract_id')->constrained('employee_contracts')->cascadeOnDelete();
                $table->string('amendment_title');
                $table->text('changes_description');
                $table->date('effective_date');
                $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected'])->default('draft')->index();
                $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('approved_at')->nullable();
                $table->text('approval_notes')->nullable();
                $table->string('amendment_document')->nullable();
                $table->timestamps();

                $table->index('employee_contract_id');
            });
        }

        // Contract Renewals
        if (!Schema::hasTable('contract_renewals')) {
            Schema::create('contract_renewals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_contract_id')->constrained('employee_contracts')->cascadeOnDelete();
                $table->date('renewal_date');
                $table->date('new_end_date');
                $table->decimal('new_salary', 12, 2)->nullable();
                $table->text('renewal_terms')->nullable();
                $table->enum('status', ['proposed', 'pending_approval', 'approved', 'rejected', 'completed'])->default('proposed')->index();
                $table->foreignId('proposed_by')->constrained('users')->restrictOnDelete();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('approved_at')->nullable();
                $table->text('approval_notes')->nullable();
                $table->timestamps();

                $table->index('employee_contract_id');
            });
        }

        // Contract Approvals (workflow)
        if (!Schema::hasTable('contract_approvals')) {
            Schema::create('contract_approvals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_contract_id')->constrained('employee_contracts')->cascadeOnDelete();
                $table->foreignId('approver_id')->constrained('users')->restrictOnDelete();
                $table->integer('approval_level')->comment('1 = Manager, 2 = HR, 3 = Finance, etc.');
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
                $table->text('comments')->nullable();
                $table->dateTime('responded_at')->nullable();
                $table->timestamps();

                $table->unique(['employee_contract_id', 'approver_id', 'approval_level'], 'contract_approval_emp_app_level');
                $table->index(['employee_contract_id', 'approval_level']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_approvals');
        Schema::dropIfExists('contract_renewals');
        Schema::dropIfExists('contract_amendments');
        Schema::dropIfExists('contract_milestones');
        Schema::table('employee_contracts', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['approved_by']);
            $table->dropColumn([
                'contract_number',
                'approved_by',
                'approved_at',
                'termination_reason',
                'renewal_date',
                'is_renewable',
                'renewal_count',
                'benefits_amount',
                'special_terms',
            ]);
        });
    }
};
