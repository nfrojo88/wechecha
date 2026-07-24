<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransfersTable extends Migration
{
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('transfer_no', 50)->unique();
            $table->foreignId('from_store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('to_store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->date('required_date')->nullable();
            $table->text('reason')->nullable();
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'in_transit', 'completed', 'rejected', 'cancelled'])->default('draft')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('received_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('transfer_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('transfer_id')->constrained('transfers')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('requested_quantity', 15, 3);
            $table->decimal('approved_quantity', 15, 3)->default(0);
            $table->decimal('sent_quantity', 15, 3)->default(0);
            $table->decimal('received_quantity', 15, 3)->default(0);
            $table->string('unit', 20);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transfer_items');
        Schema::dropIfExists('transfers');
    }
}
