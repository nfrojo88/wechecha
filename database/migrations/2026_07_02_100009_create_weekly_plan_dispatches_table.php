<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('weekly_plan_dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_header_id')->constrained('erp_plan_headers')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->date('week_start');
            $table->date('week_end');
            $table->foreignId('dispatched_to')->constrained('users')->cascadeOnDelete();
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
            $table->enum('status', ['draft', 'dispatched', 'acknowledged', 'in_progress', 'completed'])->default('draft')->index();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();

            $table->index(['week_start', 'week_end']);
            $table->index('dispatched_to');
        });
    }

    public function down()
    {
        Schema::dropIfExists('weekly_plan_dispatches');
    }
};
