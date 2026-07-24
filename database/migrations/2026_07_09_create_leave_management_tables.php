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
        // Leave Types
        if (!Schema::hasTable('leave_types')) {
            Schema::create('leave_types', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('code', 20)->unique();
                $table->integer('days_allowed')->comment('Days allowed per year');
                $table->boolean('is_paid')->default(true);
                $table->boolean('requires_documentation')->default(false);
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Leave Requests
        if (!Schema::hasTable('leave_requests')) {
            Schema::create('leave_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('leave_type_id')->constrained('leave_types')->restrictOnDelete();
                $table->date('start_date');
                $table->date('end_date');
                $table->text('reason')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending')->index();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('approved_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->string('attachment')->nullable()->comment('File path for supporting documents');
                $table->timestamps();
                $table->softDeletes();

                $table->index(['employee_id', 'status']);
                $table->index(['start_date', 'end_date']);
            });
        }

        // Leave Balance (Track used/remaining)
        if (!Schema::hasTable('leave_balances')) {
            Schema::create('leave_balances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();
                $table->integer('year');
                $table->decimal('total_days', 6, 2);
                $table->decimal('used_days', 6, 2)->default(0);
                $table->decimal('remaining_days', 6, 2);
                $table->timestamps();

                $table->unique(['employee_id', 'leave_type_id', 'year'], 'leave_balance_emp_type_year');
                $table->index(['employee_id', 'year']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('leave_types');
    }
};
