<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // e.g. 'created', 'updated', 'deleted', 'login', 'logout'
            $table->string('model_type')->nullable(); // e.g. 'App\Models\Project'
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('description'); // human-readable: "User John created Project XYZ"
            $table->string('module')->nullable(); // e.g. 'Projects', 'Inventory'
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('changes')->nullable(); // before/after data
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
