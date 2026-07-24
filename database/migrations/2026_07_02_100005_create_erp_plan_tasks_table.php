<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('erp_plan_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_header_id')->constrained('erp_plan_headers')->cascadeOnDelete();
            $table->foreignId('parent_task_id')->nullable()->constrained('erp_plan_tasks')->nullOnDelete();
            $table->string('wbs_code', 50)->nullable();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration_days'); 
            $table->decimal('planned_progress', 5, 2)->default(0);
            $table->decimal('actual_progress', 5, 2)->default(0);
            $table->decimal('planned_cost', 18, 2)->default(0);
            $table->decimal('actual_cost', 18, 2)->default(0);
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'delayed', 'blocked', 'cancelled'])->default('not_started')->index();
            $table->integer('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['plan_header_id', 'parent_task_id']);
            $table->index('start_date');
            $table->index('end_date');
            $table->index('sort_order');
        });
    }

    public function down()
    {
        Schema::dropIfExists('erp_plan_tasks');
    }
};
