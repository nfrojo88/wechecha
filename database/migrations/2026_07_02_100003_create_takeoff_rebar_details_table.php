<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('takeoff_rebar_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('takeoff_item_id')->constrained('takeoff_items')->cascadeOnDelete();
            $table->string('bar_mark', 20);
            $table->string('bar_diameter', 10);
            $table->decimal('bar_length', 10, 3);
            $table->integer('bars_per_member')->default(1);
            $table->integer('total_bars');
            $table->decimal('total_length', 15, 3);
            $table->decimal('weight_per_meter', 8, 4)->nullable();
            $table->decimal('total_weight', 15, 3)->nullable();
            $table->string('bending_type', 30)->nullable();
            $table->text('bending_details')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('takeoff_rebar_details');
    }
};
