<?php

use App\Models\Database;

class Migration_20240716195051_table_widgets extends Database{
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
            $sql = "CREATE TABLE IF NOT EXISTS widgets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                widget_name VARCHAR(100),
                widget_data JSON,
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
