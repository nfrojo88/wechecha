<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('tool_transactions');
        Schema::create('tool_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            // Equipment/Tool reference
            $table->foreignId('equipment_id')->constrained('equipment_masters')->cascadeOnDelete();
            $table->foreignId('foreman_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete(); // Store keeper
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete(); // Store keeper on return
            
            $table->dateTime('checkout_time');
            $table->dateTime('checkin_time')->nullable();
            
            $table->string('status')->default('checked_out'); // checked_out, returned
            
            $table->text('checkout_notes')->nullable();
            $table->text('checkin_notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tool_transactions');
    }
};
