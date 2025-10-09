<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory;

    protected $fillable = [
        'foto',
        'nama',
        'nidn',
        'perguruan_tinggi',
        'status_ikatan_kerja',
        'jenis_kelamin',
        'program_studi',
        'pendidikan_terakhir',
        'status_aktivitas'
    ];
}
