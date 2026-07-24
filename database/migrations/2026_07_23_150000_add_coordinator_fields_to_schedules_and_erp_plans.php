<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add coordinator tracking fields to schedules
        Schema::table('schedules', function (Blueprint $table) {
            $table->boolean('sent_to_coordinator')->default(false)->after('progress');
            $table->timestamp('sent_at')->nullable()->after('sent_to_coordinator');
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete()->after('sent_at');
        });

        // Add schedule_id FK to erp_plan_headers so deletion can unlink
        Schema::table('erp_plan_headers', function (Blueprint $table) {
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->nullOnDelete()->after('project_id');
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn(['sent_to_coordinator', 'sent_at', 'sent_by']);
        });

        Schema::table('erp_plan_headers', function (Blueprint $table) {
            $table->dropForeign(['schedule_id']);
            $table->dropColumn('schedule_id');
        });
    }
};
