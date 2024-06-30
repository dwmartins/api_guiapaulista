<?php

namespace App\Core;

use App\Http\Request;
use App\Http\Response;

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

                if (!self::methodAllowed($route['method'])) {
                    continue; // Continua para a próxima iteração do loop se o método não for permitido
                }

                $middlewares = $route['middlewares'] ?? [];
                // Verifica se possui middleware na rota;
                if(count($middlewares)) {
                    if (!self::applyMiddlewares($middlewares, new Request, new Response)) {
                        return;
                    }
                }
                
                [$controller, $action] = explode('@', $route['action']); 

                $controller = $prefixController . $controller;

                // Verifica se o controlador e o método existem
                if (!class_exists($controller) || !method_exists($controller, $action)) {
                    self::handleRouteNotFound($controller, $action);
                    return;
                }

                // Instancia o controlador e chama o método correspondente
                $extendController = new $controller();
                $extendController->$action(new Request, new Response, $matches);
                return;
            }
        }

        if(!$routesFound) {
            self::routeNotFoundResponse($url);
        } else {
            self::methodNotAllowedResponse($url); 
            // Retorna método não permitido apenas se nenhuma rota correspondente foi encontrada
        }
    }

    private static function methodAllowed($allowedMethod) {
        return Request::method() === $allowedMethod;
    }

    private static function applyMiddlewares(array $middlewares, Request $request, Response $response): bool {
        foreach ($middlewares as $middleware) {
            if (is_array($middleware) && count($middleware) >= 2 && is_string($middleware[0]) && method_exists($middleware[0], $middleware[1])) {
                $middlewareClass = new $middleware[0]();
                $middlewareMethod = $middleware[1];
                if (isset($middleware[2]) && !$middlewareClass->$middlewareMethod($request, $response, $middleware[2])) {
                    return false;
                } elseif (!isset($middleware[2]) && !$middlewareClass->$middlewareMethod($request, $response)) {
                    return false;
                }
            } else {
                logError("Invalid middleware format");
                self::internalServerErrorResponse();
                return false;
            }
        }
        return true;
    }

    private static function methodNotAllowedResponse($url) {
        logError('The "' . Request::method() . '" method is not allowed in "' . $url . '"');
        Response::json([
            'error' => true,
            'message' => "The (" . Request::method() . ") method is not allowed in ($url)"
        ], 405);
    }

    private static function routeNotFoundResponse($url) {
        logError("Route not found: $url");
        Response::json([
            'error'   => true,
            'message' => 'Sorry, route not found.'
        ], 405);
    }

    private static function handleRouteNotFound($controller, $action) {
        logError("Class or method not found: $controller@$action");
        Response::json([
            'error'   => true,
            'message' => "Class or method not found for this route."
        ], 500);
    }

    private static function internalServerErrorResponse() {
        Response::json([
            'error' => true,
            'message' => 'Internal Server Error.'
        ], 500);
    }
}