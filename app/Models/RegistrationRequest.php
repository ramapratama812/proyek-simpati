<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationRequest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'role',
        'status',
        'note',
        'username',
        'identity',
        'sinta_id',
        'password',
        'google_id',

    ];
}
