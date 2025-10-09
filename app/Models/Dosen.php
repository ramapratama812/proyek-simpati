<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'email', 
        'nomor_hp',               // ✅ tambahkan kolom email
        'nidn',
        'perguruan_tinggi',
        'status_ikatan_kerja',
        'jenis_kelamin',
        'program_studi',
        'pendidikan_terakhir',
        'status_aktivitas',
        'foto'
    ];

    /**
     * Relasi ke tabel users (jika ingin dihubungkan)
     * Asumsi: user.email = dosen.email
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
