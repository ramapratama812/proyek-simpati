<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ResearchProject;

class Publication extends Model
{
    protected $fillable = [
        'owner_id',
        'project_id',
        'judul',
        'jenis',
        'jurnal',
        'tahun',
        'volume',
        'nomor',
        'abstrak',
        'jumlah_halaman',
        'doi',
        'file',
        'issn',
        'penulis',
        'sumber',
        'validation_status',
        'validation_note',
        'validated_by',
        'gdrive_pdf_id',
        'gdrive_pdf_name',
        'gdrive_pdf_mime',
        'gdrive_pdf_size',
        'gdrive_pdf_view_link',
    ];

    protected $casts = ['penulis'=>'array','sumber'=>'array'];

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function scopeApproved($q)
    {
        return $q->where('validation_status', 'approved');
    }

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
