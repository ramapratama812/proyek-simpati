<?php

namespace Database\Seeders;

use App\Models\AhpCriteria;
use Illuminate\Database\Seeder;

class AhpCriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AhpCriteria::query()->delete(); // optional: kosongkan dulu kalau mau

        AhpCriteria::insert([
            [
                'kode'        => 'SINTA_SCORE',
                'nama'        => 'SINTA Score',
                'bobot'       => null,      // akan diisi hasil perhitungan AHP
                'is_benefit'  => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'kode'        => 'SINTA_SCORE_3YR',
                'nama'        => 'SINTA Score 3 tahun terakhir',
                'bobot'       => null,
                'is_benefit'  => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'kode'        => 'JUMLAH_HIBAH',
                'nama'        => 'Jumlah Hibah',
                'bobot'       => null,
                'is_benefit'  => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'kode'        => 'SCHOLAR_1YR',
                'nama'        => 'Publikasi Google Scholar dalam 1 tahun',
                'bobot'       => null,
                'is_benefit'  => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'kode'        => 'JUMLAH_PENELITIAN',
                'nama'        => 'Jumlah Penelitian',
                'bobot'       => null,
                'is_benefit'  => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'kode'        => 'JUMLAH_P3M',
                'nama'        => 'Jumlah Pengabdian (P3M)',
                'bobot'       => null,
                'is_benefit'  => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'kode'        => 'JUMLAH_PUBLIKASI',
                'nama'        => 'Jumlah Publikasi',
                'bobot'       => null,
                'is_benefit'  => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
