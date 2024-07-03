<?php

use App\Http\Route;
use App\Middleware\UserMiddleware;

Route::post('/emailconfig', 'EmailConfigController@create', [
    [UserMiddleware::class, 'isAuth']
]);
Route::get('/emailconfig', 'EmailConfigController@fetch', [
    [UserMiddleware::class, 'isAuth']
]);