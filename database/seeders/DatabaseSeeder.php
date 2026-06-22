<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@meditrack.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
        ]);

        // Create Apoteker User
        User::create([
            'name' => 'Dr. Lia',
            'email' => 'apoteker@meditrack.com',
            'password' => Hash::make('password'),
            'role' => 'apoteker',
            'phone' => '081234567891',
        ]);

        // Create Kurir User
        User::create([
            'name' => 'Kurir Andi',
            'email' => 'kurir@meditrack.com',
            'password' => Hash::make('password'),
            'role' => 'kurir',
            'phone' => '081234567892',
        ]);

        // Create Operator User
        User::create([
            'name' => 'Operator Radiologi',
            'email' => 'operator@meditrack.com',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'phone' => '081234567893',
        ]);

        // Create Dokter User
        User::create([
            'name' => 'Dr. Ahmad Sp.Rad',
            'email' => 'dokter@meditrack.com',
            'password' => Hash::make('password'),
            'role' => 'dokter',
            'phone' => '081234567894',
        ]);

        $this->command->info('Sample users created successfully!');
        $this->command->info('Admin: admin@meditrack.com / password');
        $this->command->info('Apoteker: apoteker@meditrack.com / password');
        $this->command->info('Kurir: kurir@meditrack.com / password');
        $this->command->info('Operator: operator@meditrack.com / password');
        $this->command->info('Dokter: dokter@meditrack.com / password');
    }
}