<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add min_productivity, max_productivity, and default_productivity to standard_works table.
     */
    public function up(): void
    {
        Schema::table('standard_works', function (Blueprint $table) {
            $table->decimal('min_productivity', 15, 4)->nullable()->after('description');
            $table->decimal('max_productivity', 15, 4)->nullable()->after('min_productivity');
            $table->decimal('default_productivity', 15, 4)->nullable()->after('max_productivity');
        });
    }

    public function down(): void
    {
        Schema::table('standard_works', function (Blueprint $table) {
            $table->dropColumn(['min_productivity', 'max_productivity', 'default_productivity']);
        });
    }
};
