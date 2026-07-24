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
        Schema::table('employees', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('employees', 'guarantee_letter')) {
                $table->string('guarantee_letter')->nullable();
            }
            if (!Schema::hasColumn('employees', 'guarantee_letter_submitted_at')) {
                $table->date('guarantee_letter_submitted_at')->nullable();
            }
            if (!Schema::hasColumn('employees', 'guarantee_letter_required')) {
                $table->boolean('guarantee_letter_required')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['guarantee_letter', 'guarantee_letter_submitted_at', 'guarantee_letter_required']);
        });
    }
};
