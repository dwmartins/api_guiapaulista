<?php

namespace App\Core;

use App\Http\Request;
use App\Http\Response;
use App\Utils\Logger;

class Core {
    public static function dispatch(array $routes) {
        $url = '/';
        
        isset($_GET['url']) && $url .= $_GET['url'];
        $url !== '/' && $url = rtrim($url, '/');

        $prefixController = 'App\\Controllers\\';

        $routesFound = false;

        foreach($routes as $route) {
            $pattern = '#^'. preg_replace('/{id}/', '([\w-]+)', $route['path'] . '$#'); 

            if(preg_match($pattern, $url, $matches)) {
                array_shift($matches);

                $routesFound = true;

                if($route['method'] !== Request::method()) {
                    logError('The "[' . Request::method() . ']" method is not allowed in "'. $url . '"');
                    Response::json([
                        'error'   => true,
                        'message' => 'Sorry, method not allowed.'
                    ], 405);

                    return;
                }

                [$controller, $action] = explode('@', $route['action']); 

                $controller = $prefixController . $controller;
                $extendController = new $controller();
                $extendController->$action(new Request, new Response, $matches);
            }
        }

        if(!$routesFound) {
            logError("Route not found: $url");
            $controller = $prefixController . 'NotFoundController';
            $extendController = new $controller();
            $extendController->index(new Request, new Response);
        }
    }
}