<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rebar_dia_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('diameter');           // e.g. 8, 10, 12 ...
            $table->decimal('unit_weight_kg_per_m', 8, 4); // kg/m (auto-filled)
            $table->foreignId('product_id')
                  ->nullable()
                  ->constrained('products')
                  ->nullOnDelete();
            $table->string('standard_length_m', 10)->default('12'); // default bar stock length in metres
            $table->timestamps();

            $table->unique('diameter');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rebar_dia_products');
    }
};
