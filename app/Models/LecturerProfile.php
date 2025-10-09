<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LecturerProfile extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','nidn','nip','bidang_keahlian','bio'];
}
