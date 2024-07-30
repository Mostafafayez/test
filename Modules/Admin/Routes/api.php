<?php 


use App\Http\Controllers\UserController;

// User signup route
Route::post('/signup', [UserController::class, 'signup']);
