<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('employee_advances')) {
            Schema::create('employee_advances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->decimal('amount', 12, 2);
                $table->date('advance_date');
                $table->integer('installments')->default(1)->comment('Number of months to recover');
                $table->string('reason')->nullable();
                $table->enum('status', ['pending', 'approved', 'disbursed', 'rejected', 'recovered'])->default('pending')->index();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('approved_at')->nullable();
                $table->dateTime('disbursed_at')->nullable();
                $table->dateTime('recovered_at')->nullable();
                $table->text('gm_notes')->nullable();
                $table->text('finance_notes')->nullable();
                $table->timestamps();

                $table->index('employee_id');
            });
        } else {
            Schema::table('employee_advances', function (Blueprint $table) {
                if (!Schema::hasColumn('employee_advances', 'gm_notes')) {
                    $table->text('gm_notes')->nullable();
                }
                if (!Schema::hasColumn('employee_advances', 'finance_notes')) {
                    $table->text('finance_notes')->nullable();
                }
            });

            // Modify status column enum if possible, or leave as string
            try {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `employee_advances` MODIFY COLUMN `status` ENUM('pending', 'approved', 'disbursed', 'rejected', 'recovered') DEFAULT 'pending'");
            } catch (\Throwable $e) {}
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('employee_advances')) {
            Schema::table('employee_advances', function (Blueprint $table) {
                if (Schema::hasColumn('employee_advances', 'gm_notes')) {
                    $table->dropColumn('gm_notes');
                }
                if (Schema::hasColumn('employee_advances', 'finance_notes')) {
                    $table->dropColumn('finance_notes');
                }
            });
        }
    }
};
