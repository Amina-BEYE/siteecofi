<?php
$users = $users ?? [];
$message = $message ?? null;
$messageType = $messageType ?? 'success';
?>

<?php if (!empty($message)): ?>
    <div class="dialog-overlay" id="dialogOverlay">
        <div class="dialog-box <?= $messageType === 'error' ? 'error' : 'success' ?>">
            <div class="dialog-icon">
                <i class="fas <?= $messageType === 'error' ? 'fa-exclamation-triangle' : 'fa-check-circle' ?>"></i>
            </div>

            <h3><?= $messageType === 'error' ? 'Erreur' : 'Succès' ?></h3>
            <p><?= htmlspecialchars($message) ?></p>

            <div class="dialog-actions">
                <button class="btn" type="button" onclick="closeDialog()">OK</button>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="card">
    <h2 style="margin-bottom: 20px;">
        <i class="fas fa-user-shield" style="color: var(--accent-color); margin-right: 10px;"></i>
        Authentification & rôles
    </h2>

    <p style="margin-bottom: 25px; color: var(--gray-color);">
        Gérez les accès administrateurs, les rôles et les permissions.
    </p>

    <div class="row">
        <div class="card">
            <h3 style="margin-bottom: 15px;">Créer un utilisateur</h3>

            <form method="post" action="adminPage.php?page=auth">
                <input type="hidden" name="action" value="add_user">

                <div class="form-group">
                    <label for="fullname">Nom complet</label>
                    <input type="text" id="fullname" name="fullname" class="form-control" placeholder="Nom complet" required>
                </div>

                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="email@exemple.com" required>
                </div>

                <div class="form-group">
                    <label for="role">Rôle</label>
                    <select id="role" name="role" class="form-control">
                        <option value="admin">Administrateur</option>
                        <option value="manager">Manager</option>
                        <option value="agent">Agent</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Mot de passe" required>
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-save"></i>
                    Enregistrer
                </button>
            </form>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 15px;">Liste des accès</h3>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['fullname']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <?php if ($user['role'] === 'admin'): ?>
                                            <span class="badge badge-info">Administrateur</span>
                                        <?php elseif ($user['role'] === 'manager'): ?>
                                            <span class="badge badge-warning">Manager</span>
                                        <?php else: ?>
                                            <span class="badge badge-info">Agent</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($user['status'] === 'active'): ?>
                                            <span class="badge badge-success">Actif</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Suspendu</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="post" action="adminPage.php?page=auth" style="display:inline;">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
                                            <input type="hidden" name="status" value="<?= $user['status'] === 'active' ? 'suspended' : 'active' ?>">

                                            <button type="submit" class="btn <?= $user['status'] === 'active' ? 'btn-danger' : 'btn-outline' ?>">
                                                <?= $user['status'] === 'active' ? 'Suspendre' : 'Activer' ?>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">Aucun utilisateur trouvé.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function closeDialog() {
    const dialog = document.getElementById('dialogOverlay');
    if (dialog) {
        dialog.style.display = 'none';
    }
}
</script>