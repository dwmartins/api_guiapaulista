<?php

namespace App\Http;

class Request {
    public static function method() {
        return $_SERVER["REQUEST_METHOD"];
    }

    public static function body() {
        $method = self::method();

        switch ($method) {
            case 'GET':
                return $_GET;

            case 'POST':
                if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
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

    public static function files() {
        return $_FILES;
    }

    public static function authorization() {
        $authorization = getallheaders();
        $bearer = explode(" ", $authorization['Authorization']);
        $token = $bearer[1];

        if(!isset($authorization['userId']) || empty($token)) {
            return false;
        }
        
        return [
            "userId" => $authorization['userId'],
            "token"  => $token
        ];
    }
}