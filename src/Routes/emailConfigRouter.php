<?php

use App\Http\Route;
use App\Middleware\UserMiddleware;

Route::post('/emailconfig', 'EmailConfigController@create', [
    [UserMiddleware::class, 'isAuth'],
    [UserMiddleware::class, 'emailSendingSettings']
]);
Route::get('/emailconfig', 'EmailConfigController@fetch', [
    [UserMiddleware::class, 'isAuth']
]);