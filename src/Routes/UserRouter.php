<?php

use App\Http\Route;
use App\Middleware\UserMiddleware;

Route::post('/user', 'UserController@create');
Route::put('/user', 'UserController@update');
Route::delete('/user/{id}', 'UserController@delete');