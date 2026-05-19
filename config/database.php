<?php

/**
 * Database configuration loader - ECOFI
 *
 * Local sans tunnel SSH :
 * - utilise directement la base test O2Switch
 *
 * Production :
 * - utilise la base O2Switch PROD
 */

$serverHost = $_SERVER['HTTP_HOST'] ?? getenv('SERVER_NAME') ?: '';

$isLocalHost =
    str_contains($serverHost, 'localhost')
    || str_contains($serverHost, '127.0.0.1')
    || str_contains($serverHost, '::1')
    || $serverHost === '';

if ($isLocalHost) {
    return [
        'host' => '109.234.166.62',
        'port' => '3306',
        'dbname' => 'fael5053_siteecofiTest',
        'username' => 'fael5053_ecofi',
        'password' => 'DXY3k!ZFikgYW0RN',
        'charset' => 'utf8mb4',
    ];
}

return [
    'host' => '109.234.166.62',
    'port' => '3306',
    'dbname' => 'fael5053_siteecofi',
    'username' => 'fael5053_ecofi',
    'password' => 'DXY3k!ZFikgYW0RN',
    'charset' => 'utf8mb4',
    'display_errors' => false,
    'environment' => 'production',
];