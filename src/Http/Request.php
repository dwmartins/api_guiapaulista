<?php

namespace App\Http;

class Request {

    private static array $attributes = [];

    public static function method() {
        return $_SERVER["REQUEST_METHOD"];
    }

    public static function body() {
        $method = self::method();

        switch ($method) {
            case 'GET':
                return $_GET;

            case 'POST':
                if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json') {
                    return json_decode(file_get_contents("php://input"), true) ?? [];
                } else {
                    return $_POST;
                }

            case 'PUT':
            case 'DELETE':
                return json_decode(file_get_contents("php://input"), true) ?? [];

            default:
                return [];
        }
    }

    public static function queryParams() {
        return $_GET;
    }

    public static function files() {
        return $_FILES;
    }

    public static function authorization() {
        $headers = getallheaders();
    
        if (!isset($headers['Authorization'])) {
            return false;
        }
    
        $bearer = explode(" ", $headers['Authorization']);
        if (count($bearer) !== 3 || $bearer[0] !== 'Bearer') {
            return false;
        }

        $fieldUser = explode(":", $bearer[1]) ?? null;
        $fieldToken = explode(":", $bearer[2]) ?? null;

        $userId =  $fieldUser[1] ?? null;
        $token = $fieldToken[1] ?? null;

        if(empty($fieldUser) || $fieldUser[0] !== 'userId' || empty($userId)) {
            return false;
        }

        if(empty($fieldToken) || $fieldToken[0] !== 'token' || empty($token)) {
            return false;
        }
    
        return [
            "userId" => $userId,
            "token"  => $token
        ];
    }

    public static function getIp() {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    }

    public static function setAttribute($key, $value) {
        self::$attributes[$key] = $value;
    }

    public static function getAttribute($key) {
        return self::$attributes[$key] ?? null;
    }
}