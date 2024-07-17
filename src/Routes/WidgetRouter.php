<?php

use App\Http\Route;

Route::get('/widgets', 'WidgetController@fetch');
Route::post('/widgets/floatingButton', 'WidgetController@floatingButton');