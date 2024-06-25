<?php

namespace App\Commands;

class GenerateClassCommand {
    public static function execute($name) {
        self::generateClass($name);
        self::generateModel($name);
    }

    private static function generateClass($name) {
        $className = ucfirst($name);
        $filename = "{$className}.php";

        $fileExists = __DIR__ . "/../Class/{$filename}";

        if(file_exists($fileExists)) {
            showAlertLog("This Class already exists.");
            return;
        }

        $template = <<<EOT
<?php

namespace App\Classes;

use App\Models\

class {$className} {
    // getters and setters methods
}

EOT;

        $path = __DIR__ . "/../Class/{$filename}";
        file_put_contents($path, $template);
        showSuccessLog("Class created: {$filename}");
    }

    private static function generateModel($name) {
        $className = ucfirst($name) . 'DAO';
        $filename = "{$className}.php";

        $fileExists = __DIR__ . "/../Models/{$filename}";

        if(file_exists($fileExists)) {
            showAlertLog("This Model already exists.");
            return;
        }

        $template = <<<EOT
<?php

namespace App\Models;

use App\Models\Database;

class {$className} extends Database{
    protected \$db;

    public function __construct(Database \$db) {
        try {
            \$this->db = self::getConnection();
        } catch (PDOException \$e) {
            showAlertLog("ERROR: ". \$e->getMessage());
            throw \$e;
        }
    }

    public function save(\$data) {
        // Implementation of the creation method
    }

    public function fetch(\$id) {
        // Implementation of the read method
    }

    public function update(\$data) {
        // Update method implementation
    }

    public function delete(\$id) {
        // Implementation of the delete method
    }
}

EOT;

        $path = __DIR__ . "/../Models/{$filename}";
        file_put_contents($path, $template);
        showSuccessLog("Model created: {$filename}");
    }
}
