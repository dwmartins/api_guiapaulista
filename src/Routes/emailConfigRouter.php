<?php

use App\Http\Route;

Route::post('/emailconfig', 'EmailConfigController@create');
Route::get('/emailconfig', 'EmailConfigController@fetch');