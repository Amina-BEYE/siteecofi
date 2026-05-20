<?php
$roles = $roles ?? [];
$pages = $pages ?? [];
$accessMatrix = $accessMatrix ?? [];
?>

<div class="access-dashboard">
<div class="card access-page">
    <div class="access-hero">
        <div>
            <span class="access-kicker"><i class="fas fa-lock"></i> Sécurité admin</span>
            <h2>Gestion des accès par profil</h2>
            <p>Autorisez ou bloquez les pages de l’administration pour chaque rôle. Les changements sont appliqués au menu et à l’accès direct par URL.</p>
        </div>
        <a class="btn btn-outline" href="adminPage.php?page=auth">
            <i class="fas fa-user-shield"></i>
            Utilisateurs
        </a>
    </div>

    <form method="post" action="adminPage.php?page=access-control" data-loading-text="Mise à jour des accès...">
        <input type="hidden" name="action" value="save_access">
        <div class="access-grid">
            <?php foreach ($roles as $roleKey => $roleLabel): ?>
                <section class="access-role-card">
                    <div class="access-role-head">
                        <div>
                            <strong><?= htmlspecialchars($roleLabel) ?></strong>
                            <span><?= htmlspecialchars($roleKey) ?></span>
                        </div>
                        <i class="fas <?= $roleKey === 'admin' ? 'fa-user-cog' : ($roleKey === 'manager' ? 'fa-user-tie' : 'fa-user') ?>"></i>
                    </div>

                    <div class="access-list">
                        <?php foreach ($pages as $pageKey => $pageConfig): ?>
                            <?php
                            $checked = !empty($accessMatrix[$roleKey][$pageKey]);
                            $locked = $roleKey === 'admin' && in_array($pageKey, ['dashboard', 'access-control'], true);
                            ?>
                            <label class="access-toggle <?= $checked ? 'is-on' : '' ?> <?= $locked ? 'is-locked' : '' ?>">
                                <span>
                                    <i class="fas <?= htmlspecialchars($pageConfig['icon'] ?? 'fa-circle') ?>"></i>
                                    <?= htmlspecialchars($pageConfig['label'] ?? $pageKey) ?>
                                </span>
                                <input
                                    type="checkbox"
                                    name="access[<?= htmlspecialchars($roleKey) ?>][<?= htmlspecialchars($pageKey) ?>]"
                                    value="1"
                                    <?= $checked ? 'checked' : '' ?>
                                    <?= $locked ? 'disabled' : '' ?>
                                >
                                <?php if ($locked): ?>
                                    <input type="hidden" name="access[<?= htmlspecialchars($roleKey) ?>][<?= htmlspecialchars($pageKey) ?>]" value="1">
                                <?php endif; ?>
                                <em><?= $checked ? 'Oui' : 'Non' ?></em>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>

        <div class="access-actions">
            <button type="submit" class="btn">
                <i class="fas fa-save"></i>
                Enregistrer les accès
            </button>
        </div>
    </form>
</div>

<div class="card access-create-card">
    <div>
        <span class="access-kicker"><i class="fas fa-user-plus"></i> Nouveau profil</span>
        <h3>Ajouter un profil</h3>
        <p>Créez un nouveau rôle, puis cochez ses droits dans la matrice d’accès.</p>
    </div>
    <form method="post" action="adminPage.php?page=access-control" class="access-create-form" data-loading-text="Création du profil...">
        <input type="hidden" name="action" value="add_role">
        <label>
            Code profil
            <input class="form-control" name="role_key" placeholder="ex: commercial" required>
        </label>
        <label>
            Libellé
            <input class="form-control" name="role_label" placeholder="Commercial" required>
        </label>
        <button type="submit" class="btn">
            <i class="fas fa-plus"></i>
            Ajouter
        </button>
    </form>
</div>
</div>

<script>
document.querySelectorAll('.access-toggle input[type="checkbox"]').forEach((input) => {
    input.addEventListener('change', () => {
        const row = input.closest('.access-toggle');
        const label = row ? row.querySelector('em') : null;

        if (!row || !label) return;

        row.classList.toggle('is-on', input.checked);
        label.textContent = input.checked ? 'Oui' : 'Non';
    });
});
</script>
