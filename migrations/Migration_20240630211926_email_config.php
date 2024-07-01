<?php

use App\Models\Database;

class Migration_20240630211926_email_config extends Database{
    protected $db;

    public function __construct() {
        try {
            $this->db = self::getConnection();
        } catch (PDOException $e) {
            showAlertLog("ERROR: ". $e->getMessage());
            throw $e;
        }
    }

    public function up() {
        // Migration implementation (up)
        try {
            $sql = "CREATE TABLE IF NOT EXISTS email_config (
                id INT AUTO_INCREMENT PRIMARY KEY,
                server VARCHAR(255),
                emailAddress VARCHAR(255),
                username VARCHAR(255),
                password VARCHAR(255),
                port INT,
                authentication VARCHAR(50),
                createdAt DATETIME,
                updatedAt DATETIME
            );";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function down() {
        // Migration implementation (rollback)
    }
}
