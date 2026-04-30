<?php


require_once __DIR__ . '/../../Core/Database.php';

use App\Core\Database;

if (isset($_SESSION['user_id'])) {
    header('Location: /app/admin/Views/adminPage.php?page=dashboard');
    exit;
}

$error = '';

try {
    $pdo = Database::getConnection();
} catch (Throwable $e) {
    $pdo = null;
}
$isLocalDev = true;
//$isLocalDev = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1'], true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        /**
         * Connexion DEV uniquement en local
         * Identifiants :
         *   email : admin
         *   mot de passe : admin
         */
        if ($isLocalDev && $email === 'admin@ecofi.sn' && $password === 'admin') {
            $_SESSION['user_id'] = 0;
            $_SESSION['user_name'] = 'Administrateur DEV';
            $_SESSION['user_email'] = 'admin@ecofi.sn';
            $_SESSION['user_role'] = 'admin';

            header('Location: /app/admin/Views/adminPage.php?page=dashboard');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Adresse email invalide.';
        } elseif (!$pdo) {
            $error = 'Connexion à la base de données indisponible.';
        } else {
            try {
                $stmt = $pdo->prepare("
                    SELECT id, fullname, email, password, role, status
                    FROM users
                    WHERE email = :email
                    LIMIT 1
                ");
                $stmt->execute([':email' => $email]);

                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user) {
                    $error = 'Email ou mot de passe incorrect.';
                } elseif (($user['status'] ?? 'active') !== 'active') {
                    $error = 'Votre compte est suspendu.';
                } elseif (!password_verify($password, $user['password'])) {
                    $error = 'Email ou mot de passe incorrect.';
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['fullname'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];

                    header('Location: /app/admin/Views/adminPage.php?page=dashboard');
                    exit;
                }
            } catch (PDOException $e) {
                $error = 'Erreur lors de la connexion.';
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Login Container */
        .login-wrapper {
            width: 50%;
            max-width: 450px;
            margin: 1rem auto;
        }

        .login-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        /* Logo */
        .login-logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo-img {
            width: 90px;
            height: 90px;
            object-fit: contain;
            margin: 0 auto 16px;
            display: block;
        }

        .login-logo h1 {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #FF8533, #ff6b1a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .login-logo p {
            color: #666;
            font-size: 0.85rem;
        }

        /* Title */
        .login-title {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-title h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a2c3e;
            margin-bottom: 8px;
        }

        .login-title p {
            color: #888;
            font-size: 0.85rem;
        }

        /* Form */
        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 0.85rem;
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .input-group input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #eef2f6;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s;
            background: #f8f9fa;
        }

        .input-group input:focus {
            outline: none;
            border-color: #FF8533;
            background: white;
            box-shadow: 0 0 0 3px rgba(255, 133, 51, 0.1);
        }

        /* Options */
        .login-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 0.85rem;
            flex-wrap: wrap;
            gap: 10px;
        }

        .checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: #666;
        }

        .checkbox input {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #FF8533;
        }

        .forgot-link {
            color: #FF8533;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .forgot-link:hover {
            color: #ff6b1a;
            text-decoration: underline;
        }

        /* Button */
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #FF8533, #ff6b1a);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(255, 133, 51, 0.4);
        }

        /* Error message */
        .error-message {
            background: #fef2f2;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            border-left: 3px solid #dc2626;
        }

        .error-message i {
            font-size: 1rem;
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #eef2f6;
            color: #999;
            font-size: 0.75rem;
        }

        .login-footer a {
            color: #FF8533;
            text-decoration: none;
        }

        /* Demo credentials */
        .demo-credentials {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 12px 16px;
            margin-top: 20px;
            text-align: center;
            font-size: 0.8rem;
            border: 1px solid #eef2f6;
        }

        .demo-credentials p {
            color: #666;
            margin-bottom: 8px;
        }

        .demo-credentials code {
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 6px;
            font-family: monospace;
            color: #FF8533;
            font-weight: 600;
            font-size: 0.8rem;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 32px 24px;
            }
            
            .login-title h2 {
                font-size: 1.3rem;
            }
            
            .login-options {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .logo-img {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-logo">
                <img src="../IMG/logo-ecofi.png" alt="ECOFI" class="logo-img" onerror="this.src='https://via.placeholder.com/80x80?text=ECOFI'">
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
                        <input type="email" name="email" placeholder="admin@ecofi.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label>Mot de passe</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="login-options">
                    <label class="checkbox">
                        <input type="checkbox" name="remember">
                        <span>Se souvenir de moi</span>
                    </label>
                    <a href="#" class="forgot-link">Mot de passe oublié ?</a>
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