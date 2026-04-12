<?php

require_once __DIR__ . '/../../Core/Database.php';

use App\Core\Database;

class AuthModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAllUsers(): array
    {
        $sql = "
            SELECT 
                id,
                fullname,
                email,
                role,
                status,
                created_at
            FROM users
            ORDER BY created_at DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function addUser(
        string $fullname,
        string $email,
        string $password,
        string $role = 'agent'
    ): bool {
        $sql = "
            INSERT INTO users (fullname, email, password, role, status)
            VALUES (:fullname, :email, :password, :role, :status)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':fullname' => trim($fullname),
            ':email' => trim($email),
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':role' => in_array($role, ['admin', 'manager', 'agent'], true) ? $role : 'agent',
            ':status' => 'active',
        ]);
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
}