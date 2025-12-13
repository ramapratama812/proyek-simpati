<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMember extends Model
{
    use HasFactory;
    protected $fillable = ['project_id','user_id','peran'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ResearchProject::class, 'project_id', 'id');
    }
}