<?php
// admin/dashboard.php
session_start();
require_once __DIR__ . '/../config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Statistiques
try {
    $stats = [
        'clients' => $pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn() ?: 0,
        'devis' => $pdo->query("SELECT COUNT(*) FROM devis")->fetchColumn() ?: 0,
        'commandes' => $pdo->query("SELECT COUNT(*) FROM commandes")->fetchColumn() ?: 0,
        'chiffre_affaires' => $pdo->query("SELECT SUM(total_ttc) FROM commandes WHERE statut != 'annulee'")->fetchColumn() ?: 0
    ];

    // Dernières commandes
    $commandes = $pdo->query("
        SELECT c.*, cl.nom as client_nom 
        FROM commandes c 
        JOIN clients cl ON c.client_id = cl.id 
        ORDER BY c.date_commande DESC 
        LIMIT 10
    ")->fetchAll();
} catch (PDOException $e) {
    $error = "Erreur de base de données: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard ECOFI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; }
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100%;
            background: #333;
            color: white;
            padding: 20px;
        }
        .sidebar h2 {
            color: #FF8533;
            margin-bottom: 30px;
            border-bottom: 2px solid #FF8533;
            padding-bottom: 10px;
        }
        .sidebar ul { list-style: none; }
        .sidebar li { margin-bottom: 10px; }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 12px 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #FF8533;
        }
        .sidebar i { margin-right: 10px; width: 20px; }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .top-bar {
            background: white;
            padding: 15px 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .user-info span {
            font-weight: 600;
            color: #333;
        }
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }
        .logout-btn:hover {
            background: #c0392b;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            background: #FF8533;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }
        .stat-info h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .stat-info p {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }
        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        .card-header h3 { color: #333; }
        .btn {
            padding: 10px 20px;
            background: #FF8533;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }
        .btn:hover { background: #ff6b1a; }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>ECOFI</h2>
        <ul>
            <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Accueil</a></li>
            <li><a href="devis.php"><i class="fas fa-file-invoice"></i> Devis</a></li>
            <li><a href="commandes.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
            <li><a href="clients.php"><i class="fas fa-users"></i> Clients</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="top-bar">
            <h1>Tableau de bord</h1>
            <div class="user-info">
                <span><i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <h3>Clients</h3>
                    <p><?= $stats['clients'] ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-file-invoice"></i></div>
                <div class="stat-info">
                    <h3>Devis</h3>
                    <p><?= $stats['devis'] ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
                <div class="stat-info">
                    <h3>Commandes</h3>
                    <p><?= $stats['commandes'] ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-money-bill"></i></div>
                <div class="stat-info">
                    <h3>CA Total</h3>
                    <p><?= number_format($stats['chiffre_affaires'], 0, ',', ' ') ?> FCFA</p>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3>Dernières commandes</h3>
                <a href="commandes.php" class="btn">Voir toutes</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>N° Commande</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $cmd): ?>
                    <tr>
                        <td><?= htmlspecialchars($cmd['numero_commande']) ?></td>
                        <td><?= htmlspecialchars($cmd['client_nom']) ?></td>
                        <td><?= date('d/m/Y', strtotime($cmd['date_commande'])) ?></td>
                        <td><?= number_format($cmd['total_ttc'], 0, ',', ' ') ?> FCFA</td>
                        <td><?= htmlspecialchars($cmd['statut']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>