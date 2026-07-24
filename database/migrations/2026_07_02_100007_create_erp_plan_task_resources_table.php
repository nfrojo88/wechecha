<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('erp_plan_task_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('erp_plan_tasks')->cascadeOnDelete();
            $table->string('resource_type', 20); // manpower, equipment, material
            $table->string('resource_name', 255);
            $table->decimal('quantity', 10, 2)->default(1);
            $table->string('unit', 20)->nullable();
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->json('details')->nullable();
            $table->timestamps();

            $table->index(['task_id', 'resource_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('erp_plan_task_resources');
    }
};
