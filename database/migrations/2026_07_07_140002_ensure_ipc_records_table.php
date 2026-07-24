<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Standalone migration to ensure ipc_records table exists.
     * Creates subcon_agreements first if also missing (required FK dependency).
     */
    public function up(): void
    {
        // subcon_agreements must exist before ipc_records (FK constraint)
        if (!Schema::hasTable('subcon_agreements')) {
            Schema::create('subcon_agreements', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('agreement_no', 50)->unique();
                $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
                $table->string('subcontractor_name', 255);
                $table->string('subcontractor_contact', 255)->nullable();
                $table->text('scope_of_work');
                $table->date('start_date');
                $table->date('end_date')->nullable();
                $table->decimal('contract_value', 18, 2)->default(0);
                $table->decimal('paid_to_date', 18, 2)->default(0);
                $table->decimal('retention_percent', 5, 2)->default(10);
                $table->decimal('retention_amount', 18, 2)->default(0);
                $table->enum('status', ['draft', 'active', 'completed', 'terminated', 'cancelled'])->default('draft')->index();
                $table->text('terms_conditions')->nullable();
                $table->string('agreement_file', 500)->nullable();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('ipc_records')) {
            Schema::create('ipc_records', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('ipc_no', 50)->unique();
                $table->foreignId('agreement_id')->constrained('subcon_agreements')->cascadeOnDelete();
                $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
                $table->integer('ipc_number');
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
        }

        if (!Schema::hasTable('ipc_items')) {
            Schema::create('ipc_items', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->foreignId('ipc_record_id')->constrained('ipc_records')->cascadeOnDelete();
                $table->string('description');
                $table->string('unit', 50)->nullable();
                $table->decimal('quantity', 18, 4)->default(0);
                $table->decimal('unit_rate', 18, 2)->default(0);
                $table->decimal('total_amount', 18, 2)->default(0);
                $table->decimal('previous_quantity', 18, 4)->default(0);
                $table->decimal('current_quantity', 18, 4)->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipc_items');
        Schema::dropIfExists('ipc_records');
        Schema::dropIfExists('subcon_agreements');
    }
};
