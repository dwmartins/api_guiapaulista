<?php

namespace App\Controllers;

use App\Class\User;
use App\Class\UserPermissions;
use App\Http\JWTManager;
use App\Http\Request;
use App\Http\Response;
use App\Models\UserDAO;
use App\Validators\UserValidators;
use Exception;

class UserController {
    public function fetch(Request $request, Response $response) {
        try {
            $filters = $request->queryParams();

            $users = UserDAO::fetchAll($filters);

            return $response->json($users);

        } catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Falha ao buscar os usuários."
            ], 500);
        }
    }

    public function recoverPassword(Request $request, Response $response) {
        try {
            $data = $request->body();

            if(!UserValidators::recoverPassword($data)) {
                return;
            }

            $userExists = UserDAO::fetchByEmail($data['email']);

            if(!empty($userExists)) {
                $user = new User($userExists);

                if($user->getActive() === "Y") {
                    $emailInfo = [
                        'to' => $user->getEmail(),
                        'subject' => 'Recuperação de senha'
                    ];

                    $sendEmail = new SendEmailController($emailInfo);
                    $sendEmail->recoverPassword($user->getName());

                    return $response->json([
                        'success' => true,
                        'message' => "Código de verificação encaminhado no e-mail."
                    ]);
                }
            }

            return $response->json([
                'error'   => true,
                'message'    => "E-mail inválido."
            ], 401); 
        } catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Falha ao enviar o código de recuperação de senha."
            ], 500);
        }
    }

    public function create(Request $request, Response $response) {
        try {
            $body = $request->body();
            $userRequest = $request->getAttribute('userRequest');

            if(!UserValidators::create($body)) {
                return;
            }

            if(UserDAO::fetchByEmail($body['email']) != false) {
                return $response->json([
                    'error'     => true,
                    'message'   => "Este e-mail já está em uso."
                ], 409);
            }

            $user = new User($body);
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));
            $user->setToken(JWTManager::newCrypto());
            $user->setCreatedBy($userRequest->getId());
            $user->save();

            $this->setPermissions($user);

            $response->json([
                'success' => true,
                'message' => "Usuário criado com sucesso."
            ], 201);

        } catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Falha ao criar o usuário."
            ], 500);
        }
    }

    public function setPermissions(User $user) {
        // Permissões para ações de usuários;  
        $users = [
            'permission' => false,
            'label' => 'Usuários'
        ];

        // Permissões para conteúdos do site;
        $content = [
            'permission' => false,
            'label' => 'Conteúdos do site.'
        ];

        //Permissões para ações de configurações;
        $siteInfo = [
            'permission' => true,
            'label' => 'Informações do site'
        ];

        //Permissões para configurações de e-mail;
        $emailSending = [
            'permission' => false,
            'label' => 'Configurações de e-mail'
        ];

        $userPermissions = [
            "user_id" => $user->getId(),
            "users" => $users,
            "content" => $content,
            "siteInfo" => $siteInfo,
            'emailSending' => $emailSending
        ];

        $permissions = new UserPermissions($userPermissions);
        $permissions->save();
    }

    public function update(Request $request, Response $response) {
        try {
            $body = $request->body();

            if(!UserValidators::update($body)) {
                return;
            }

            $emailExists = UserDAO::fetchByEmail($body['email']);

            if($emailExists != false && $emailExists['id'] != $body['id']) {
                return $response->json([
                    'error'     => true,
                    'message'   => "Este e-mail já está em uso."
                ], 409);
            }

            $user = new User($body);
            $user->update();

            return $response->json([
                'success'   => true,
                'message'   => "Usuário atualizado com sucesso."
            ], 201);

        } catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
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

            return $response->json([
                'success'   => true,
                'message'   => "Usuário deletado com sucesso."
            ], 200);

        } catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Falha ao deletar o usuário."
            ], 500);
        }
    }
}
