<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGmApprovalAndAllowanceColumnsToPayrolls extends Migration
{
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Separate allowance breakdown
            $table->decimal('transport_allowance', 15, 2)->default(0)->after('allowances');
            $table->decimal('house_allowance',     15, 2)->default(0)->after('transport_allowance');
            $table->decimal('position_allowance',  15, 2)->default(0)->after('house_allowance');
            // Pension deduction (employee 7% of basic)
            $table->decimal('pension',             15, 2)->default(0)->after('tax');
            // Gross salary stored separately for display
            $table->decimal('gross_salary',        15, 2)->default(0)->after('pension');
            // GM Approval workflow
            $table->string('gm_status')->nullable()->after('status'); // submitted | approved | rejected
            $table->text('gm_notes')->nullable()->after('gm_status');
            $table->unsignedBigInteger('gm_approved_by')->nullable()->after('gm_notes');
            $table->timestamp('gm_approved_at')->nullable()->after('gm_approved_by');
            $table->timestamp('submitted_to_gm_at')->nullable()->after('gm_approved_at');
        });
    }

    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'transport_allowance', 'house_allowance', 'position_allowance',
                'pension', 'gross_salary',
                'gm_status', 'gm_notes', 'gm_approved_by', 'gm_approved_at', 'submitted_to_gm_at',
            ]);
        });
    }
}
