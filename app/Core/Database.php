<?php

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        $dbConfigPath = __DIR__ . '/../../config/database.php';

        if (!file_exists($dbConfigPath)) {
            throw new RuntimeException(
                'config/database.php introuvable'
            );
        }

        $config = require $dbConfigPath;

        $host = trim($config['host'] ?? '');
        $port = trim((string)($config['port'] ?? '3306'));
        $dbname = trim($config['dbname'] ?? '');
        $username = trim($config['username'] ?? '');
        $password = $config['password'] ?? '';
        $charset = trim($config['charset'] ?? 'utf8mb4');

        if (
            $host === '' ||
            $dbname === '' ||
            $username === ''
        ) {
            throw new RuntimeException(
                'Configuration MySQL incomplète'
            );
        }

        if ($host === 'localhost') {
            $host = '127.0.0.1';
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $host,
            $port,
            $dbname,
            $charset
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            self::$connection = new PDO(
                $dsn,
                $username,
                $password,
                $options
            );
        } catch (PDOException $e) {
            error_log(sprintf(
                '[Database] Connexion MySQL impossible host=%s port=%s dbname=%s user=%s error=%s',
                $host,
                $port,
                $dbname,
                $username,
                $e->getMessage()
            ));

            throw new RuntimeException(
                'Connexion MySQL impossible',
                (int) $e->getCode(),
                $e
            );
        }

        return self::$connection;
    }
}
