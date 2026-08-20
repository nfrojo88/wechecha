<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // ── GM Decisions (versioned per round) ────────────────────────────
        Schema::create('pr_gm_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')
                  ->constrained('purchase_requests')->cascadeOnDelete();
            $table->unsignedTinyInteger('round')->default(1); // loop counter
            $table->enum('decision', ['approve', 'reject', 'send_back']);
            $table->enum('payment_method', ['pay_and_buy', 'buy_by_credit'])->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('decided_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pr_gm_decisions');
    }
};
