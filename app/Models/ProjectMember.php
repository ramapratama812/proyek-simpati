<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMember extends Model
{
    use HasFactory;

    /**
     * Nama tabel (opsional tapi aman)
     */
    protected $table = 'project_members';

    /**
     * Kolom yang boleh diisi
     */
    protected $fillable = [
        'project_id',
        'user_id',
        'peran', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function project()
    {
        return $this->belongsTo(ResearchProject::class, 'project_id');
    }
}
