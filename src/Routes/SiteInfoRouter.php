<?php

use App\Controllers\SiteInfoController;
use App\Http\Route;
use App\Middleware\UserMiddleware;

Route::post('/siteinfo', 'SiteInfoController@create', [
    [UserMiddleware::class, 'isAuth'],
    [UserMiddleware::class, 'siteInfo']
]);

Route::post('/siteinfo/updateimages', 'SiteInfoController@setImages');
Route::get('/siteinfo', 'SiteInfoController@fetch');
