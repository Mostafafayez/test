<?php

namespace Modules\Test\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TestController extends Controller
{

    public function get(){


$usr= User::all();
return response()->json (['user'=>$usr]);

    }
}