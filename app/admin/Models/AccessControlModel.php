<?php

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../Core/Database.php';

use App\Core\Database;

class AccessControlModel
{
    private PDO $db;
    private string $tableName = 'app_features';

    private array $roles = [
        'admin' => 'Administrateur',
        'manager' => 'Manager',
        'agent' => 'Agent',
    ];

    private array $pages = [
        'dashboard' => ['label' => 'Tableau de bord', 'icon' => 'fa-chart-line'],
        'auth' => ['label' => 'Authentification & rôles', 'icon' => 'fa-user-shield'],
        'access-control' => ['label' => 'Gestion des accès', 'icon' => 'fa-lock'],
        'clients' => ['label' => 'Clients & contacts', 'icon' => 'fa-users'],
        'products' => ['label' => 'Produits & stock', 'icon' => 'fa-box'],
        'orders' => ['label' => 'Commandes & factures', 'icon' => 'fa-file-invoice'],
        'programme-immo' => ['label' => 'Programme Immo', 'icon' => 'fa-building'],
        'payment-schedules' => ['label' => 'Échéances paiement', 'icon' => 'fa-calendar-check'],
        'newsletter' => ['label' => 'Newsletter', 'icon' => 'fa-bullhorn'],
        'messaging' => ['label' => 'Messagerie', 'icon' => 'fa-envelope-open-text'],
        'settings' => ['label' => 'Paramétrage général', 'icon' => 'fa-sliders'],
        'employees' => ['label' => 'Personnel', 'icon' => 'fa-user-tie'],
        'profile' => ['label' => 'Mon profil', 'icon' => 'fa-user-gear'],
        'notifications' => ['label' => 'Notifications', 'icon' => 'fa-bell'],
    ];

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function ensureDefaults(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS app_roles (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                role_key VARCHAR(50) NOT NULL UNIQUE,
                role_label VARCHAR(120) NOT NULL,
                is_system TINYINT(1) NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $roleStmt = $this->db->prepare("
            INSERT INTO app_roles (role_key, role_label, is_system)
            VALUES (:role_key, :role_label, 1)
            ON DUPLICATE KEY UPDATE role_label = VALUES(role_label)
        ");

        foreach ($this->roles as $roleKey => $roleLabel) {
            $roleStmt->execute([
                ':role_key' => $roleKey,
                ':role_label' => $roleLabel,
            ]);
        }

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS {$this->tableName} (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                role_key VARCHAR(50) NOT NULL,
                page_key VARCHAR(80) NOT NULL,
                can_access TINYINT(1) NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_app_features (role_key, page_key),
                INDEX idx_app_features_role (role_key),
                INDEX idx_app_features_page (page_key)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $this->migrateLegacyAccess();

        $stmt = $this->db->prepare("
            INSERT INTO {$this->tableName} (role_key, page_key, can_access)
            VALUES (:role_key, :page_key, :can_access)
            ON DUPLICATE KEY UPDATE page_key = VALUES(page_key)
        ");

        foreach (array_keys($this->getRoles()) as $role) {
            foreach (array_keys($this->pages) as $page) {
                $stmt->execute([
                    ':role_key' => $role,
                    ':page_key' => $page,
                    ':can_access' => $this->defaultAccess($role, $page) ? 1 : 0,
                ]);
            }
        }
    }

    public function getRoles(): array
    {
        $this->ensureRoleTableOnly();

        $stmt = $this->db->query("
            SELECT role_key, role_label
            FROM app_roles
            ORDER BY is_system DESC, role_label ASC
        ");

        $roles = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $roles[(string) $row['role_key']] = (string) $row['role_label'];
        }

        return $roles ?: $this->roles;
    }

    public function getPages(): array
    {
        return $this->pages;
    }

    public function getMatrix(): array
    {
        $this->ensureDefaults();

        $matrix = [];
        foreach (array_keys($this->getRoles()) as $role) {
            foreach (array_keys($this->pages) as $page) {
                $matrix[$role][$page] = $this->defaultAccess($role, $page);
            }
        }

        $stmt = $this->db->query("
            SELECT role_key, page_key, can_access
            FROM {$this->tableName}
        ");

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $role = (string) ($row['role_key'] ?? '');
            $page = (string) ($row['page_key'] ?? '');

            if (isset($matrix[$role][$page])) {
                $matrix[$role][$page] = (int) ($row['can_access'] ?? 0) === 1;
            }
        }

        return $matrix;
    }

    public function saveMatrix(array $postedAccess): bool
    {
        $this->ensureDefaults();

        try {

            // Démarre la transaction seulement si aucune transaction active
            if (!$this->db->inTransaction()) {
                $this->db->beginTransaction();
            }

            $stmt = $this->db->prepare("
            INSERT INTO {$this->tableName} (role_key, page_key, can_access)
            VALUES (:role_key, :page_key, :can_access)
            ON DUPLICATE KEY UPDATE
                can_access = VALUES(can_access),
                updated_at = NOW()
        ");

            foreach (array_keys($this->getRoles()) as $role) {

                foreach (array_keys($this->pages) as $page) {

                    $canAccess =
                        !empty($postedAccess[$role][$page]) ||
                        $this->forcedAccess($role, $page);

                    $stmt->execute([
                        ':role_key' => $role,
                        ':page_key' => $page,
                        ':can_access' => $canAccess ? 1 : 0,
                    ]);
                }
            }

            // Commit uniquement si transaction active
            if ($this->db->inTransaction()) {
                $this->db->commit();
            }

            return true;

        } catch (Throwable $e) {

            // Évite l'erreur "There is no active transaction"
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            error_log('[AccessControlModel] saveMatrix error: ' . $e->getMessage());

            return false;
        }
    }

    public function canAccess(string $role, string $page): bool
    {
        if (!isset($this->getRoles()[$role]) || !isset($this->pages[$page])) {
            return false;
        }

        if ($this->forcedAccess($role, $page)) {
            return true;
        }

        $sessionPages = $_SESSION['admin_features'] ?? null;
        if (is_array($sessionPages) && $sessionPages !== []) {
            return in_array($page, $sessionPages, true);
        }

        return $this->canAccessFromStorage($role, $page);
    }

    public function getAccessiblePages(string $role): array
    {
        $sessionPages = $_SESSION['admin_features'] ?? null;

        if (is_array($sessionPages) && $sessionPages !== []) {
            return array_intersect_key($this->pages, array_flip($sessionPages));
        }

        $pages = [];

        foreach ($this->getPages() as $key => $page) {
            if ($this->canAccessFromStorage($role, $key)) {
                $pages[$key] = $page;
            }
        }

        return $pages;
    }

    public function getAllowedPageKeys(string $role): array
    {
        return array_keys($this->getAccessiblePagesFromDatabase($role));
    }

    public function getAccessiblePagesFromDatabase(string $role): array
    {
        $pages = [];

        foreach ($this->getPages() as $key => $page) {
            if ($this->canAccess($role, $key)) {
                $pages[$key] = $page;
            }
        }

        return $pages;
    }

    public function loadSessionFeatures(string $role): void
    {
        $_SESSION['admin_features'] = $this->getAllowedPageKeys($role);
    }

    public function addRole(string $roleKey, string $roleLabel): bool
    {
        $this->ensureDefaults();

        $roleKey = strtolower(trim($roleKey));
        $roleKey = preg_replace('/[^a-z0-9_-]/', '-', $roleKey);
        $roleKey = trim((string) $roleKey, '-');
        $roleLabel = trim($roleLabel);

        if ($roleKey === '' || $roleLabel === '') {
            return false;
        }

        $stmt = $this->db->prepare("
            INSERT INTO app_roles (role_key, role_label, is_system)
            VALUES (:role_key, :role_label, 0)
            ON DUPLICATE KEY UPDATE role_label = VALUES(role_label)
        ");

        $ok = $stmt->execute([
            ':role_key' => $roleKey,
            ':role_label' => $roleLabel,
        ]);

        foreach (array_keys($this->pages) as $page) {
            $featureStmt = $this->db->prepare("
                INSERT INTO {$this->tableName} (role_key, page_key, can_access)
                VALUES (:role_key, :page_key, :can_access)
                ON DUPLICATE KEY UPDATE page_key = VALUES(page_key)
            ");
            $featureStmt->execute([
                ':role_key' => $roleKey,
                ':page_key' => $page,
                ':can_access' => $page === 'dashboard' ? 1 : 0,
            ]);
        }

        return $ok;
    }

    private function defaultAccess(string $role, string $page): bool
    {
        if ($role === 'admin') {
            return true;
        }

        if ($role === 'manager') {
            return !in_array($page, ['auth', 'access-control', 'settings'], true);
        }

        return in_array($page, ['dashboard', 'clients', 'orders', 'programme-immo', 'payment-schedules', 'newsletter', 'messaging', 'profile', 'notifications'], true);
    }

    private function ensureRoleTableOnly(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS app_roles (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                role_key VARCHAR(50) NOT NULL UNIQUE,
                role_label VARCHAR(120) NOT NULL,
                is_system TINYINT(1) NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private function forcedAccess(string $role, string $page): bool
    {
        return $page === 'profile' || ($role === 'admin' && in_array($page, ['dashboard', 'access-control'], true));
    }

    private function canAccessFromStorage(string $role, string $page): bool
    {
        if ($this->forcedAccess($role, $page)) {
            return true;
        }

        $this->ensureDefaults();

        $stmt = $this->db->prepare("
            SELECT can_access
            FROM {$this->tableName}
            WHERE role_key = :role_key AND page_key = :page_key
            LIMIT 1
        ");
        $stmt->execute([
            ':role_key' => $role,
            ':page_key' => $page,
        ]);

        $value = $stmt->fetchColumn();

        if ($value === false) {
            return $this->defaultAccess($role, $page);
        }

        return (int) $value === 1;
    }

    private function migrateLegacyAccess(): void
    {
        try {
            $exists = $this->db->query("SHOW TABLES LIKE 'admin_role_access'")->fetchColumn();

            if (!$exists) {
                return;
            }

            $this->db->exec("
                INSERT INTO {$this->tableName} (role_key, page_key, can_access, created_at, updated_at)
                SELECT role_key, page_key, can_access, created_at, updated_at
                FROM admin_role_access
                ON DUPLICATE KEY UPDATE
                    can_access = VALUES(can_access),
                    updated_at = VALUES(updated_at)
            ");
        } catch (Throwable $e) {
            error_log('[AccessControlModel] legacy migration error: ' . $e->getMessage());
        }
    }
}
