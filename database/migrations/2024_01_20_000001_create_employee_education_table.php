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
        if (!Schema::hasTable('employee_education')) {
            Schema::create('employee_education', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->string('degree_level'); // Bachelor, Master, PhD, Diploma, Certificate
                $table->string('field_of_study');
                $table->string('institution_name');
                $table->string('location')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->string('grade_gpa')->nullable();
                $table->text('description')->nullable();
                $table->string('certificate_photo')->nullable(); // Path to photo/document
                $table->boolean('is_verified')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_education');
    }
};
