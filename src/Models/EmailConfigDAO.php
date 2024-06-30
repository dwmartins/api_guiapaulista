<?php

namespace App\Models;

use App\Class\EmailConfig;
use App\Models\Database;
use PDOException;

class EmailConfigDAO extends Database{
    public static function save(EmailConfig $emailConfig) {
        // Implementation of the creation method
    }

    public static function fetch($id) {
        // Implementation of the read method
    }

    public static function update($data) {
        // Update method implementation
    }

    public static function delete($id) {
        // Implementation of the delete method
    }
}
