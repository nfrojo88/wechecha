<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('takeoff_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('takeoff_sheet_id')->constrained('takeoff_sheets')->cascadeOnDelete();
            $table->string('element', 255);
            $table->string('member_type', 50)->nullable();
            $table->integer('count')->default(1);
            $table->text('dimensions')->nullable();
            $table->text('formula')->nullable();
            $table->decimal('result_quantity', 15, 3)->default(0);
            $table->string('result_unit', 20)->nullable();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->decimal('converted_quantity', 15, 3)->nullable();
            $table->integer('sort_order')->default(0);
            $table->json('calculation_data')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('takeoff_items');
    }
};
