<?php
$clients = $clients ?? [];
$message = $message ?? null;
$messageType = $messageType ?? 'success';
?>

<?php if (!empty($message)): ?>
    <div class="dialog-overlay" id="dialogOverlay">
        <div class="dialog-box <?= $messageType === 'error' ? 'error' : 'success' ?>">
            
            <div class="dialog-icon">
                <i class="fas <?= $messageType === 'error' ? 'fa-exclamation-triangle' : 'fa-check-circle' ?>"></i>
            </div>

            <h3>
                <?= $messageType === 'error' ? 'Erreur' : 'Succès' ?>
            </h3>

            <p><?= htmlspecialchars($message) ?></p>

            <div class="dialog-actions">
                <button class="btn" onclick="closeDialog()">OK</button>
            </div>
        </div>
    </div>
<?php endif; ?>

<section class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <h3>Total clients</h3>
            <p><?= count($clients) ?></p>
        </div>
    </div>
</section>

<section class="row">
    <div class="card">
        <h2 style="margin-bottom: 20px;">Ajouter un client</h2>

        <form method="POST" action="adminPage.php?page=clients">
            <input type="hidden" name="action" value="add_client">

            <div class="form-group">
                <label for="nom">Nom du client</label>
                <input
                    type="text"
                    id="nom"
                    name="nom"
                    class="form-control"
                    placeholder="Nom ou raison sociale"
                    required
                >
            </div>

            <div class="form-group">
                <label for="email">Adresse email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    placeholder="client@email.com"
                    required
                >
            </div>

            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input
                    type="text"
                    id="telephone"
                    name="telephone"
                    class="form-control"
                    placeholder="+225 00 00 00 00 00"
                    required
                >
            </div>

            <button type="submit" class="btn">
                <i class="fas fa-plus"></i>
                Ajouter
            </button>
        </form>
    </div>
</section>

<section class="card">
    <h2 style="margin-bottom: 20px;">Liste des clients</h2>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Date création</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($clients)): ?>
                    <?php foreach ($clients as $client): ?>
                        <tr>
                            <td><?= (int) $client['id'] ?></td>
                            <td><?= htmlspecialchars($client['nom']) ?></td>
                            <td><?= htmlspecialchars($client['email']) ?></td>
                            <td><?= htmlspecialchars($client['telephone']) ?></td>
                            <td><?= htmlspecialchars($client['created_at'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Aucun client trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>