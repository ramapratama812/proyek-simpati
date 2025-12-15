<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';

    /**
     * Daftar kolom yang boleh diisi secara massal (create/update).
     * Pastikan program_studi & perguruan_tinggi ada di sini.
     */
    protected $fillable = [
        'user_id',
        'nama',
        'nim',
        'email',
        'jenis_kelamin',
        'semester',
        'status_aktivitas',
        'program_studi',      // <-- TAMBAHAN PENTING
        'perguruan_tinggi',   // <-- TAMBAHAN PENTING
    ];

    /**
     * Relasi Mahasiswa → User
     * (Many-to-One)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi Mahasiswa ↔ Kegiatan (Many-to-Many)
     * Mengambil daftar kegiatan yang diikuti mahasiswa.
     */
    public function kegiatan()
    {
        return $this->belongsToMany(
            Kegiatan::class,          // Model Kegiatan
            'kegiatan_mahasiswa',     // Tabel pivot
            'mahasiswa_id',           // FK pivot → mahasiswa
            'kegiatan_id'             // FK pivot → kegiatan
        )->withTimestamps();          // otomatis update created_at / updated_at pivot
    }
}