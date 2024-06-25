<?php

namespace App\Validators;

use App\Http\Response;
use App\Validators\Validator;
use Exception;

class UserValidators {
    public static function create(array $data) {
        try {
            Validator::validate([
                'nome'        => $data['name'] ?? '',
                'sobre nome'  => $data['lastName'] ?? '',
                'e-mail'      => $data['email'] ?? '',
                'senha'       => $data['password'] ?? ''
            ]);
        }
        catch (Exception $e) {
            Response::json([
                'error'     => true,
                'message'   => $e->getMessage()
            ], 400);
        }
    }

    public static function update(array $data) {
        try {
            Validator::validate([
                'nome'        => $data['name'] ?? '',
                'sobre nome'  => $data['lastName'] ?? '',
                'e-mail'      => $data['email'] ?? '',
            ]);
        }
        catch (Exception $e) {
            Response::json([
                'error'     => true,
                'message'   => $e->getMessage()
            ], 400);
        }
    }
}