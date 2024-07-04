<?php

use App\Http\Route;
use App\Middleware\UserMiddleware;

Route::post('/siteinfo', 'SiteInfoController@create', [
    [UserMiddleware::class, 'isAuth'],
    [UserMiddleware::class, 'siteInfo']
]);
Route::get('/siteinfo', 'SiteInfoController@fetch');
