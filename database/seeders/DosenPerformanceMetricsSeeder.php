<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\DosenPerformanceMetric;
use Illuminate\Database\Seeder;

class DosenPerformanceMetricsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tahun yang mau diuji ranking-nya
        $tahun = now()->year;

        // Bersihkan data metrics untuk tahun ini dulu biar nggak bentrok
        DosenPerformanceMetric::where('tahun', $tahun)->delete();

        // Ambil beberapa dosen (misal 5–10 orang pertama)
        $dosens = Dosen::orderBy('id')->take(7)->get();

        if ($dosens->isEmpty()) {
            $this->command->warn('Tidak ada data dosen. Isi dulu tabel dosen sebelum seeding metrics.');
            return;
        }

        foreach ($dosens as $index => $dosen) {
            // Biar datanya nggak terlalu random ngawur,
            // kita buat variasi yang masih masuk akal tapi beda-beda
            $base = 10 + $index * 5; // cuma buat variasi antar dosen

            $sintaScore       = fake()->randomFloat(2, $base, $base + 30); // 10–50, 15–45, dst
            $sintaScore3Yr    = fake()->randomFloat(2, $base / 2, $base + 15);

            $jumlahHibah      = fake()->numberBetween(0, 5);
            $pubScholar1Th    = fake()->numberBetween(0, 10);
            $jumlahPenelitian = fake()->numberBetween(0, 8);
            $jumlahP3m        = fake()->numberBetween(0, 6);
            $jumlahPublikasi  = fake()->numberBetween(0, 20);

            DosenPerformanceMetric::create([
                'user_id'                => $dosen->id,
                'tahun'                  => $tahun,
                'sinta_score'            => $sintaScore,
                'sinta_score_3yr'        => $sintaScore3Yr,
                'jumlah_hibah'           => $jumlahHibah,
                'publikasi_scholar_1th'  => $pubScholar1Th,
                'jumlah_penelitian'      => $jumlahPenelitian,
                'jumlah_p3m'             => $jumlahP3m,
                'jumlah_publikasi'       => $jumlahPublikasi,
                // skor_akhir & peringkat biarkan null, nanti diisi service ranking
            ]);
        }

        $this->command->info('Dummy data dosen_performance_metrics untuk tahun '.$tahun.' berhasil dibuat.');
    }
}
