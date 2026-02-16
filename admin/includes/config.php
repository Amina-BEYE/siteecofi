<?php
// includes/config.php
session_start();

// Configuration PostgreSQL
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecofi_db');        // Votre base dans pgAdmin
define('DB_USER', 'postgres');        // Utilisateur PostgreSQL
define('DB_PASS', 'admin123'); // Mettez votre mot de passe

// Fonction de connexion
function connectDB() {
    try {
        $dsn = "pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de connexion PostgreSQL : " . $e->getMessage());
    }
}

// Vérifier si admin est connecté
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Rediriger si non connecté
function requireLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}
?>