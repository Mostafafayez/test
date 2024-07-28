<?php

namespace Modules\Test\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Test\Database\Factories\UserFactory;

class User extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['id', 'name ' , 'e-mail','paswword'];

    // protected static function newFactory(): UserFactory
    // {
    //     //return UserFactory::new();
    // }
}
