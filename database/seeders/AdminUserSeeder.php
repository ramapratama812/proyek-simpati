<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('email','admin@simpati.local')->exists()) {
            User::create([
                'name'=>'Administrator',
                'username'=>'admin',
                'email'=>'admin@simpati.local',
                'password'=>Hash::make('password'),
                'role'=>'admin',
            ]);
        }
    }
}
