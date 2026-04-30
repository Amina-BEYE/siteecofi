<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_NAME', 'Ecofi');
define('APP_URL', 'https://ecofi-services.com/');

define('DB_HOST', 'localhost');
define('DB_NAME', 'fael5053_siteecofi');
define('DB_USER', 'fael5053_ecofi');
define('DB_PASS', 'Hz7*gYVzO0nVC)%s');
define('DB_CHARSET', 'utf8mb4');

date_default_timezone_set('Africa/Dakar');

error_reporting(E_ALL);
ini_set('display_errors', '1');