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
        if (!Schema::hasTable('employee_experience')) {
            Schema::create('employee_experience', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->string('job_title');
                $table->string('company_name');
                $table->string('location')->nullable();
                $table->date('start_date');
                $table->date('end_date')->nullable(); // Null if currently working
                $table->boolean('is_current')->default(false);
                $table->text('responsibilities')->nullable();
                $table->string('reference_name')->nullable();
                $table->string('reference_phone')->nullable();
                $table->string('license_document')->nullable(); // Path to license PDF/image
                $table->string('license_number')->nullable();
                $table->date('license_expiry')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_experience');
    }
};
