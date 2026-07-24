<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStandardWorksTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Main work item (e.g., "Concrete Work", unit "m3")
        Schema::create('standard_works', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // e.g., Civil, Finishing, MEP
            $table->string('name');     // e.g., Plain Concrete Work
            $table->string('unit');     // e.g., m3, m2, m, pcs
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Material conversion: for 1 unit of this work, how much material?
        Schema::create('standard_work_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('standard_work_id');
            $table->string('material_name');   // e.g., Cement
            $table->decimal('quantity', 15, 4); // e.g., 2
            $table->string('unit');            // e.g., bag, kg, m3, litre
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Manpower conversion: for 1 unit of this work, how many man-hours/days?
        Schema::create('standard_work_manpower', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('standard_work_id');
            $table->string('role');             // e.g., Skilled Labor, Foreman
            $table->decimal('quantity', 15, 4); // e.g., 0.5
            $table->string('unit');             // e.g., man-day, man-hour
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Equipment conversion: for 1 unit of this work, how many equipment-hours?
        Schema::create('standard_work_equipment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('standard_work_id');
            $table->string('equipment_name');   // e.g., Concrete Mixer, Excavator
            $table->decimal('quantity', 15, 4); // e.g., 0.1
            $table->string('unit');             // e.g., hour, day
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('standard_work_equipment');
        Schema::dropIfExists('standard_work_manpower');
        Schema::dropIfExists('standard_work_materials');
        Schema::dropIfExists('standard_works');
    }
}
