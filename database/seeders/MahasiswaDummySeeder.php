<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Mahasiswa;
use Illuminate\Support\Str;

class MahasiswaDummySeeder extends Seeder
{
    public function run(): void
    {
        // Data mahasiswa nyata (Makiyah) - gunakan firstOrCreate agar tidak duplikat
        $user = User::firstOrCreate(
            ['email' => 'makiyah@mhs.politala.ac.id'], // cek unik
            [
                'name' => 'Makiyah',
                'username' => 'makiyah',
                'password' => bcrypt('password123'),
                'role' => 'mahasiswa',
            ]
        );

        // Pastikan Mahasiswa Makiyah ada
        Mahasiswa::firstOrCreate(
            ['email' => 'makiyah@mhs.politala.ac.id'],
            [
                'nama' => 'Makiyah',
                'nim' => '2401301064',
                'jenis_kelamin' => 'Perempuan',
                'program_studi' => 'Teknologi Informasi',
                'perguruan_tinggi' => 'Politeknik Negeri Tanah Laut',
                'status_terakhir' => 'Aktif 2025/2026',
                'user_id' => $user->id,
            ]
        );

        // Data dummy tambahan 9 mahasiswa
        for ($i = 1; $i <= 9; $i++) {
            $dummyUser = User::firstOrCreate(
                ['email' => 'mahasiswa' . $i . '@mhs.politala.ac.id'],
                [
                    'name' => 'Mahasiswa ' . $i,
                    'username' => 'mahasiswa' . $i,
                    'password' => bcrypt('password123'),
                    'role' => 'mahasiswa',
                ]
            );

            Mahasiswa::firstOrCreate(
                ['email' => 'mahasiswa' . $i . '@mhs.politala.ac.id'],
                [
                    'nama' => 'Mahasiswa ' . $i,
                    'nim' => '24013010' . str_pad($i + 64, 2, '0', STR_PAD_LEFT),
                    'jenis_kelamin' => $i % 2 === 0 ? 'Laki-laki' : 'Perempuan',
                    'program_studi' => 'Teknologi Informasi',
                    'perguruan_tinggi' => 'Politeknik Negeri Tanah Laut',
                    'status_terakhir' => 'Aktif 2025/2026',
                    'user_id' => $dummyUser->id,
                ]
            );
        }
    }
}
