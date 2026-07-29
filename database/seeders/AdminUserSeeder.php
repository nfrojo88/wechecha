<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Create or update the admin user (no 'username' column in users table)
        $admin = User::updateOrCreate(
            ['email' => 'fro@wechecha.com'],
            [
                'name'      => 'General Admin',
                'email'     => 'fro@wechecha.com',
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]
        );

        if (method_exists($admin, 'assignRole')) {
            try {
                $admin->assignRole('global_admin');
            } catch (\Exception $e) {
                // Role may not exist yet — ignore
            }
        }
    }
}
