<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Dosen;
use App\Models\Mahasiswa;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    /**
     * Kolom yang bisa diisi (mass assignable)
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
        'foto',
        'google_id',
        'status',
    ];

    protected $casts = [
        'google_access_token' => 'encrypted',
        'google_refresh_token' => 'encrypted',
        'google_token_expires_at' => 'datetime',
    ];

    protected $hidden = ['password','remember_token'];

    // Relasi ke profil dosen
    public function lecturerProfile()
    {
        return $this->hasOne(Dosen::class);
    }

    // Relasi ke profil mahasiswa
    public function studentProfile()
    {
        return $this->hasOne(Mahasiswa::class);
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
