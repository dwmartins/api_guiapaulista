<?php

namespace App\Models;

use App\Class\EmailConfig;
use App\Models\Database;
use Exception;
use PDO;
use PDOException;

class EmailConfigDAO extends Database{
    public static function save(EmailConfig $emailConfig): int {
        try {
            $pdo = self::getConnection();

            $emailConfig->setCreatedAt(date('Y-m-d H:i:s'));
            $emailConfig->setUpdatedAt(date('Y-m-d H:i:s'));

            $emailConfigArray = $emailConfig->toArray();

            $columns = [];
            $placeholders = [];
            $values = [];

            foreach ($emailConfigArray as $key => $value) {
                $columns[] = $key;
                $placeholders[] = "?";
                $values[] = $value;
            }

            $columns = implode(", ", $columns);
            $placeholders = implode(", ", $placeholders);

            $stmt = $pdo->prepare(
                "INSERT INTO email_config ($columns)
                VALUES ($placeholders)"
            );

            $stmt->execute($values);

            return $pdo->lastInsertId();
        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to save email settings.");
        }
    }

    public static function update(EmailConfig $emailConfig): int {
        try {
            $pdo = self::getConnection();

            $emailConfig->setUpdatedAt(date('Y-m-d H:i:s'));
            $emailConfigArray = $emailConfig->toArray();

            $columns = [];
            $values = [];

            $ignoredColumns = ["id", "createdAt"];

            foreach ($emailConfigArray as $key => $value) {
                if (in_array($key, $ignoredColumns)) {
                    continue;
                }

                $columns[] = "$key = ?";
                $values[] = $value;
            }

            $columns = implode(", ", $columns);
            $values[] = $emailConfig->getId();

            $stmt = $pdo->prepare(
                "UPDATE email_config 
                SET $columns
                WHERE id = ?"
            );

            $stmt->execute($values);

            return $stmt->rowCount();

        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to update email settings.");
        }
    }

    public static function fetch(): array {
        try {
            $pdo = self::getConnection();
            $emailConfig = new EmailConfig();
            $emailConfigArray = $emailConfig->toArray();

            $columns = [];

            foreach ($emailConfigArray as $key => $value) {
                $columns[] = $key;
            }

            $columns = implode(", ", $columns);

            $stmt = $pdo->prepare(
                "SELECT $columns FROM email_config LIMIT 1"
            );

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ?: [];

        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to search for email settings");
        }
    }
}
