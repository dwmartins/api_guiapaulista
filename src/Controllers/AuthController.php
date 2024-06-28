<?php

namespace App\Controllers;

use App\Class\User;
use App\Class\UserAccess;
use App\Http\JWTManager;
use App\Http\Request;
use App\Http\Response;
use App\Models\UserDAO;
use App\Validators\UserValidators;
use Exception;

class AuthController {
    public function login(Request $request, Response $response) {
        try {
            $data = $request->body();

            if(!UserValidators::login($data)) {
                return;
            }

            $userExists = UserDAO::fetchByEmail($data['email']);

            if(!empty($userExists)) {
                $user = new User($userExists);

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
}
