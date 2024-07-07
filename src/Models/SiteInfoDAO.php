<?php

namespace App\Models;

use App\Class\SiteInfo;
use App\Models\Database;
use Exception;
use PDO;
use PDOException;

class SiteInfoDAO extends Database {
    public static function save(SiteInfo $siteInfo): int {
        try {
            $pdo = self::getConnection();

            $siteInfo->setCreatedAt(date('Y-m-d H:i:s'));
            $siteInfo->setUpdatedAt(date('Y-m-d H:i:s'));

            $columns = [];
            $placeholders = [];
            $values = [];

            foreach ($siteInfo->toArray() as $key => $value) {
                $columns[] = $key;
                $placeholders[] = "?";
                $values[] = $value;
            }
            
            $columns = implode(", ", $columns);
            $placeholders = implode(", ", $placeholders);

            $stmt = $pdo->prepare(
                "INSERT INTO site_Info ($columns)
                VALUES ($placeholders)"
            );

            $stmt->execute($values);

            return $pdo->lastInsertId();

        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error executing query to save site information.");
        }
    }

    public static function update(SiteInfo $siteInfo): int {
        try {
            $pdo = self::getConnection();

            $siteInfo->setUpdatedAt(date('Y-m-d H:i:s'));

            $columns = [];
            $values = [];

            foreach ($siteInfo->toArray() as $key => $value) {
                if(empty($value)) {
                    continue;
                }

                $columns[] = "$key = ?";
                $values[] = $value;
            }

            $columns = implode(", ", $columns);
            $values[] = $siteInfo->getId();

            $stmt = $pdo->prepare(
                "UPDATE site_Info 
                SET $columns
                WHERE id = ?"
            );

            $stmt->execute($values);

            return $stmt->rowCount();
            
        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to update site information.");
        }
    }

    public static function fetch(): array {
        try {
            $pdo = self::getConnection();

            $stmt = $pdo->prepare(
                "SELECT * FROM site_Info LIMIT 1"
            );

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ?: [];

        } catch (PDOException $e) {
            logError($e->getMessage());
            throw new Exception("Error when executing query to search for site information.");
        }
    }
}