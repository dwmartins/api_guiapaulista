<?php

namespace App\Models;

use PDO;
use PDOException;
class Database {
    public static function getConnection() {
        $host       = $_ENV['DB_HOST'];
        $dbname     = $_ENV['DB_DATABASE'];
        $username   = $_ENV['DB_USERNAME'];
        $password   = $_ENV['DB_PASSWORD'];
        $dbType     = $_ENV['DB_TYPE'];

        try {
            $pdo = new PDO("$dbType:host=$host;dbname=$dbname", $username, $password);
            return $pdo;
        } catch (PDOException $e) {
            logError($e->getMessage());
            return throw $e;
        }
    }
}