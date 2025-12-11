<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DosenPrestasi extends Model
{
    protected $fillable = [
        'dosen_id',
        'judul',
        'kategori',
        'deskripsi',
        'tahun',
        'tingkat',
        'file_bukti',
        'link',
        'status',
    ];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function getFileUrlAttribute()
    {
        return $this->file_bukti ? Storage::url($this->file_bukti) : null;
    }
}
