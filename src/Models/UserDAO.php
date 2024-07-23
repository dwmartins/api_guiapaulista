<?php

namespace App\Models;

use App\Class\User;
use App\Models\Database;
use App\Utils\Logger;
use Exception;
use PDO;
use PDOException;

class UserDAO extends Database {

    public static function save(User $user): int {
        try {
            $pdo = self::getConnection();

            $user->setCreatedAt(date('Y-m-d H:i:s'));
            $user->setUpdatedAt(date('Y-m-d H:i:s'));

            $userArray = $user->toArray();

            $columns = [];
            $placeholders = [];
            $values = [];

            foreach ($userArray as $key => $value) {
                $columns[] = $key;
                $placeholders[] = "?";
                $values[] = $value;
            }

            $columns = implode(", ", $columns);
            $placeholders = implode(", ", $placeholders);

            $stmt = $pdo->prepare(
                "INSERT INTO users ($columns)
                VALUES ($placeholders)"
            );

            $stmt->execute($values);

            return $pdo->lastInsertId();
        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to create user");
        }
    }

    public static function update(User $user): int {
        try {
            $pdo = self::getConnection();

            $user->setUpdatedAt(date('Y-m-d H:i:s'));
            $userArray = $user->toArray();

            $columns = [];
            $values = [];

            $ignoredColumns = ["token"];

            foreach ($userArray as $key => $value) {
                if(empty($value)) {
                    continue;
                }

                if (in_array($key, $ignoredColumns)) {
                    continue;
                }

                $columns[] = "$key = ?";
                $values[] = $value;
            }

            $columns = implode(", ", $columns);
            $values[] = $user->getId();

            $stmt = $pdo->prepare(
                "UPDATE users 
                SET $columns
                WHERE id = ?"
            );

            $stmt->execute($values);

            return $stmt->rowCount();

        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to update user");
        }
    }

    public static function updatePassword(User $user): int {
        try {
            $pdo = self::getConnection();

            $user->setUpdatedAt(date('Y-m-d H:i:s'));

            $values = [
                $user->getPassword(),
                $user->getUpdatedAt(),
                $user->getId()
            ];

            $stmt = $pdo->prepare(
                "UPDATE users 
                SET password = ?, updatedAt = ? 
                WHERE id = ?"
            );

            $stmt->execute($values);
            return $stmt->rowCount();

        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to update password");
        }
    }

    public static function updateRole(User $user) {
        try {
            $pdo = self::getConnection();

            $user->setUpdatedAt(date('Y-m-d H:i:s'));

            $values = [
                $user->getRole(),
                $user->getUpdatedAt(),
                $user->getId()
            ];
            
            $stmt = $pdo->prepare(
                "UPDATE users 
                SET role = ?, updatedAt = ? 
                WHERE id = ?"
            );

            $stmt->execute($values);
            return $stmt->rowCount();

        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to update user role");
        }
    }

    public static function updatePhoto(User $user): int {
        try {
            $pdo = self::getConnection();

            $user->setUpdatedAt(date('Y-m-d H:i:s'));

            $values = [
                $user->getPhoto(),
                $user->getUpdatedAt(),
                $user->getId()
            ];

            $stmt = $pdo->prepare(
                "UPDATE users 
                SET photo = ?, updatedAt = ? 
                WHERE id = ?"
            );

            $stmt->execute($values);
            return $stmt->rowCount();
            
        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to update photo");
        }
    }
 
    public static function delete(User $user): int {
        try {
            $pdo = self::getConnection();

            $stmt = $pdo->prepare(
                "DELETE FROM users
                WHERE id = ?"
            );

            $stmt->execute([$user->getId()]);

            return $stmt->rowCount();

        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to delete user");
        }
    }

    public static function deleteMultiples($ids): int {
        try {
            $pdo = self::getConnection();
            $in = str_repeat('?,', count($ids) - 1) . '?';

            $stmt = $pdo->prepare(
                "DELETE FROM users
                WHERE id IN ($in)"
            );

            $stmt->execute($ids);
            return $stmt->rowCount();

        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to exclude multiple users");
        }
    }

    public static function fetchAll(array $filters): array {
        try {
            $pdo = self::getConnection();
            $user = new User();
            $userArray = $user->toArray();

            $conditions[] = "u.role != ?";
            $parameters[] = 'support';

            $columns = [];
            $ignoredColumns = ["token", "password"];

            foreach ($userArray as $key => $value) {
                if(in_array($key, $ignoredColumns)) {
                    continue;
                }

                $columns[] = "u." . $key;
            }

            $columns[] = "creator.name AS createdByName";
            $columns = implode(", ", $columns);
    
            $sql =  "SELECT $columns FROM users u LEFT JOIN users creator ON u.createdBy = creator.id";
    
            if (!empty($filters['active'])) {
                $conditions[] = "u.active = ?";
                $parameters[] = $filters['active'];
            }

            if (!empty($filters['role'])) {
                if ($filters['role'] !== 'support') {
                    $conditions[] = "u.role = ?";
                    $parameters[] = $filters['role'];
                }
            }

            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }
            
            $stmt = $pdo->prepare($sql);
    
            $stmt->execute($parameters);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result ?: [];
    
        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when running query to search for users");
        }
    }
    
    public static function fetchByEmail(string $email): array {
        try {
            $pdo = self::getConnection();

            $stmt = $pdo->prepare(
                " SELECT u.*, creator.name AS createdByName
                FROM users u
                LEFT JOIN users creator ON u.createdBy = creator.id
                WHERE u.email = ?"
            );

            $stmt->execute([$email]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ?: [];

        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public static function fetchById(int $id): array {
        try {
            $pdo = self::getConnection();

            $stmt = $pdo->prepare(
                "SELECT u.*, creator.name AS createdByName
                    FROM users u
                    LEFT JOIN users creator ON u.createdBy = creator.id
                    WHERE u.id = ?"
            );

            $stmt->execute([$id]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ?: [];

        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to search for user by id");
        }
    }
}