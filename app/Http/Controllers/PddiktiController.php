<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\PddiktiFeederClient;
use App\Models\User;

class PddiktiController extends Controller
{
    public function syncDosen(PddiktiFeederClient $c)
    {
        abort_unless(auth()->user()?->role === 'admin', 403);

        $c->auth();
        $offset = 0; $imported = 0;
        do {
            $res = $c->listDosen(200, $offset);
            $rows = $res['data'] ?? [];
            foreach ($rows as $d) {
                User::firstOrCreate(
                    ['email' => $d['email'] ?? (Str::slug($d['nama_dosen']).'@politala.local')],
                    [
                        'name' => $d['nama_dosen'] ?? 'Dosen Tanpa Nama',
                        'username' => Str::slug(($d['nidn'] ?? $d['id_dosen'] ?? Str::uuid()).'_'.$imported),
                        'password' => bcrypt(Str::random(16)),
                        'role' => 'dosen',
                        'pddikti_id' => $d['id_dosen'] ?? null,
                    ]
                );
            }
            $imported += count($rows);
            $offset += 200;
        } while (!empty($rows));

        return back()->with('ok', "Sinkron dosen: {$imported} baris");
    }

    public function syncMhs(PddiktiFeederClient $c)
    {
        abort_unless(auth()->user()?->role === 'admin', 403);

        $c->auth();
        $offset = 0; $imported = 0;
        do {
            $res = $c->listMahasiswa(200, $offset);
            $rows = $res['data'] ?? [];
            foreach ($rows as $m) {
                User::firstOrCreate(
                    ['email' => $m['email'] ?? (Str::slug($m['nama_mahasiswa']).'@mhs.politala.local')],
                    [
                        'name' => $m['nama_mahasiswa'] ?? 'Mahasiswa',
                        'username' => Str::slug(($m['nim'] ?? $m['id_mahasiswa'] ?? Str::uuid()).'_'.$imported),
                        'password' => bcrypt(Str::random(16)),
                        'role' => 'mahasiswa',
                        'pddikti_id' => $m['id_mahasiswa'] ?? null,
                    ]
                );
            }
            $imported += count($rows);
            $offset += 200;
        } while (!empty($rows));

        return back()->with('ok', "Sinkron mahasiswa: {$imported} baris");
    }
}
