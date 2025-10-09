<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'role',
        'pddikti_id',
        'sinta_id',
        'garuda_id',
        'scholar_id',
        'orcid_id',
        'foto', // 🆕 tambahkan ini untuk menyimpan path foto profil
    ];

    protected $hidden = ['password', 'remember_token'];

    public function lecturerProfile()
    { 
        return $this->hasOne(LecturerProfile::class); 
    }

    public function studentProfile()
    { 
        return $this->hasOne(StudentProfile::class); 
    }

    // 🔗 Relasi ke tabel mahasiswas
    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class, 'user_id');
    }
}
