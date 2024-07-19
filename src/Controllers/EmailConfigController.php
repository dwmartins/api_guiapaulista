<?php

namespace App\Controllers;

use App\Class\EmailConfig;
use App\Http\Request;
use App\Http\Response;
use App\Models\EmailConfigDAO;
use App\Validators\EmailConfigValidator;
use Exception;

class EmailConfigController {
    public function create(Request $request, Response $response) {
        try {
            $data = $request->body();

            if(!EmailConfigValidator::create($data)) {
                return false;
            }

            $emailConfig = new EmailConfig($data);
            $emailConfig->fetch();

            if(!empty($emailConfig->getId())) {
                $emailConfig->update($data);
            }

            $emailConfig->save();
            
            $response->json([
                'success' => true,
                'message' => "Configurações salvas com sucesso."
            ], 201);

        }  catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Falha ao salvar configurações de e-mail."
            ], 500);
        }
    }

    public function updateStatus(Request $request, Response $response) {
        $data = $request->body();
        $active = $data['activated'] ? "Y" : "N";
        $msgStatus = $data['activated'] ? "ativada" : "desativada";

        try {
            $emailConfig = new EmailConfig($data);
            $emailConfig->fetch();
            $emailConfig->setActivated($active);

            $emailConfig->save();


            $response->json([
                'success' => true,
                'message' => "Configurações de e-mail $msgStatus com sucesso."
            ], 201);
        } catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Falha ao $msgStatus as configurações de e-mail."
            ], 500);
        }
    }

    public function fetch(Request $request, Response $response) {
        try {
            $emailConfig = new EmailConfig();
            $result = $emailConfig->fetch();

            if(!empty($result)) {
                unset($result['password']);
                $result['activated'] = $result['activated'] === "Y" ? true : false;
            }

            $response->json($result);

        }  catch (Exception $e) {
            logError($e->getMessage());
            return $response->json([
                'error'   => true,
                'message' => "Falha ao buscar as configurações de e-mail."
            ], 500);
        }
    }
}
