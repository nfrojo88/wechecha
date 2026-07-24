<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoqItemsTable extends Migration
{
    public function up()
    {
        Schema::create('boq_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boq_id')->constrained()->cascadeOnDelete();
            $table->string('item_code')->nullable(); // Optional item code
            $table->text('description'); // Work item description
            $table->string('unit'); // m2, m3, kg, ls, etc.
            $table->decimal('quantity', 15, 3)->default(0);
            $table->decimal('unit_rate', 15, 2)->default(0);
            $table->decimal('amount', 18, 2)->storedAs('quantity * unit_rate');
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete(); // If mapped to a specific product/material
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('boq_items');
    }
}
