<?php
$users = $users ?? [];
$roles = $roles ?? ['admin' => 'Administrateur', 'manager' => 'Manager', 'agent' => 'Agent'];
$message = $message ?? null;
$messageType = $messageType ?? 'success';
$activeUsers = count(array_filter($users, static fn (array $user): bool => ($user['status'] ?? '') === 'active'));
$suspendedUsers = max(0, count($users) - $activeUsers);
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

<div class="auth-admin">
    <section class="auth-hero">
        <div>
            <span class="auth-kicker"><i class="fas fa-user-shield"></i> Sécurité admin</span>
            <h2>Authentification & rôles</h2>
            <p>Gérez les comptes, profils et mots de passe de l’espace d’administration ECOFI.</p>
        </div>
        <div class="auth-hero-metrics">
            <div><strong><?= count($users) ?></strong><span>Utilisateurs</span></div>
            <div><strong><?= $activeUsers ?></strong><span>Actifs</span></div>
            <div><strong><?= $suspendedUsers ?></strong><span>Suspendus</span></div>
        </div>
    </section>

    <section class="card admin-list-card auth-users-card">
        <div class="section-heading auth-list-heading">
            <div>
                <h2><i class="fas fa-users-gear"></i> Liste des utilisateurs</h2>
                <p>Les formulaires s’ouvrent maintenant en dialog pour garder la liste visible et rapide à parcourir.</p>
            </div>
            <button type="button" class="btn" onclick="openAuthDialog('createUserDialog')">
                <i class="fas fa-user-plus"></i>
                Nouvel utilisateur
            </button>
        </div>

        <div class="admin-list-toolbar">
            <label class="admin-search-box">
                <i class="fas fa-search"></i>
                <input
                    type="search"
                    class="admin-search-input"
                    data-admin-search
                    data-target="#usersTable tbody tr[data-search]"
                    placeholder="Rechercher par nom, email ou profil"
                    aria-label="Rechercher un accès"
                >
            </label>
        </div>

        <div class="table-container">
            <table id="usersTable" class="modern-table">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Messagerie</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Créé le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <?php
                            $userId = (int) ($user['id'] ?? 0);
                            $roleKey = (string) ($user['role'] ?? '');
                            $status = (string) ($user['status'] ?? '');
                            ?>
                            <tr data-search="<?= htmlspecialchars(($user['fullname'] ?? '') . ' ' . ($user['email'] ?? '') . ' ' . ($roles[$roleKey] ?? $roleKey) . ' ' . $status) ?>">
                                <td>
                                    <strong><?= htmlspecialchars($user['fullname'] ?? '-') ?></strong>
                                    <small><?= htmlspecialchars($user['email'] ?? '-') ?></small>
                                </td>
                                <td>
                                    <?php if (!empty($user['email_address'])): ?>
                                        <strong><?= htmlspecialchars($user['email_address']) ?></strong>
                                        <small>IMAP <?= !empty($user['has_imap_password']) ? 'configuré' : 'incomplet' ?> / SMTP <?= !empty($user['has_smtp_password']) ? 'configuré' : 'incomplet' ?></small>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Non configurée</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-info"><?= htmlspecialchars($roles[$roleKey] ?? $roleKey ?: 'Agent') ?></span>
                                </td>
                                <td>
                                    <?php if ($status === 'active'): ?>
                                        <span class="badge badge-success">Actif</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Suspendu</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($user['created_at'] ?? '-') ?></td>
                                <td>
                                    <div class="action-stack">
                                        <button type="button" class="btn btn-outline" onclick="openAuthDialog('editUserDialog<?= $userId ?>')">
                                            <i class="fas fa-pen"></i>
                                            Modifier
                                        </button>
                                        <button type="button" class="btn btn-outline" onclick="openAuthDialog('resetPasswordDialog<?= $userId ?>')">
                                            <i class="fas fa-key"></i>
                                            Mot de passe
                                        </button>
                                        <form method="post" action="adminPage.php?page=auth" class="inline-form" data-loading-text="Mise à jour du statut...">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="user_id" value="<?= $userId ?>">
                                            <input type="hidden" name="status" value="<?= $status === 'active' ? 'suspended' : 'active' ?>">
                                            <button type="submit" class="btn <?= $status === 'active' ? 'btn-danger' : 'btn-outline' ?>">
                                                <i class="fas <?= $status === 'active' ? 'fa-ban' : 'fa-check' ?>"></i>
                                                <?= $status === 'active' ? 'Suspendre' : 'Activer' ?>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                            <td colspan="6">Aucun utilisateur trouvé.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<div class="auth-dialog" id="createUserDialog" aria-hidden="true">
    <div class="auth-dialog-panel" role="dialog" aria-modal="true" aria-labelledby="createUserTitle">
        <button type="button" class="auth-dialog-close" onclick="closeAuthDialog('createUserDialog')" aria-label="Fermer">&times;</button>
        <div class="auth-dialog-head">
            <span><i class="fas fa-user-plus"></i> Nouvel accès</span>
            <h3 id="createUserTitle">Créer un utilisateur</h3>
            <p>Ajoutez un compte et attribuez-lui un profil d’administration.</p>
        </div>
        <form method="post" action="adminPage.php?page=auth" class="admin-form auth-dialog-form" data-loading-text="Création de l’utilisateur..." enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_user">
            <div class="form-grid dialog-grid">
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
                        <?php foreach ($roles as $roleKey => $roleLabel): ?>
                            <option value="<?= htmlspecialchars($roleKey) ?>"><?= htmlspecialchars($roleLabel) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Mot de passe" minlength="6" required>
                </div>
            </div>
            <div class="mail-config-section">
                <h4><i class="fas fa-envelope"></i> Configuration messagerie</h4>
                <div class="form-grid dialog-grid">
                    <div class="form-group full-span">
                        <label>Importer une configuration O2switch mobileconfig</label>
                        <input type="file" name="mail_config_file" class="form-control" accept=".mobileconfig,.xml,application/xml,text/xml">
                    </div>
                    <div class="form-group full-span">
                        <label>Importer une configuration JSON</label>
                        <input type="file" name="imap_config" class="form-control" accept=".json,application/json">
                    </div>
                    <div class="form-group">
                        <label>Adresse email</label>
                        <input type="email" name="email_address" class="form-control" placeholder="contact@mondomaine.com">
                    </div>
                    <div class="form-group">
                        <label>Serveur IMAP</label>
                        <input type="text" name="imap_host" class="form-control" value="mail.mondomaine.com">
                    </div>
                    <div class="form-group">
                        <label>Port IMAP</label>
                        <input type="number" name="imap_port" class="form-control" value="993" min="1" max="65535">
                    </div>
                    <div class="form-group">
                        <label>Sécurité IMAP</label>
                        <select name="imap_encryption" class="form-control">
                            <option value="ssl" selected>SSL</option>
                            <option value="tls">TLS</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Identifiant IMAP</label>
                        <input type="text" name="imap_username" class="form-control" placeholder="contact@mondomaine.com">
                    </div>
                    <div class="form-group">
                        <label>Mot de passe IMAP</label>
                        <input type="password" name="imap_password" class="form-control" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label>Serveur SMTP</label>
                        <input type="text" name="smtp_host" class="form-control" value="mail.mondomaine.com">
                    </div>
                    <div class="form-group">
                        <label>Port SMTP</label>
                        <input type="number" name="smtp_port" class="form-control" value="465" min="1" max="65535">
                    </div>
                    <div class="form-group">
                        <label>Sécurité SMTP</label>
                        <select name="smtp_encryption" class="form-control">
                            <option value="ssl" selected>SSL</option>
                            <option value="tls">TLS</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Identifiant SMTP</label>
                        <input type="text" name="smtp_username" class="form-control" placeholder="contact@mondomaine.com">
                    </div>
                    <div class="form-group">
                        <label>Mot de passe SMTP</label>
                        <input type="password" name="smtp_password" class="form-control" autocomplete="new-password">
                    </div>
                </div>
            </div>
            <div class="auth-dialog-actions">
                <button type="button" class="btn btn-outline" onclick="closeAuthDialog('createUserDialog')">Annuler</button>
                <button type="submit" name="test_mail_config" value="1" class="btn btn-outline" formnovalidate>
                    <i class="fas fa-plug"></i>
                    Tester la connexion
                </button>
                <button type="submit" class="btn"><i class="fas fa-save"></i> Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<?php foreach ($users as $user): ?>
    <?php
    $userId = (int) ($user['id'] ?? 0);
    $roleKey = (string) ($user['role'] ?? '');
    $status = (string) ($user['status'] ?? 'active');
    ?>
    <div class="auth-dialog" id="editUserDialog<?= $userId ?>" aria-hidden="true">
        <div class="auth-dialog-panel" role="dialog" aria-modal="true" aria-labelledby="editUserTitle<?= $userId ?>">
            <button type="button" class="auth-dialog-close" onclick="closeAuthDialog('editUserDialog<?= $userId ?>')" aria-label="Fermer">&times;</button>
            <div class="auth-dialog-head">
                <span><i class="fas fa-pen"></i> Modification</span>
                <h3 id="editUserTitle<?= $userId ?>">Modifier l’utilisateur</h3>
                <p><?= htmlspecialchars($user['fullname'] ?? '-') ?> - <?= htmlspecialchars($user['email'] ?? '-') ?></p>
            </div>
            <form method="post" action="adminPage.php?page=auth" class="admin-form auth-dialog-form" data-loading-text="Modification de l’utilisateur..." enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" name="user_id" value="<?= $userId ?>">
                <div class="form-grid dialog-grid">
                    <div class="form-group">
                        <label>Nom complet</label>
                        <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Rôle</label>
                        <select name="role" class="form-control">
                            <?php foreach ($roles as $optionKey => $roleLabel): ?>
                                <option value="<?= htmlspecialchars($optionKey) ?>" <?= $roleKey === $optionKey ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($roleLabel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Statut</label>
                        <select name="status" class="form-control">
                            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Actif</option>
                            <option value="suspended" <?= $status === 'suspended' ? 'selected' : '' ?>>Suspendu</option>
                        </select>
                    </div>
                </div>
                <div class="mail-config-section">
                    <h4><i class="fas fa-envelope"></i> Configuration messagerie</h4>
                    <div class="form-grid dialog-grid">
                        <div class="form-group full-span">
                            <label>Importer une configuration O2switch mobileconfig</label>
                            <input type="file" name="mail_config_file" class="form-control" accept=".mobileconfig,.xml,application/xml,text/xml">
                        </div>
                        <div class="form-group full-span">
                            <label>Importer une configuration JSON</label>
                            <input type="file" name="imap_config" class="form-control" accept=".json,application/json">
                        </div>
                        <div class="form-group">
                            <label>Adresse email</label>
                            <input type="email" name="email_address" class="form-control" value="<?= htmlspecialchars($user['email_address'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Serveur IMAP</label>
                            <input type="text" name="imap_host" class="form-control" value="<?= htmlspecialchars($user['imap_host'] ?? 'mail.mondomaine.com') ?>">
                        </div>
                        <div class="form-group">
                            <label>Port IMAP</label>
                            <input type="number" name="imap_port" class="form-control" value="<?= htmlspecialchars((string) ($user['imap_port'] ?? '993')) ?>" min="1" max="65535">
                        </div>
                        <div class="form-group">
                            <label>Sécurité IMAP</label>
                            <select name="imap_encryption" class="form-control">
                                <option value="ssl" <?= ($user['imap_encryption'] ?? 'ssl') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                <option value="tls" <?= ($user['imap_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Identifiant IMAP</label>
                            <input type="text" name="imap_username" class="form-control" value="<?= htmlspecialchars($user['imap_username'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Mot de passe IMAP</label>
                            <input type="password" name="imap_password" class="form-control" placeholder="<?= !empty($user['has_imap_password']) ? 'Déjà enregistré - remplir pour remplacer' : '' ?>" autocomplete="new-password">
                        </div>
                        <div class="form-group">
                            <label>Serveur SMTP</label>
                            <input type="text" name="smtp_host" class="form-control" value="<?= htmlspecialchars($user['smtp_host'] ?? 'mail.mondomaine.com') ?>">
                        </div>
                        <div class="form-group">
                            <label>Port SMTP</label>
                            <input type="number" name="smtp_port" class="form-control" value="<?= htmlspecialchars((string) ($user['smtp_port'] ?? '465')) ?>" min="1" max="65535">
                        </div>
                        <div class="form-group">
                            <label>Sécurité SMTP</label>
                            <select name="smtp_encryption" class="form-control">
                                <option value="ssl" <?= ($user['smtp_encryption'] ?? 'ssl') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                <option value="tls" <?= ($user['smtp_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Identifiant SMTP</label>
                            <input type="text" name="smtp_username" class="form-control" value="<?= htmlspecialchars($user['smtp_username'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Mot de passe SMTP</label>
                            <input type="password" name="smtp_password" class="form-control" placeholder="<?= !empty($user['has_smtp_password']) ? 'Déjà enregistré - remplir pour remplacer' : '' ?>" autocomplete="new-password">
                        </div>
                    </div>
                </div>
                <div class="auth-dialog-actions">
                    <button type="button" class="btn btn-outline" onclick="closeAuthDialog('editUserDialog<?= $userId ?>')">Annuler</button>
                    <button type="submit" name="test_mail_config" value="1" class="btn btn-outline" formnovalidate>
                        <i class="fas fa-plug"></i>
                        Tester la connexion
                    </button>
                    <button type="submit" class="btn"><i class="fas fa-save"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <div class="auth-dialog" id="resetPasswordDialog<?= $userId ?>" aria-hidden="true">
        <div class="auth-dialog-panel auth-dialog-panel--small" role="dialog" aria-modal="true" aria-labelledby="resetPasswordTitle<?= $userId ?>">
            <button type="button" class="auth-dialog-close" onclick="closeAuthDialog('resetPasswordDialog<?= $userId ?>')" aria-label="Fermer">&times;</button>
            <div class="auth-dialog-head">
                <span><i class="fas fa-key"></i> Sécurité</span>
                <h3 id="resetPasswordTitle<?= $userId ?>">Réinitialiser le mot de passe</h3>
                <p><?= htmlspecialchars($user['fullname'] ?? '-') ?></p>
            </div>
            <form method="post" action="adminPage.php?page=auth" class="admin-form auth-dialog-form" data-loading-text="Réinitialisation du mot de passe...">
                <input type="hidden" name="action" value="reset_password">
                <input type="hidden" name="user_id" value="<?= $userId ?>">
                <div class="form-group">
                    <label>Nouveau mot de passe</label>
                    <input type="password" name="new_password" class="form-control" minlength="6" required>
                </div>
                <div class="auth-dialog-actions">
                    <button type="button" class="btn btn-outline" onclick="closeAuthDialog('resetPasswordDialog<?= $userId ?>')">Annuler</button>
                    <button type="submit" class="btn"><i class="fas fa-key"></i> Réinitialiser</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<script>
function closeDialog() {
    const dialog = document.getElementById('dialogOverlay');
    if (dialog) {
        dialog.style.display = 'none';
    }
}

function openAuthDialog(id) {
    const dialog = document.getElementById(id);
    if (!dialog) return;
    dialog.classList.add('is-open');
    dialog.setAttribute('aria-hidden', 'false');
    const firstField = dialog.querySelector('input, select, textarea, button');
    if (firstField) {
        setTimeout(() => firstField.focus(), 80);
    }
}

function closeAuthDialog(id) {
    const dialog = document.getElementById(id);
    if (!dialog) return;
    dialog.classList.remove('is-open');
    dialog.setAttribute('aria-hidden', 'true');
}

document.querySelectorAll('.auth-dialog').forEach(dialog => {
    dialog.addEventListener('click', event => {
        if (event.target === dialog) {
            closeAuthDialog(dialog.id);
        }
    });
});

document.addEventListener('keydown', event => {
    if (event.key !== 'Escape') return;
    document.querySelectorAll('.auth-dialog.is-open').forEach(dialog => closeAuthDialog(dialog.id));
});

function fillMailConfig(form, config) {
    const keys = [
        'email_address', 'imap_host', 'imap_port', 'imap_encryption', 'imap_username', 'imap_password',
        'smtp_host', 'smtp_port', 'smtp_encryption', 'smtp_username', 'smtp_password'
    ];

    keys.forEach(key => {
        if (typeof config[key] === 'undefined' || config[key] === null) return;
        const field = form.querySelector(`[name="${key}"]`);
        if (field) {
            field.value = config[key];
        }
    });
}

function applyO2switchDefaults(form) {
    const emailField = form.querySelector('[name="email_address"]');
    const email = emailField ? emailField.value.trim() : '';
    const domain = email.includes('@') ? email.split('@').pop().trim() : '';

    if (!domain) return;

    const host = 'mail.' + domain;
    const defaults = {
        imap_host: host,
        imap_port: '993',
        imap_encryption: 'ssl',
        imap_username: email,
        smtp_host: host,
        smtp_port: '465',
        smtp_encryption: 'ssl',
        smtp_username: email
    };

    Object.entries(defaults).forEach(([key, value]) => {
        const field = form.querySelector(`[name="${key}"]`);
        if (field && !field.value.trim()) {
            field.value = value;
        }
    });
}

document.querySelectorAll('.auth-dialog-form').forEach(form => {
    const emailField = form.querySelector('[name="email_address"]');
    if (emailField) {
        emailField.addEventListener('blur', () => applyO2switchDefaults(form));
    }

    const jsonField = form.querySelector('[name="imap_config"]');
    if (jsonField) {
    jsonField.addEventListener('change', () => {
        const file = jsonField.files && jsonField.files[0];
        if (!file) return;

        if (!file.name.toLowerCase().endsWith('.json') || file.size > 64 * 1024) {
            alert('Veuillez sélectionner un fichier JSON valide de moins de 64 Ko.');
            jsonField.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = () => {
            try {
                const config = JSON.parse(String(reader.result || '{}'));
                fillMailConfig(form, config);
                applyO2switchDefaults(form);
            } catch (error) {
                alert('Le fichier sélectionné n’est pas un JSON valide.');
                jsonField.value = '';
            }
        };
        reader.readAsText(file);
    });
    }

    const mobileConfigField = form.querySelector('[name="mail_config_file"]');
    if (mobileConfigField) {
        mobileConfigField.addEventListener('change', () => {
            const file = mobileConfigField.files && mobileConfigField.files[0];
            if (!file) return;

            const validExtension = file.name.toLowerCase().endsWith('.mobileconfig') || file.name.toLowerCase().endsWith('.xml');
            if (!validExtension || file.size > 256 * 1024) {
                alert('Veuillez sélectionner un fichier .mobileconfig ou .xml de moins de 256 Ko.');
                mobileConfigField.value = '';
            }
        });
    }
});
</script>
