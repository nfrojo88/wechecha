<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance', 'overtime_hours')) {
                $table->decimal('overtime_hours', 5, 2)->default(0)->after('hours_worked');
            }
            if (!Schema::hasColumn('attendance', 'overtime_type')) {
                $table->enum('overtime_type', ['none', 'holiday', 'rest_day', 'night_12_4', 'night_4_12'])
                      ->default('none')
                      ->after('overtime_hours')
                      ->comment('holiday=×2.5, rest_day=×2.0, night_12_4=×1.5, night_4_12=×1.75');
            }
            if (!Schema::hasColumn('attendance', 'overtime_pay')) {
                $table->decimal('overtime_pay', 10, 2)->default(0)->after('overtime_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn(['overtime_hours', 'overtime_type', 'overtime_pay']);
        });
    }
};
