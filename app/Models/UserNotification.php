<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $fillable = ['user_id','project_id','type','message','is_shown'];
    protected $casts = ['is_shown'=>'boolean'];

    public function project(){ return $this->belongsTo(ResearchProject::class,'project_id'); }
}
