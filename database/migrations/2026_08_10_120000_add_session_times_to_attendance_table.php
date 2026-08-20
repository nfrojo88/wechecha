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
        Schema::table('attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance', 'morning_in')) {
                $table->time('morning_in')->nullable()->after('attendance_date');
            }
            if (!Schema::hasColumn('attendance', 'morning_out')) {
                $table->time('morning_out')->nullable()->after('morning_in');
            }
            if (!Schema::hasColumn('attendance', 'afternoon_in')) {
                $table->time('afternoon_in')->nullable()->after('morning_out');
            }
            if (!Schema::hasColumn('attendance', 'afternoon_out')) {
                $table->time('afternoon_out')->nullable()->after('afternoon_in');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn(['morning_in', 'morning_out', 'afternoon_in', 'afternoon_out']);
        });
    }
};
