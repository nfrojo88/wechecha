<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('schedule_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_task_id')->nullable()->constrained('schedule_tasks')->nullOnDelete();
            $table->string('wbs_code')->nullable();
            $table->string('name');
            $table->string('type')->default('Normal Task');
            $table->string('priority')->default('Medium');
            $table->string('status')->default('Not Started');
            $table->boolean('is_milestone')->default(false);
            $table->foreignId('predecessor_id')->nullable()->constrained('schedule_tasks')->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('duration_days')->nullable();
            $table->decimal('planned_cost', 18, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_tasks');
    }
}
