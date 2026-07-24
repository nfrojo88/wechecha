<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToBoqItemsTable extends Migration
{
    public function up()
    {
        Schema::table('boq_items', function (Blueprint $table) {
            $table->decimal('tender_quantity', 15, 3)->default(0)->after('unit');
            $table->foreignId('schedule_task_id')->nullable()->after('product_id')->constrained('schedule_tasks')->nullOnDelete();
            $table->foreignId('takeoff_item_id')->nullable()->after('schedule_task_id')->constrained('takeoff_items')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('boq_items', function (Blueprint $table) {
            $table->dropForeign(['schedule_task_id']);
            $table->dropForeign(['takeoff_item_id']);
            $table->dropColumn(['tender_quantity', 'schedule_task_id', 'takeoff_item_id']);
        });
    }
}
