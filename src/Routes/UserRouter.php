<?php

use App\Http\Route;
use App\Middleware\UserMiddleware;

Route::post('/user', 'UserController@create', [
    [UserMiddleware::class, 'adminLogged']
]);