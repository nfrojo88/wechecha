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
        // Salary Structure (base salary + allowances breakdown)
        if (!Schema::hasTable('salary_structures')) {
            Schema::create('salary_structures', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->decimal('base_salary', 12, 2);
                $table->decimal('house_allowance', 12, 2)->default(0);
                $table->decimal('transport_allowance', 12, 2)->default(0);
                $table->decimal('meal_allowance', 12, 2)->default(0);
                $table->decimal('other_allowance', 12, 2)->default(0);
                $table->decimal('gross_salary', 12, 2);
                $table->date('effective_from');
                $table->date('effective_to')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();

                $table->index(['employee_id', 'is_active']);
            });
        }

        // Payroll Components (for breakdown)
        if (!Schema::hasTable('payroll_components')) {
            Schema::create('payroll_components', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payroll_id')->constrained('payrolls')->cascadeOnDelete();
                $table->string('component_name')->comment('e.g., Basic, HRA, Transport, Performance Bonus');
                $table->enum('type', ['earning', 'deduction'])->default('earning');
                $table->decimal('amount', 12, 2);
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index('payroll_id');
            });
        }

        // Payroll Adjustments (manual adjustments, bonuses, etc.)
        if (!Schema::hasTable('payroll_adjustments')) {
            Schema::create('payroll_adjustments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payroll_id')->constrained('payrolls')->cascadeOnDelete();
                $table->string('adjustment_type')->comment('Bonus, Fine, Advance, Loan Deduction');
                $table->decimal('amount', 12, 2);
                $table->text('reason');
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('approved_at')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
                $table->timestamps();

                $table->index('payroll_id');
            });
        }

        // Employee Advances (salary advances)
        if (!Schema::hasTable('employee_advances')) {
            Schema::create('employee_advances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->decimal('amount', 12, 2);
                $table->date('advance_date');
                $table->integer('installments')->default(1)->comment('Number of months to recover');
                $table->string('reason')->nullable();
                $table->enum('status', ['pending', 'approved', 'disbursed', 'recovered'])->default('pending')->index();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('approved_at')->nullable();
                $table->dateTime('disbursed_at')->nullable();
                $table->dateTime('recovered_at')->nullable();
                $table->timestamps();

                $table->index('employee_id');
            });
        }

        // Add columns to existing payrolls table
        if (Schema::hasTable('payrolls')) {
            Schema::table('payrolls', function (Blueprint $table) {
                if (!Schema::hasColumn('payrolls', 'payroll_ref')) {
                    $table->string('payroll_ref')->nullable()->unique()->after('id')->comment('Reference number for payroll batch');
                }
                if (!Schema::hasColumn('payrolls', 'gross_salary')) {
                    $table->decimal('gross_salary', 12, 2)->nullable()->after('overtime_pay');
                }
                if (!Schema::hasColumn('payrolls', 'remarks')) {
                    $table->text('remarks')->nullable()->after('notes');
                }
                if (!Schema::hasColumn('payrolls', 'payment_method')) {
                    $table->enum('payment_method', ['bank_transfer', 'cash', 'cheque'])->default('bank_transfer')->after('remarks');
                }
                if (!Schema::hasColumn('payrolls', 'processed_at')) {
                    $table->dateTime('processed_at')->nullable()->after('paid_at');
                }
                if (!Schema::hasColumn('payrolls', 'processed_by')) {
                    $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete()->after('created_by');
                }
            });
        }

        // Payroll Summary (monthly aggregate)
        if (!Schema::hasTable('payroll_summaries')) {
            Schema::create('payroll_summaries', function (Blueprint $table) {
                $table->id();
                $table->integer('year');
                $table->integer('month');
                $table->integer('total_employees');
                $table->decimal('total_gross', 14, 2);
                $table->decimal('total_allowances', 14, 2);
                $table->decimal('total_deductions', 14, 2);
                $table->decimal('total_taxes', 14, 2);
                $table->decimal('total_net', 14, 2);
                $table->integer('processed_count')->default(0);
                $table->integer('paid_count')->default(0);
                $table->enum('status', ['draft', 'processing', 'processed', 'submitted', 'approved', 'paid'])->default('draft')->index();
                $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
                $table->dateTime('finalized_at')->nullable();
                $table->timestamps();

                $table->unique(['year', 'month'], 'payroll_summary_year_month');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_summaries');
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['processed_by']);
            $table->dropColumn(['payroll_ref', 'gross_salary', 'remarks', 'payment_method', 'processed_at', 'processed_by']);
        });
        Schema::dropIfExists('employee_advances');
        Schema::dropIfExists('payroll_adjustments');
        Schema::dropIfExists('payroll_components');
        Schema::dropIfExists('salary_structures');
    }
};
