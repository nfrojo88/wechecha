<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunicationAdminTablesPhase910 extends Migration
{
    public function up()
    {
        // Drop tables if they exist to handle re-entrancy
        Schema::dropIfExists('activity_time_logs');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('equipment_productivity');
        Schema::dropIfExists('equipment_masters');
        Schema::dropIfExists('messages');

        // Messages
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->string('subject', 255)->nullable();
            $table->text('body');
            $table->enum('status', ['sent', 'read', 'deleted'])->default('sent');
            $table->string('attachment', 500)->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('messages')->nullOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['receiver_id', 'status']);
        });

        // Equipment Master
        Schema::create('equipment_masters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->string('code', 50)->unique();
            $table->string('category', 100)->nullable();
            $table->string('unit', 20)->default('hour');
            $table->decimal('hourly_rate', 15, 2)->default(0);
            $table->decimal('daily_rate', 15, 2)->default(0);
            $table->text('specifications')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Equipment Productivity
        Schema::create('equipment_productivity', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('equipment_id')->constrained('equipment_masters')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->date('work_date');
            $table->decimal('hours_operated', 7, 2)->default(0);
            $table->string('task_performed', 255)->nullable();
            $table->decimal('output_quantity', 15, 3)->nullable();
            $table->string('output_unit', 20)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // System Settings
        Schema::create('system_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string'); // string/integer/boolean/json
            $table->string('group', 50)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Activity Time Logs
        Schema::create('activity_time_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('page_url', 500);
            $table->string('page_title', 255)->nullable();
            $table->timestamp('entered_at');
            $table->timestamp('exited_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'entered_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_time_logs');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('equipment_productivity');
        Schema::dropIfExists('equipment_masters');
        Schema::dropIfExists('messages');
    }
}
