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
        try {
            $filters = $request::queryParams();

            $users = UserDAO::fetchAll($filters);

            return $response::json($users);

        } catch (Exception $e) {
            logError($e->getMessage());
            return $response::json([
                'error'   => true,
                'message' => "Falha ao buscar os usuários."
            ], 500);
        }
    }

    public function show(Request $request, Response $response, $id) {
        // Code to show a specific resource
    }

    public function create(Request $request, Response $response) {
        try {
            $body = $request::body();
            $userRequest = $request::getAttribute('userRequest');

            if(!UserValidators::create($body)) {
                return;
            }

            if(UserDAO::fetchByEmail($body['email']) != false) {
                return $response::json([
                    'error'     => true,
                    'message'   => "Este e-mail já está em uso."
                ], 409);
            }

            $user = new User($body);
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));
            $user->setToken(JWTManager::newCrypto());
            $user->setCreatedBy($userRequest->getId());
            $user->save();

            $response::json([
                'success' => true,
                'message' => "Usuário criado com sucesso."
            ], 201);

        } catch (Exception $e) {
            logError($e->getMessage());
            return $response::json([
                'error'   => true,
                'message' => "Falha ao criar o usuário."
            ], 500);
        }
    }

    public function update(Request $request, Response $response) {
        try {
            $body = $request::body();

            if(!UserValidators::update($body)) {
                return;
            }

            $emailExists = UserDAO::fetchByEmail($body['email']);

            if($emailExists != false && $emailExists['id'] != $body['id']) {
                return $response::json([
                    'error'     => true,
                    'message'   => "Este e-mail já está em uso."
                ], 409);
            }

            $user = new User($body);
            $user->update();

            return $response::json([
                'success'   => true,
                'message'   => "Usuário atualizado com sucesso."
            ], 201);

        } catch (Exception $e) {
            logError($e->getMessage());
            return $response::json([
                'error'   => true,
                'message' => "Falha ao atualizar o usuário."
            ], 500);
        }
    }

    public function delete(Request $request, Response $response, $id) {
        try {
            $user = UserDAO::fetchById($id[0]);
            $user = new User($user);
            $user->delete();

            return $response::json([
                'success'   => true,
                'message'   => "Usuário deletado com sucesso."
            ], 200);

        } catch (Exception $e) {
            logError($e->getMessage());
            return $response::json([
                'error'   => true,
                'message' => "Falha ao deletar o usuário."
            ], 500);
        }
    }
}
