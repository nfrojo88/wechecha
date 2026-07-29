<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify status column on MySQL to allow 'converted' and 'locked'
        DB::statement("ALTER TABLE `takeoff_sheets` MODIFY COLUMN `status` ENUM('draft', 'review', 'approved', 'converted', 'locked') NOT NULL DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `takeoff_sheets` MODIFY COLUMN `status` ENUM('draft', 'review', 'approved', 'converted') NOT NULL DEFAULT 'draft'");
    }
};
