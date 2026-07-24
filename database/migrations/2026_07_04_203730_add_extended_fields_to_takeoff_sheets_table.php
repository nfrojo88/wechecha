<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtendedFieldsToTakeoffSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('takeoff_sheets', function (Blueprint $table) {
            $table->unsignedBigInteger('boq_id')->nullable()->after('project_id');
            $table->string('category')->nullable()->after('sheet_type');
            $table->string('discipline')->nullable()->after('category');
            $table->string('ref_drawing')->nullable()->after('discipline');
            $table->string('measurement_std')->nullable()->default('IS-1200 (Standard)')->after('ref_drawing');
            $table->string('execution_type')->nullable()->after('measurement_std');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('takeoff_sheets', function (Blueprint $table) {
            $table->dropColumn([
                'boq_id', 
                'category', 
                'discipline', 
                'ref_drawing', 
                'measurement_std', 
                'execution_type'
            ]);
        });
    }
}
