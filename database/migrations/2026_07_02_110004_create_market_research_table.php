<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketResearchTable extends Migration
{
    public function up()
    {
        Schema::create('market_research', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('purchase_request_id')->constrained('purchase_requests')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->decimal('quoted_total', 18, 2)->default(0);
            $table->enum('status', ['pending', 'submitted', 'selected', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('market_research_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('market_research_id')->constrained('market_research')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('unit_price', 15, 2);
            $table->decimal('quantity', 15, 3);
            $table->decimal('total', 15, 2)->storedAs('unit_price * quantity');
            $table->integer('delivery_days')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('market_research_items');
        Schema::dropIfExists('market_research');
    }
}
