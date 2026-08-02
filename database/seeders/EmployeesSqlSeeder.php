<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class EmployeesSqlSeeder extends Seeder
{
    public function run()
    {
        $sqlPath = base_path('employees.sql');
        if (!File::exists($sqlPath)) {
            if ($this->command) {
                $this->command->error("employees.sql file not found at $sqlPath");
            }
            return;
        }

        $sql = File::get($sqlPath);

        // Extract just the INSERT INTO statement from old dump
        $start = strpos($sql, 'INSERT INTO `employees`');
        if ($start === false) {
            if ($this->command) {
                $this->command->info('No INSERT statement found in employees.sql. Skipping.');
            }
            return;
        }

        $insertQuery = substr($sql, $start);
        $end = strpos($insertQuery, 'ALTER TABLE');
        if ($end !== false) {
            $insertQuery = substr($insertQuery, 0, $end);
        }

        // Redirect the INSERT into a temp table
        $insertQuery = str_replace('INSERT INTO `employees`', 'INSERT INTO `employees_temp`', $insertQuery);

        try {
            Schema::dropIfExists('employees_temp');

            // Create temp table matching the OLD schema exactly
            Schema::create('employees_temp', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->id();
                $table->string('employee_id_number')->nullable();
                $table->string('user_id')->nullable();
                $table->text('full_name')->nullable();
                $table->text('department')->nullable();
                $table->text('designation')->nullable();
                $table->text('phone_number')->nullable();
                $table->text('base_salary')->nullable();
                $table->text('position')->nullable();
                $table->text('joining_date')->nullable();
                $table->text('salary')->nullable();
                $table->text('status')->nullable();
                $table->text('created_at')->nullable();
                $table->text('employment_date')->nullable();
                $table->text('educational_background')->nullable();
                $table->text('educational_file')->nullable();
                $table->text('experience_years')->nullable();
                $table->text('experience_file')->nullable();
                $table->text('application_letter_file')->nullable();
                $table->text('id_card_file')->nullable();
                $table->text('license_file')->nullable();
                $table->text('phone_number_2')->nullable();
                $table->text('guarantee_letter_file')->nullable();
                $table->text('contract_type')->nullable();
                $table->text('subcontractor_id')->nullable();
                $table->text('site_id')->nullable();
                $table->text('bank_info')->nullable();
                $table->text('rating')->nullable();
                $table->text('transport_allowance')->nullable();
                $table->text('house_allowance')->nullable();
                $table->text('position_allowance')->nullable();
            });

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::unprepared($insertQuery);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $oldEmployees = DB::table('employees_temp')->get();
            $imported    = 0;

            // Detect which optional columns actually exist in the live employees table
            $hasBankName           = Schema::hasColumn('employees', 'bank_name');
            $hasAccountNumber      = Schema::hasColumn('employees', 'account_number');
            $hasGuaranteeLetter    = Schema::hasColumn('employees', 'guarantee_letter');
            $hasTransportAllowance = Schema::hasColumn('employees', 'transport_allowance');
            $hasHouseAllowance     = Schema::hasColumn('employees', 'house_allowance');
            $hasPositionAllowance  = Schema::hasColumn('employees', 'position_allowance');
            $hasContractType       = Schema::hasColumn('employees', 'contract_type');

            foreach ($oldEmployees as $emp) {
                $code = $emp->employee_id_number
                    ?: ('EMP-' . str_pad($emp->id, 4, '0', STR_PAD_LEFT));

                // Skip if already imported
                if (DB::table('employees')->where('employee_code', $code)->exists()) {
                    continue;
                }

                // Validate user_id exists
                $userId = $emp->user_id;
                if ($userId && !User::where('id', $userId)->exists()) {
                    $userId = null;
                }

                // Parse bank info
                $bankName      = null;
                $accountNumber = null;
                if (!empty($emp->bank_info) && $emp->bank_info !== 'null' && $emp->bank_info !== '[]') {
                    $bankInfo = json_decode($emp->bank_info, true);
                    if (is_array($bankInfo) && count($bankInfo) > 0) {
                        $bankName      = $bankInfo[0]['bank_name'] ?? null;
                        $accountNumber = $bankInfo[0]['account_number'] ?? null;
                    }
                }

                // Build only the fields that definitely exist
                $data = [
                    'employee_code'   => $code,
                    'user_id'         => $userId,
                    'full_name'       => $emp->full_name ?: 'Unknown Employee',
                    'department'      => $emp->department,
                    'role_title'      => $emp->designation ?: $emp->position,
                    'phone'           => $emp->phone_number,
                    'date_of_joining' => $emp->joining_date ?: ($emp->employment_date ?: now()->toDateString()),
                    'employment_type' => (strtolower($emp->contract_type ?: 'full-time') === 'full-time') ? 'permanent' : 'contract',
                    'status'          => ($emp->status ?: 'active'),
                    'basic_salary'    => is_numeric($emp->salary) ? (float) $emp->salary : 0,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];

                // Conditionally add optional columns only if they exist in DB
                if ($hasContractType) {
                    $data['contract_type'] = $emp->contract_type;
                }
                if ($hasTransportAllowance) {
                    $data['transport_allowance'] = is_numeric($emp->transport_allowance) ? (float) $emp->transport_allowance : 0;
                }
                if ($hasHouseAllowance) {
                    $data['house_allowance'] = is_numeric($emp->house_allowance) ? (float) $emp->house_allowance : 0;
                }
                if ($hasPositionAllowance) {
                    $data['position_allowance'] = is_numeric($emp->position_allowance) ? (float) $emp->position_allowance : 0;
                }
                if ($hasBankName) {
                    $data['bank_name'] = $bankName;
                }
                if ($hasAccountNumber) {
                    $data['account_number'] = $accountNumber;
                }
                if ($hasGuaranteeLetter) {
                    $data['guarantee_letter'] = $emp->guarantee_letter_file;
                }

                DB::table('employees')->insert($data);
                $imported++;
            }

            Schema::dropIfExists('employees_temp');

            if ($this->command) {
                $this->command->info("$imported employees imported from employees.sql successfully.");
            }

        } catch (\Throwable $e) {
            Schema::dropIfExists('employees_temp');
            throw $e;
        }
    }
}
