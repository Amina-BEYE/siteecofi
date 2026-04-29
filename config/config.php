<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_NAME', 'SITEECOFI');
define('APP_URL', 'http://localhost:8888/SITEECOFI');

define('DB_HOST', 'localhost');
define('DB_NAME', 'siteecofi');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_CHARSET', 'utf8mb4');

date_default_timezone_set('Africa/Abidjan');

error_reporting(E_ALL);
ini_set('display_errors', '1');