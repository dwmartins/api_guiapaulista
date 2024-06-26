<?php

namespace App\Models;

use App\Class\User;
use App\Models\Database;
use App\Utils\Logger;
use Exception;
use PDO;
use PDOException;

class UserDAO extends Database {

    public static function save(User $user) {
        try {
            $pdo = self::getConnection();

            $user->setCreatedAt(date('Y-m-d H:i:s'));
            $user->setUpdatedAt(date('Y-m-d H:i:s'));

            $columns = [];
            $placeholders = [];
            $values = [];

            foreach ($user as $key => $value) {
                if(!property_exists($user, $key)) {
                    continue;
                }

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
            throw new Exception("Falha ao criar o usuário");
        }
    }

    public static function update(User $user) {
        try {
            $pdo = self::getConnection();

            $user->setUpdatedAt(date('Y-m-d H:i:s'));

            $columns = [];
            $values = [];

            $ignoredColumns = ["id", "photo", "password", "token", "createdAt"];

            foreach ($user as $key => $value) {
                if(!property_exists($user, $key)) {
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
            throw new Exception("Falha ao Atualizar o usuário");
        }
    }

    public static function delete($id) {
        try {
            $pdo = self::getConnection();

            $stmt = $pdo->prepare(
                "DELETE FROM users
                WHERE id = ?"
            );

            $stmt->execute([$id]);

            return $stmt->rowCount();

        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Falha ao deletar o usuário");
        }
    }

    public static function fetchAll($active) {
        try {
            $pdo = self::getConnection();
            $parameters = [];
    
            $sql =  "SELECT id, name, lastName, email, active, role, photo, createdAt, updatedAt 
                        FROM users";
    
            if(!empty($active)) {
                $sql .= " WHERE active = ?";
                $parameters[] = $active;
            }
            
            $stmt = $pdo->prepare($sql);
    
            $stmt->execute($parameters);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Falha ao buscar os usuários");
        }
    }
    
    public static function fetchByEmail($email) {
        try {
            $pdo = self::getConnection();

            $stmt = $pdo->prepare(
                "SELECT *
                FROM users
                WHERE email = ?"
            );

            $stmt->execute([$email]);

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public static function fetchById($id) {
        try {
            $pdo = self::getConnection();

            $stmt = $pdo->prepare(
                "SELECT *
                FROM users
                WHERE active = 'Y' 
                AND id = ?"
            );

            $stmt->execute([$id]);

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Falha ao buscar o usuário por id.");
        }
    }
}