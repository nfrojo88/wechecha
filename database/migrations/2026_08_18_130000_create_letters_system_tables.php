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
        // 1. Letters Table
        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->string('letter_number', 60)->unique();
            $table->enum('type', ['incoming', 'outgoing'])->default('incoming');
            $table->date('date');
            $table->string('subject', 255);
            $table->longText('specification');
            $table->string('sender', 255)->nullable(); // external sender or originating dept
            $table->string('sender_department', 100)->nullable();
            $table->string('recipient_organization', 255)->nullable(); // for outgoing letters
            $table->enum('priority', ['normal', 'urgent'])->default('normal');
            $table->enum('status', ['pending', 'viewed', 'redirected', 'closed'])->default('pending');
            
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->text('closing_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Letter Attachments Table
        Schema::create('letter_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_id')->constrained('letters')->onDelete('cascade');
            $table->string('file_path', 500);
            $table->string('file_name', 255);
            $table->string('file_type', 30); // pdf, png, jpg, jpeg
            $table->unsignedBigInteger('file_size')->default(0); // bytes
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 3. Letter Recipients & Routing Log Table
        Schema::create('letter_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_id')->constrained('letters')->onDelete('cascade');
            $table->foreignId('from_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('to_role_name', 100)->nullable(); // e.g. finance, site_engineer, manager
            $table->enum('action', ['initial_sent', 'redirected', 'viewed', 'closed'])->default('initial_sent');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'viewed', 'redirected', 'closed'])->default('pending');
            $table->timestamp('viewed_at')->nullable();
            $table->timestamps();
        });

        // 4. Letter In-app Notifications Table
        Schema::create('letter_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('letter_id')->constrained('letters')->onDelete('cascade');
            $table->string('message', 255);
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_notifications');
        Schema::dropIfExists('letter_recipients');
        Schema::dropIfExists('letter_attachments');
        Schema::dropIfExists('letters');
    }
};
