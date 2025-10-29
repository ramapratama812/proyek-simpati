<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'mahasiswa';

    // Kolom yang bisa diisi
    protected $fillable = [
        'user_id',
        'nama',
        'nim',
        'email',
        'jenis_kelamin',
        'program_studi',
        'perguruan_tinggi',
        'jenjang_pendidikan', // ⚡ tambahkan agar sinkron dengan ProfileController
        'semester',
        'status_aktivitas',
        'no_hp', // ⚡ tambahkan juga karena di controller digunakan
    ];

    // Relasi ke tabel users
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
