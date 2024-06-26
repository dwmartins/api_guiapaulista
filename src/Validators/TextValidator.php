<?php

namespace App\Validators;

class TextValidator {
    public static function fullText(string $field) {
        $fullText = "/^[a-zA-Z0-9\s\-\_\.\,\!\?\@\#\$\%\&\*\(\)]+$/";

        if(!empty($key) && !preg_match($fullText, $field)) {
            return false;
        }

        return true;
    }

    public static function simpleText(string $field) {
        $simpleText = "/^[a-zA-Z]+$/";

        if(!empty($field) && !preg_match($simpleText, $field)) {
            return false;
        }

        return true;
    }

    public static function email(string $email) {
        if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }
}