<?php

try {
    $pdo = new PDO(
        'mysql:host=109.234.166.62;port=3306;dbname=fael5053_siteecofiTest;charset=utf8mb4',
        'fael5053_ecofi',
        'DXY3k!ZFikgYW0RN',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    echo "OK connexion O2Switch TEST";
} catch (PDOException $e) {
    echo "ERREUR : " . $e->getMessage();
}