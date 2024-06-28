<?php

use App\Models\Database;

class Migration_20240627213622_user_permissions extends Database{
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
            $toUsers = [
                'create' => false,
                'update' => false,
                'delete' => false
            ];

            $toPages = ['listUsers', 'siteInfo'];

            $toProducts = [
                'create' => false,
                'update' => false,
                'delete' => false
            ];

            $toConfigsEmail = [
                'update' => false
            ];

            $sql = "CREATE TABLE IF NOT EXISTS user_permissions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                toUsers JSON,
                toPages JSON,
                toProducts JSON,
                toConfigsEmail JSON,
                createdAt DATETIME,
                updatedAt DATETIME,
                CONSTRAINT fk_userPer_permissions FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
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
