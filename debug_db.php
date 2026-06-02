<?php
$dsn = "mysql:host=109.234.166.62;port=3306;dbname=fael5053_siteecofiTest;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, "fael5053_ecofi", "DXY3k!ZFikgYW0RN", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]);
    $stmt = $pdo->prepare("SELECT can_access FROM app_features WHERE role_key = :role_key AND page_key = :page_key LIMIT 1");
    $stmt->execute([':role_key' => 'agent', ':page_key' => 'dashboard']);
    var_dump($stmt->fetchColumn());
    echo "OK\n";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}
