<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTakeoffSectionsAndUpdateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('takeoff_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('takeoff_sheet_id');
            $table->unsignedBigInteger('schedule_task_id')->nullable();
            $table->string('name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('takeoff_items', function (Blueprint $table) {
            $table->unsignedBigInteger('takeoff_section_id')->nullable()->after('takeoff_sheet_id');
            $table->decimal('unit_rate', 15, 2)->nullable()->after('result_unit');
            $table->decimal('total_cost', 15, 2)->nullable()->after('unit_rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('takeoff_items', function (Blueprint $table) {
            $table->dropColumn(['takeoff_section_id', 'unit_rate', 'total_cost']);
        });

        Schema::dropIfExists('takeoff_sections');
    }
}
