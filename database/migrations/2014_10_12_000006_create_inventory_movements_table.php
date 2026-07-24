<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryMovementsTable extends Migration
{
    public function up()
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventory')->cascadeOnDelete();
            $table->string('type', 20); // in/out/transfer/adjustment/return
            $table->decimal('quantity', 15, 3);
            $table->nullableMorphs('reference'); // reference_type, reference_id
            $table->foreignId('performed_by')->constrained('users')->cascadeOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_movements');
    }
}
