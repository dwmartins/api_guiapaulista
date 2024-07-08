<?php

namespace App\Middleware;

use App\Class\User;
use App\Class\UserPermissions;
use App\Http\JWTManager;
use App\Http\Request;
use App\Http\Response;
use App\Models\UserDAO;
use App\Models\UserPermissionsDAO;
use Exception;

class UserMiddleware {
    public static function isAuth(Request $request, Response $response) {
        try {
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

            $user = new User();
            $user->fetchById($userId);

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

        } catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Oops, Ocorreu um erro inesperado."
            ], 500);
        }
    }

    public function permissionsToUsers(Request $request, Response $response) {
        try {
            $user = $request->getAttribute('userRequest');

            if($user->getRole() === 'super') {
                return true;
            }

            $permission = new UserPermissions();
            $permission->getPermissions($user);

            if(!empty($permission->getId())) {
                $toUser = $permission->getUsers();

                if($toUser['permission']) {
                    return true;
                }

                return $response::json([
                    'error'             => true,
                    'invalidPermission' => "Você não tem permissão para executar essa ação."
                ], 403);
                
            }

            return $response::json([
                'error'             => true,
                'invalidPermission' => "Você não tem permissão para executar essa ação."
            ], 403);

        } catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Oops, Ocorreu um erro inesperado."
            ], 500);
        }
    }

    public function emailSendingSettings(Request $request, Response $response) {
        try {
            $user = $request->getAttribute('userRequest');

            if($user->getRole() === 'super') {
                return true;
            }

            $userPermission = new UserPermissions();
            $userPermission->getPermissions($user);

            if(!empty($userPermission->getId())) {
                $emailSendConfig = $userPermission->getEmailSending();

                if($emailSendConfig['permission']) {
                    return true;
                }
            }

            return $response::json([
                'error'             => true,
                'invalidPermission' => "Você não tem permissão para executar essa ação."
            ], 403);

        } catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Oops, Ocorreu um erro inesperado."
            ], 500);
        }
    }

    public function siteInfo(Request $request, Response $response) {
        try {
            $user = $request->getAttribute('userRequest');

            if($user->getRole() === 'super') {
                return true;
            }

            $userPermission = new UserPermissions();
            $userPermission->getPermissions($user);

            if(!empty($userPermission->getId())) {
                $siteInfoConfig = $userPermission->getSiteInfo();

                if($siteInfoConfig['permission']) {
                    return true;
                }
            }

            return $response::json([
                'error'             => true,
                'invalidPermission' => "Você não tem permissão para executar essa ação."
            ], 403);

        } catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Oops, Ocorreu um erro inesperado."
            ], 500);
        }
    }
}