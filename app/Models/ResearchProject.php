<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class ResearchProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis','judul','kategori_kegiatan','bidang_ilmu','skema','abstrak',
        'mulai','selesai','sumber_dana','biaya','ketua_id','is_public','external_refs'
        ,'created_by'
    ];

    protected $casts = [
        'external_refs'=>'array',
        'is_public'=>'boolean',
        'mulai'=>'date',
        'selesai'=>'date',
        'created_by'
    ];

    public function ketua()
    {
        return $this->belongsTo(User::class,'ketua_id');
    }

    public function images()
    {
        return $this->hasMany(ProjectImage::class,'project_id');
    }

    // public function publications(){ return $this->hasMany(Publication::class,'project_id'); }
    public function publications()
    {
        return $this->belongsToMany(\App\Models\Publication::class, 'project_publications', 'project_id', 'publication_id')
                    ->withTimestamps();
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members', 'project_id', 'user_id')
                    ->withPivot('peran')
                    ->withTimestamps();
    }

}
