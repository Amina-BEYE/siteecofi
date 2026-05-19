<?php

require_once __DIR__ . '/admin-functions.php';
require_admin();

$pdo = get_db_connection();

$adhesions = [];
$summary = [
    'total' => 0,
    'nouveau' => 0,
    'encours' => 0,
    'valide' => 0,
    'refuse' => 0,
];

try {
    $statistics = $pdo->query("SELECT status, COUNT(*) AS total FROM adhesions GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($statistics as $row) {
        $statusKey = strtolower(str_replace(' ', '', $row['status']));
        $summary[$statusKey] = (int) $row['total'];
        $summary['total'] += (int) $row['total'];
    }

    $stmt = $pdo->query('SELECT id, nom, prenom, telephone, email, cni, created_at, status, mode_paiement FROM adhesions ORDER BY created_at DESC');
    $adhesions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $exception) {
    $adhesions = [];
}

$flashMessage = get_flash_message();
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - ECOFI CRM</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header class="admin-header">
        <div>
            <h1>CRM ECOFI</h1>
            <p>Bienvenue, <?= sanitize($_SESSION['admin_name'] ?? 'Administrateur') ?></p>
        </div>
        <div class="admin-header-actions">
            <a class="admin-btn admin-btn-secondary" href="export-csv.php">Exporter CSV</a>
            <a class="admin-btn admin-btn-outline" href="logout.php">Se déconnecter</a>
        </div>
    </header>
    <section class="admin-summary-grid">
        <article class="admin-card">
            <h3>Toutes les demandes</h3>
            <strong><?= $summary['total'] ?></strong>
        </article>
        <article class="admin-card">
            <h3>Nouveaux</h3>
            <strong><?= $summary['nouveau'] ?></strong>
        </article>
        <article class="admin-card">
            <h3>En cours</h3>
            <strong><?= $summary['encours'] ?></strong>
        </article>
        <article class="admin-card">
            <h3>Validés</h3>
            <strong><?= $summary['valide'] ?></strong>
        </article>
        <article class="admin-card">
            <h3>Refusés</h3>
            <strong><?= $summary['refuse'] ?></strong>
        </article>
    </section>

    <?php if ($flashMessage): ?>
        <div class="admin-alert admin-alert-success"><?= sanitize($flashMessage) ?></div>
    <?php endif; ?>

    <section class="admin-table-section">
        <div class="admin-table-toolbar">
            <h2>Liste des demandes d’adhésion</h2>
        </div>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Téléphone</th>
                        <th>Email</th>
                        <th>N°CNI</th>
                        <th>Date</th>
                        <th>Mode paiem.</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($adhesions)): ?>
                    <tr><td colspan="9">Aucune demande trouvée.</td></tr>
                <?php else: ?>
                    <?php foreach ($adhesions as $adhesion): ?>
                        <tr>
                            <td><?= sanitize($adhesion['id']) ?></td>
                            <td><?= sanitize($adhesion['nom'] . ' ' . $adhesion['prenom']) ?></td>
                            <td><?= sanitize($adhesion['telephone']) ?></td>
                            <td><?= sanitize($adhesion['email']) ?></td>
                            <td><?= sanitize($adhesion['cni']) ?></td>
                            <td><?= sanitize($adhesion['created_at']) ?></td>
                            <td><?= sanitize($adhesion['mode_paiement']) ?></td>
                            <td><span class="status-badge status-<?= strtolower(str_replace(' ', '-', $adhesion['status'])) ?>"><?= sanitize($adhesion['status']) ?></span></td>
                            <td>
                                <a class="admin-action" href="view-demande.php?id=<?= sanitize($adhesion['id']) ?>">Voir</a>
                                <a class="admin-action admin-action-delete" href="delete-demande.php?id=<?= sanitize($adhesion['id']) ?>" onclick="return confirm('Supprimer cette demande ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</body>
</html>
