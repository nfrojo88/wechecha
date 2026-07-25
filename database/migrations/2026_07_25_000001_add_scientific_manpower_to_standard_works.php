<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a `type` column to standard_work_manpower so we can distinguish
     * regular manpower from scientific manpower rows.
     */
    public function up(): void
    {
        Schema::table('standard_work_manpower', function (Blueprint $table) {
            $table->string('type')->default('regular')->after('unit'); // 'regular' | 'scientific'
        });
    }

    public function down(): void
    {
        Schema::table('standard_work_manpower', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
