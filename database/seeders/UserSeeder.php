<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name'              => 'Admin User',
            'email'             => 'admin@marketingcrm.com',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'email_verified_at' => now(),
        ]);

        // Marketing Managers
        User::create([
            'name'              => 'Sarah Johnson',
            'email'             => 'sarah@marketingcrm.com',
            'password'          => Hash::make('password'),
            'role'              => 'marketing_manager',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name'              => 'Mike Chen',
            'email'             => 'mike@marketingcrm.com',
            'password'          => Hash::make('password'),
            'role'              => 'marketing_manager',
            'email_verified_at' => now(),
        ]);

        // Viewers
        User::create([
            'name'              => 'Emily Davis',
            'email'             => 'emily@marketingcrm.com',
            'password'          => Hash::make('password'),
            'role'              => 'viewer',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name'              => 'James Wilson',
            'email'             => 'james@marketingcrm.com',
            'password'          => Hash::make('password'),
            'role'              => 'viewer',
            'email_verified_at' => now(),
        ]);

        $this->command->info('Users seeded: admin@marketingcrm.com / password');
    }
}
