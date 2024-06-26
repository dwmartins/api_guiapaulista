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
        $headers = getallheaders();
    
        if (!isset($headers['Authorization'])) {
            return false;
        }
    
        $bearer = explode(" ", $headers['Authorization']);
        if (count($bearer) !== 2 || $bearer[0] !== 'Bearer') {
            return false;
        }
    
        $token = $bearer[1];
    
        if (!isset($headers['userId']) || empty($token)) {
            return false;
        }
    
        return [
            "userId" => $headers['userId'],
            "token"  => $token
        ];
    }
}