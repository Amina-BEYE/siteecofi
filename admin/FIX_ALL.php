<?php
// admin/ULTIMATE_TEST.php - TEST ULTIME
session_start();

echo "<h1>🔬 TEST ULTIME DE LA CONNEXION</h1>";
echo "<style>
    body { font-family: Arial; padding: 20px; background: #f0f0f0; }
    .test { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #ddd; }
    .success { border-left-color: #4CAF50; }
    .error { border-left-color: #f44336; }
    .warning { border-left-color: #FF9800; }
    code { background: #f5f5f5; padding: 2px 5px; border-radius: 3px; }
    .btn { display: inline-block; padding: 10px 20px; background: #667eea; color: white; 
           text-decoration: none; border-radius: 5px; margin: 5px; }
</style>";

// TEST 1: Configuration de base
echo "<div class='test'>";
echo "<h3>1. Configuration serveur</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Extensions chargées:<br>";
echo "- pgsql: " . (extension_loaded('pgsql') ? '✅' : '❌') . "<br>";
echo "- pdo_pgsql: " . (extension_loaded('pdo_pgsql') ? '✅' : '❌') . "<br>";
echo "</div>";

// TEST 2: Connexion directe à PostgreSQL
echo "<div class='test'>";
echo "<h3>2. Connexion à PostgreSQL</h3>";
try {
    $pdo = new PDO("pgsql:host=localhost;port=5432;dbname=ecofi_db", "postgres", "admin123");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion réussie<br>";
    
    // Vérifier la table administrateurs
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM administrateurs");
    $result = $stmt->fetch();
    echo "✅ Table administrateurs: " . $result['count'] . " enregistrement(s)<br>";
    
    // Vérifier l'admin spécifique
    $stmt = $pdo->prepare("SELECT * FROM administrateurs WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<div class='test success'>";
        echo "✅ Admin 'admin' trouvé<br>";
        echo "- ID: " . $admin['id'] . "<br>";
        echo "- Nom: " . $admin['nom_complet'] . "<br>";
        echo "- Hash: <code>" . $admin['password_hash'] . "</code><br>";
        echo "- Longueur hash: " . strlen($admin['password_hash']) . " caractères<br>";
        echo "</div>";
        
        // TEST du mot de passe
        echo "<div class='test'>";
        echo "<h3>3. Test du mot de passe 'admin123'</h3>";
        if (password_verify('admin123', $admin['password_hash'])) {
            echo "✅ password_verify('admin123') = TRUE<br>";
            
            // Simuler la connexion
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_nom'] = $admin['nom_complet'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_logged_in'] = true;
            
            echo "<div class='test success'>";
            echo "✅ Session créée:<br>";
            echo "- admin_id: " . $_SESSION['admin_id'] . "<br>";
            echo "- admin_nom: " . $_SESSION['admin_nom'] . "<br>";
            echo "- Session ID: " . session_id() . "<br>";
            echo "</div>";
            
        } else {
            echo "<div class='test error'>";
            echo "❌ password_verify('admin123') = FALSE<br>";
            echo "Le hash ne correspond pas à 'admin123'<br>";
            echo "</div>";
            
            // Générer un nouveau hash
            $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
            echo "<div class='test warning'>";
            echo "🔄 Nouveau hash pour 'admin123':<br>";
            echo "<code>" . $new_hash . "</code><br>";
            echo "<button onclick=\"document.getElementById('sqlfix').style.display='block'\">Afficher la commande de correction</button>";
            echo "<div id='sqlfix' style='display:none; margin-top:10px;'>";
            echo "<textarea style='width:100%; height:60px;'>";
            echo "UPDATE administrateurs SET password_hash = '$new_hash' WHERE username = 'admin';";
            echo "</textarea>";
            echo "</div>";
            echo "</div>";
        }
        
    } else {
        echo "<div class='test error'>";
        echo "❌ Admin 'admin' NON trouvé dans la base";
        echo "</div>";
    }
    
} catch (PDOException $e) {
    echo "<div class='test error'>";
    echo "❌ Erreur de connexion: " . $e->getMessage();
    echo "</div>";
}
echo "</div>";

// TEST 4: Vérifier le fichier login.php
echo "<div class='test'>";
echo "<h3>4. Analyse du fichier login.php</h3>";

$login_path = __DIR__ . '/login.php';
if (file_exists($login_path)) {
    $login_content = file_get_contents($login_path);
    
    // Vérifier des points clés
    $checks = [
        'session_start' => strpos($login_content, 'session_start') !== false,
        'password_verify' => strpos($login_content, 'password_verify') !== false,
        '$_SESSION' => strpos($login_content, '$_SESSION') !== false,
        'isAdminLoggedIn' => strpos($login_content, 'isAdminLoggedIn') !== false,
    ];
    
    foreach ($checks as $check => $result) {
        echo ($result ? '✅' : '❌') . " $check<br>";
    }
    
    echo "<button onclick=\"document.getElementById('logincontent').style.display='block'\">Voir le code de login.php</button>";
    echo "<div id='logincontent' style='display:none; margin-top:10px;'>";
    echo "<textarea style='width:100%; height:300px; font-family: monospace;'>" . 
         htmlspecialchars($login_content) . 
         "</textarea>";
    echo "</div>";
    
} else {
    echo "❌ Fichier login.php non trouvé";
}
echo "</div>";

// TEST 5: Tester la redirection
echo "<div class='test'>";
echo "<h3>5. Test de la redirection</h3>";

// Vérifier si on est connecté
$is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

if ($is_logged_in) {
    echo "✅ Vous êtes connecté<br>";
    echo "<a href='dashboard.php' class='btn'>Accéder au dashboard</a>";
    
    // Tester la fonction requireLogin()
    echo "<br><br>Tester requireLogin():<br>";
    
    // Simuler l'appel
    if (!isset($_SESSION['admin_logged_in'])) {
        echo "❌ La redirection se déclencherait";
    } else {
        echo "✅ Pas de redirection (déjà connecté)";
    }
    
} else {
    echo "❌ Vous n'êtes PAS connecté<br>";
    echo "<a href='FORCE_LOGIN.php' class='btn'>Forcer la connexion</a>";
}
echo "</div>";

// TEST 6: Créer un script de connexion FORCÉE
echo "<div class='test'>";
echo "<h3>6. Créer un script de connexion forcée</h3>";
echo "<a href='CREATE_FORCE_LOGIN.php' class='btn'>Créer FORCE_LOGIN.php</a>";
echo "</div>";
?>