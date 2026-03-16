<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Super Admin
        User::create([
            'username' => 'admin',
            'name' => 'Super Admin',
            'email' => 'admin@lovedoc.com',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
        ]);

        // Regular User - Abel
        User::create([
            'username' => 'abel',
            'name' => 'Abel',
            'email' => 'abel@lovedoc.com',
            'password' => Hash::make('abel'),
            'role' => 'user',
        ]);

        // Regular User - Akhsa
        User::create([
            'username' => 'akhsa',
            'name' => 'Akhsa',
            'email' => 'akhsa@lovedoc.com',
            'password' => Hash::make('akhsa'),
            'role' => 'user',
        ]);
    }
}
