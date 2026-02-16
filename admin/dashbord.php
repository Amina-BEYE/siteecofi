<?php
// admin/DASHBOARD_SIMPLE.php
session_start();

// Vérifier la connexion
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo "<h1>Accès refusé</h1>";
    echo "<p>Vous n'êtes pas connecté.</p>";
    echo "<a href='TEST_ULTRA_SIMPLE.php'>Se connecter</a>";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Simple</title>
    <style>
        body { font-family: Arial; margin: 0; }
        .header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 20px; }
        .content { padding: 20px; }
        .card { background: white; border-radius: 10px; padding: 20px; margin: 10px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn { padding: 10px 20px; background: #FF8533; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏢 ECOFI Dashboard</h1>
        <p>Bienvenue, <?php echo htmlspecialchars($_SESSION['admin_nom']); ?>!</p>
        <a href="logout.php" class="btn">Déconnexion</a>
    </div>
    
    <div class="content">
        <h2>🎉 FÉLICITATIONS !</h2>
        <p>Vous êtes connecté avec succès au système d'administration ECOFI.</p>
        
        <div class="card">
            <h3>Vos informations :</h3>
            <p>Nom : <?php echo htmlspecialchars($_SESSION['admin_nom']); ?></p>
            <p>Email : <?php echo htmlspecialchars($_SESSION['admin_email']); ?></p>
            <p>Session ID : <?php echo session_id(); ?></p>
        </div>
        
        <div class="card">
            <h3>Actions rapides :</h3>
            <a href="commandes.php" class="btn">📦 Voir les commandes</a>
            <a href="demandes.php" class="btn">📋 Voir les demandes</a>
        </div>
    </div>
</body>
</html>