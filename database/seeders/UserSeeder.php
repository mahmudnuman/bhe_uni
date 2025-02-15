<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin Users
        User::create([
            'name' => 'Admin One',
            'email' => 'admin1@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Admin Two',
            'email' => 'admin2@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create Counselor Users
        User::create([
            'name' => 'Counselor One',
            'email' => 'counselor1@example.com',
            'password' => Hash::make('password123'),
            'role' => 'counselor',
        ]);

        User::create([
            'name' => 'Counselor Two',
            'email' => 'counselor2@example.com',
            'password' => Hash::make('password123'),
            'role' => 'counselor',
        ]);
    }
}
