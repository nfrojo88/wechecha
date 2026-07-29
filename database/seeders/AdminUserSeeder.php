<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $admin = User::updateOrCreate(
            ['email' => 'fro@wechecha.com'],
            [
                'name' => 'General Admin',
                'username' => 'fro_admin',
                'email' => 'fro@wechecha.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        if (method_exists($admin, 'assignRole')) {
            $admin->assignRole('global_admin');
        }
    }
}
