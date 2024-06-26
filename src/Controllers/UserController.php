<?php

namespace App\Controllers;

use App\Class\User;
use App\Http\JWTManager;
use App\Http\Request;
use App\Http\Response;
use App\Models\UserDAO;
use App\Validators\UserValidators;
use Exception;

class UserController {
    public function fetch(Request $request, Response $response) {
        // Code to list resources
    }

    public function show(Request $request, Response $response, $id) {
        // Code to show a specific resource
    }

    public function create(Request $request, Response $response) {
        try {
            $body = $request::body();

            UserValidators::create($body);

            if(UserDAO::fetchByEmail($body['email']) != false) {
                return $response::json([
                    'error'     => true,
                    'message'   => "Este e-mail já está em uso."
                ], 409);
            }

            $user = new User($body);
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));
            $user->setToken(JWTManager::newCrypto());
            $user->save();

            $response::json([
                'success' => true,
                'message' => "Usuário criado com sucesso."
            ], 201);

        } catch (Exception $e) {
            return $response::json([
                'error'   => true,
                'message' => "Falha ao criar o usuário."
            ], 500);
        }
    }

    public function update(Request $request, Response $response) {
        // Code to update a specific resource
    }

    public function delete(Request $request, Response $response, $id) {
        // Code to delete a specific resource
    }
}
