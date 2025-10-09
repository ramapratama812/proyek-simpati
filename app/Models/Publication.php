<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ResearchProject;

class Publication extends Model
{
    use HasFactory;
    protected $fillable = ['owner_id','project_id','judul','jenis','jurnal','tahun','doi','issn','penulis','sumber','tautan'];
    protected $casts = ['penulis'=>'array','sumber'=>'array','tautan'=>'array'];

    public function owner()
    {
        return $this->belongsTo(User::class,'owner_id');
    }

    public function project()
    {
        return $this->belongsTo(ResearchProject::class);
    }

    public function projects()
    {
        return $this->belongsToMany(ResearchProject::class, 'project_publications', 'publication_id', 'project_id')
                    ->withTimestamps();
    }
}
