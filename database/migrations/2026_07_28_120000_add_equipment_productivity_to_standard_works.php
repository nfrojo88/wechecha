<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add min_equipment_productivity, max_equipment_productivity, and default_equipment_productivity to standard_works table.
     */
    public function up(): void
    {
        Schema::table('standard_works', function (Blueprint $table) {
            $table->decimal('min_equipment_productivity', 15, 4)->nullable()->after('default_productivity');
            $table->decimal('max_equipment_productivity', 15, 4)->nullable()->after('min_equipment_productivity');
            $table->decimal('default_equipment_productivity', 15, 4)->nullable()->after('max_equipment_productivity');
        });
    }

    public function down(): void
    {
        Schema::table('standard_works', function (Blueprint $table) {
            $table->dropColumn(['min_equipment_productivity', 'max_equipment_productivity', 'default_equipment_productivity']);
        });
    }
};
