<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Pastikan user login
        if (! $request->user()) {
            abort(403, 'Unauthorized');
        }

        // Cek apakah role sesuai
        if ($request->user()->role !== $role) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
        Mahasiswa::create([
            'nama' => 'Ahmad Fauzi',
            'nim' => '2401301065',
            'jenis_kelamin' => 'Laki-laki',
            'program_studi' => 'Sistem Informasi',
            'perguruan_tinggi' => 'Universitas Indonesia',
            'status_terakhir' => 'Aktif 2025/2026',
            'user_id' => 2,
        ]);

        Mahasiswa::create([
            'nama' => 'Siti Nurhaliza',
            'nim' => '2401301066',
            'jenis_kelamin' => 'Perempuan',
            'program_studi' => 'Teknik Komputer',
            'perguruan_tinggi' => 'Institut Teknologi Bandung',
            'status_terakhir' => 'Aktif 2025/2026',
            'user_id' => 3,
        ]);

        Mahasiswa::create([
            'nama' => 'Budi Santoso',
            'nim' => '2401301067',
            'jenis_kelamin' => 'Laki-laki',
            'program_studi' => 'Teknologi Informasi',
            'perguruan_tinggi' => 'Universitas Gadjah Mada',
            'status_terakhir' => 'Aktif 2025/2026',
            'user_id' => 4,
        ]);

        Mahasiswa::create([
            'nama' => 'Dewi Lestari',
            'nim' => '2401301068',
            'jenis_kelamin' => 'Perempuan',
            'program_studi' => 'Sistem Informasi',
            'perguruan_tinggi' => 'Universitas Airlangga',
            'status_terakhir' => 'Aktif 2025/2026',
            'user_id' => 5,
        ]);