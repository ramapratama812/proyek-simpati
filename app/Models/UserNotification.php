<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    // update susunannya + tambah read_at
    protected $fillable = [
        'user_id',
        'project_id',
        'type',
        'message',
        'is_shown',
        'read_at'
    ];

    protected $casts = [
        'is_shown' => 'boolean',
        'read_at' => 'datetime'
    ];
    public function project(){
        return $this->belongsTo(ResearchProject::class,'project_id');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function getIsReadAttribute(): bool
    {
        return ! is_null($this->read_at);
    }

}
