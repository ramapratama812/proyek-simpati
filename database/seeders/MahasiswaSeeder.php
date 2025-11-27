<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Data tetap: Makiyah
        $user1 = User::create([
            'username' => 'makiyah',
            'name'     => 'Makiyah',
            'email'    => 'makiyah@mhs.politala.ac.id',
            'password' => Hash::make('password123'),
            'role'     => 'mahasiswa',
        ]);

        Mahasiswa::create([
            'nama'             => 'Makiyah',
            'nim'              => '2401301064',
            'email'            => 'makiyah@mhs.politala.ac.id',
            'jenis_kelamin'    => 'Perempuan',
            'program_studi'    => 'Teknologi Informasi',
            'perguruan_tinggi' => 'Politeknik Negeri Tanah Laut',
            'status_terakhir'  => 'Aktif 2025/2026',
            'user_id'          => $user1->id,
        ]);

        // ✅ Data tetap: Budi
        $user2 = User::create([
            'username' => 'budi',
            'name'     => 'Budi Santoso',
            'email'    => 'budi@mhs.politala.ac.id',
            'password' => Hash::make('password123'),
            'role'     => 'mahasiswa',
        ]);

        Mahasiswa::create([
            'nama'             => 'Budi Santoso',
            'nim'              => '2401301055',
            'email'            => 'budi@mhs.politala.ac.id',
            'jenis_kelamin'    => 'Laki-laki',
            'program_studi'    => 'Teknologi Informasi',
            'perguruan_tinggi' => 'Politeknik Negeri Tanah Laut',
            'status_terakhir'  => 'Aktif 2025/2026',
            'user_id'          => $user2->id,
        ]);

        // ✅ Tambahkan 8 mahasiswa dummy lainnya
        for ($i = 1; $i <= 8; $i++) {
            $user = User::create([
                'name'     => "Mahasiswa {$i}",
                'username' => "mhs{$i}",
                'email'    => "mhs{$i}@example.com",
                'password' => Hash::make('password123'),
                'role'     => 'mahasiswa',
            ]);

            Mahasiswa::create([
                'nama'             => "Mahasiswa {$i}",
                'nim'              => "24013010{$i}",
                'email'            => "mhs{$i}@example.com",
                'jenis_kelamin'    => $i % 2 == 0 ? 'Perempuan' : 'Laki-laki',
                'program_studi'    => 'Teknologi Informasi',
                'perguruan_tinggi' => 'Politeknik Negeri Tanah Laut',
                'status_terakhir'  => 'Aktif 2025/2026',
                'user_id'          => $user->id,
            ]);
        }
    }
}
