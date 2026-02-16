<?php
// admin/demandes.php
require_once __DIR__ . '/includes/config.php';

// Vérifier la connexion
if (!isAdminLoggedIn()) {
    header('Location: login.php?error=auth');
    exit();
}

try {
    $pdo = connectDB();
    
    // Récupérer toutes les demandes
    $stmt = $pdo->query("SELECT * FROM demandes_contact ORDER BY created_at DESC");
    $demandes = $stmt->fetchAll();
    
    // Statistiques par type
    $stats_type = [];
    $types = ['devis', 'contact', 'info'];
    
    foreach ($types as $type) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM demandes_contact WHERE type = :type");
        $stmt->execute([':type' => $type]);
        $stats_type[$type] = $stmt->fetch()['count'];
    }
    
} catch (Exception $e) {
    $error_db = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demandes de contact - ECOFI Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Style similaire aux autres pages */
        :root { --primary: #667eea; --secondary: #764ba2; --accent: #FF8533; }
        
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; margin: 0; padding: 0; }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        
        .header { 
            background: white; 
            padding: 25px; 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 { 
            color: var(--dark); 
            font-size: 32px; 
            margin: 0; 
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .stat-card h3 {
            font-size: 36px;
            margin: 0 0 10px 0;
            color: var(--primary);
        }
        
        .demandes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        
        .demande-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .demande-card:hover {
            transform: translateY(-5px);
        }
        
        .demande-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .demande-type {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .type-devis { background: #DBEAFE; color: #1D4ED8; }
        .type-contact { background: #D1FAE5; color: #065F46; }
        .type-info { background: #FEF3C7; color: #D97706; }
        
        .demande-body p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .demande-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--secondary);
        }
        
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #f5f5f5;
            color: #333;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .back-btn:hover {
            background: #e5e5e5;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Retour au tableau de bord
        </a>
        
        <div class="header">
            <h1>
                <i class="fas fa-envelope"></i>
                Demandes de contact
            </h1>
            <div>
                <a href="logout.php" style="padding: 10px 20px; background: #EF4444; color: white; 
                   text-decoration: none; border-radius: 8px; font-weight: 600;">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo $stats_type['devis'] ?? 0; ?></h3>
                <p>Demandes de devis</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats_type['contact'] ?? 0; ?></h3>
                <p>Contacts généraux</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats_type['info'] ?? 0; ?></h3>
                <p>Demandes d'info</p>
            </div>
            <div class="stat-card">
                <h3><?php echo count($demandes); ?></h3>
                <p>Total demandes</p>
            </div>
        </div>
        
        <div class="demandes-grid">
            <?php foreach ($demandes as $demande): ?>
            <div class="demande-card">
                <div class="demande-header">
                    <div>
                        <h3 style="margin: 0 0 5px 0;"><?php echo htmlspecialchars($demande['nom']); ?></h3>
                        <p style="margin: 0; color: #666; font-size: 14px;">
                            <?php echo htmlspecialchars($demande['email']); ?> | 
                            <?php echo htmlspecialchars($demande['telephone'] ?? 'N/A'); ?>
                        </p>
                    </div>
                    <span class="demande-type type-<?php echo $demande['type']; ?>">
                        <?php 
                            $type_labels = [
                                'devis' => 'Devis',
                                'contact' => 'Contact',
                                'info' => 'Information'
                            ];
                            echo $type_labels[$demande['type']] ?? $demande['type'];
                        ?>
                    </span>
                </div>
                
                <div class="demande-body">
                    <p><strong>Service :</strong> <?php echo htmlspecialchars($demande['service'] ?? 'Non spécifié'); ?></p>
                    <p><strong>Message :</strong></p>
                    <p style="background: #f9f9f9; padding: 10px; border-radius: 6px;">
                        <?php echo nl2br(htmlspecialchars($demande['message'])); ?>
                    </p>
                </div>
                
                <div class="demande-footer">
                    <span style="color: #888; font-size: 13px;">
                        <i class="far fa-clock"></i> 
                        <?php echo date('d/m/Y H:i', strtotime($demande['created_at'])); ?>
                    </span>
                    <button class="btn btn-primary">
                        <i class="fas fa-check"></i> Marquer comme traité
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>