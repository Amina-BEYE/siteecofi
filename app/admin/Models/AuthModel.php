<?php

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../Core/Database.php';
require_once __DIR__ . '/../../Core/MailCredentialCrypto.php';

use App\Core\Database;
use App\Core\MailCredentialCrypto;

class AuthModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->ensureMailColumns();
        $this->ensureMailSettingsTable();
    }

    public function getAllUsers(): array
    {
        $sql = "
            SELECT 
                users.id,
                users.fullname,
                users.email,
                users.role,
                users.status,
                users.email_address,
                users.imap_host,
                users.imap_port,
                users.imap_encryption,
                users.imap_username,
                users.smtp_host,
                users.smtp_port,
                users.smtp_encryption,
                users.smtp_username,
                users.imap_password,
                users.smtp_password,
                ms.email_address AS ms_email_address,
                ms.imap_host AS ms_imap_host,
                ms.imap_port AS ms_imap_port,
                ms.imap_encryption AS ms_imap_encryption,
                ms.imap_username AS ms_imap_username,
                ms.imap_password_encrypted AS ms_imap_password,
                ms.smtp_host AS ms_smtp_host,
                ms.smtp_port AS ms_smtp_port,
                ms.smtp_encryption AS ms_smtp_encryption,
                ms.smtp_username AS ms_smtp_username,
                ms.smtp_password_encrypted AS ms_smtp_password,
                ms.config_source AS ms_config_source,
                users.created_at
            FROM users
            LEFT JOIN user_mail_settings ms ON ms.user_id = users.id
            ORDER BY users.created_at DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return array_map([$this, 'mergeMailSettingsRow'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getUserById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT users.id, fullname, email, role, status,
                   users.email_address, users.imap_host, users.imap_port, users.imap_encryption, users.imap_username,
                   users.smtp_host, users.smtp_port, users.smtp_encryption, users.smtp_username,
                   ms.email_address AS ms_email_address,
                   ms.imap_host AS ms_imap_host,
                   ms.imap_port AS ms_imap_port,
                   ms.imap_encryption AS ms_imap_encryption,
                   ms.imap_username AS ms_imap_username,
                   ms.smtp_host AS ms_smtp_host,
                   ms.smtp_port AS ms_smtp_port,
                   ms.smtp_encryption AS ms_smtp_encryption,
                   ms.smtp_username AS ms_smtp_username,
                   ms.config_source AS ms_config_source,
                   users.created_at
            FROM users
            LEFT JOIN user_mail_settings ms ON ms.user_id = users.id
            WHERE users.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ? $this->mergeMailSettingsRow($user) : null;
    }

    public function emailExists(string $email): bool
    {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':email' => trim($email),
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function emailExistsForAnotherUser(string $email, int $userId): bool
    {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email AND id <> :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':email' => trim($email),
            ':id' => $userId,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function addUser(
        string $fullname,
        string $email,
        string $password,
        string $role = 'agent',
        array $mailConfig = []
    ): bool {
        $mail = $this->normalizeMailConfig($mailConfig);

        $sql = "
            INSERT INTO users (
                fullname, email, password, role, status,
                email_address, imap_host, imap_port, imap_encryption, imap_username, imap_password,
                smtp_host, smtp_port, smtp_encryption, smtp_username, smtp_password
            )
            VALUES (
                :fullname, :email, :password, :role, :status,
                :email_address, :imap_host, :imap_port, :imap_encryption, :imap_username, :imap_password,
                :smtp_host, :smtp_port, :smtp_encryption, :smtp_username, :smtp_password
            )
        ";

        $stmt = $this->db->prepare($sql);

        $ok = $stmt->execute([
            ':fullname' => trim($fullname),
            ':email' => trim($email),
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':role' => preg_match('/^[a-z0-9_-]{2,50}$/', $role) ? $role : 'agent',
            ':status' => 'active',
            ':email_address' => $mail['email_address'],
            ':imap_host' => $mail['imap_host'],
            ':imap_port' => $mail['imap_port'],
            ':imap_encryption' => $mail['imap_encryption'],
            ':imap_username' => $mail['imap_username'],
            ':imap_password' => MailCredentialCrypto::encrypt($mail['imap_password']),
            ':smtp_host' => $mail['smtp_host'],
            ':smtp_port' => $mail['smtp_port'],
            ':smtp_encryption' => $mail['smtp_encryption'],
            ':smtp_username' => $mail['smtp_username'],
            ':smtp_password' => MailCredentialCrypto::encrypt($mail['smtp_password']),
        ]);

        if ($ok) {
            $this->saveMailSettings((int) $this->db->lastInsertId(), $mail);
        }

        return $ok;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $allowed = ['active', 'suspended'];

        if (!in_array($status, $allowed, true)) {
            return false;
        }

        $sql = "
            UPDATE users
            SET status = :status
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':id' => $id,
        ]);
    }

    public function updateUser(int $id, string $fullname, string $email, string $role, string $status, array $mailConfig = []): bool
    {
        $allowedStatus = ['active', 'suspended'];

        if ($id <= 0 || trim($fullname) === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (!in_array($status, $allowedStatus, true)) {
            return false;
        }

        $safeRole = preg_match('/^[a-z0-9_-]{2,50}$/', $role) ? $role : 'agent';
        $mail = $this->normalizeMailConfig($mailConfig);
        $passwordSql = '';
        $params = [
            ':fullname' => trim($fullname),
            ':email' => trim($email),
            ':role' => $safeRole,
            ':status' => $status,
            ':email_address' => $mail['email_address'],
            ':imap_host' => $mail['imap_host'],
            ':imap_port' => $mail['imap_port'],
            ':imap_encryption' => $mail['imap_encryption'],
            ':imap_username' => $mail['imap_username'],
            ':smtp_host' => $mail['smtp_host'],
            ':smtp_port' => $mail['smtp_port'],
            ':smtp_encryption' => $mail['smtp_encryption'],
            ':smtp_username' => $mail['smtp_username'],
            ':id' => $id,
        ];

        if ($mail['imap_password'] !== '') {
            $passwordSql .= ', imap_password = :imap_password';
            $params[':imap_password'] = MailCredentialCrypto::encrypt($mail['imap_password']);
        }

        if ($mail['smtp_password'] !== '') {
            $passwordSql .= ', smtp_password = :smtp_password';
            $params[':smtp_password'] = MailCredentialCrypto::encrypt($mail['smtp_password']);
        }

        $stmt = $this->db->prepare("
            UPDATE users
            SET fullname = :fullname,
                email = :email,
                role = :role,
                status = :status,
                email_address = :email_address,
                imap_host = :imap_host,
                imap_port = :imap_port,
                imap_encryption = :imap_encryption,
                imap_username = :imap_username,
                smtp_host = :smtp_host,
                smtp_port = :smtp_port,
                smtp_encryption = :smtp_encryption,
                smtp_username = :smtp_username
                {$passwordSql}
            WHERE id = :id
        ");

        $ok = $stmt->execute($params);

        if ($ok) {
            $this->saveMailSettings($id, $mail);
        }

        return $ok;
    }

    public function updatePassword(int $id, string $password): bool
    {
        if ($id <= 0 || strlen($password) < 6) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE users
            SET password = :password
            WHERE id = :id
        ");

        return $stmt->execute([
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':id' => $id,
        ]);
    }

    public function changePassword(int $id, string $currentPassword, string $newPassword): bool
    {
        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $hash = (string) ($stmt->fetchColumn() ?: '');

        if ($hash === '' || !password_verify($currentPassword, $hash)) {
            return false;
        }

        return $this->updatePassword($id, $newPassword);
    }

    public function getUserByEmail(string $email): ?array
    {
        $sql = "
            SELECT *
            FROM users
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':email' => trim($email),
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function getMailConfigByUserId(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT users.id, users.fullname,
                   users.email_address, users.imap_host, users.imap_port, users.imap_encryption, users.imap_username, users.imap_password,
                   users.smtp_host, users.smtp_port, users.smtp_encryption, users.smtp_username, users.smtp_password,
                   ms.email_address AS ms_email_address,
                   ms.imap_host AS ms_imap_host,
                   ms.imap_port AS ms_imap_port,
                   ms.imap_encryption AS ms_imap_encryption,
                   ms.imap_username AS ms_imap_username,
                   ms.imap_password_encrypted AS ms_imap_password,
                   ms.smtp_host AS ms_smtp_host,
                   ms.smtp_port AS ms_smtp_port,
                   ms.smtp_encryption AS ms_smtp_encryption,
                   ms.smtp_username AS ms_smtp_username,
                   ms.smtp_password_encrypted AS ms_smtp_password,
                   ms.config_source AS ms_config_source
            FROM users
            LEFT JOIN user_mail_settings ms ON ms.user_id = users.id
            WHERE users.id = :id AND users.status = 'active'
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $row = $this->mergeMailSettingsRow($row);
        $row['imap_password_plain'] = MailCredentialCrypto::decrypt($row['imap_password'] ?? '');
        $row['smtp_password_plain'] = MailCredentialCrypto::decrypt($row['smtp_password'] ?? '');
        unset($row['imap_password'], $row['smtp_password']);

        return $row;
    }

    public function verifyLogin(string $email, string $password): ?array
    {
        $user = $this->getUserByEmail($email);

        if (!$user) {
            return null;
        }

        if ($user['status'] !== 'active') {
            return null;
        }

        if (!password_verify($password, $user['password'])) {
            return null;
        }

        return $user;
    }

    private function normalizeMailConfig(array $input): array
    {
        $emailAddress = trim((string) ($input['email_address'] ?? ''));
        $domain = filter_var($emailAddress, FILTER_VALIDATE_EMAIL) ? substr(strrchr($emailAddress, '@') ?: '', 1) : '';
        $o2switchHost = $domain !== '' ? 'mail.' . $domain : '';

        return [
            'email_address' => filter_var($emailAddress, FILTER_VALIDATE_EMAIL) ? $emailAddress : null,
            'imap_host' => trim((string) ($input['imap_host'] ?? '')) ?: $o2switchHost,
            'imap_port' => $this->normalizePort($input['imap_port'] ?? 993, 993),
            'imap_encryption' => $this->normalizeEncryption($input['imap_encryption'] ?? 'ssl'),
            'imap_username' => trim((string) ($input['imap_username'] ?? '')) ?: $emailAddress,
            'imap_password' => trim((string) ($input['imap_password'] ?? '')),
            'smtp_host' => trim((string) ($input['smtp_host'] ?? '')) ?: $o2switchHost,
            'smtp_port' => $this->normalizePort($input['smtp_port'] ?? 465, 465),
            'smtp_encryption' => $this->normalizeEncryption($input['smtp_encryption'] ?? 'ssl'),
            'smtp_username' => trim((string) ($input['smtp_username'] ?? '')) ?: $emailAddress,
            'smtp_password' => trim((string) ($input['smtp_password'] ?? '')),
            'config_source' => trim((string) ($input['config_source'] ?? 'manual')) ?: 'manual',
        ];
    }

    private function mergeMailSettingsRow(array $row): array
    {
        foreach ([
            'email_address',
            'imap_host',
            'imap_port',
            'imap_encryption',
            'imap_username',
            'imap_password',
            'smtp_host',
            'smtp_port',
            'smtp_encryption',
            'smtp_username',
            'smtp_password',
        ] as $key) {
            $msKey = 'ms_' . $key;
            if (array_key_exists($msKey, $row) && $row[$msKey] !== null && $row[$msKey] !== '') {
                $row[$key] = $row[$msKey];
            }
            unset($row[$msKey]);
        }

        $row['config_source'] = $row['ms_config_source'] ?? $row['config_source'] ?? 'manual';
        unset($row['ms_config_source']);
        $row['has_imap_password'] = empty($row['imap_password']) ? 0 : 1;
        $row['has_smtp_password'] = empty($row['smtp_password']) ? 0 : 1;

        return $row;
    }

    private function saveMailSettings(int $userId, array $mail): bool
    {
        if ($userId <= 0 || empty($mail['email_address'])) {
            return false;
        }

        $existing = $this->getRawMailSettings($userId);
        $imapPassword = $mail['imap_password'] !== ''
            ? MailCredentialCrypto::encrypt($mail['imap_password'])
            : ($existing['imap_password_encrypted'] ?? null);
        $smtpPassword = $mail['smtp_password'] !== ''
            ? MailCredentialCrypto::encrypt($mail['smtp_password'])
            : ($existing['smtp_password_encrypted'] ?? null);

        $stmt = $this->db->prepare("
            INSERT INTO user_mail_settings (
                user_id, email_address, imap_host, imap_port, imap_encryption, imap_username, imap_password_encrypted,
                smtp_host, smtp_port, smtp_encryption, smtp_username, smtp_password_encrypted, config_source
            )
            VALUES (
                :user_id, :email_address, :imap_host, :imap_port, :imap_encryption, :imap_username, :imap_password_encrypted,
                :smtp_host, :smtp_port, :smtp_encryption, :smtp_username, :smtp_password_encrypted, :config_source
            )
            ON DUPLICATE KEY UPDATE
                email_address = VALUES(email_address),
                imap_host = VALUES(imap_host),
                imap_port = VALUES(imap_port),
                imap_encryption = VALUES(imap_encryption),
                imap_username = VALUES(imap_username),
                imap_password_encrypted = VALUES(imap_password_encrypted),
                smtp_host = VALUES(smtp_host),
                smtp_port = VALUES(smtp_port),
                smtp_encryption = VALUES(smtp_encryption),
                smtp_username = VALUES(smtp_username),
                smtp_password_encrypted = VALUES(smtp_password_encrypted),
                config_source = VALUES(config_source),
                updated_at = NOW()
        ");

        return $stmt->execute([
            ':user_id' => $userId,
            ':email_address' => $mail['email_address'],
            ':imap_host' => $mail['imap_host'],
            ':imap_port' => $mail['imap_port'],
            ':imap_encryption' => $mail['imap_encryption'],
            ':imap_username' => $mail['imap_username'],
            ':imap_password_encrypted' => $imapPassword,
            ':smtp_host' => $mail['smtp_host'],
            ':smtp_port' => $mail['smtp_port'],
            ':smtp_encryption' => $mail['smtp_encryption'],
            ':smtp_username' => $mail['smtp_username'],
            ':smtp_password_encrypted' => $smtpPassword,
            ':config_source' => in_array($mail['config_source'], ['manual', 'json', 'mobileconfig'], true) ? $mail['config_source'] : 'manual',
        ]);
    }

    private function getRawMailSettings(int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM user_mail_settings WHERE user_id = :user_id LIMIT 1");
        $stmt->execute([':user_id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    private function normalizePort(mixed $port, int $fallback): int
    {
        $port = (int) $port;
        return $port > 0 && $port <= 65535 ? $port : $fallback;
    }

    private function normalizeEncryption(string $value): string
    {
        $value = strtolower(trim($value));
        return in_array($value, ['ssl', 'tls'], true) ? $value : 'ssl';
    }

    private function ensureMailColumns(): void
    {
        $columns = [
            'email_address' => 'VARCHAR(180) NULL',
            'imap_host' => 'VARCHAR(180) NULL',
            'imap_port' => 'INT UNSIGNED NULL DEFAULT 993',
            'imap_encryption' => "VARCHAR(10) NULL DEFAULT 'ssl'",
            'imap_username' => 'VARCHAR(180) NULL',
            'imap_password' => 'TEXT NULL',
            'smtp_host' => 'VARCHAR(180) NULL',
            'smtp_port' => 'INT UNSIGNED NULL DEFAULT 465',
            'smtp_encryption' => "VARCHAR(10) NULL DEFAULT 'ssl'",
            'smtp_username' => 'VARCHAR(180) NULL',
            'smtp_password' => 'TEXT NULL',
        ];

        foreach ($columns as $column => $definition) {
            try {
                $exists = $this->db->query("SHOW COLUMNS FROM users LIKE " . $this->db->quote($column))->fetchColumn();
                if (!$exists) {
                    $this->db->exec("ALTER TABLE users ADD COLUMN {$column} {$definition}");
                }
            } catch (Throwable $e) {
                error_log('[AuthModel] ensureMailColumns ' . $column . ': ' . $e->getMessage());
            }
        }
    }

    private function ensureMailSettingsTable(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS user_mail_settings (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id INT UNSIGNED NOT NULL UNIQUE,
                email_address VARCHAR(180) NOT NULL,
                imap_host VARCHAR(180) NULL,
                imap_port INT UNSIGNED NULL DEFAULT 993,
                imap_encryption VARCHAR(10) NULL DEFAULT 'ssl',
                imap_username VARCHAR(180) NULL,
                imap_password_encrypted TEXT NULL,
                smtp_host VARCHAR(180) NULL,
                smtp_port INT UNSIGNED NULL DEFAULT 465,
                smtp_encryption VARCHAR(10) NULL DEFAULT 'ssl',
                smtp_username VARCHAR(180) NULL,
                smtp_password_encrypted TEXT NULL,
                config_source VARCHAR(40) NOT NULL DEFAULT 'manual',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_user_mail_settings_email (email_address)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }
}
