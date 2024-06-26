<?php

// Exemplo de uso para adicionar Middleware nas rotas;

// use App\Http\Route;
// use App\Middleware\UserAuth;

// Route::get('/fetch', 'HomeController@fetch', [
//    [UserAuth::class, 'isAuthenticated'],   // Middleware 1: Verifica se o usuário está autenticado
//    [UserAuth::class, 'isAdmin']   // Middleware 2: Verifica outra condição relacionada ao usuário
// ]);

$routesDirectory = scandir(__DIR__."../");
$routes = array_diff($routesDirectory, ['.', '..', '.gitignore']);

foreach ($routes as $route) {
    if($route === "main.php") {
        continue;
    }

    include __DIR__."../$route";
}