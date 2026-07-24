<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('weekly_plan_dispatch_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispatch_id')->constrained('weekly_plan_dispatches')->cascadeOnDelete();
            $table->foreignId('task_id')->constrained('erp_plan_tasks')->cascadeOnDelete();
            $table->text('dispatch_notes')->nullable();
            $table->decimal('target_progress', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['dispatch_id', 'task_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('weekly_plan_dispatch_tasks');
    }
};
