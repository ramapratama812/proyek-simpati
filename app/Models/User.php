<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang bisa diisi
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

    /**
     * Kolom tersembunyi
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /* =====================================================
     | RELASI PROFIL
     ===================================================== */

    /**
     * 🔗 USER → MAHASISWA
     */
    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class, 'user_id', 'id');
    }

    /**
     * 🔗 USER → DOSEN
     */
    public function dosen()
    {
        return $this->hasOne(Dosen::class, 'user_id', 'id');
    }

    /* =====================================================
     | KEGIATAN / PROJECT
     ===================================================== */

    /**
     * 🔗 KEGIATAN YANG DIIKUTI USER
     * (mahasiswa & dosen)
     *
     * project_members.user_id → users.id
     */
    public function projectMembers()
    {
        return $this->hasMany(ProjectMember::class, 'user_id', 'id');
    }

    /**
     * 🔗 KEGIATAN YANG DIKETUAI USER (DOSEN)
     */
    public function projectsLed()
    {
        return $this->hasMany(ResearchProject::class, 'ketua_id', 'id');
    }

    /* =====================================================
     | NOTIFIKASI
     ===================================================== */

    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class, 'user_id', 'id');
    }
}
