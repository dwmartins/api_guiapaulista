<?php

use App\Models\Database;

class Migration_20240701220406_recover_password extends Database{
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
            $sql = "CREATE TABLE IF NOT EXISTS user_recover_password (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                token TEXT NOT NULL,
                used ENUM('Y', 'N'),
                expiration DATETIME,
                createdAt DATETIME,
                CONSTRAINT fk_userRecoverPasswordId FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
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
