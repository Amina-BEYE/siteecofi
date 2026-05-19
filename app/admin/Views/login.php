<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../Core/Database.php';

use App\Core\Database;

$error = '';

try {
    $pdo = Database::getConnection();
} catch (Throwable $e) {
    die('ERREUR DB LOGIN : ' . $e->getMessage());
}

$host = $_SERVER['HTTP_HOST'] ?? '';

$isLocalDev = true; // Forcer le mode développement pour les tests locaux

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        if ($isLocalDev && $email === 'admin@ecofi.sn' && $password === 'admin') {
            $_SESSION['admin_id'] = 0;
            $_SESSION['admin_name'] = 'Administrateur DEV';
            $_SESSION['admin_email'] = 'admin@ecofi.sn';
            $_SESSION['admin_role'] = 'admin';

            header('Location: adminPage.php?page=dashboard');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Adresse email invalide.';
        } else {
            try {
                $stmt = $pdo->prepare("
                    SELECT id, fullname, email, password, role, status
                    FROM users
                    WHERE email = :email
                    LIMIT 1
                ");

                $stmt->execute([
                    ':email' => $email
                ]);

                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user) {
                    $error = 'Email ou mot de passe incorrect.';
                } elseif (($user['status'] ?? 'active') !== 'active') {
                    $error = 'Votre compte est suspendu.';
                } elseif (!password_verify($password, $user['password'])) {
                    $error = 'Email ou mot de passe incorrect.';
                } else {
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_name'] = $user['fullname'];
                    $_SESSION['admin_email'] = $user['email'];
                    $_SESSION['admin_role'] = $user['role'];

                    header('Location: adminPage.php?page=dashboard');
                    exit;
                }
            } catch (PDOException $e) {
                die('ERREUR LOGIN SQL : ' . $e->getMessage());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - ECOFI Administration</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-logo">
                <img src="../../IMG/logo-ecofi.png" alt="ECOFI" class="logo-img">
            </div>

            <div class="login-title">
                <h2>Connexion</h2>
                <p>Connectez-vous à votre espace d'administration</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label>Mot de passe</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" required>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-arrow-right-to-bracket"></i>
                    Se connecter
                </button>
            </form>
        </div>
    </div>
</body>
</html>
