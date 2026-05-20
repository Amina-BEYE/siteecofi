<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/admin/Models/AccessControlModel.php';

use App\Core\Database;

function get_db_connection(): PDO
{
    try {
        return Database::getConnection();
    } catch (\Throwable $e) {
        // Log full exception for administrators, but provide a generic message to callers
        error_log('[admin_functions] DB connection error: ' . $e->getMessage());
        throw new \RuntimeException('Impossible d\'obtenir une connexion à la base de données.');
    }
}

function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function is_admin_logged_in(): bool
{
    return !empty($_SESSION['admin_id']) && ($_SESSION['admin_role'] ?? '') === 'admin';
}

function find_admin_user(PDO $pdo, string $email): ?array
{
    $queries = [
        'users' => 'SELECT id, fullname, email, password, role, status FROM users WHERE email = :email LIMIT 1',
        'admins' => "SELECT id, fullname, email, password, 'admin' AS role, status FROM admins WHERE email = :email LIMIT 1",
    ];

    $lastException = null;

    foreach ($queries as $table => $sql) {
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin) {
                return $admin;
            }
        } catch (Throwable $exception) {
            $lastException = $exception;
            error_log(sprintf('[admin_functions] Admin lookup failed on %s: %s', $table, $exception->getMessage()));
        }
    }

    if ($lastException) {
        throw $lastException;
    }

    return null;
}

function require_admin(): void
{
    if (!is_admin_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function login_admin(array $admin): void
{
    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int) $admin['id'];
    $_SESSION['admin_name'] = $admin['fullname'] ?? 'Administrateur';
    $_SESSION['admin_email'] = $admin['email'] ?? '';
    $_SESSION['admin_role'] = $admin['role'] ?? 'admin';
    (new AccessControlModel())->loadSessionFeatures((string) $_SESSION['admin_role']);
}

function flash_message(string $message): void
{
    $_SESSION['flash_message'] = $message;
}

function get_flash_message(): ?string
{
    if (!empty($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }

    return null;
}
