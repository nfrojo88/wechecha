<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('takeoff_items', function (Blueprint $table) {
            $table->boolean('is_header')->default(false)->after('element');
        });
    }

    public function down(): void
    {
        Schema::table('takeoff_items', function (Blueprint $table) {
            $table->dropColumn('is_header');
        });
    }
};
