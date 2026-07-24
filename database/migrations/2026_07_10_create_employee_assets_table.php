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
        if (!Schema::hasTable('employee_assets')) {
            Schema::create('employee_assets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->date('assigned_date')->default(now());
                $table->date('returned_date')->nullable();
                $table->enum('status', ['assigned', 'in_use', 'returned', 'damaged'])->default('assigned')->index();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['employee_id', 'product_id'], 'employee_asset_unique');
                $table->index(['employee_id']);
                $table->index(['product_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_assets');
    }
};
