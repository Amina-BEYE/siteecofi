<?php
// admin/TEST_ULTRA_SIMPLE.php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Ultra Simple</title>
    <style>
        body { font-family: Arial; padding: 50px; text-align: center; }
        .box { display: inline-block; padding: 30px; background: #f5f5f5; border-radius: 10px; }
        input { padding: 10px; margin: 10px; width: 200px; display: block; }
        button { padding: 10px 30px; background: #667eea; color: white; border: none; cursor: pointer; }
        .result { margin-top: 20px; padding: 15px; background: #e3f2fd; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Test de Connexion Direct</h1>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            
            echo "<div class='result'>";
            echo "<h3>Résultat :</h3>";
            
            try {
                // 1. Connexion DB
                $pdo = new PDO("pgsql:host=localhost;dbname=ecofi_db", "postgres", "admin123");
                echo "✅ Connexion DB OK<br>";
                
                // 2. Chercher l'admin
                $stmt = $pdo->prepare("SELECT * FROM administrateurs WHERE username = ?");
                $stmt->execute([$username]);
                $admin = $stmt->fetch();
                
                if ($admin) {
                    echo "✅ Utilisateur '$username' trouvé<br>";
                    echo "Hash: " . substr($admin['password_hash'], 0, 30) . "...<br>";
                    
                    // 3. Vérifier le mot de passe
                    if (password_verify($password, $admin['password_hash'])) {
                        echo "✅ Mot de passe CORRECT<br>";
                        
                        // 4. Créer la session
                        $_SESSION['admin_id'] = $admin['id'];
                        $_SESSION['admin_nom'] = $admin['nom_complet'];
                        $_SESSION['admin_email'] = $admin['email'];
                        $_SESSION['admin_logged_in'] = true;
                        
                        echo "<div style='background: #4CAF50; color: white; padding: 10px; margin-top: 10px;'>";
                        echo "🎉 CONNEXION RÉUSSIE !<br>";
                        echo "Session créée. Redirection...";
                        echo "</div>";
                        
                        // Redirection après 2 secondes
                        echo "<script>
                            setTimeout(function() {
                                window.location.href = 'DASHBOARD_SIMPLE.php';
                            }, 2000);
                        </script>";
                        
                    } else {
                        echo "❌ Mot de passe INCORRECT<br>";
                    }
                } else {
                    echo "❌ Utilisateur '$username' NON trouvé<br>";
                }
                
            } catch (Exception $e) {
                echo "❌ Erreur: " . $e->getMessage() . "<br>";
            }
            
            echo "</div>";
        }
        ?>
        
        <form method="POST">
            <input type="text" name="username" value="admin" placeholder="Nom d'utilisateur" required>
            <input type="password" name="password" value="admin123" placeholder="Mot de passe" required>
            <button type="submit">Tester la connexion</button>
        </form>
        
        <div style="margin-top: 20px; font-size: 12px; color: #666;">
            <p>Utilisateur: <strong>admin</strong></p>
            <p>Mot de passe: <strong>admin123</strong></p>
        </div>
    </div>
    
    <div style="margin-top: 30px;">
        <a href="login.php">Page login normale</a> | 
        <a href="check_session.php">Vérifier session</a>
    </div>
</body>
</html>