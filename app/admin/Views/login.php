<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../Core/Database.php';
require_once __DIR__ . '/../Models/AccessControlModel.php';

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
            (new AccessControlModel())->loadSessionFeatures('admin');

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
                    (new AccessControlModel())->loadSessionFeatures((string) $user['role']);

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
    <style>
        :root {
            --accent: #ff8533;
            --accent-dark: #e66f1d;
            --ink: #1d2939;
            --muted: #667085;
            --line: #e4e7ec;
            --surface: #ffffff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--ink);
            background:
                linear-gradient(135deg, rgba(17, 24, 39, 0.86), rgba(31, 41, 55, 0.72)),
                url("../../IMG/HERO.jpeg") center/cover no-repeat;
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .login-wrapper {
            width: min(100%, 460px);
        }

        .login-card {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 18px;
            box-shadow: 0 28px 80px rgba(0, 0, 0, 0.28);
            padding: 34px;
        }

        .login-logo {
            width: 92px;
            height: 92px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 14px 36px rgba(255, 133, 51, 0.24);
            border: 1px solid rgba(255, 133, 51, 0.2);
        }

        .logo-img {
            width: 72px;
            height: 72px;
            object-fit: contain;
            border-radius: 50%;
        }

        .login-title {
            text-align: center;
            margin-bottom: 26px;
        }

        .login-title h2 {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 8px;
            color: #111827;
        }

        .login-title p {
            color: var(--muted);
            line-height: 1.55;
        }

        .error-message {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 10px;
            color: #b42318;
            background: #fee4e2;
            border: 1px solid #fecdca;
            font-weight: 700;
            margin-bottom: 18px;
        }

        form {
            display: grid;
            gap: 18px;
        }

        .form-group {
            display: grid;
            gap: 8px;
        }

        label {
            font-size: 14px;
            color: #344054;
            font-weight: 700;
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent);
        }

        .input-group input {
            width: 100%;
            min-height: 48px;
            padding: 12px 14px 12px 42px;
            border: 1px solid var(--line);
            border-radius: 10px;
            font: inherit;
            color: var(--ink);
            background: #fff;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(255, 133, 51, 0.14);
        }

        .btn-login {
            min-height: 50px;
            border: none;
            border-radius: 999px;
            background: var(--accent);
            color: white;
            font: inherit;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            box-shadow: 0 18px 38px rgba(255, 133, 51, 0.28);
            transition: transform 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-login:hover {
            background: var(--accent-dark);
            transform: translateY(-2px);
            box-shadow: 0 22px 44px rgba(255, 133, 51, 0.34);
        }

        .login-loader {
            position: fixed;
            inset: 0;
            z-index: 20;
            display: none;
            place-items: center;
            background: rgba(17, 24, 39, 0.68);
            color: #fff;
        }

        .login-loader.is-visible {
            display: grid;
        }

        .login-loader-box {
            display: grid;
            gap: 12px;
            justify-items: center;
            padding: 24px 28px;
            border-radius: 16px;
            background: rgba(17, 24, 39, 0.88);
        }

        .login-spinner {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 4px solid rgba(255,255,255,.25);
            border-top-color: var(--accent);
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        @media (max-width: 520px) {
            body {
                padding: 16px;
            }

            .login-card {
                padding: 26px 20px;
                border-radius: 14px;
            }
        }
    </style>
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

            <form method="POST" id="loginForm">
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
    <div class="login-loader" id="loginLoader">
        <div class="login-loader-box">
            <span class="login-spinner"></span>
            <strong>Connexion en cours...</strong>
        </div>
    </div>
    <script>
        document.getElementById('loginForm')?.addEventListener('submit', () => {
            document.getElementById('loginLoader')?.classList.add('is-visible');
        });
    </script>
</body>
</html>
