<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manpower_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // e.g. Mason, Helper, DL, Foreman
            $table->string('default_unit')->default('day'); // day / hr
            $table->string('category')->nullable(); // e.g. Skilled, Unskilled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manpower_roles');
    }
};
