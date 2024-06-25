<?php

namespace App\Commands;

class GenerateControllerCommand {
    public static function execute($name) {
        $className = ucfirst($name) . 'Controller';
        $filename = "{$className}.php";

        $fileExists = __DIR__ . "/../Controllers/{$filename}";

        if(file_exists($fileExists)) {
            showAlertLog("This controller already exists.");
            return;
        }

        $template = <<<EOT
<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class {$className} {
    public function fetch(Request \$request, Response \$response) {
        // Code to list resources
    }

    public function show(Request \$request, Response \$response, \$id) {
        // Code to show a specific resource
    }

    public function create(Request \$request, Response \$response) {
        // Code to create a new resource
    }

    public function update(Request \$request, Response \$response) {
        // Code to update a specific resource
    }

    public function delete(Request \$request, Response \$response, \$id) {
        // Code to delete a specific resource
    }
}

EOT;

        $path = __DIR__ . "/../Controllers/{$filename}";
        file_put_contents($path, $template);
        showSuccessLog("Controller created: {$filename}");
    }
}
