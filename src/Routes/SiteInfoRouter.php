<?php

use App\Http\Route;

Route::post('/siteinfo', 'SiteInfoController@create');
Route::get('/siteinfo', 'SiteInfoController@fetch');
