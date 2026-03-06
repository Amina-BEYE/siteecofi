<?php
// C:\xampp\htdocs\ecofi\config.php

$host = 'localhost';
$port = '5432'; // Port par défaut de PostgreSQL
$dbname = 'ecofi_db'; // ou le nom de votre base PostgreSQL
$username = 'postgres'; // Utilisateur par défaut de PostgreSQL
$password = '1203'; // Mot de passe PostgreSQL (souvent vide ou 'postgres')

try {
    // Connexion PostgreSQL avec PDO
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données PostgreSQL: " . $e->getMessage());
}

function generateNumero($prefix) {
    return $prefix . '-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
}
?>