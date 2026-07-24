<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@constructpro.com'],
            [
                'name' => 'System Administrator',
                'email' => 'admin@constructpro.com',
                'password' => Hash::make('Admin@1234!'),
                'is_active' => true,
            ]
        );

        $admin->assignRole('global_admin');
    }
}
