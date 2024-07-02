<?php

namespace App\Validators;

class TextValidator {
    public static function fullText(string $field) {
        $fullText = "/^[a-zA-Z0-9\s\-\_\.\,\!\?\@\#\$\%\&\*\(\)]+$/";

        if(!preg_match($fullText, $field)) {
            return false;
        }

        return true;
    }

    public static function simpleText(string $field) {
        $simpleText = "/^[a-zA-Z]+$/";

        if(!preg_match($simpleText, $field)) {
            return false;
        }

        return true;
    }

    public static function email(string $email) {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }

    public static function emailServer(string $server) {
        if (empty($server)) {
            return false;
        }

        if (!filter_var($server, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return false;
        }

        return true;
    }
}