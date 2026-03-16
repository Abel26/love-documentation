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
        User::create([
            'username' => 'abel',
            'name' => 'Abel',
            'email' => 'abel@lovedoc.com',
            'password' => Hash::make('abel'),
        ]);

        User::create([
            'username' => 'akhsa',
            'name' => 'Akhsa',
            'email' => 'akhsa@lovedoc.com',
            'password' => Hash::make('akhsa'),
        ]);
    }
}
