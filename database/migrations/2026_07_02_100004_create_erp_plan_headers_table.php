<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('erp_plan_headers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->date('plan_start_date');
            $table->date('plan_end_date');
            $table->integer('total_duration_days'); // Normally a generated column, but keeping it standard per spec
            $table->enum('status', ['draft', 'submitted', 'approved', 'active', 'completed', 'archived'])->default('draft')->index();
            $table->decimal('total_budget', 18, 2)->default(0);
            $table->decimal('consumed_budget', 18, 2)->default(0);
            $table->decimal('overall_progress', 5, 2)->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('erp_plan_headers');
    }
};
