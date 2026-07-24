<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Add UNIQUE KEY to device_attendance_logs ────────────────────────
        // The ZKTeco device resends the same records repeatedly until it gets OK.
        // The UNIQUE KEY ensures INSERT IGNORE / insertOrIgnore won't create duplicates.
        if (Schema::hasTable('device_attendance_logs')) {
            // Drop the unique key first if it exists (idempotent re-runs)
            try {
                DB::statement('ALTER TABLE `device_attendance_logs` DROP INDEX `unique_punch`');
            } catch (\Exception $e) {
                // Index didn't exist — that's fine
            }

            DB::statement('ALTER TABLE `device_attendance_logs` ADD UNIQUE KEY `unique_punch` (`device_sn`(50), `device_user_id`(50), `punch_time`)');
        }

        // ── 2. Create zk_devices table — tracks device heartbeat/status ────────
        if (!Schema::hasTable('zk_devices')) {
            Schema::create('zk_devices', function (Blueprint $table) {
                $table->id();
                $table->string('serial_number')->unique();    // e.g. AF6P230860018
                $table->string('name')->nullable();           // Friendly label
                $table->string('location')->nullable();       // e.g. "Main Gate", "Office"
                $table->dateTime('last_seen_at')->nullable(); // Updated every heartbeat
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // ── 3. Add zkteco_synced_at to device_attendance_logs ─────────────────
        // Tracks which records have already been synced to the attendance table
        if (Schema::hasTable('device_attendance_logs') && !Schema::hasColumn('device_attendance_logs', 'synced_at')) {
            Schema::table('device_attendance_logs', function (Blueprint $table) {
                $table->dateTime('synced_at')->nullable()->after('full_name');
            });
        }
    }

    public function down(): void
    {
        // Remove UNIQUE KEY
        try {
            DB::statement('ALTER TABLE `device_attendance_logs` DROP INDEX `unique_punch`');
        } catch (\Exception $e) {}

        // Remove synced_at column
        if (Schema::hasColumn('device_attendance_logs', 'synced_at')) {
            Schema::table('device_attendance_logs', function (Blueprint $table) {
                $table->dropColumn('synced_at');
            });
        }

        Schema::dropIfExists('zk_devices');
    }
};
