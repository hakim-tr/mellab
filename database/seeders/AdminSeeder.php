<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'], // email dyal admin
            [
                'name' => 'Super Admin',
                'password' => Hash::make('123456'), // mot de passe
                'role' => User::ROLE_ADMIN,
            ]
        );
    }
}