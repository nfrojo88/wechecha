<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // ── Marketing Price Variance ───────────────────────────────────────
        Schema::create('pr_marketing_variances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')
                  ->constrained('purchase_requests')->cascadeOnDelete();
            $table->decimal('market_price', 18, 2)->nullable();
            $table->decimal('variance_amount', 18, 2)->nullable();
            $table->decimal('variance_percentage', 8, 2)->nullable();
            $table->text('variance_notes')->nullable();
            $table->foreignId('added_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // ── Procurement Payments (COA-based, creates journal entry) ────────
        Schema::create('procurement_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')
                  ->constrained('purchase_requests')->cascadeOnDelete();
            $table->enum('method', ['cash', 'credit'])->default('cash');
            // Finance Head selects a COA account to debit
            $table->foreignId('coa_account_id')->nullable()
                  ->constrained('chart_of_accounts')->nullOnDelete();
            $table->decimal('amount', 18, 2)->default(0);
            // For cash path: Finance Head assigns Finance Staff
            $table->foreignId('assigned_finance_staff_id')->nullable()
                  ->constrained('users')->nullOnDelete();
            // The Finance Staff who actually executed the payment
            $table->foreignId('paid_by')->nullable()
                  ->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending_assignment', 'pending_payment', 'paid'])->default('pending_assignment');
            // Link to the journal entry created on payment
            $table->foreignId('journal_entry_id')->nullable()
                  ->constrained('journal_entries')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // ── Procurement Receipts (upload + verification) ───────────────────
        Schema::create('procurement_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')
                  ->constrained('purchase_requests')->cascadeOnDelete();
            $table->string('file_path', 500);
            $table->string('original_filename', 255)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('verified_by')->nullable()
                  ->constrained('users')->nullOnDelete();
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('verification_notes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        // ── Driver Bookings (linked to Employee from HR) ───────────────────
        Schema::create('driver_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')
                  ->constrained('purchase_requests')->cascadeOnDelete();
            // Driver is an Employee record managed by HR
            $table->foreignId('driver_employee_id')
                  ->constrained('employees')->cascadeOnDelete();
            $table->string('vehicle_number', 50)->nullable();
            $table->string('vehicle_description', 255)->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->text('booking_notes')->nullable();
            $table->foreignId('booked_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('driver_bookings');
        Schema::dropIfExists('procurement_receipts');
        Schema::dropIfExists('procurement_payments');
        Schema::dropIfExists('pr_marketing_variances');
    }
};
