<?php

// Exemplo de uso para adicionar Middleware nas rotas;

// use App\Http\Route;
// use App\Middleware\UserAuth;

// Route::get('/users', 'UserController@create', [
//    [UserAuth::class, 'isAuth'],   // Middleware 1: Verifica se o usuário está autenticado
//    [UserAuth::class, 'permissionsToUsers', 'create']   // Middleware 2: Verifica outra condição relacionada ao usuário
// ]);
//
// O ultimo campo 'crete', pode ser recuperado mo middleware ex:
// public function permissionsToUsers(Request $request, Response $response, $param) {
//      if ($param == 'create') {
//
//      }
// }

$routesDirectory = scandir(__DIR__."../");
$routes = array_diff($routesDirectory, ['.', '..', '.gitignore']);

foreach ($routes as $route) {
    if($route === "main.php") {
        continue;
    }

    include __DIR__."../$route";
}