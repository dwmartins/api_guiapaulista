<?php

use App\Http\Route;
use App\Middleware\UserMiddleware;

Route::get('/user', 'UserController@fetch', [
    [UserMiddleware::class, 'isAuth']
]);

Route::post('/user', 'UserController@create', [
    [UserMiddleware::class, 'isAuth'],
    [UserMiddleware::class, 'permissionsToUsers', 'create']
]);

Route::put('/user', 'UserController@update', [
    [UserMiddleware::class, 'isAuth']
]);

Route::delete('/user/{id}', 'UserController@delete', [
    [UserMiddleware::class, 'isAuth']
]);

Route::post('/user/recover-password', 'UserController@recoverPassword');
Route::post('/user/validate-recovery-code', 'UserController@validateRecoveryCode');
