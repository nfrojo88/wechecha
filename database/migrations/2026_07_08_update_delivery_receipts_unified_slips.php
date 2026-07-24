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
        Schema::table('delivery_receipts', function (Blueprint $table) {
            // Add slip_type column (receive or send)
            if (!Schema::hasColumn('delivery_receipts', 'slip_type')) {
                $table->enum('slip_type', ['receive', 'send'])->default('receive')->after('dr_no');
            }

            // Add is_void flag for audit
            if (!Schema::hasColumn('delivery_receipts', 'is_void')) {
                $table->boolean('is_void')->default(false)->after('status');
            }

            // Add sequence_status for validation
            if (!Schema::hasColumn('delivery_receipts', 'sequence_status')) {
                $table->enum('sequence_status', ['valid', 'gap', 'pending'])->default('pending')->after('is_void');
            }

            // Add to_store_id for send slips
            if (!Schema::hasColumn('delivery_receipts', 'to_store_id')) {
                $table->foreignId('to_store_id')->nullable()->constrained('stores')->nullOnDelete()->after('store_id');
            }

            // Add created_by for audit trail
            if (!Schema::hasColumn('delivery_receipts', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            }

            // Add supplier_name for slips (optional)
            if (!Schema::hasColumn('delivery_receipts', 'supplier_name')) {
                $table->string('supplier_name', 255)->nullable();
            }

            // Add reference_no for slips (optional)
            if (!Schema::hasColumn('delivery_receipts', 'reference_no')) {
                $table->string('reference_no', 100)->nullable();
            }

            // Add receipt_date for slips (alternative to received_date)
            if (!Schema::hasColumn('delivery_receipts', 'receipt_date')) {
                $table->date('receipt_date')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_receipts', function (Blueprint $table) {
            $table->dropColumnIfExists(['slip_type', 'is_void', 'sequence_status', 'to_store_id', 'created_by', 'supplier_name', 'reference_no', 'receipt_date']);
        });
    }
};
