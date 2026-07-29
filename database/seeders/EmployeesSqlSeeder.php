<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class EmployeesSqlSeeder extends Seeder
{
    public function run()
    {
        $sqlPath = base_path('employees.sql');
        if (!File::exists($sqlPath)) {
            $this->command->error("employees.sql file not found at $sqlPath");
            return;
        }

        $sql = File::get($sqlPath);

        // Turn off foreign key constraints during execution
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Execute raw SQL statements
        DB::unprepared($sql);

        // Enable foreign key constraints back
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('employees.sql imported successfully.');
    }
}
