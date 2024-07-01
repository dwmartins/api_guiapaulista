<?php

namespace App\Validators;

use App\Http\Response;
use App\Validators\Validator;
use Exception;

class EmailConfigValidator {
    public static function create(array $data) {
        try {
            $fields = [
                'Servidor' => $data['server'] ?? '',
                'Endereço de e-mail' => $data['emailAddress'] ?? '',
                'Nome de usuário' => $data['username'] ?? ''
            ];

            Validator::validate($fields);

            foreach ($fields as $key => $value) {
                if($key === "Servidor") {
                    if(!TextValidator::emailServer($value)) {
                        Response::json([
                            'error'     => true,
                            'message'   => "O campo ($key) não é valido"
                        ], 400);

                        return false;
                    }

                    continue;
                }

                if(!TextValidator::email($value)) {
                    Response::json([
                        'error'     => true,
                        'message'   => "O campo ($key) não é valido"
                    ], 400);

                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            Response::json([
                'error'     => true,
                'message'   => $e->getMessage()
            ], 400);

            return false;
        }
    }
}