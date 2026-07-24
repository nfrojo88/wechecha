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
            $table->boolean('is_approved_by_gm')->default(false)->after('status');
            $table->timestamp('gm_approved_at')->nullable()->after('is_approved_by_gm');
            $table->foreignId('gm_approved_by')->nullable()->constrained('users')->nullOnDelete()->after('gm_approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['gm_approved_by']);
            $table->dropColumn(['is_approved_by_gm', 'gm_approved_at', 'gm_approved_by']);
        });
    }
};
