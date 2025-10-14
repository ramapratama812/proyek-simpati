<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            AdminUserSeeder::class,
            MahasiswaSeeder::class,
            MahasiswaDummySeeder::class,
            DosenSeeder::class, // ✅ tambahkan seeder dosen di sini
        ]);
    }
}
