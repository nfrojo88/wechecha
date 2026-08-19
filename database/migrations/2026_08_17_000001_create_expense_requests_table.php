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
        if (!Schema::hasTable('expense_requests')) {
            Schema::create('expense_requests', function (Blueprint $table) {
                $table->id();
                $table->string('request_number', 50)->unique();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
                $table->string('category', 50); // Transport, Office Material, Loading & Unloading, Contract Work, Other
                $table->string('other_reason')->nullable();
                $table->decimal('amount', 12, 2);
                $table->text('description');
                $table->string('attachment')->nullable(); // Receipt photo/document URL or path
                $table->string('status', 50)->default('Pending (HR Review)'); // Pending (HR Review), Pending (GM Review), Rejected, Approved - Assigned to Finance, Assigned to Finance, Paid

                // Step 1: HR Review
                $table->foreignId('hr_reviewer_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('hr_reviewed_at')->nullable();

                // Step 2: GM Review (> 5000 ETB)
                $table->foreignId('gm_reviewer_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('gm_approver_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('gm_reviewed_at')->nullable();
                $table->timestamp('gm_approved_at')->nullable();

                // Rejection Reason
                $table->text('rejection_reason')->nullable();

                // Step 3: Finance Head Assignment
                $table->foreignId('finance_head_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
                $table->foreignId('coa_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
                $table->foreignId('chart_of_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
                $table->foreignId('assigned_finance_staff_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('finance_staff_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('finance_assigned_at')->nullable();

                // Step 4: Payment Processing
                $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('paid_at')->nullable();
                $table->string('payment_reference')->nullable();
                $table->text('payment_notes')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->index(['status', 'user_id']);
                $table->index(['assigned_finance_staff_id', 'status']);
                $table->index(['finance_staff_id', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_requests');
    }
};
