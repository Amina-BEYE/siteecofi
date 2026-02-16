<?php
// admin/update_password.php
require_once __DIR__ . '/includes/config.php';

try {
    $pdo = connectDB();
    
    // Générer un nouveau hash pour "admin123"
    $new_password = 'admin123';
    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
    
    echo "<h2>Mise à jour du mot de passe</h2>";
    echo "Nouveau mot de passe : $new_password<br>";
    echo "Nouveau hash : $new_hash<br><br>";
    
    // Mettre à jour dans la base
    $stmt = $pdo->prepare("UPDATE administrateurs SET password_hash = :hash WHERE id = 3");
    $stmt->execute([':hash' => $new_hash]);
    
    echo "✅ Mot de passe mis à jour avec succès !<br><br>";
    
    // Vérifier
    $stmt = $pdo->prepare("SELECT password_hash FROM administrateurs WHERE id = 3");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    echo "Hash en base : " . $admin['password_hash'] . "<br>";
    
    if (password_verify('admin123', $admin['password_hash'])) {
        echo "✅ Vérification : Le mot de passe 'admin123' est maintenant valide !";
    } else {
        echo "❌ Vérification échouée";
    }
    
    echo "<br><br><a href='login.php'>Aller à la page de connexion</a>";
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>