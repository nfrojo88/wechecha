<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollsTable extends Migration
{
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('month'); // 1–12
            $table->unsignedSmallInteger('year');
            $table->decimal('basic_salary',  15, 2)->default(0);
            $table->decimal('allowances',    15, 2)->default(0);
            $table->decimal('overtime_pay',  15, 2)->default(0);
            $table->decimal('deductions',    15, 2)->default(0);
            $table->decimal('tax',           15, 2)->default(0);
            $table->decimal('net_salary',    15, 2)->default(0);
            $table->string('status')->default('draft'); // draft, paid
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->unique(['employee_id', 'month', 'year']); // one payroll per employee per month
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payrolls');
    }
}
