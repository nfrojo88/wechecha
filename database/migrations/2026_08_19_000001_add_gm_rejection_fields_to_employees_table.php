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
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'gm_approval_status')) {
                $table->string('gm_approval_status', 30)->default('pending')->after('is_approved_by_gm');
            }
            if (!Schema::hasColumn('employees', 'gm_rejection_reason')) {
                $table->text('gm_rejection_reason')->nullable()->after('gm_approval_status');
            }
            if (!Schema::hasColumn('employees', 'gm_rejected_at')) {
                $table->timestamp('gm_rejected_at')->nullable()->after('gm_rejection_reason');
            }
            if (!Schema::hasColumn('employees', 'gm_rejected_by')) {
                $table->foreignId('gm_rejected_by')->nullable()->constrained('users')->nullOnDelete()->after('gm_rejected_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'gm_rejected_by')) {
                $table->dropForeign(['gm_rejected_by']);
                $table->dropColumn(['gm_rejected_by']);
            }
            if (Schema::hasColumn('employees', 'gm_rejected_at')) {
                $table->dropColumn(['gm_rejected_at']);
            }
            if (Schema::hasColumn('employees', 'gm_rejection_reason')) {
                $table->dropColumn(['gm_rejection_reason']);
            }
            if (Schema::hasColumn('employees', 'gm_approval_status')) {
                $table->dropColumn(['gm_approval_status']);
            }
        });
    }
};
