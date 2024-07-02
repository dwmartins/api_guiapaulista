<?php

namespace App\Validators;

use App\Http\Response;
use App\Validators\Validator;
use Exception;

class UserValidators {
    public static function create(array $data) {
        try {
            $fields = [
                'Nome'        => $data['name'] ?? '',
                'Sobre nome'  => $data['lastName'] ?? '',
                'E-mail'      => $data['email'] ?? '',
                'Senha'       => $data['password'] ?? ''
            ];

            Validator::validate($fields);
            
            foreach ($fields as $key => $value) {
                if($key === "E-mail") {
                    if(!TextValidator::email($value)) {
                        Response::json([
                            'error'     => true,
                            'message'   => "O e-mail é invalido"
                        ], 400);

                        return false;
                    }

                    continue;
                }

                if($key === "Senha") {
                    continue;
                }

                if(!TextValidator::simpleText($value)) {
                    Response::json([
                        'error'     => true,
                        'message'   => "O campo ($key) contem caracteres inválidos."
                    ], 400);

                    return false;
                }
            }

            return true;
        }
        catch (Exception $e) {
            Response::json([
                'error'     => true,
                'message'   => $e->getMessage()
            ], 400);

            return false;
        }
    }

    public static function update(array $data) {
        try {
            $fields = [
                'Nome'        => $data['name'] ?? '',
                'Sobre nome'  => $data['lastName'] ?? '',
                'E-mail'      => $data['email'] ?? '',
            ];

            Validator::validate($fields);

            foreach ($fields as $key => $value) {
                if($key === "E-mail") {
                    if(!TextValidator::email($value)) {
                        Response::json([
                            'error'     => true,
                            'message'   => "O e-mail é invalido"
                        ], 400);

                        return false;
                    }

                    continue;
                }

                if(!TextValidator::simpleText($value)) {
                    Response::json([
                        'error'     => true,
                        'message'   => "O campo ($key) contem caracteres inválidos."
                    ], 400);

                    return false;
                }
            }

            return true;
        }
        catch (Exception $e) {
            Response::json([
                'error'     => true,
                'message'   => $e->getMessage()
            ], 400);

            return false;
        }
    }

    public static function login(array $data) {
        try {
            $fields = [
                "E-mail" => $data['email'] ?? '',
                "Senha"  => $data['password'] ?? ''
            ];

            Validator::validate($fields);

            if(!TextValidator::email($data['email'])) {
                Response::json([
                    'error'     => true,
                    'message'   => "O e-mail é invalido"
                ], 400);

                return false;
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

    public static function recoverPassword(array $data) {
        try {
            $fields = [
                "E-mail" => $data['email'] ?? ''
            ];

            Validator::validate($fields);

            if(!TextValidator::email($data['email'])) {
                Response::json([
                    'error'     => true,
                    'message'   => "O e-mail é invalido"
                ], 400);

                return false;
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