<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ProductRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function findAll(): array
    {
        $sql = "
            SELECT p.*, c.nom AS categorie_nom
            FROM produits p
            INNER JOIN categories c ON c.id = p.categorie_id
            ORDER BY p.id DESC
        ";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM produits WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        return $product ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO produits (
                categorie_id, nom, description, prix, ancien_prix,
                image, note, nb_avis, type_media, media_src, actif
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['categorie_id'],
            $data['nom'],
            $data['description'],
            $data['prix'],
            $data['ancien_prix'],
            $data['image'],
            $data['note'],
            $data['nb_avis'],
            $data['type_media'],
            $data['media_src'],
            $data['actif']
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE produits
            SET categorie_id = ?, nom = ?, description = ?, prix = ?, ancien_prix = ?,
                image = ?, note = ?, nb_avis = ?, type_media = ?, media_src = ?, actif = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['categorie_id'],
            $data['nom'],
            $data['description'],
            $data['prix'],
            $data['ancien_prix'],
            $data['image'],
            $data['note'],
            $data['nb_avis'],
            $data['type_media'],
            $data['media_src'],
            $data['actif'],
            $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM produits WHERE id = ?");
        return $stmt->execute([$id]);
    }
}