<?php

namespace App\Middleware;

use App\Class\User;
use App\Class\UserPermissions;
use App\Http\JWTManager;
use App\Http\Request;
use App\Http\Response;
use App\Models\UserDAO;
use App\Models\UserPermissionsDAO;

class UserMiddleware {
    public static function isAuth(Request $request, Response $response) {
        $allowedRoles = ["admin", "super"];
        $headers = $request->authorization();

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
                    'invalidPermission' => "Você não tem permissão para executar essa ação.",
                    'redirect'          => true
                ], 403);
            }

            $request->setAttribute('userRequest', $user);
            return true;

        } else {
            return $response::json([
                'error'        => true,
                'invalidToken' => "Realize o login novamente para continuar."
            ], 401);
        }
    }

    public function permissionsToUsers(Request $request, Response $response, $param) {
        $user = $request->getAttribute('userRequest');

        if($user->getRole() === 'super') {
            return true;
        }

        $user = UserPermissionsDAO::getPermissions($user);

        if(!empty($user)) {
            $user = new UserPermissions($user);
            $toUser = $user->getUsers();

            if($toUser['permission']) {
                return true;
            }

            return $response::json([
                'error'             => true,
                'invalidPermission' => "Você não tem permissão para executar essa ação."
            ], 403);
            
        } else {
            return $response::json([
                'error'             => true,
                'invalidPermission' => "Você não tem permissão para executar essa ação."
            ], 403);
        }
    }
}