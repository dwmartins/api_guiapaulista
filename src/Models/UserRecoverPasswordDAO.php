<?php

namespace App\Models;

use App\Class\UserRecoverPassword;
use App\Models\Database;
use PDOException;
use Exception;

class UserRecoverPasswordDAO extends Database{
    public static function save(UserRecoverPassword $userRecoverPassword) {
        try {
            $pdo = self::getConnection();
            $userRecoverPassword->setCreatedAt(date('Y-m-d H:i:s'));
            $recoverArray = $userRecoverPassword->toArray();

            $columns = [];
            $placeholders = [];
            $values = [];

            foreach ($recoverArray as $key => $value) {
                $columns[] = $key;
                $placeholders[] = "?";
                $values[] = $value;
            }

            $columns = implode(", ", $columns);
            $placeholders = implode(", ", $placeholders);

            $stmt = $pdo->prepare(
                "INSERT INTO user_recover_password ($columns)
                VALUES ($placeholders)"
            );
            
            $stmt->execute($values);

            return $pdo->lastInsertId();

        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to save password recovery code");
        }
    }

    public static function fetch($id) {
        // Implementation of the read method
    }
}
