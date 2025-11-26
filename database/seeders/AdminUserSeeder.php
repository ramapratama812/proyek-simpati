<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('email','ramapratama812@gmail.com')->exists()) {
            User::create([
                'name'=>'Administrator No 2',
                'username'=>'adminKedua',
                'email'=>'ramapratama812@gmail.com',
                'password'=>Hash::make('password'),
                'role'=>'admin',
            ]);
        }
    }
}
