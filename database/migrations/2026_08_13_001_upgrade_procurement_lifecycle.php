<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // ── material_requests: add planning approval columns ────────────────
        Schema::table('material_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('material_requests', 'planning_approval_status')) {
                $table->enum('planning_approval_status', ['pending', 'approved', 'rejected'])
                      ->nullable()->after('source');
            }
            if (!Schema::hasColumn('material_requests', 'planning_approved_by')) {
                $table->foreignId('planning_approved_by')->nullable()
                      ->constrained('users')->nullOnDelete()->after('planning_approval_status');
            }
            if (!Schema::hasColumn('material_requests', 'planning_approved_at')) {
                $table->timestamp('planning_approved_at')->nullable()->after('planning_approved_by');
            }
            if (!Schema::hasColumn('material_requests', 'planning_rejection_reason')) {
                $table->text('planning_rejection_reason')->nullable()->after('planning_approved_at');
            }
        });

        // ── purchase_requests: change status to VARCHAR + add sourcing fields ─
        if (Schema::hasColumn('purchase_requests', 'status')) {
            // Raw DB statement avoids requiring doctrine/dbal package
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `purchase_requests` MODIFY `status` VARCHAR(60) NOT NULL DEFAULT 'draft'");
        }

        Schema::table('purchase_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_requests', 'sourcing_method')) {
                $table->enum('sourcing_method', ['direct_buy', 'proforma'])
                      ->nullable()->after('status');
            }
            if (!Schema::hasColumn('purchase_requests', 'direct_buy_amount')) {
                $table->decimal('direct_buy_amount', 18, 2)->nullable()->after('sourcing_method');
            }
            if (!Schema::hasColumn('purchase_requests', 'direct_buy_added_by')) {
                $table->foreignId('direct_buy_added_by')->nullable()
                      ->constrained('users')->nullOnDelete()->after('direct_buy_amount');
            }
            if (!Schema::hasColumn('purchase_requests', 'procurement_team_notes')) {
                $table->text('procurement_team_notes')->nullable()->after('direct_buy_added_by');
            }
            if (!Schema::hasColumn('purchase_requests', 'pm_sendback_reason')) {
                $table->text('pm_sendback_reason')->nullable();
            }
            if (!Schema::hasColumn('purchase_requests', 'gm_loop_count')) {
                $table->unsignedTinyInteger('gm_loop_count')->default(0);
            }
            if (!Schema::hasColumn('purchase_requests', 'current_owner_role')) {
                $table->string('current_owner_role', 60)->nullable();
            }
        });

        // ── purchase_orders: link back to purchase_request ─────────────────
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'purchase_request_id')) {
                $table->foreignId('purchase_request_id')->nullable()
                      ->constrained('purchase_requests')->nullOnDelete()->after('project_id');
            }
            // Add supplier_id FK if missing (currently supplier_name is text)
            if (!Schema::hasColumn('purchase_orders', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()
                      ->constrained('suppliers')->nullOnDelete()->after('supplier_name');
            }
        });

        // ── transfers: link back to material_request ───────────────────────
        Schema::table('transfers', function (Blueprint $table) {
            if (!Schema::hasColumn('transfers', 'material_request_id')) {
                $table->foreignId('material_request_id')->nullable()
                      ->constrained('material_requests')->nullOnDelete()->after('id');
            }
        });

        // ── delivery_receipts: link to purchase_request ────────────────────
        Schema::table('delivery_receipts', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_receipts', 'purchase_request_id')) {
                $table->foreignId('purchase_request_id')->nullable()
                      ->constrained('purchase_requests')->nullOnDelete()->after('purchase_order_id');
            }
            if (!Schema::hasColumn('delivery_receipts', 'intake_completed_at')) {
                $table->timestamp('intake_completed_at')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('delivery_receipts', function (Blueprint $table) {
            $table->dropForeignSafe(['purchase_request_id']);
            $table->dropColumnIfExists(['purchase_request_id', 'intake_completed_at']);
        });

        Schema::table('transfers', function (Blueprint $table) {
            $table->dropForeignSafe(['material_request_id']);
            $table->dropColumnIfExists(['material_request_id']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeignSafe(['purchase_request_id', 'supplier_id']);
            $table->dropColumnIfExists(['purchase_request_id', 'supplier_id']);
        });

        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropForeignSafe(['direct_buy_added_by']);
            $table->dropColumnIfExists([
                'sourcing_method', 'direct_buy_amount', 'direct_buy_added_by',
                'procurement_team_notes', 'pm_sendback_reason', 'gm_loop_count', 'current_owner_role'
            ]);
        });

        Schema::table('material_requests', function (Blueprint $table) {
            $table->dropForeignSafe(['planning_approved_by']);
            $table->dropColumnIfExists([
                'planning_approval_status', 'planning_approved_by',
                'planning_approved_at', 'planning_rejection_reason'
            ]);
        });
    }
};
