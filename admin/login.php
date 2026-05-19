<?php

require_once __DIR__ . '/admin-functions.php';

$error = '';

if (is_admin_logged_in()) {
    header('Location: ../app/admin/Views/adminPage.php?page=dashboard');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!$email || trim($password) === '') {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        try {
            $pdo = get_db_connection();
        } catch (Throwable $exception) {
            error_log('[admin_login] DB connection error: ' . $exception->getMessage());
            $error = 'Erreur de connexion à la base de données.';
        }

        if ($error === '') {
            try {
                $admin = find_admin_user($pdo, $email);

                if (!$admin || !password_verify($password, $admin['password'])) {
                    $error = 'Email ou mot de passe incorrect.';
                } elseif (($admin['status'] ?? 'active') !== 'active') {
                    $error = 'Votre compte administrateur est désactivé.';
                } else {
                    login_admin($admin);
                    header('Location: ../app/admin/Views/adminPage.php?page=dashboard');
                    exit;
                }
            } catch (Throwable $exception) {
                error_log('[admin_login] Login SQL error: ' . $exception->getMessage());
                $error = 'Impossible de vérifier les identifiants administrateur. Vérifiez que la table users existe avec les colonnes fullname, email, password, role et status.';
            }
        }
    }
}
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - ECOFI CRM</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <main class="admin-login-page">
        <div class="admin-login-card">
            <div class="admin-login-brand">
                <img src="../app/IMG/logo-ecofi.png" alt="ECOFI" class="admin-logo">
                <h1>Espace Admin ECOFI</h1>
                <p>Authentifiez-vous pour accéder à la gestion CRM des demandes d’adhésion.</p>
            </div>
            <?php if ($error): ?>
                <div class="admin-alert admin-alert-error"><?= sanitize($error) ?></div>
            <?php endif; ?>
            <form method="POST" action="login.php" class="admin-login-form">
                <label for="email">Email admin</label>
                <input id="email" name="email" type="email" placeholder="admin@ecofi.sn" required>

                <label for="password">Mot de passe</label>
                <input id="password" name="password" type="password" placeholder="Mot de passe" required>

                <button type="submit" class="admin-btn admin-btn-primary">Se connecter</button>
            </form>
        </div>
    </main>
</body>
</html>
