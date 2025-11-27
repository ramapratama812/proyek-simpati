<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'email',
        'nomor_hp',
        'nidn',
        'nip', // tambahkan ini jika memang ada kolom NIP di tabel
        'status_ikatan_kerja',
        'jenis_kelamin',
        'pendidikan_terakhir',
        'status_aktivitas', // enum: Aktif / Tidak Aktif / Cuti
        'foto',
    ];

    /**
     * Relasi ke tabel users (jika ingin dihubungkan)
     * Asumsi: user.email = dosen.email
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');

    }
}
