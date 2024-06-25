<?php

namespace App\Validators;

use App\Http\Response;
use App\Validators\Validator;
use Exception;

class ProductValidators {
    public static function create(array $data) {
        try {
            $fields = [
                "Nome" => $data['name'],
                "tipo de produto" => $data['type'],
                "Código de referencia" => $data['referenceCode']
            ];

            $textValid = TextValidator::validFullText($fields);

            if(isset($textValid['textInvalid']) && $textValid['textInvalid'] == true) {
                Response::json([
                    'error'     => true,
                    'message'   => $textValid['message']
                ], 400);

                return;
            }

            Validator::validate([
                'nome'             => $data['name'] ?? '',
                'tipo de produto'  => $data['type'] ?? '',
            ]);

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
                "Nome" => $data['name'],
                "tipo de produto" => $data['type'],
                "Código de referencia" => $data['referenceCode']
            ];

            $textValid = TextValidator::validFullText($fields);

            if(isset($textValid['textInvalid']) && $textValid['textInvalid'] == true) {
                Response::json([
                    'error'     => true,
                    'message'   => $textValid['message']
                ], 400);

                return;
            }

            Validator::validate([
                'nome'             => $data['name'] ?? '',
                'tipo de produto'  => $data['type'] ?? '',
            ]);

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
}