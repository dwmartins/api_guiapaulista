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
        $modelName = "{$className}DAO";

        $fileExists = __DIR__ . "/../Class/{$filename}";

        if(file_exists($fileExists)) {
            showAlertLog("This Class already exists.");
            return;
        }

        $template = <<<EOT
<?php

namespace App\Class;

use App\Models\\$modelName;

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
use PDOException;

class {$className} extends Database{
    public static function save(\$data) {
        // Implementation of the creation method
    }

    public static function fetch(\$id) {
        // Implementation of the read method
    }

    public static function update(\$data) {
        // Update method implementation
    }

    public static function delete(\$id) {
        // Implementation of the delete method
    }
}

EOT;

        $path = __DIR__ . "/../Models/{$filename}";
        file_put_contents($path, $template);
        showSuccessLog("Model created: {$filename}");
    }
}
