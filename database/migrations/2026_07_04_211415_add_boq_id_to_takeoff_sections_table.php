<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBoqIdToTakeoffSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('takeoff_sections', function (Blueprint $table) {
            $table->unsignedBigInteger('boq_id')->nullable()->after('takeoff_sheet_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('takeoff_sections', function (Blueprint $table) {
            $table->dropColumn('boq_id');
        });
    }
}
