<?php

use App\Http\Route;

Route::post('/siteinfo/create', 'SiteInfoController@create');
Route::put('/siteinfo/update', 'SiteInfoController@update');
Route::get('/siteinfo/fetch', 'SiteInfoController@fetch');
