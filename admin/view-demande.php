<?php

require_once __DIR__ . '/admin-functions.php';
require_admin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: dashboard.php');
    exit;
}

$pdo = get_db_connection();
$adhesion = null;
$notes = [];
$message = null;

try {
    $stmt = $pdo->prepare('SELECT * FROM adhesions WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $adhesion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$adhesion) {
        header('Location: dashboard.php');
        exit;
    }

    $noteStmt = $pdo->prepare('SELECT n.*, COALESCE(u.fullname, a.fullname, "Administrateur") AS admin_name FROM admin_notes n LEFT JOIN users u ON n.admin_id = u.id LEFT JOIN admins a ON n.admin_id = a.id WHERE n.adhesion_id = :id ORDER BY n.created_at DESC');
    $noteStmt->execute([':id' => $id]);
    $notes = $noteStmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($_GET['success'])) {
        $message = 'Les modifications ont été enregistrées.';
    }
} catch (Throwable $exception) {
    header('Location: dashboard.php');
    exit;
}
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la demande - ECOFI CRM</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header class="admin-header">
        <div>
            <h1>Demande #<?= sanitize($adhesion['id']) ?></h1>
            <p>Utilisateur : <?= sanitize($adhesion['nom'] . ' ' . $adhesion['prenom']) ?></p>
        </div>
        <div class="admin-header-actions">
            <a class="admin-btn admin-btn-secondary" href="dashboard.php">Retour au tableau</a>
            <a class="admin-btn admin-btn-outline" href="logout.php">Se déconnecter</a>
        </div>
    </header>

    <?php if ($message): ?>
        <div class="admin-alert admin-alert-success"><?= sanitize($message) ?></div>
    <?php endif; ?>

    <section class="admin-view-grid">
        <article class="admin-card admin-details-card">
            <h2>Informations de l’adhérent</h2>
            <dl class="admin-detail-list">
                <dt>Nom complet</dt><dd><?= sanitize($adhesion['nom'] . ' ' . $adhesion['prenom']) ?></dd>
                <dt>Date de naissance</dt><dd><?= sanitize($adhesion['date_naissance']) ?></dd>
                <dt>Lieu de naissance</dt><dd><?= sanitize($adhesion['lieu_naissance']) ?></dd>
                <dt>Adresse</dt><dd><?= sanitize($adhesion['adresse']) ?></dd>
                <dt>Téléphone</dt><dd><?= sanitize($adhesion['telephone']) ?></dd>
                <dt>Email</dt><dd><?= sanitize($adhesion['email']) ?></dd>
                <dt>N°CNI / Passeport</dt><dd><?= sanitize($adhesion['cni']) ?></dd>
                <dt>Mode de paiement</dt><dd><?= sanitize($adhesion['mode_paiement']) ?></dd>
                <dt>Statut</dt><dd><span class="status-badge status-<?= strtolower(str_replace(' ', '-', $adhesion['status'])) ?>"><?= sanitize($adhesion['status']) ?></span></dd>
                <dt>Date de demande</dt><dd><?= sanitize($adhesion['created_at']) ?></dd>
            </dl>
            <div class="admin-card-section">
                <h3>Message du demandeur</h3>
                <p><?= nl2br(sanitize($adhesion['message'] ?: 'Aucun message fourni.')) ?></p>
            </div>
        </article>

        <aside class="admin-card admin-sidebar-card">
            <h2>Actions</h2>
            <form method="POST" action="update-status.php" class="admin-form-inline">
                <input type="hidden" name="id" value="<?= sanitize($adhesion['id']) ?>">

                <label for="status">Changer le statut</label>
                <select id="status" name="status" required>
                    <?php foreach (['Nouveau', 'En cours', 'Validé', 'Refusé'] as $statusOption): ?>
                        <option value="<?= sanitize($statusOption) ?>" <?= $adhesion['status'] === $statusOption ? 'selected' : '' ?>><?= sanitize($statusOption) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="note">Note interne</label>
                <textarea id="note" name="note" rows="4" placeholder="Ajouter une note interne..."></textarea>

                <button type="submit" class="admin-btn admin-btn-primary">Enregistrer</button>
            </form>

            <form method="POST" action="delete-demande.php" onsubmit="return confirm('Supprimer définitivement cette demande ?');">
                <input type="hidden" name="id" value="<?= sanitize($adhesion['id']) ?>">
                <button type="submit" class="admin-btn admin-btn-delete">Supprimer la demande</button>
            </form>
        </aside>
    </section>

    <section class="admin-card admin-notes-card">
        <h2>Notes internes</h2>
        <?php if (empty($notes)): ?>
            <p>Aucune note interne pour cette demande.</p>
        <?php else: ?>
            <ul class="admin-note-list">
                <?php foreach ($notes as $noteItem): ?>
                    <li>
                        <strong><?= sanitize($noteItem['admin_name'] ?? 'Admin') ?></strong>
                        <span><?= sanitize($noteItem['created_at']) ?></span>
                        <p><?= nl2br(sanitize($noteItem['note'])) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</body>
</html>
