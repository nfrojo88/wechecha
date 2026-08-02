<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ensure all required employee columns exist — safe to run multiple times.
     */
    public function up(): void
    {
        if (!Schema::hasTable('employees')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'contract_type')) {
                $table->string('contract_type')->nullable()->after('employment_type');
            }
            if (!Schema::hasColumn('employees', 'transport_allowance')) {
                $table->decimal('transport_allowance', 15, 2)->default(0)->after('basic_salary');
            }
            if (!Schema::hasColumn('employees', 'house_allowance')) {
                $table->decimal('house_allowance', 15, 2)->default(0)->after('transport_allowance');
            }
            if (!Schema::hasColumn('employees', 'position_allowance')) {
                $table->decimal('position_allowance', 15, 2)->default(0)->after('house_allowance');
            }
            if (!Schema::hasColumn('employees', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('position_allowance');
            }
            if (!Schema::hasColumn('employees', 'account_number')) {
                $table->string('account_number', 50)->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('employees', 'guarantee_letter')) {
                $table->string('guarantee_letter')->nullable()->after('account_number');
            }
            if (!Schema::hasColumn('employees', 'guarantee_letter_submitted_at')) {
                $table->date('guarantee_letter_submitted_at')->nullable()->after('guarantee_letter');
            }
            if (!Schema::hasColumn('employees', 'guarantee_letter_required')) {
                $table->boolean('guarantee_letter_required')->default(true)->after('guarantee_letter_submitted_at');
            }
        });

        // Make date_of_joining nullable using raw SQL (avoids requiring doctrine/dbal)
        try {
            \Illuminate\Support\Facades\DB::statement(
                'ALTER TABLE `employees` MODIFY COLUMN `date_of_joining` DATE NULL'
            );
        } catch (\Throwable $e) {
            // Column may already be nullable or table structure differs — safe to ignore
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('employees')) {
            return;
        }
        Schema::table('employees', function (Blueprint $table) {
            $cols = ['contract_type', 'transport_allowance', 'house_allowance',
                     'position_allowance', 'bank_name', 'account_number',
                     'guarantee_letter', 'guarantee_letter_submitted_at', 'guarantee_letter_required'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('employees', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
