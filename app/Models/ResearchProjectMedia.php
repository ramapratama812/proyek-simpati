<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchProjectMedia extends Model
{
    use HasFactory;

    protected $table = 'research_project_media';

    protected $fillable = [
        'research_project_id',
        'gdrive_file_id',
        'name',
        'mime_type',
        'size',
        'web_view_link',
    ];

    public function project()
    {
        return $this->belongsTo(ResearchProject::class, 'research_project_id');
    }
}
