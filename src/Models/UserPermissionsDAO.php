<?php

namespace App\Models;

use App\Class\User;
use App\Class\UserPermissions;
use App\Models\Database;
use PDO;
use PDOException;
use Exception;

class UserPermissionsDAO extends Database{
    protected $db;

    public static function save(UserPermissions $permission): int {
        try {
            $pdo = self::getConnection();

            $permission->setCreatedAt(date('Y-m-d H:i:s'));
            $permission->setUpdatedAt(date('Y-m-d H:i:s'));
    
            $permissionArray = $permission->toArray();
    
            $columns = [];
            $placeholders = [];
            $values = [];
    
            foreach ($permissionArray as $key => $value) {
                $columns[] = $key;
                $placeholders[] = "?";
                $values[] = $value;
            }
    
            $columns = implode(", ", $columns);
            $placeholders = implode(", ", $placeholders);
    
            $stmt = $pdo->prepare(
                "INSERT INTO user_permissions ($columns)
                VALUES ($placeholders)"
            );
    
            $stmt->execute($values);
    
            return $pdo->lastInsertId();
        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to save user permissions");
        }
    }

    public static function update(UserPermissions $permission) {
        try {
            $pdo = self::getConnection();

            $permission->setUpdatedAt(date('Y-m-d H:i:s'));
            $permissionArray = $permission->toArray();

            $columns = [];
            $values = [];

            foreach ($permissionArray as $key => $value) {
                if(empty($value)) {
                    continue;
                }

                $columns[] = "$key = ?";
                $values[] = $value;
            }

            $columns = implode(", ", $columns);
            $values[] = $permission->getId();

            $stmt = $pdo->prepare(
                "UPDATE user_permissions 
                SET $columns
                WHERE id = ?"
            );

            $stmt->execute($values);

            return $stmt->rowCount();

        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to update user permissions");
        }
    }

    public static function getPermissions(User $user): array {
        try {
            $pdo = self::getConnection();

            $stmt = $pdo->prepare(
                "SELECT * FROM user_permissions WHERE user_id = ? LIMIT 1"
            );

            $stmt->execute([$user->getId()]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ?: [];
        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to search for user permissions");
        }
    }
}
