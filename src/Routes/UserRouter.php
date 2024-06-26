<?php

use App\Http\Route;
use App\Middleware\UserMiddleware;

Route::get('/user', 'UserController@fetch', [
    [UserMiddleware::class, 'adminLogged']
]);

Route::post('/user', 'UserController@create', [
    [UserMiddleware::class, 'adminLogged']
]);

Route::put('/user', 'UserController@update', [
    [UserMiddleware::class, 'modLogged']
]);

Route::delete('/user/{id}', 'UserController@delete', [
    [UserMiddleware::class, 'modLogged']
]);