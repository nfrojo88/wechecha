<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot: which engineers are assigned to a work order
        Schema::create('eng_work_order_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('eng_work_orders')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Per-engineer acceptance status
            $table->enum('status', [
                'pending', 'accepted', 'declined', 'in_progress', 'on_hold', 'completed'
            ])->default('pending')->index();

            $table->text('decline_reason')->nullable();
            $table->integer('actual_hours')->nullable(); // logged by engineer on completion
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->unique(['work_order_id', 'user_id']);
        });

        // Comments / notes on a work order
        Schema::create('eng_work_order_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('eng_work_orders')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });

        // Full audit trail of status changes
        Schema::create('eng_work_order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('eng_work_orders')->cascadeOnDelete();
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eng_work_order_status_history');
        Schema::dropIfExists('eng_work_order_comments');
        Schema::dropIfExists('eng_work_order_assignees');
    }
};
