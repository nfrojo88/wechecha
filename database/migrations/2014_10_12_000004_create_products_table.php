<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('code', 50)->unique();
            $table->string('category', 100);
            $table->string('unit', 20); // kg/m/m2/m3/pcs/liter/ton/bag/roll/set/pair
            $table->text('description')->nullable();
            $table->string('specification', 500)->nullable();
            $table->decimal('standard_cost', 15, 2)->nullable();
            $table->decimal('current_cost', 15, 2)->nullable();
            $table->decimal('selling_price', 15, 2)->nullable();
            $table->decimal('min_stock_level', 15, 3)->default(0);
            $table->decimal('max_stock_level', 15, 3)->nullable();
            $table->decimal('reorder_level', 15, 3)->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('product_type', 20)->default('material'); // material/equipment/labor
            $table->json('properties')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
