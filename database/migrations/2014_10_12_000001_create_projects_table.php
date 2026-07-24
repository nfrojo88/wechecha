<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->string('location', 255)->nullable();
            $table->string('client_name', 255)->nullable();
            $table->string('client_contact', 255)->nullable();
            $table->enum('status', ['planning', 'bidding', 'active', 'on_hold', 'completed', 'cancelled', 'handover'])->default('planning')->index();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('contract_value', 18, 2)->default(0);
            $table->decimal('budget_allocated', 18, 2)->default(0);
            $table->decimal('budget_consumed', 18, 2)->default(0);
            // default_store_id will be added later due to circular dependency
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
