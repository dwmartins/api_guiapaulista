<?php

namespace App\Controllers;

use App\Class\User;
use App\Class\UserAccess;
use App\Class\UserPermissions;
use App\Http\JWTManager;
use App\Http\Request;
use App\Http\Response;
use App\Models\UserDAO;
use App\Models\UserPermissionsDAO;
use App\Validators\UserValidators;
use Exception;

class AuthController {
    public function login(Request $request, Response $response) {
        try {
            $data = $request->body();

            if(!UserValidators::login($data)) {
                return;
            }

            $user = new User();
            $user->fetchByEmail($data['email']);

            if(!empty($user->getId())) {
                if($user->getActive() === "Y") {
                    if(password_verify($data['password'], $user->getPassword())) {
                        $userData = array(
                            "id"        => $user->getId(),
                            "name"      => $user->getName(),
                            "lastName"  => $user->getLastName(),
                            "email"     => $user->getEmail(),
                            "role"      => $user->getRole(),
                            "photo"     => $user->getPhoto(),
                            "token"     => JWTManager::generate($user),
                            "createdAt" => $user->getCreatedAt(),
                            "updatedAt" => $user->getUpdatedAt()
                        );

                        $userPermissions = new UserPermissions();
                        $userPermissions->getPermissions($user);

                        $userData['permissions'] = [
                            "users"        => $userPermissions->getUsers(),
                            "content"      => $userPermissions->getContent(),
                            "siteInfo"     => $userPermissions->getSiteInfo(),
                            "emailSending" => $userPermissions->getEmailSending()
                        ];

                        $userAccess = new UserAccess([
                            'user_id' => $user->getId(),
                            'ip'      => $request->getIp()
                        ]);
                        $userAccess->save();

                        return $response->json($userData, 200);
                    }
                }
            }

            return $response->json([
                'error'   => true,
                'message'    => "Usuário ou senha inválidos."
            ], 401); 
            
        } catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Falha ao realizar o login"
            ], 500);
        }
    }

    public function auth(Request $request, Response $response) {
        try {
            $headers = $request->authorization();

            if($headers) {
                $user = UserDAO::fetchById($headers['userId']);
                $user = new User($user);

                if(!empty($user)) {
                    $tokenDecode = JWTManager::validate($headers['token'], $user);
                    
                    if(!empty($tokenDecode) && !isset($tokenDecode->expired)) {
                        $permissions = UserPermissionsDAO::getPermissions($user);
                        
                        return $response::json([
                            'success' => true,
                            'role'    => $user->getRole(),
                            'permissions' => $permissions
                        ]);

                    } else if(isset($tokenDecode->expired)) {
                        return $response::json([
                            'error'        => true,
                            'expiredToken' => "Sua sessão expirou, realize o login novamente."
                        ], 401);
                    }
                }
            }

            return $response::json([
                'error'        => true,
                'invalidToken' => "Realize o login para acessar esta área."
            ], 401);

        } catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Falha ao validar o usuário logado."
            ], 500);
        }
    }
}
