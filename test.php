<?php
// test_pgsql.php
echo "<h2>🔍 Test de connexion PostgreSQL</h2>";

try {
    // Test avec vos identifiants
    $pdo = new PDO("pgsql:host=localhost;port=5432;dbname=postgres", "postgres", "");
    echo "✅ Connexion au serveur PostgreSQL réussie<br>";
    
    // Lister les bases de données
    $stmt = $pdo->query("SELECT datname FROM pg_database WHERE datistemplate = false");
    $bases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Bases de données disponibles :<br>";
    echo "<ul>";
    foreach ($bases as $base) {
        echo "<li>$base</li>";
    }
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage() . "<br>";
    echo "Vérifiez que PostgreSQL est bien installé et que les identifiants sont corrects.";
}
?>