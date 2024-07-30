<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
// use Laravel\Sanctum\Http\Middleware\AuthenticateSession;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
class users extends Authenticatable
{


    use HasFactory, Notifiable, HasApiTokens;
    protected $table = 'user';


    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'type',
        'password',
    ];


    protected $hidden = [
        'password',
    ];

    // Optional casting
    protected $casts = [
        'type' => 'string',
    ];



    public $timestamps = false;
}
