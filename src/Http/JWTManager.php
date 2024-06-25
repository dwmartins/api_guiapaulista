<?php

namespace App\Http;

use App\class\User;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;

class JWTManager {
    public static function generate(User $user) {
        $payload = array(
            "user_id" => $user->getId(),
            "email"   => $user->getEmail(),
            "role"    => $user->getRole(),
            "exp"     => time() + 3600
        );
        
        return JWT::encode($payload, $user->getToken(), 'HS256');
    }

    public static function validate($token, User $user) {
        try {
            $decoded = JWT::decode($token, new Key($user->getToken(), 'HS256'));
            return $decoded;
        } catch (Exception $e) {

            if($e->getMessage() === "Expired token") {
                $expiredObject = new stdClass();
                $expiredObject->expired = true;
                return $expiredObject;
            }

            return false;
        }
    }

    public static function newCrypto() {
        $randomBytes = random_bytes(32);
        $hexString = bin2hex($randomBytes);

        return $hexString;
    }
}