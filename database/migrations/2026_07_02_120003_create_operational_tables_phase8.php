<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationalTablesPhase8 extends Migration
{
    public function up()
    {
        // Drop tables if they exist to handle failed previous migrations
        Schema::dropIfExists('waste_items');
        Schema::dropIfExists('waste');
        Schema::dropIfExists('issue_comments');
        Schema::dropIfExists('issues');
        Schema::dropIfExists('cut_optimization_items');
        Schema::dropIfExists('cut_optimizations');
        Schema::dropIfExists('material_usage_items');
        Schema::dropIfExists('material_usages');
        Schema::dropIfExists('material_plan_items');
        Schema::dropIfExists('material_plans');

        // Material Plans
        Schema::create('material_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('plan_header_id')->nullable()->constrained('erp_plan_headers')->nullOnDelete();
            $table->string('title', 255);
            $table->date('plan_week_start');
            $table->date('plan_week_end');
            $table->enum('status', ['draft', 'store_review', 'purchase_review', 'approved', 'converted'])->default('draft')->index();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('material_plan_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('material_plan_id')->constrained('material_plans')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('task_id')->nullable()->constrained('erp_plan_tasks')->nullOnDelete();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->decimal('required_quantity', 15, 3);
            $table->decimal('available_quantity', 15, 3)->default(0);
            $table->decimal('shortage_quantity', 15, 3)->storedAs('CASE WHEN required_quantity > available_quantity THEN required_quantity - available_quantity ELSE 0 END');
            $table->string('unit', 20);
            $table->boolean('auto_pr_created')->default(false);
            $table->foreignId('generated_pr_id')->nullable()->constrained('purchase_requests')->nullOnDelete();
            $table->timestamps();
        });

        // Material Usages
        Schema::create('material_usages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('usage_no', 50)->unique();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('task_id')->nullable()->constrained('erp_plan_tasks')->nullOnDelete();
            $table->date('usage_date');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'confirmed', 'returned'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('material_usage_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('material_usage_id')->constrained('material_usages')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('used_quantity', 15, 3);
            $table->decimal('returned_quantity', 15, 3)->default(0);
            $table->string('unit', 20);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Cut Optimizations
        Schema::create('cut_optimizations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('material_type', 50);
            $table->string('standard_length', 20);
            $table->decimal('total_waste_percent', 5, 2)->nullable();
            $table->enum('status', ['draft', 'optimized', 'executed', 'cancelled'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->json('optimization_result')->nullable();
            $table->timestamps();
        });

        Schema::create('cut_optimization_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('cut_optimization_id')->constrained('cut_optimizations')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('piece_mark', 50);
            $table->decimal('required_length', 10, 3);
            $table->integer('quantity');
            $table->string('diameter', 10)->nullable();
            $table->integer('assigned_bar_no')->nullable();
            $table->decimal('waste_from_piece', 10, 3)->nullable();
            $table->timestamps();
        });

        // Issues
        Schema::create('issues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('reported_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('task_id')->nullable()->constrained('erp_plan_tasks')->nullOnDelete();
            $table->string('title', 255);
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium')->index();
            $table->enum('category', ['safety', 'quality', 'schedule', 'material', 'equipment', 'other'])->index();
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed', 'reopened'])->default('open')->index();
            $table->date('due_date')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution')->nullable();
            $table->string('photo', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('issue_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('issue_id')->constrained('issues')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('comment');
            $table->string('attachment', 500)->nullable();
            $table->timestamps();
        });

        // Waste
        Schema::create('waste', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->date('waste_date');
            $table->enum('reason', ['damage', 'excess_cutting', 'quality_reject', 'theft', 'other'])->index();
            $table->text('description')->nullable();
            $table->string('photo', 500)->nullable();
            $table->enum('status', ['reported', 'verified', 'written_off'])->default('reported');
            $table->timestamps();
        });

        Schema::create('waste_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('waste_id')->constrained('waste')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('quantity', 15, 3);
            $table->string('unit', 20);
            $table->decimal('estimated_value', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waste_items');
        Schema::dropIfExists('waste');
        Schema::dropIfExists('issue_comments');
        Schema::dropIfExists('issues');
        Schema::dropIfExists('cut_optimization_items');
        Schema::dropIfExists('cut_optimizations');
        Schema::dropIfExists('material_usage_items');
        Schema::dropIfExists('material_usages');
        Schema::dropIfExists('material_plan_items');
        Schema::dropIfExists('material_plans');
    }
}
