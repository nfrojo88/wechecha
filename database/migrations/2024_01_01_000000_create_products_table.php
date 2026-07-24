<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Uses hasTable() guard so this is safe to run even when the products
     * table was already created by an earlier migration (e.g. 2014_10_12_000004).
     * Any columns missing from the existing table are added individually.
     */
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            // Fresh install – create the complete table
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->string('sku', 100)->unique();
                $table->string('unit', 50);
                $table->decimal('standard_length', 10, 2)->default(0.00);
                $table->string('category', 100)->nullable();
                $table->decimal('max_stock', 10, 2)->default(100.00);
                $table->integer('reorder_level')->default(20);
                $table->integer('carton_size')->nullable();
                $table->decimal('unit_price', 10, 2)->default(0.00);
                $table->decimal('selling_price', 10, 2)->default(0.00);
                $table->softDeletes();
                $table->timestamps();
                $table->decimal('standard_width', 10, 3)->default(0.000);
                $table->string('sub_category', 100)->nullable();
                $table->string('equipment_condition', 100)->default('Good');
                $table->string('assigned_to', 255)->default('Unassigned');
                $table->string('current_location', 255)->default('Main Store');
                $table->string('asset_status', 50)->default('Available');
                $table->date('baseline_date')->nullable();
                $table->decimal('purchase_threshold', 5, 2)->default(5.00);

                // Indexes for performance
                $table->index('category');
                $table->index('sub_category');
                $table->index('asset_status');
                $table->index('current_location');
            });
        } else {
            // Table already exists – add any columns that might be missing
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'sku')) {
                    $table->string('sku', 100)->nullable()->unique()->after('name');
                }
                if (!Schema::hasColumn('products', 'standard_length')) {
                    $table->decimal('standard_length', 10, 2)->default(0.00)->after('unit');
                }
                if (!Schema::hasColumn('products', 'max_stock')) {
                    $table->decimal('max_stock', 10, 2)->default(100.00)->after('category');
                }
                if (!Schema::hasColumn('products', 'carton_size')) {
                    $table->integer('carton_size')->nullable()->after('reorder_level');
                }
                if (!Schema::hasColumn('products', 'unit_price')) {
                    $table->decimal('unit_price', 10, 2)->default(0.00)->after('carton_size');
                }
                if (!Schema::hasColumn('products', 'standard_width')) {
                    $table->decimal('standard_width', 10, 3)->default(0.000);
                }
                if (!Schema::hasColumn('products', 'sub_category')) {
                    $table->string('sub_category', 100)->nullable();
                }
                if (!Schema::hasColumn('products', 'equipment_condition')) {
                    $table->string('equipment_condition', 100)->default('Good');
                }
                if (!Schema::hasColumn('products', 'assigned_to')) {
                    $table->string('assigned_to', 255)->default('Unassigned');
                }
                if (!Schema::hasColumn('products', 'current_location')) {
                    $table->string('current_location', 255)->default('Main Store');
                }
                if (!Schema::hasColumn('products', 'asset_status')) {
                    $table->string('asset_status', 50)->default('Available');
                }
                if (!Schema::hasColumn('products', 'baseline_date')) {
                    $table->date('baseline_date')->nullable();
                }
                if (!Schema::hasColumn('products', 'purchase_threshold')) {
                    $table->decimal('purchase_threshold', 5, 2)->default(5.00);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
