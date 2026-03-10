<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class CategoryRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function findAll(): array
    {
        return $this->pdo
            ->query("SELECT * FROM categories ORDER BY id DESC")
            ->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch();

        return $category ?: null;
    }

    public function create(string $nom, string $description): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO categories (nom, description)
            VALUES (?, ?)
        ");

        return $stmt->execute([$nom, $description]);
    }

    public function update(int $id, string $nom, string $description): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE categories
            SET nom = ?, description = ?
            WHERE id = ?
        ");

        return $stmt->execute([$nom, $description, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}