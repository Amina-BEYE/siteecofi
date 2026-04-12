<?php

require_once __DIR__ . '/../../Core/Database.php';

use App\Core\Database;

class ProduitCategoryModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAllCategories(): array
    {
        $sql = "
            SELECT 
                id,
                nom,
                description,
                created_at
            FROM categories
            ORDER BY created_at DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllProducts(): array
    {
        $sql = "
            SELECT 
                p.id,
                p.categorie_id,
                p.nom,
                p.description,
                p.prix,
                p.ancien_prix,
                p.image,
                p.note,
                p.nb_avis,
                p.type_media,
                p.media_src,
                p.actif,
                p.created_at,
                c.nom AS categorie_nom
            FROM produits p
            INNER JOIN categories c ON c.id = p.categorie_id
            ORDER BY p.created_at DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addCategory(string $nom, ?string $description = null): bool
    {
        $sql = "
            INSERT INTO categories (nom, description)
            VALUES (:nom, :description)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':nom' => trim($nom),
            ':description' => $description !== null ? trim($description) : null,
        ]);
    }

    public function addProduct(
        int $categorieId,
        string $nom,
        ?string $description,
        float $prix,
        ?float $ancienPrix = null,
        ?string $image = null,
        ?float $note = null,
        int $nbAvis = 0,
        string $typeMedia = 'image',
        ?string $mediaSrc = null,
        int $actif = 1
    ): bool {
        $sql = "
            INSERT INTO produits (
                categorie_id,
                nom,
                description,
                prix,
                ancien_prix,
                image,
                note,
                nb_avis,
                type_media,
                media_src,
                actif
            ) VALUES (
                :categorie_id,
                :nom,
                :description,
                :prix,
                :ancien_prix,
                :image,
                :note,
                :nb_avis,
                :type_media,
                :media_src,
                :actif
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':categorie_id' => $categorieId,
            ':nom' => trim($nom),
            ':description' => $description !== null ? trim($description) : null,
            ':prix' => $prix,
            ':ancien_prix' => $ancienPrix,
            ':image' => $image,
            ':note' => $note,
            ':nb_avis' => $nbAvis,
            ':type_media' => in_array($typeMedia, ['image', 'video'], true) ? $typeMedia : 'image',
            ':media_src' => $mediaSrc,
            ':actif' => $actif ? 1 : 0,
        ]);
    }

    public function categoryExistsByName(string $nom): bool
    {
        $sql = "SELECT COUNT(*) FROM categories WHERE nom = :nom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nom' => trim($nom)]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function productExistsByName(string $nom): bool
    {
        $sql = "SELECT COUNT(*) FROM produits WHERE nom = :nom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nom' => trim($nom)]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function getCategoryById(int $id): ?array
    {
        $sql = "
            SELECT id, nom, description, created_at
            FROM categories
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        return $category ?: null;
    }
}