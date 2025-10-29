<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class ProjectImage extends Model
{
    use HasFactory;
    protected $fillable = ['project_id','path'];
    protected $appends = ['url'];

    public function getUrlAttribute(): string
    {
        $raw = ltrim((string) $this->path, '/');
        $normalized = preg_replace('#^(storage/|public/)#', '', $raw);
        return Storage::disk('public')->url($normalized); // -> APP_URL/storage/...
    }

}
