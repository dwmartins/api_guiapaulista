<?php

namespace App\Controllers;

use App\Class\EmailConfig;
use App\Class\User;
use App\Class\UserPermissions;
use App\Class\UserRecoverPassword;
use App\Http\JWTManager;
use App\Http\Request;
use App\Http\Response;
use App\Models\UserDAO;
use App\Validators\TextValidator;
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

            $emailConfig = new EmailConfig();

            if(!$emailConfig->configActive()) {
                return $response->json([
                    'error'   => true,
                    'message' => "As configurações de e-mail não foram definidas."
                ], 503);
            }

            $request->setAttribute('emailConfig', $emailConfig);

            $userExists = UserDAO::fetchByEmail($data['email']);

            if(!empty($userExists)) {
                $user = new User($userExists);

                if($user->getActive() === "Y") {
                    $emailInfo = [
                        'to' => $user->getEmail(),
                        'subject' => 'Recuperação de senha'
                    ];

                    $token = JWTManager::newCrypto();
                    
                    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                    $domain = $_SERVER['HTTP_HOST'];
                    $resetLink = $protocol . "://" . $domain . "/usuario/recuperacao-senha?userId=". $user->getId() ."&token=" . $token;

                    // Envia o link de recuperação no e-mail;
                    $sendEmail = new SendEmailController($emailInfo);
                    $sendEmail->recoverPassword($user->getName(), $resetLink);

                    // Salva os dados do link no banco;
                    $userRecover = new UserRecoverPassword();
                    $userRecover->setToken($token);
                    $userRecover->setUserId($user->getId());
                    $userRecover->setUsed("N");
                    $userRecover->setExpiration(date('Y-m-d H:i:s', strtotime('+1 hour')));
                    $userRecover->save();

                    return $response->json([
                        'success' => true,
                        'message' => "As instruções para redefinição de senha foi enviado para o seu e-mail."
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
                'message' => "Falha ao enviar o código de recuperação."
            ], 500);
        }
    }

    public function validateRecoveryToken(Request $request, Response $response) {
        try {
            $requestData = $request->body();

            $data = [
                'user_id' => $requestData['userId'] ?? 0,
                'token'  => $requestData['token'] ?? ''
            ];

            $recoverPassword = new UserRecoverPassword($data);
            $tokenExists = $recoverPassword->fetchByTokenAndUser();

            if(!empty($tokenExists) && $recoverPassword->getExpiration() >= date('Y-m-d H:i:s')) {

                $user = new User();
                $user->fetchById($data['user_id']);

                return $response->json([
                    'success'    => true,
                    'tokenValid' => true,
                    'userData'   => [
                        'id'       => $user->getId(),
                        'name'     => $user->getName(),
                        'lastName' => $user->getLastName()
                    ]
                ]);
            }

            return $response->json([
                'error'   => true,
                'message'    => "O link de recuperação não é valido ou já expirou."
            ], 401); 

        } catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Falha ao validar o código de recuperação."
            ], 500);
        }
    }

    /**
    * Updates the user's password using the recovery token.
    * Method HTTP: PUT
    * Expected data in the request body:
    * - password (string): The user's new password. (required)
    * - token (string): The recovery token sent to the user's email. (required)
    * - userId (int): The ID of the user who wants to reset the password. (required)
    * @return Response
    */
    public function updatePasswordByRecovery(Request $request, Response $response) {
        try {
            $requestData = $request->body();

            if(empty($requestData['password'])) {
                return $response->json([
                    'error'   => true,
                    'message' => "O campo (Senha) é obrigatório"
                ], 400);
            }

            $data = [
                'user_id' => $requestData['userId'] ?? 0,
                'token'  => $requestData['token'] ?? ''
            ];

            $recoverPassword = new UserRecoverPassword($data);
            $tokenExists = $recoverPassword->fetchByTokenAndUser();

            if(!empty($tokenExists) && $recoverPassword->getExpiration() >= date('Y-m-d H:i:s')) {
                $user = new User();
                $user->fetchById($requestData['userId']);
    
                if(!empty($user) && $user->getActive() === "Y") {
                    $user->setPassword($requestData['password']);
                    $user->updatePassword();
                    
                    $recoverPassword->setUsed("Y");
                    $recoverPassword->update();

                    return $response->json([
                        'success'   => true,
                        'message'   => "Senha atualizada com sucesso."
                    ]);
                }
            }

            return $response->json([
                'error'   => true,
                'message'    => "O link de recuperação não é valido ou já expirou."
            ], 401); 

        } catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Falha ao atualizar sua senha"
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
