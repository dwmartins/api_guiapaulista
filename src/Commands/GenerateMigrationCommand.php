<?php

namespace App\Commands;

class GenerateMigrationCommand {
    public static function execute($description) {
        $timestamp = date('YmdHis');
        $filename = "Migration_{$timestamp}_{$description}.php";
        $template = <<<EOT
<?php

use App\Models\Database;

class Migration_{$timestamp}_{$description} extends Database{
    protected \$db;

    public function __construct() {
        try {
            \$this->db = self::getConnection();
        } catch (PDOException \$e) {
            showAlertLog("ERROR: ". \$e->getMessage());
            throw \$e;
        }
    }

    public function up() {
        // Migration implementation (up)
        try {
            \$sql = "";

            \$stmt = \$this->db->prepare(\$sql);
            \$stmt->execute();
        } catch (PDOException \$e) {
            throw \$e;
        }
    }

    public function down() {
        // Migration implementation (rollback)
    }
}

EOT;

        $path = __DIR__ . "/../../migrations/{$filename}";
        file_put_contents($path, $template);
        showSuccessLog("Migration created: {$filename}");
    }
}
