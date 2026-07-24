<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('subcon_agreement_takeoff_items')) {
            Schema::create('subcon_agreement_takeoff_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('agreement_id')->constrained('subcon_agreements')->cascadeOnDelete();
                $table->foreignId('takeoff_item_id')->constrained('takeoff_items')->cascadeOnDelete();
                $table->decimal('selected_quantity', 12, 4)->default(0);
                $table->decimal('rate', 12, 2)->default(0);
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->timestamps();

                // Use explicit shorter index name to avoid MySQL identifier length limit (64 chars)
                $table->unique(['agreement_id', 'takeoff_item_id'], 'subcon_agreement_takeoff_unique');
                $table->index(['agreement_id'], 'subcon_agreement_idx');
                $table->index(['takeoff_item_id'], 'takeoff_item_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcon_agreement_takeoff_items');
    }
};
