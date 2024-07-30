<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Admin\Database\Factories\AdminsFactory;

class admins extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

     protected $table = 'admin';

     protected $fillable = [
         'email',
         'password',
         'name',
         'phone',

     ];

     protected $hidden = [
         'password',
     ];
     public $timestamps = false;

}
