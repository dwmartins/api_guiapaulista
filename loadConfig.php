<?php

use App\Http\Response;

date_default_timezone_set('America/Sao_Paulo');

// Verifica se o script está sendo executado via CLI
function isCli() {
    return php_sapi_name() === 'cli' || defined('STDIN');
}

if(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] === '/favicon.ico') {
    exit;
}

// Carrega as configurações de produção ou desenvolvimento
$envPath = __DIR__ . "/";
$envFile = '.env';

if (file_exists($envPath . '.env.development')) {
    define("DEV_MODE", true);
    $envFile = '.env.development';
} elseif (file_exists($envPath . '.env')) {
    define("DEV_MODE", false);
    $envFile = '.env';
}

$dotenv = Dotenv\Dotenv::createImmutable($envPath, $envFile);
$dotenv->load();

// Carrega as configurações de CORS
$allowed_origin = $_ENV['ALLOWED_ORIGIN'];
$requestOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (!isCli() && DEV_MODE) {
    setHeaders($allowed_origin);

} else if (!isCli() && !DEV_MODE) {
    if($requestOrigin !== $allowed_origin) {
        Response::json([
            "error" => true,
            "message" => "You are not authorized to access this API"
        ], 403);

        exit;
    }

    setHeaders($allowed_origin);
}

function setHeaders($allowed_origin) {
    header("Access-Control-Allow-Origin: $allowed_origin");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, userId, token");

    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header("HTTP/1.1 200 OK");
        exit;
    }
}