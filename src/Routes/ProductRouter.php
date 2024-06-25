<?php

use App\Http\Route;

Route::get('/product/fetch', 'ProductController@fetch');
Route::post('/product/create', 'ProductController@create');
Route::put('/product/update', 'ProductController@update');
Route::delete('/product/delete/{id}', 'ProductController@delete');