<?php

 use Modules\User\Http\Controllers\UserController;
 use Modules\Admin\Http\Controllers\AdminController;
// User signup route
 Route::post('/signup', [UserController::class, 'signup']);
 Route::post('/login', [UserController::class, 'login']);





Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/show', [UserController::class, 'show']);

    Route::post('/user', [UserController::class, 'update']);
});

Route::post('/send-verification-code', [UserController::class, 'sendVerificationCode']);
Route::post('auth/google', [UserController::class, 'loginWithGoogle']);


Route::group(['prefix' => 'admin'], function () {
    Route::post('/signup', [AdminController::class, 'signup']);
    Route::post('/login', [AdminController::class, 'login']);
    Route::post('/verify-user', [AdminController::class, 'verifyUser']);

    Route::delete('/user/{id}', [AdminController::class, 'deleteUser']);

});
