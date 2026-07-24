<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Client IPCs — Payment Certificates issued BY OUR COMPANY to the client.
     * These are separate from ipc_records which track payments TO subcontractors.
     */
    public function up(): void
    {
        if (!Schema::hasTable('client_ipcs')) {
            Schema::create('client_ipcs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('ipc_no', 50)->unique();
                $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
                $table->foreignId('boq_id')->nullable()->constrained('boqs')->nullOnDelete();
                $table->integer('ipc_number')->default(1);

                $table->date('period_from');
                $table->date('period_to');
                $table->date('submission_date')->nullable();

                // Financial columns
                $table->decimal('gross_amount', 18, 2)->default(0);     // total work certified this period
                $table->decimal('previous_certified', 18, 2)->default(0);
                $table->decimal('cumulative_certified', 18, 2)->default(0);
                $table->decimal('retention_percent', 5, 2)->default(5);
                $table->decimal('retention_amount', 18, 2)->default(0);
                $table->decimal('net_amount', 18, 2)->default(0);        // amount due after retention

                $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'paid', 'rejected'])
                      ->default('draft')->index();

                $table->text('notes')->nullable();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('approved_at')->nullable();

                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('client_ipc_items')) {
            Schema::create('client_ipc_items', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->foreignId('client_ipc_id')->constrained('client_ipcs')->cascadeOnDelete();
                $table->foreignId('boq_item_id')->nullable()->constrained('boq_items')->nullOnDelete();

                $table->string('description');
                $table->string('unit', 50)->nullable();
                $table->decimal('boq_quantity', 18, 4)->default(0);      // original BOQ qty
                $table->decimal('previous_quantity', 18, 4)->default(0);
                $table->decimal('current_quantity', 18, 4)->default(0);  // certified this period
                $table->decimal('cumulative_quantity', 18, 4)->default(0);
                $table->decimal('unit_rate', 18, 2)->default(0);
                $table->decimal('current_amount', 18, 2)->default(0);    // current_qty × unit_rate
                $table->decimal('cumulative_amount', 18, 2)->default(0);

                $table->timestamps();
            });
        }

        // Add status column to payments table if missing
        if (Schema::hasTable('payments') && !Schema::hasColumn('payments', 'status')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->enum('status', ['pending', 'approved', 'paid', 'cancelled'])
                      ->default('pending')->after('description');
            });
        }

        // Add client_ipc_id link to payments
        if (Schema::hasTable('payments') && !Schema::hasColumn('payments', 'client_ipc_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->unsignedBigInteger('client_ipc_id')->nullable()->after('project_id');
                $table->foreign('client_ipc_id')->references('id')->on('client_ipcs')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (Schema::hasColumn('payments', 'client_ipc_id')) {
                    $table->dropForeign(['client_ipc_id']);
                    $table->dropColumn('client_ipc_id');
                }
                if (Schema::hasColumn('payments', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }
        Schema::dropIfExists('client_ipc_items');
        Schema::dropIfExists('client_ipcs');
    }
};
