<?php
$profileUser = $profileUser ?? null;
?>

<div class="card">
    <div class="section-heading">
        <div>
            <h2><i class="fas fa-user-gear"></i> Mon profil</h2>
            <p>Gérez les informations de votre compte administrateur.</p>
        </div>
    </div>

    <div class="profile-layout">
        <div class="profile-summary">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <h3><?= htmlspecialchars($profileUser['fullname'] ?? ($_SESSION['admin_name'] ?? 'Admin ECOFI')) ?></h3>
            <p><?= htmlspecialchars($profileUser['email'] ?? ($_SESSION['admin_email'] ?? '')) ?></p>
            <span class="badge badge-info"><?= htmlspecialchars($profileUser['role'] ?? ($_SESSION['admin_role'] ?? 'admin')) ?></span>
        </div>

        <div class="card admin-form-card">
            <h3>Changer le mot de passe</h3>
            <form method="post" action="adminPage.php?page=profile" class="admin-form" data-loading-text="Modification du mot de passe...">
                <div class="form-group">
                    <label for="current_password">Mot de passe actuel</label>
                    <input type="password" id="current_password" name="current_password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="new_password">Nouveau mot de passe</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" minlength="6" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" minlength="6" required>
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-key"></i>
                    Modifier mon mot de passe
                </button>
            </form>
        </div>
    </div>
</div>
