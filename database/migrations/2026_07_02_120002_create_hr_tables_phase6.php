<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrTablesPhase6 extends Migration
{
    public function up()
    {
        // Departments
        Schema::create('departments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code', 50)->unique();
            // head_id references employees, added after employees table via FK constraint below
            $table->unsignedBigInteger('head_id')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Designations
        Schema::create('designations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->decimal('min_salary', 15, 2)->nullable();
            $table->decimal('max_salary', 15, 2)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Attendance
        Schema::create('attendance', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('attendance_date')->index();
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->decimal('hours_worked', 5, 2)->nullable();
            $table->enum('status', ['present', 'absent', 'half_day', 'leave', 'holiday', 'weekend'])->default('present')->index();
            $table->enum('source', ['manual', 'biometric', 'mobile'])->default('manual');
            $table->string('biometric_device_id', 50)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['employee_id', 'attendance_date']);
        });

        // Manpower Requests
        Schema::create('manpower_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('task_id')->nullable()->constrained('erp_plan_tasks')->nullOnDelete();
            $table->enum('type', ['normal', 'emergency'])->default('normal');
            $table->date('required_date')->nullable();
            $table->text('requirements')->nullable();
            $table->enum('status', ['pending', 'approved', 'fulfilled', 'rejected', 'cancelled'])->default('pending')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('manpower_request_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('manpower_request_id')->constrained('manpower_requests')->cascadeOnDelete();
            $table->string('role_title');
            $table->integer('quantity')->default(1);
            $table->string('skill_level', 100)->nullable();
            $table->decimal('daily_rate', 15, 2)->nullable();
            $table->integer('duration_days')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Employee Contracts
        Schema::create('employee_contracts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('contract_type', 50);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('salary', 15, 2)->default(0);
            $table->text('terms')->nullable();
            $table->string('contract_file', 500)->nullable();
            $table->enum('status', ['active', 'expired', 'terminated', 'renewed'])->default('active');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Employee Expense Claims
        Schema::create('employee_expense_claims', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('category', 100);
            $table->date('expense_date');
            $table->text('description');
            $table->string('receipt_file', 500)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_expense_claims');
        Schema::dropIfExists('employee_contracts');
        Schema::dropIfExists('manpower_request_items');
        Schema::dropIfExists('manpower_requests');
        Schema::dropIfExists('attendance');
        Schema::dropIfExists('designations');
        Schema::dropIfExists('departments');
    }
}
