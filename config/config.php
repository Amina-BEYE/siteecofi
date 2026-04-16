<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_NAME', 'SITEECOFI');
define('APP_URL', 'http://localhost/SITEECOFI');

define('DB_HOST', 'localhost');
define('DB_NAME', 'siteecofi');
define('DB_USER', 'root');
define('DB_PASS', ''); // adapte selon ta config MAMP
define('DB_CHARSET', 'utf8mb4');

date_default_timezone_set('Africa/Abidjan');

error_reporting(E_ALL);
ini_set('display_errors', '1');