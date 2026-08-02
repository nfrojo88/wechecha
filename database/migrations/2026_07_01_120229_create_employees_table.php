<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('employee_code')->unique();
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('role_title')->nullable();
            $table->string('department')->nullable();
            $table->string('employment_type')->default('permanent'); // permanent, contract, daily
            $table->string('contract_type')->nullable();
            $table->date('date_of_joining')->nullable();
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->decimal('transport_allowance', 15, 2)->default(0);
            $table->decimal('house_allowance', 15, 2)->default(0);
            $table->decimal('position_allowance', 15, 2)->default(0);
            $table->string('bank_name')->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('guarantee_letter')->nullable();
            $table->date('guarantee_letter_submitted_at')->nullable();
            $table->boolean('guarantee_letter_required')->default(true);
            $table->string('status')->default('active'); // active, suspended, terminated
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
