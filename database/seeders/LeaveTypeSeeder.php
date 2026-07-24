<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Annual Leave',
                'code' => 'AL',
                'days_allowed' => 21,
                'is_paid' => true,
                'requires_documentation' => false,
                'description' => 'Paid annual vacation leave',
                'is_active' => true,
            ],
            [
                'name' => 'Sick Leave',
                'code' => 'SL',
                'days_allowed' => 10,
                'is_paid' => true,
                'requires_documentation' => true,
                'description' => 'Leave for illness or medical treatment',
                'is_active' => true,
            ],
            [
                'name' => 'Personal Leave',
                'code' => 'PL',
                'days_allowed' => 3,
                'is_paid' => true,
                'requires_documentation' => false,
                'description' => 'Personal time off',
                'is_active' => true,
            ],
            [
                'name' => 'Bereavement Leave',
                'code' => 'BL',
                'days_allowed' => 5,
                'is_paid' => true,
                'requires_documentation' => true,
                'description' => 'Leave for death of family member',
                'is_active' => true,
            ],
            [
                'name' => 'Parental Leave',
                'code' => 'PARL',
                'days_allowed' => 60,
                'is_paid' => true,
                'requires_documentation' => true,
                'description' => 'Leave for childbirth or adoption',
                'is_active' => true,
            ],
            [
                'name' => 'Unpaid Leave',
                'code' => 'UL',
                'days_allowed' => 30,
                'is_paid' => false,
                'requires_documentation' => false,
                'description' => 'Unpaid leave at employee request',
                'is_active' => true,
            ],
            [
                'name' => 'Compassionate Leave',
                'code' => 'CL',
                'days_allowed' => 3,
                'is_paid' => true,
                'requires_documentation' => true,
                'description' => 'Paid leave for emergencies',
                'is_active' => true,
            ],
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::firstOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}
