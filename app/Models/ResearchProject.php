<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResearchProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis','judul','kategori_kegiatan','bidang_ilmu','skema','abstrak',
        'mulai','selesai','sumber_dana','biaya','ketua_id','is_public','external_refs'
    ];

    protected $casts = [
        'external_refs'=>'array',
        'is_public'=>'boolean',
        'mulai'=>'date',
        'selesai'=>'date',
    ];

    public function ketua(){ return $this->belongsTo(User::class,'ketua_id'); }
    public function images(){ return $this->hasMany(ProjectImage::class,'project_id'); }
    public function publications(){ return $this->hasMany(Publication::class,'project_id'); }
}
