<?php

/**
 * ========================================
 * CONFIGURATION PRINCIPALE - ECOFI
 * ========================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| DEBUG LOGGER
|--------------------------------------------------------------------------
*/

function debug_log($label, $value = null): void
{
    if (getenv('ECOFI_DEBUG_CONFIG') !== '1') {
        return;
    }

    echo "<pre style='background:#111;color:#0f0;padding:10px;font-size:12px;'>";

    echo "[DEBUG] " . $label;

    if ($value !== null) {

        echo " : ";

        if (is_array($value) || is_object($value)) {
            print_r($value);
        } else {
            echo htmlspecialchars((string)$value);
        }
    }

    echo "</pre>";
}

/*
|--------------------------------------------------------------------------
| APP CONFIG
|--------------------------------------------------------------------------
*/

define('APP_NAME', 'Ecofi');
define('APP_URL', 'https://ecofi-services.com/');

date_default_timezone_set('Africa/Dakar');

debug_log('APP START');

/*
|--------------------------------------------------------------------------
| ENVIRONMENT DETECTION
|--------------------------------------------------------------------------
*/

$serverHost = $_SERVER['HTTP_HOST']
    ?? getenv('SERVER_NAME')
    ?? '';

$isLocalHost =
    str_contains($serverHost, 'localhost')
    || str_contains($serverHost, '127.0.0.1');

debug_log('SERVER HOST', $serverHost);
debug_log('IS LOCAL HOST', $isLocalHost ? 'YES' : 'NO');

/*
|--------------------------------------------------------------------------
| PRODUCTION CONFIG
|--------------------------------------------------------------------------
*/

$productionConfigPath = __DIR__ . '/production.php';

debug_log('PRODUCTION CONFIG PATH', $productionConfigPath);

if (!file_exists($productionConfigPath)) {

    die('production.php introuvable');
}

$prod = require $productionConfigPath;

debug_log('PRODUCTION CONFIG', $prod);

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if (
    empty($prod['host']) ||
    empty($prod['port']) ||
    empty($prod['dbname']) ||
    empty($prod['username']) ||
    empty($prod['password'])
) {

    die('Configuration MySQL invalide');
}

/*
|--------------------------------------------------------------------------
| DATABASE CONSTANTS
|--------------------------------------------------------------------------
*/

define('DB_HOST', $prod['host']);
define('DB_PORT', $prod['port']);
define('DB_NAME', $prod['dbname']);
define('DB_USER', $prod['username']);
define('DB_PASS', $prod['password']);
define('DB_CHARSET', $prod['charset'] ?? 'utf8mb4');

/*
|--------------------------------------------------------------------------
| ENV
|--------------------------------------------------------------------------
*/

define('APP_ENV', $isLocalHost ? 'local' : 'production');

debug_log('APP_ENV', APP_ENV);

/*
|--------------------------------------------------------------------------
| FINAL DB VALUES
|--------------------------------------------------------------------------
*/

debug_log('DB_HOST', DB_HOST);
debug_log('DB_PORT', DB_PORT);
debug_log('DB_NAME', DB_NAME);
debug_log('DB_USER', DB_USER);
debug_log('DB_PASS', '********');
debug_log('DB_CHARSET', DB_CHARSET);

/*
|--------------------------------------------------------------------------
| ERROR HANDLING
|--------------------------------------------------------------------------
*/

if ($isLocalHost) {

    error_reporting(E_ALL);

    ini_set('display_errors', '1');

    debug_log('DEBUG MODE', 'ON');

} else {

    error_reporting(0);

    ini_set('display_errors', '0');

    debug_log('DEBUG MODE', 'OFF');
}

debug_log('CONFIG LOADED SUCCESSFULLY');
