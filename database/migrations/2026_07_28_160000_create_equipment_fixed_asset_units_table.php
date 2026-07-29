<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_fixed_asset_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_master_id')->constrained('equipment_masters')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete(); // linked Fixed Asset product
            $table->string('asset_name', 255);          // e.g. "Sino Truck"
            $table->string('plate_number', 100)->nullable(); // e.g. "AA 12345"
            $table->string('chassis_number', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->year('year')->nullable();
            $table->string('condition', 50)->default('good'); // good, fair, maintenance
            $table->enum('status', ['available', 'on_site', 'maintenance', 'retired'])->default('available');
            $table->string('current_location', 255)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_fixed_asset_units');
    }
};
