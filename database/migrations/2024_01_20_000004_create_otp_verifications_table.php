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
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->index();
            $table->string('otp');
            $table->boolean('verified')->default(false);
            $table->timestamp('expires_at');
            $table->integer('attempts')->default(0);
            $table->timestamps();
        });

        // Add phone_verified to users table
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('phone_verified')->default(false)->after('email_verified_at');
            $table->timestamp('phone_verified_at')->nullable()->after('phone_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_verified', 'phone_verified_at']);
        });
    }
};
