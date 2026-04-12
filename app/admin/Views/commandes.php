<?php
// admin/commandes.php
require_once __DIR__ . '/includes/config.php';

// Vérifier la connexion
if (!isAdminLoggedIn()) {
    header('Location: login.php?error=auth');
    exit();
}

try {
    $pdo = connectDB();
    
    // Récupérer toutes les commandes
    $stmt = $pdo->query("SELECT * FROM commandes ORDER BY created_at DESC");
    $commandes = $stmt->fetchAll();
    
    // Statistiques par statut
    $stats = [];
    $status_types = ['en_attente', 'traite', 'annule'];
    
    foreach ($status_types as $status) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM commandes WHERE status = :status");
        $stmt->execute([':status' => $status]);
        $stats[$status] = $stmt->fetch()['count'];
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
    <title>Gestion des commandes - ECOFI Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Même style que dashboard.php */
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --accent: #FF8533;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --light: #F9FAFB;
            --dark: #1F2937;
            --gray: #6B7280;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', system-ui, sans-serif; }
        
        body { background: #f5f7fa; color: var(--dark); min-height: 100vh; display: flex; }
        
        /* SIDEBAR (identique à dashboard) */
        .sidebar { width: 250px; background: white; box-shadow: 2px 0 10px rgba(0,0,0,0.1); 
                  display: flex; flex-direction: column; position: fixed; height: 100vh; z-index: 100; }
        
        .logo { padding: 25px 20px; text-align: center; border-bottom: 1px solid #eee; 
               background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; }
        
        .logo h1 { font-size: 24px; font-weight: 700; letter-spacing: 1px; }
        .logo span { color: var(--accent); }
        
        .nav-menu { flex: 1; padding: 20px 0; }
        .nav-item { display: flex; align-items: center; padding: 15px 25px; color: var(--gray); 
                   text-decoration: none; transition: all 0.3s; border-left: 3px solid transparent; }
        .nav-item:hover { background: var(--light); color: var(--primary); border-left-color: var(--primary); }
        .nav-item.active { background: linear-gradient(90deg, rgba(102, 126, 234, 0.1), transparent); 
                          color: var(--primary); border-left-color: var(--primary); font-weight: 600; }
        .nav-item i { width: 24px; margin-right: 15px; font-size: 18px; }
        
        .user-profile { padding: 20px; border-top: 1px solid #eee; display: flex; align-items: center; background: white; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; 
                      background: linear-gradient(135deg, var(--primary), var(--secondary)); 
                      display: flex; align-items: center; justify-content: center; color: white; 
                      font-weight: bold; margin-right: 12px; }
        .user-info h4 { font-size: 15px; margin-bottom: 3px; }
        .user-info p { font-size: 12px; color: var(--gray); }
        
        /* MAIN CONTENT */
        .main-content { flex: 1; margin-left: 250px; padding: 20px; }
        
        .header { display: flex; justify-content: space-between; align-items: center; padding: 20px; 
                 background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 30px; }
        
        .header h2 { color: var(--dark); font-size: 28px; font-weight: 700; }
        
        .header-actions { display: flex; gap: 15px; align-items: center; }
        
        .btn { padding: 10px 20px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; 
               transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; }
        
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3); }
        
        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { background: #DC2626; }
        
        /* FILTRES */
        .filters { display: flex; gap: 15px; margin-bottom: 25px; flex-wrap: wrap; }
        
        .filter-group { display: flex; gap: 10px; align-items: center; }
        
        .filter-select { padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; 
                        background: white; min-width: 150px; }
        
        /* TABLE */
        .table-container { background: white; border-radius: 12px; padding: 25px; 
                          box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow-x: auto; }
        
        table { width: 100%; border-collapse: collapse; min-width: 1000px; }
        
        th { text-align: left; padding: 15px; color: var(--gray); font-weight: 500; 
             border-bottom: 1px solid #eee; font-size: 14px; }
        
        td { padding: 15px; border-bottom: 1px solid #f5f5f5; }
        
        .status { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
        .status.pending { background: #FEF3C7; color: #D97706; }
        .status.completed { background: #D1FAE5; color: #065F46; }
        .status.cancelled { background: #FEE2E2; color: #DC2626; }
        
        .action-btn { padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; 
                     margin: 0 3px; font-size: 12px; }
        
        .btn-edit { background: #DBEAFE; color: #1D4ED8; }
        .btn-delete { background: #FEE2E2; color: #DC2626; }
        .btn-view { background: #D1FAE5; color: #065F46; }
        
        /* STATS CARDS */
        .stats-row { display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap; }
        
        .stat-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
                    flex: 1; min-width: 200px; }
        
        .stat-card h3 { font-size: 28px; margin-bottom: 5px; color: var(--dark); }
        .stat-card p { color: var(--gray); font-size: 14px; }
        
        @media (max-width: 768px) {
            .sidebar { width: 70px; }
            .logo h1 span { display: none; }
            .logo h1:after { content: "E"; font-size: 24px; }
            .nav-item span { display: none; }
            .nav-item i { margin-right: 0; font-size: 20px; }
            .user-info { display: none; }
            .main-content { margin-left: 70px; }
            .stats-row { flex-direction: column; }
        }
    </style>
</head>
<body>
    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="logo">
            <h1>ECO<span>FI</span></h1>
            <p style="font-size: 12px; opacity: 0.9;">Administration</p>
        </div>
        
        <div class="nav-menu">
            <a href="dashboard.php" class="nav-item">
                <i class="fas fa-tachometer-alt"></i>
                <span>Tableau de bord</span>
            </a>
            <a href="commandes.php" class="nav-item active">
                <i class="fas fa-shopping-cart"></i>
                <span>Commandes</span>
            </a>
            <a href="demandes.php" class="nav-item">
                <i class="fas fa-envelope"></i>
                <span>Demandes</span>
            </a>
            <a href="produits.php" class="nav-item">
                <i class="fas fa-box"></i>
                <span>Produits</span>
            </a>
            <a href="statistiques.php" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Statistiques</span>
            </a>
        </div>
        
        <div class="user-profile">
            <div class="user-avatar">
                <?php 
                    $initial = substr($_SESSION['admin_nom'], 0, 1);
                    echo strtoupper($initial);
                ?>
            </div>
            <div class="user-info">
                <h4><?php echo htmlspecialchars($_SESSION['admin_nom']); ?></h4>
                <p>Administrateur</p>
            </div>
        </div>
    </div>
    
    <!-- MAIN CONTENT -->
    <div class="main-content">
        <!-- HEADER -->
        <div class="header">
            <h2><i class="fas fa-shopping-cart"></i> Gestion des commandes</h2>
            <div class="header-actions">
                <a href="dashboard.php" class="btn">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </a>
                <a href="logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    Déconnexion
                </a>
            </div>
        </div>
        
        <!-- STATS -->
        <div class="stats-row">
            <div class="stat-card">
                <h3><?php echo $stats['en_attente'] ?? 0; ?></h3>
                <p>En attente</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['traite'] ?? 0; ?></h3>
                <p>Traitées</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['annule'] ?? 0; ?></h3>
                <p>Annulées</p>
            </div>
            <div class="stat-card">
                <h3><?php echo count($commandes); ?></h3>
                <p>Total commandes</p>
            </div>
        </div>
        
        <!-- FILTRES -->
        <div class="filters">
            <div class="filter-group">
                <label>Statut :</label>
                <select class="filter-select" id="statusFilter">
                    <option value="all">Tous les statuts</option>
                    <option value="en_attente">En attente</option>
                    <option value="traite">Traitées</option>
                    <option value="annule">Annulées</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label>Date :</label>
                <select class="filter-select" id="dateFilter">
                    <option value="all">Toutes les dates</option>
                    <option value="today">Aujourd'hui</option>
                    <option value="week">Cette semaine</option>
                    <option value="month">Ce mois</option>
                </select>
            </div>
        </div>
        
        <!-- TABLE -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Téléphone</th>
                        <th>Produit</th>
                        <th>Quantité</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $commande): ?>
                    <tr>
                        <td>#<?php echo $commande['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($commande['client_nom']); ?></strong><br>
                            <small><?php echo htmlspecialchars($commande['client_email'] ?? 'N/A'); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($commande['client_telephone']); ?></td>
                        <td><?php echo htmlspecialchars($commande['produit']); ?></td>
                        <td><?php echo $commande['quantite']; ?></td>
                        <td><strong><?php echo htmlspecialchars($commande['total'] ?? 'N/A'); ?></strong></td>
                        <td>
                            <?php 
                                $status_class = '';
                                $status_text = '';
                                switch($commande['status']) {
                                    case 'en_attente':
                                        $status_class = 'pending';
                                        $status_text = 'En attente';
                                        break;
                                    case 'traite':
                                        $status_class = 'completed';
                                        $status_text = 'Traité';
                                        break;
                                    case 'annule':
                                        $status_class = 'cancelled';
                                        $status_text = 'Annulé';
                                        break;
                                }
                            ?>
                            <span class="status <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($commande['created_at'])); ?></td>
                        <td>
                            <button class="action-btn btn-view" title="Voir">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn btn-edit" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn btn-delete" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        // Filtrage des commandes
        document.getElementById('statusFilter').addEventListener('change', function() {
            filterTable();
        });
        
        document.getElementById('dateFilter').addEventListener('change', function() {
            filterTable();
        });
        
        function filterTable() {
            const status = document.getElementById('statusFilter').value;
            const dateFilter = document.getElementById('dateFilter').value;
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const rowStatus = row.querySelector('.status').className;
                let show = true;
                
                // Filtre par statut
                if (status !== 'all') {
                    const statusMap = {
                        'en_attente': 'pending',
                        'traite': 'completed',
                        'annule': 'cancelled'
                    };
                    if (!rowStatus.includes(statusMap[status])) {
                        show = false;
                    }
                }
                
                // Filtre par date (simplifié)
                if (show && dateFilter !== 'all') {
                    const dateText = row.cells[7].textContent;
                    // Ici vous implémenteriez la logique de filtrage par date
                }
                
                row.style.display = show ? '' : 'none';
            });
        }
        
        // Actions sur les boutons
        document.querySelectorAll('.btn-view').forEach(btn => {
            btn.addEventListener('click', function() {
                alert('Fonctionnalité "Voir détails" à implémenter');
            });
        });
        
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                alert('Fonctionnalité "Modifier" à implémenter');
            });
        });
        
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Voulez-vous vraiment supprimer cette commande ?')) {
                    alert('Fonctionnalité "Supprimer" à implémenter');
                }
            });
        });
    </script>
</body>
</html>