<?php

$routesDirectory = scandir(__DIR__."../");
$routes = array_diff($routesDirectory, ['.', '..', '.gitignore']);

foreach ($routes as $route) {
    if($route === "main.php") {
        continue;
    }

    $teste = __DIR__."../$route";

    include __DIR__."../$route";
}