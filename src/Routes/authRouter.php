<?php

use App\Http\Route;

Route::post('/auth/login', 'AuthController@login');
Route::get('/auth/auth', 'AuthController@auth');
