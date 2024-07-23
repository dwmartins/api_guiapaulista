<?php

use App\Http\Route;
use App\Middleware\UserMiddleware;

Route::get('/user', 'UserController@fetch', [
    [UserMiddleware::class, 'isAuth'],
    [UserMiddleware::class, 'permissionsToUsers']
]);

Route::post('/user', 'UserController@create', [
    [UserMiddleware::class, 'isAuth'],
    [UserMiddleware::class, 'permissionsToUsers']
]);

Route::put('/user', 'UserController@update', [
    [UserMiddleware::class, 'isAuth'],
    [UserMiddleware::class, 'permissionsToUsers']
]);

Route::delete('/user/{id}', 'UserController@delete', [
    [UserMiddleware::class, 'isAuth'],
    [UserMiddleware::class, 'permissionsToUsers']
]);

Route::post('/user/delete-multiples', 'UserController@deleteMultiples', [
    [UserMiddleware::class, 'isAuth'],
    [UserMiddleware::class, 'permissionsToUsers']
]);

Route::post('/user/update-photo', 'UserController@updatePhoto', [
    [UserMiddleware::class, 'isAuth']
]);

Route::put('/user/update-role', 'UserController@updateRole', [
    [UserMiddleware::class, 'isAuth'],
    [UserMiddleware::class, 'permissionsToUsers']
]);

Route::post('/user/recover-password', 'UserController@recoverPassword');
Route::get('/user/validate-recovery-token', 'UserController@validateRecoveryToken');
Route::put('/user/update-password-by-token', 'UserController@updatePasswordByRecovery');
