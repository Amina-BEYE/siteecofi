<?php
// dashboardv2.php
// Les données sont passées depuis AdminController::dashboard()

// Récupérer le nom de l'admin connecté
$adminName = $_SESSION['user_name'] ?? $_SESSION['admin_name'] ?? 'Administrateur';
?>

<!-- ========== SECTION BIENVENUE STYLÉE ========== -->
<div class="welcome-hero">
    <div class="welcome-bg-pattern"></div>
    <div class="welcome-content">
        <div class="welcome-avatar">
            <div class="avatar-ring">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <div class="welcome-text">
            <span class="welcome-badge">
                <i class="fas fa-crown"></i> ECOFI Administration
            </span>
            <h1 class="welcome-title">
                Bienvenue, <span class="highlight"><?= htmlspecialchars($adminName) ?></span>
            </h1>
            <p class="welcome-subtitle">
                <i class="fas fa-chart-pie"></i> Tableau de bord · 
                <i class="fas fa-calendar-alt"></i> <?= date('l d F Y') ?>
            </p>
            <div class="welcome-stats-brief">
                <div class="brief-stat">
                    <i class="fas fa-rocket"></i>
                    <span>Vue d'ensemble de votre activité</span>
                </div>
                <div class="brief-stat">
                    <i class="fas fa-chart-simple"></i>
                    <span>Performances en temps réel</span>
                </div>
            </div>
        </div>
        <div class="welcome-decoration">
            <div class="floating-icons">
                <i class="fas fa-chart-line floating-icon-1"></i>
                <i class="fas fa-users floating-icon-2"></i>
                <i class="fas fa-shopping-cart floating-icon-3"></i>
                <i class="fas fa-money-bill-wave floating-icon-4"></i>
            </div>
        </div>
    </div>
</div>

<!-- ========== STATISTIQUES GÉNÉRALES ========== -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <h3>Clients</h3>
            <p><?= number_format($stats['clients'] ?? 0) ?></p>
            <small>Total inscrits</small>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-file-invoice"></i></div>
        <div class="stat-info">
            <h3>Devis</h3>
            <p><?= number_format($stats['devis'] ?? 0) ?></p>
            <small>En attente/validés</small>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
        <div class="stat-info">
            <h3>Commandes</h3>
            <p><?= number_format($stats['commandes'] ?? 0) ?></p>
            <small>Commandes passées</small>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
        <div class="stat-info">
            <h3>Chiffre d'affaires</h3>
            <p><?= number_format($stats['chiffre_affaires'] ?? 0, 0, ',', ' ') ?> FCFA</p>
            <small>Total général</small>
        </div>
    </div>
</div>

<!-- ========== DEUXIÈME LIGNE DE STATISTIQUES ========== -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-boxes"></i></div>
        <div class="stat-info">
            <h3>Produits en stock</h3>
            <p><?= number_format($stats['produits_stock'] ?? 0) ?></p>
            <small>Articles disponibles</small>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-truck"></i></div>
        <div class="stat-info">
            <h3>Commandes en cours</h3>
            <p><?= number_format($stats['commandes_en_cours'] ?? 0) ?></p>
            <small>À traiter</small>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
            <h3>Commandes livrées</h3>
            <p><?= number_format($stats['commandes_livrees'] ?? 0) ?></p>
            <small>Terminées</small>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
        <div class="stat-info">
            <h3>CA ce mois</h3>
            <p><?= number_format($stats['ca_mois'] ?? 0, 0, ',', ' ') ?> FCFA</p>
            <small><?= date('F Y') ?></small>
        </div>
    </div>
</div>

<!-- ========== GRAPHIQUE ET ACTIVITÉ RÉCENTE ========== -->
<div class="row">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-bar"></i> Évolution des ventes (6 derniers mois)</h3>
        </div>
        <div class="chart-container">
            <canvas id="salesChart" style="max-height: 300px;"></canvas>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-crown"></i> Top 5 produits les plus vendus</h3>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Quantité vendue</th>
                        <th>Chiffre d'affaires</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($topProduits)): ?>
                        <?php foreach ($topProduits as $produit): ?>
                        <tr>
                            <td><?= htmlspecialchars($produit['nom']) ?></td>
                            <td><?= $produit['quantite'] ?></td>
                            <td><?= number_format($produit['ca'], 0, ',', ' ') ?> FCFA</td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3">Aucune donnée disponible</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========== DERNIÈRES COMMANDES ========== -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-clock"></i> Dernières commandes</h3>
        <a href="/SITEECOFI/app/admin/admin.php?page=orders" class="btn">Voir toutes</a>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>N° Commande</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($commandes)): ?>
                    <?php foreach ($commandes as $cmd): ?>
                    <tr>
                        <td>#<?= htmlspecialchars($cmd['numero_commande'] ?? $cmd['id']) ?></td>
                        <td><?= htmlspecialchars($cmd['client_nom'] ?? $cmd['nom_client']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($cmd['date_commande'] ?? $cmd['created_at'])) ?></td>
                        <td><?= number_format($cmd['total_ttc'] ?? $cmd['montant'], 0, ',', ' ') ?> FCFA</td>
                        <td>
                            <?php 
                            $statut = $cmd['statut'] ?? 'en_attente';
                            $badgeClass = match($statut) {
                                'livree', 'livré' => 'badge-success',
                                'en_cours', 'en_attente' => 'badge-warning',
                                'annulee', 'annulé' => 'badge-danger',
                                default => 'badge-info'
                            };
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= ucfirst(str_replace('_', ' ', $statut)) ?></span>
                        </td>
                        <td>
                            <a href="/SITEECOFI/app/admin/admin.php?page=orders&action=view&id=<?= $cmd['id'] ?>" class="btn-small">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6">Aucune commande récente</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ========== ALERTES STOCK ET ACTIVITÉ ========== -->
<div class="row">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Stock faible</h3>
            <a href="/SITEECOFI/app/admin/admin.php?page=products" class="btn-small">Gérer</a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Stock actuel</th>
                        <th>Seuil d'alerte</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($stockFaible)): ?>
                        <?php foreach ($stockFaible as $produit): ?>
                        <tr>
                            <td><?= htmlspecialchars($produit['nom']) ?></td>
                            <td class="text-danger"><?= $produit['stock'] ?></td>
                            <td><?= $produit['seuil_alerte'] ?? 5 ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3">✅ Tous les stocks sont suffisants</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-user-plus"></i> Nouveaux clients</h3>
            <a href="/SITEECOFI/app/admin/admin.php?page=clients" class="btn-small">Voir tous</a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Email</th>
                        <th>Date d'inscription</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($nouveauxClients)): ?>
                        <?php foreach ($nouveauxClients as $client): ?>
                        <tr>
                            <td><?= htmlspecialchars($client['nom']) ?></td>
                            <td><?= htmlspecialchars($client['email']) ?></td>
                            <td><?= date('d/m/Y', strtotime($client['date_inscription'] ?? $client['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3">Aucun nouveau client</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========== STYLES POUR LE WELCOME HERO ========== -->
<style>
/* Welcome Hero Section */
.welcome-hero {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    border-radius: 24px;
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.2);
}

.welcome-bg-pattern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.5;
}

.welcome-content {
    position: relative;
    z-index: 2;
    padding: 35px 40px;
    display: flex;
    align-items: center;
    gap: 30px;
    flex-wrap: wrap;
}

.welcome-avatar {
    flex-shrink: 0;
}

.avatar-ring {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, var(--accent-color), #ff6b1a);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: pulse-ring 2s infinite;
}

.avatar-ring i {
    font-size: 3rem;
    color: white;
    animation: float 3s ease-in-out infinite;
}

.welcome-text {
    flex: 1;
}

.welcome-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 133, 51, 0.2);
    backdrop-filter: blur(10px);
    padding: 5px 15px;
    border-radius: 50px;
    font-size: 0.8rem;
    color: var(--accent-color);
    margin-bottom: 15px;
    border: 1px solid rgba(255, 133, 51, 0.3);
}

.welcome-title {
    font-size: 2.2rem;
    font-weight: 700;
    color: white;
    margin-bottom: 10px;
}

.welcome-title .highlight {
    background: linear-gradient(135deg, var(--accent-color), #ff6b1a);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    position: relative;
    display: inline-block;
}

.welcome-subtitle {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.welcome-subtitle i {
    color: var(--accent-color);
    margin-right: 5px;
}

.welcome-stats-brief {
    display: flex;
    gap: 20px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.brief-stat {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(5px);
    padding: 8px 18px;
    border-radius: 50px;
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.9);
}

.brief-stat i {
    color: var(--accent-color);
    font-size: 1rem;
}

.welcome-decoration {
    flex-shrink: 0;
}

.floating-icons {
    position: relative;
    width: 120px;
    height: 120px;
}

.floating-icons i {
    position: absolute;
    font-size: 1.5rem;
    color: rgba(255, 133, 51, 0.6);
}

.floating-icon-1 {
    top: 0;
    left: 20%;
    animation: float 4s ease-in-out infinite;
}

.floating-icon-2 {
    top: 30%;
    right: 0;
    animation: float 3.5s ease-in-out infinite 0.5s;
}

.floating-icon-3 {
    bottom: 0;
    left: 0;
    animation: float 4.5s ease-in-out infinite 1s;
}

.floating-icon-4 {
    bottom: 20%;
    right: 20%;
    animation: float 3s ease-in-out infinite 1.5s;
}

/* Animations */
@keyframes pulse-ring {
    0% {
        box-shadow: 0 0 0 0 rgba(255, 133, 51, 0.4);
    }
    70% {
        box-shadow: 0 0 0 15px rgba(255, 133, 51, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(255, 133, 51, 0);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

/* Responsive welcome */
@media (max-width: 768px) {
    .welcome-content {
        padding: 25px 20px;
        flex-direction: column;
        text-align: center;
    }
    
    .welcome-title {
        font-size: 1.6rem;
    }
    
    .avatar-ring {
        width: 70px;
        height: 70px;
    }
    
    .avatar-ring i {
        font-size: 2rem;
    }
    
    .welcome-stats-brief {
        justify-content: center;
    }
    
    .welcome-subtitle {
        justify-content: center;
    }
    
    .floating-icons {
        display: none;
    }
}

/* Card header styles */
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
}

.card-header h3 {
    font-size: 1.1rem;
    color: var(--primary-color);
    display: flex;
    align-items: center;
    gap: 8px;
}

.card-header h3 i {
    color: var(--accent-color);
}

.btn-small {
    padding: 6px 12px;
    background: var(--light-color);
    color: var(--primary-color);
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    transition: var(--transition);
}

.btn-small:hover {
    background: var(--accent-color);
    color: white;
}

.text-danger {
    color: #e74c3c;
    font-weight: bold;
}

.chart-container {
    position: relative;
    height: 300px;
}

.row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 25px;
    margin-bottom: 25px;
}

@media (max-width: 768px) {
    .row {
        grid-template-columns: 1fr;
    }
    
    .chart-container {
        height: 250px;
    }
}
</style>

<!-- ========== GRAPHIQUE AVEC CHART.JS ========== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($moisLabels ?? ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun']) ?>,
                datasets: [{
                    label: 'Chiffre d\'affaires (FCFA)',
                    data: <?= json_encode($ventesMensuelles ?? [0, 0, 0, 0, 0, 0]) ?>,
                    borderColor: '#FF8533',
                    backgroundColor: 'rgba(255, 133, 51, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#FF8533',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw.toLocaleString() + ' FCFA';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' FCFA';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>