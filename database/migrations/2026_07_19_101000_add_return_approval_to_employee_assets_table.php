<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReturnApprovalToEmployeeAssetsTable extends Migration
{
    public function up()
    {
        Schema::table('employee_assets', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_assets', 'return_status')) {
                $table->string('return_status')->default('assigned')->after('status');
                $table->foreignId('store_manager_id')->nullable()->constrained('users')->nullOnDelete()->after('return_status');
                $table->text('return_notes')->nullable()->after('store_manager_id');
            }
        });
    }

    public function down()
    {
        Schema::table('employee_assets', function (Blueprint $table) {
            $table->dropForeign(['store_manager_id']);
            $table->dropColumn(['return_status', 'store_manager_id', 'return_notes']);
        });
    }
}
