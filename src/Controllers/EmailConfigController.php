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

            $configExists = EmailConfigDAO::fetch();
            $emailConfig = new EmailConfig($data);

            if(!empty($configExists)) {
                $emailConfig->update();
            } else {
                $emailConfig->save();
            }
            
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

    public function fetch(Request $request, Response $response) {
        try {
            $emailConfig = new EmailConfig();
            $result = $emailConfig->fetch();
            
            unset($result['password']);

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
