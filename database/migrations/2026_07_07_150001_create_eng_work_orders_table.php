<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eng_work_orders', function (Blueprint $table) {
            $table->id();

            // Core info
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('category')->nullable();

            // Dates
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');

            // Priority & Status
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->index();
            $table->enum('status', [
                'draft', 'assigned', 'accepted', 'declined',
                'in_progress', 'on_hold', 'completed', 'cancelled'
            ])->default('draft')->index();

            // Recurrence (simplified: null = no recurrence)
            $table->enum('recurrence_type', ['none', 'daily', 'weekly', 'monthly'])->default('none');
            $table->integer('recurrence_interval')->default(1); // every N days/weeks/months
            $table->date('recurrence_end_date')->nullable();

            // Relations
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->nullOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eng_work_orders');
    }
};
