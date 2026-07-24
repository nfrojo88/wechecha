<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pr_no', 50)->unique();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('material_request_id')->nullable()->constrained('material_requests')->nullOnDelete();
            $table->enum('priority', ['normal', 'high', 'urgent'])->default('normal')->index();
            $table->enum('type', ['normal', 'emergency', 'direct'])->default('normal')->index();
            $table->date('required_date')->nullable();
            $table->text('justification')->nullable();
            $table->enum('status', ['draft', 'submitted', 'under_review', 'market_research', 'pending_gm_decision', 'po_created', 'cancelled', 'rejected'])->default('draft')->index();
            $table->foreignId('merged_into_pr_id')->nullable()->constrained('purchase_requests')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('purchase_request_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('purchase_request_id')->constrained('purchase_requests')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('quantity', 15, 3);
            $table->string('unit', 20);
            $table->text('specifications')->nullable();
            $table->decimal('estimated_unit_cost', 15, 2)->nullable();
            $table->decimal('estimated_total', 15, 2)->storedAs('quantity * COALESCE(estimated_unit_cost, 0)');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_request_items');
        Schema::dropIfExists('purchase_requests');
    }
}
