<?php

namespace App\Models;

use App\Class\UserAccess;
use App\Models\Database;
use Exception;
use PDO;
use PDOException;

class UserAccessDAO extends Database {
    public static function save(UserAccess $userAccess): int {
        try {
            $pdo = self::getConnection();
            $userAccess->setCreatedAt(date('Y-m-d H:i:s'));

            $accessArray = $userAccess->toArray();

            $columns = [];
            $placeholders = [];
            $values = [];

            foreach ($accessArray as $key => $value) {
                $columns[] = $key;
                $placeholders[] = "?";
                $values[] = $value;
            }

            $columns = implode(", ", $columns);
            $placeholders = implode(", ", $placeholders);
            
            $stmt = $pdo->prepare(
                "INSERT INTO user_access ($columns)
                VALUES ($placeholders)"
            );

            $stmt->execute($values);

            return $pdo->lastInsertId();

        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to save user access");
        }
    }
}