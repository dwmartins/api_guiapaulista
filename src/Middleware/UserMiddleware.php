<?php

namespace App\Middleware;

use App\Class\User;
use App\Http\JWTManager;
use App\Http\Request;
use App\Http\Response;
use App\Models\UserDAO;

class UserMiddleware {
    public static function adminLogged(Request $request, Response $response) {
        $allowedRoles = ["admin", "super"];
        $headers = Request::authorization();

        $token  = $headers['token'] ?? '';
        $userId = $headers['userId'] ?? '';

        if(!$token) {
            return $response::json([
                'error'        => true,
                'invalidToken' => "Realize o login novamente para continuar."
            ], 401);
        }

        $user = UserDAO::fetchById($userId);
        $user = new User($user);

        if(!empty($user) && $user->getActive() === "Y") {
            $decoded = JWTManager::validate($token, $user);

            if(!$decoded) {
                return $response::json([
                    'error'        => true,
                    'invalidToken' => "Realize o login novamente para continuar."
                ], 401);
            }

            if(isset($decoded->expired)) {
                return $response::json([
                    'error'        => true,
                    'expiredToken' => "Sua sessão expirou, realize o login novamente."
                ], 401);
            }

            if(!in_array($user->getRole(), $allowedRoles)) {
                return $response::json([
                    'error'             => true,
                    'invalidPermission' => "Você não tem permissão para executar essa ação."
                ], 403);
            }

            return true;

        } else {
            return $response::json([
                'error'        => true,
                'invalidToken' => "Realize o login novamente para continuar."
            ], 401);
        }
    }

    public static function modLogged(Request $request, Response $response) {
        $allowedRoles = ["admin", "super", "mod"];
        $headers = Request::authorization();

        $token  = $headers['token'] ?? '';
        $userId = $headers['userId'] ?? '';

        if(!$token) {
            return $response::json([
                'error'        => true,
                'invalidToken' => "Realize o login novamente para continuar."
            ], 401);
        }

        $user = UserDAO::fetchById($userId);
        $user = new User($user);

        if(!empty($user) && $user->getActive() === "Y") {
            $decoded = JWTManager::validate($token, $user);

            if(!$decoded) {
                return $response::json([
                    'error'        => true,
                    'invalidToken' => "Realize o login novamente para continuar."
                ], 401);
            }

            if(isset($decoded->expired)) {
                return $response::json([
                    'error'        => true,
                    'expiredToken' => "Sua sessão expirou, realize o login novamente."
                ], 401);
            }

            if(!in_array($user->getRole(), $allowedRoles)) {
                return $response::json([
                    'error'             => true,
                    'invalidPermission' => "Você não tem permissão para executar essa ação."
                ], 403);
            }

            return true;

        } else {
            return $response::json([
                'error'        => true,
                'invalidToken' => "Realize o login novamente para continuar."
            ], 401);
        }
    }
}