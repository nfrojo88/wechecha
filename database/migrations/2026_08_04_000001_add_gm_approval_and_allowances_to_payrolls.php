<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ensure all required columns exist across payrolls and related tables — safe to run multiple times.
     */
    public function up(): void
    {
        if (!Schema::hasTable('payrolls')) {
            return;
        }

        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'transport_allowance')) {
                $table->decimal('transport_allowance', 15, 2)->default(0)->after('allowances');
            }
            if (!Schema::hasColumn('payrolls', 'house_allowance')) {
                $table->decimal('house_allowance', 15, 2)->default(0)->after('transport_allowance');
            }
            if (!Schema::hasColumn('payrolls', 'position_allowance')) {
                $table->decimal('position_allowance', 15, 2)->default(0)->after('house_allowance');
            }
            if (!Schema::hasColumn('payrolls', 'pension')) {
                $table->decimal('pension', 15, 2)->default(0)->after('tax');
            }
            if (!Schema::hasColumn('payrolls', 'gross_salary')) {
                $table->decimal('gross_salary', 15, 2)->default(0)->after('pension');
            }
            if (!Schema::hasColumn('payrolls', 'gm_status')) {
                $table->string('gm_status')->nullable()->after('status');
            }
            if (!Schema::hasColumn('payrolls', 'gm_notes')) {
                $table->text('gm_notes')->nullable()->after('gm_status');
            }
            if (!Schema::hasColumn('payrolls', 'gm_approved_by')) {
                $table->unsignedBigInteger('gm_approved_by')->nullable()->after('gm_notes');
            }
            if (!Schema::hasColumn('payrolls', 'gm_approved_at')) {
                $table->timestamp('gm_approved_at')->nullable()->after('gm_approved_by');
            }
            if (!Schema::hasColumn('payrolls', 'submitted_to_gm_at')) {
                $table->timestamp('submitted_to_gm_at')->nullable()->after('gm_approved_at');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('payrolls')) {
            return;
        }

        Schema::table('payrolls', function (Blueprint $table) {
            $cols = [
                'transport_allowance', 'house_allowance', 'position_allowance',
                'pension', 'gross_salary', 'gm_status', 'gm_notes',
                'gm_approved_by', 'gm_approved_at', 'submitted_to_gm_at',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('payrolls', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
