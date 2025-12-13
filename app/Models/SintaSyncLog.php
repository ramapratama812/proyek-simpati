<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SintaSyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun',
        'triggered_by',
        'source',
        'total_metrics',
        'status',
        'message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

}
