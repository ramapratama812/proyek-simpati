<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Dosen;       // ❗ TAMBAHKAN INI ❗
use App\Models\Mahasiswa;   // ❗ TAMBAHKAN INI ❗ 

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang bisa diisi (mass assignable).
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'nidn',
        'pddikti_id',
        'sinta_id',
        'garuda_id',
        'scholar_id',
        'orcid_id',
        'foto', // Sudah ada di sini
        'google_id',
        'status',
    ];

    protected $hidden = ['password','remember_token'];

    // ❗ RELASI BARU UNTUK MENGAMBIL DATA FOTO DARI TABEL TERKAIT ❗
    
    /**
     * Relasi ke profil dosen (Dosen Model).
     * Asumsi: foreign key di tabel `dosens` adalah `user_id`.
     */
    public function dosen()
    {
        return $this->hasOne(Dosen::class, 'user_id');
    }

    /**
     * Relasi ke profil mahasiswa (Mahasiswa Model).
     * Asumsi: foreign key di tabel `mahasiswas` adalah `user_id`.
     */
    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class, 'user_id');
    }

    // Relasi ke proyek penelitian yang diketuai
    public function projectsLed()
    {
        return $this->hasMany(ResearchProject::class,'ketua_id');
    }

    // Relasi ke notifikasi pengguna
    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class, 'user_id');
    }
}
