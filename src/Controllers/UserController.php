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
use App\Utils\UploadFile;
use App\Validators\FileValidators;
use App\Validators\UserValidators;
use Exception;

class UserController {
    private string $userImagesFolder = "userImages";

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
            $emailConfig->fetch();

            if(!$emailConfig->configActive()) {
                return $response->json([
                    'error'   => true,
                    'message' => "As configurações de e-mail não foram definidas."
                ], 503);
            }

            $request->setAttribute('emailConfig', $emailConfig);

            $user = new User();
            $user->fetchByEmail($data['email']);

            if(!empty($user->getId())) {
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

            if(!empty($recoverPassword->fetchByTokenAndUser()) && $recoverPassword->getExpiration() >= date('Y-m-d H:i:s')) {
                $user = new User();
                $user->fetchById($requestData['userId']);
    
                if(!empty($user->getId()) && $user->getActive() === "Y") {
                    $user->setPassword($requestData['password']);
                    $user->save();
                    
                    $recoverPassword->setUsed("Y");
                    $recoverPassword->save();

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

    public function updatePhoto(Request $request, Response $response) {
        try {
            $file = $request->files();
            $requestData = $request->body();

            if(isset($file['photo']) && !empty($file['photo'])) {
                $photo = $file['photo'];
                $fileData = FileValidators::validImage($photo);

                $user = new User();
                $user->fetchById($requestData['userId']);

                if(isset($fileData['invalid'])) {
                    return $response->json([
                        'error'   => true,
                        'message' => $fileData['invalid']
                    ], 400);
                }

                $fileName = $user->getId() . "_user." . $fileData['mimeType'];
                UploadFile::uploadFile($photo, $this->userImagesFolder, $fileName);
                
                $user->setPhoto($fileName);
                $user->save();

                return $response->json([
                    'success' => true,
                    'message' => "Foto alterada com sucesso."
                ]);
            }

        } catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Falha ao atualizar a foto."
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

            if(!empty(UserDAO::fetchByEmail($body['email']))) {
                return $response->json([
                    'error'     => true,
                    'message'   => "Este e-mail já está em uso."
                ], 409);
            }

            $user = new User($body);
            $user->setToken(JWTManager::newCrypto());
            $user->setPassword($body['password']);
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

            if($emailExists && $emailExists['id'] != $body['id']) {
                return $response->json([
                    'error'     => true,
                    'message'   => "Este e-mail já está em uso."
                ], 409);
            }

            $user = new User();
            $user->fetchById($body['id']);
            $user->update($body);
            $user->save();

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

    /**
     * Updates user role
     * MEthod HTTP: PUT
     * 
     * Expected data in the request body:
     * - userId (int)
     * - role (string)
     * @return Response
     */
    public function updateRole(Request $request, Response $response) {
        try {
            $requestData = $request->body();

            $user = new User();
            $user->fetchById($requestData['userId']);

            $user->setRole($requestData['role']);
            $user->save();

            return $response->json([
                'success'   => true,
                'message'   => "Função do usuário atualizado com sucesso."
            ], 201);

        } catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Falha ao atualizar a função do usuário."
            ], 500);
        }
    }

    public function delete(Request $request, Response $response, $id) {
        try {
            $user = new User();
            $user->fetchById($id[0]);

            if(!empty($user->getId())) {
                $user->delete();

                return $response->json([
                    'success'   => true,
                    'message'   => "Usuário deletado com sucesso."
                ], 200);
            }

            return $response->json([
                'error'   => true,
                'message'   => "Usuário não encontrado."
            ], 404);

        } catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Falha ao deletar o usuário."
            ], 500);
        }
    }
}
