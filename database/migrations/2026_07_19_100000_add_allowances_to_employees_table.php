<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllowancesToEmployeesTable extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'transport_allowance')) {
                $table->decimal('transport_allowance', 15, 2)->default(0)->after('basic_salary');
            }
            if (!Schema::hasColumn('employees', 'house_allowance')) {
                $table->decimal('house_allowance', 15, 2)->default(0)->after('transport_allowance');
            }
            if (!Schema::hasColumn('employees', 'position_allowance')) {
                $table->decimal('position_allowance', 15, 2)->default(0)->after('house_allowance');
            }
            if (!Schema::hasColumn('employees', 'contract_type')) {
                $table->string('contract_type')->nullable()->after('employment_type');
            }
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'transport_allowance',
                'house_allowance',
                'position_allowance',
                'contract_type'
            ]);
        });
    }
}
