<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment_masters', function (Blueprint $table) {
            if (!Schema::hasColumn('equipment_masters', 'product_id')) {
                $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete()->after('id');
            }
            if (!Schema::hasColumn('equipment_masters', 'total_price')) {
                $table->decimal('total_price', 15, 2)->nullable()->after('daily_rate');
            }
            if (!Schema::hasColumn('equipment_masters', 'plate_number')) {
                $table->string('plate_number', 100)->nullable()->after('total_price');
            }
            if (!Schema::hasColumn('equipment_masters', 'total_on_hand')) {
                $table->decimal('total_on_hand', 15, 3)->nullable()->default(0)->after('plate_number');
            }
            if (!Schema::hasColumn('equipment_masters', 'last_sequence_number')) {
                $table->unsignedInteger('last_sequence_number')->default(0)->after('total_on_hand');
            }
        });
    }

    public function down(): void
    {
        Schema::table('equipment_masters', function (Blueprint $table) {
            if (Schema::hasColumn('equipment_masters', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }
            if (Schema::hasColumn('equipment_masters', 'total_price')) {
                $table->dropColumn('total_price');
            }
            if (Schema::hasColumn('equipment_masters', 'plate_number')) {
                $table->dropColumn('plate_number');
            }
            if (Schema::hasColumn('equipment_masters', 'total_on_hand')) {
                $table->dropColumn('total_on_hand');
            }
            if (Schema::hasColumn('equipment_masters', 'last_sequence_number')) {
                $table->dropColumn('last_sequence_number');
            }
        });
    }
};
