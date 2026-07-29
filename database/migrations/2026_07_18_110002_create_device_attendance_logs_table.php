<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
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
        Schema::dropIfExists('device_attendance_logs');
    }
};
