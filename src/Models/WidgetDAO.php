<?php

namespace App\Models;

use App\Class\Widget;
use App\Models\Database;
use PDOException;
use PDO;
use Exception;

class WidgetDAO extends Database{
    public static function save(Widget $widget): int {
        try {
            $pdo = self::getConnection();

            $widget->setCreatedAt(date('Y-m-d H:i:s'));
            $widget->setUpdatedAt(date('Y-m-d H:i:s'));

            $widgetArray = $widget->toArray();

            $columns = [];
            $placeholders = [];
            $values = [];

            foreach ($widgetArray as $key => $value) {
                $columns[] = $key;
                $placeholders[] = "?";
                $values[] = $value;
            }

            $columns = implode(", ", $columns);
            $placeholders = implode(", ", $placeholders);

            $stmt = $pdo->prepare(
                "INSERT INTO widgets ($columns)
                VALUES ($placeholders)"
            );

            $stmt->execute($values);
    
            return $pdo->lastInsertId();

        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to save widget");
        }
    }

    public static function update(Widget $widget): int {
        try {
            $pdo = self::getConnection();
            $widget->setUpdatedAt(date('Y-m-d H:i:s'));
            $widgetArray = $widget->toArray();

            $columns = [];
            $values = [];

            foreach ($widgetArray as $key => $value) {
                if(empty($value)) {
                    continue;
                }

                $columns[] = "$key = ?";
                $values[] = $value;
            }

            $columns = implode(", ", $columns);
            $values[] = $widget->getWidgetName();

            $stmt = $pdo->prepare(
                "UPDATE widgets 
                SET $columns
                WHERE widget_name = ?"
            );

            $stmt->execute($values);

            return $stmt->rowCount();

        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to update widget");
        }
    }

    public static function fetchAll(): array {
        try {
            $pdo = self::getConnection();

            $stmt = $pdo->prepare(
                "SELECT * FROM widgets"
            );

            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result ?: [];

        }  catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to search for widgets");
        }
    }

    public static function fetchById(int $id): array {
        try {
            $pdo = self::getConnection();

            $stmt = $pdo->prepare(
                "SELECT * FROM widgets WHERE id = ?"
            );

            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: [];

        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to search for widgets by name");
        }
    }
}
