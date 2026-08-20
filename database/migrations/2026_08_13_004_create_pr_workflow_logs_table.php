<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // ── Workflow Audit Log (every handoff is recorded) ─────────────────
        Schema::create('pr_workflow_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')
                  ->constrained('purchase_requests')->cascadeOnDelete();
            $table->string('from_stage', 60)->nullable();
            $table->string('to_stage', 60);
            $table->string('action', 80);           // e.g., "send_to_procurement_team"
            $table->string('actor_role', 60)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('actor_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });

        // ── SMS Log (track every SMS sent for procurement) ─────────────────
        Schema::create('procurement_sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')
                  ->constrained('purchase_requests')->cascadeOnDelete();
            $table->string('recipient_phone', 30);
            $table->string('recipient_role', 60)->nullable();
            $table->text('message');
            $table->enum('status', ['sent', 'failed'])->default('sent');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('procurement_sms_logs');
        Schema::dropIfExists('pr_workflow_logs');
    }
};
