<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Extend the source enum to include device and bulk_upload
        DB::statement("ALTER TABLE `attendance` MODIFY `source` ENUM('manual', 'biometric', 'mobile', 'device', 'bulk_upload') NOT NULL DEFAULT 'manual'");

        // Also add the device_user_id and device_attendance_logs table if not exists
        if (!Schema::hasColumn('employees', 'device_user_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->string('device_user_id')->nullable()->after('employee_code');
            });
        }

        if (!Schema::hasTable('device_attendance_logs')) {
            Schema::create('device_attendance_logs', function (Blueprint $table) {
                $table->id();
                $table->string('device_sn')->nullable();
                $table->string('device_user_id')->nullable()->index();
                $table->dateTime('punch_time')->nullable();
                $table->string('status')->nullable();
                $table->string('verify_mode')->nullable();
                $table->string('full_name')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        DB::statement("ALTER TABLE `attendance` MODIFY `source` ENUM('manual', 'biometric', 'mobile') NOT NULL DEFAULT 'manual'");

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('device_user_id');
        });

        Schema::dropIfExists('device_attendance_logs');
    }
};
