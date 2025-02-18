<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'User Admin',
                'username' => 'Admin',
                'password' => Hash::make('pkn@2022'), 
                'role' => 'Admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'kader@gmail.com'],
            [
                'name' => 'User Kader',
                'username' => 'kader',
                'password' => Hash::make('mempesona@2023'),
                'role' => 'Kader',
            ]
        );
        
        User::updateOrCreate(
            ['email' => 'manager@gmail.com'],
            [
                'name' => 'User Manager',
                'username' => 'Manager',
                'password' => Hash::make('pesona@2024'),
                'role' => 'Manager',
            ]
        );
        $this->command->info('user created or already exists.');
        
    }
}
