<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('erp_plan_task_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('erp_plan_tasks')->cascadeOnDelete();
            $table->foreignId('depends_on_task_id')->constrained('erp_plan_tasks')->cascadeOnDelete();
            $table->enum('type', ['finish_to_start', 'start_to_start', 'finish_to_finish', 'start_to_finish'])->default('finish_to_start');
            $table->integer('lag_days')->default(0);
            $table->timestamps();

            $table->unique(['task_id', 'depends_on_task_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('erp_plan_task_dependencies');
    }
};
