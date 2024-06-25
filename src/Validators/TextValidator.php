<?php

namespace App\Validators;

class TextValidator {

    private static $fullText = "/^[a-zA-Z0-9\s\-\_\.\,\!\?\@\#\$\%\&\*\(\)]+$/";

    public static function validFullText(array $fields) {

        foreach ($fields as $key => $value) {

            if(!empty($key) && !preg_match(self::$fullText, $value)) {
                return [
                    "textInvalid" => true,
                    "message" => "O campo ($key) contém caracteres inválidos."
                ];
            }
        }
        
        return true;
    }
}