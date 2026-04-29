<?php

require_once __DIR__ . '/../../Core/Database.php';

use App\Core\Database;

class ProgrammeImmobilierModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * 🔹 Récupérer tous les programmes immobiliers
     */
    public function getAllProgrammes(): array
    {
        $stmt = $this->db->query("
            SELECT id, nom, localisation, description, surface_totale, prix, nombre_unites, status, created_at
            FROM programme_immobilier
            ORDER BY created_at DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 🔹 Récupérer un programme par ID
     */
    public function getProgrammeById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, nom, localisation, description, surface_totale, prix, nombre_unites, status, created_at, updated_at
            FROM programme_immobilier
            WHERE id = :id
        ");

        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * 🔹 Ajouter un nouveau programme immobilier
     */
    public function addProgramme(
        string $nom,
        string $localisation,
        string $description,
        float $surface_totale,
        float $prix,
        int $nombre_unites,
        string $status = 'planning'
    ): bool {
        $stmt = $this->db->prepare("
            INSERT INTO programme_immobilier (nom, localisation, description, surface_totale, prix, nombre_unites, status)
            VALUES (:nom, :localisation, :description, :surface_totale, :prix, :nombre_unites, :status)
        ");

        return $stmt->execute([
            ':nom' => $nom,
            ':localisation' => $localisation,
            ':description' => $description,
            ':surface_totale' => $surface_totale,
            ':prix' => $prix,
            ':nombre_unites' => $nombre_unites,
            ':status' => $status,
        ]);
    }

    /**
     * 🔹 Mettre à jour un programme immobilier
     */
    public function updateProgramme(
        int $id,
        string $nom,
        string $localisation,
        string $description,
        float $surface_totale,
        float $prix,
        int $nombre_unites,
        string $status
    ): bool {
        $stmt = $this->db->prepare("
            UPDATE programme_immobilier
            SET nom = :nom,
                localisation = :localisation,
                description = :description,
                surface_totale = :surface_totale,
                prix = :prix,
                nombre_unites = :nombre_unites,
                status = :status,
                updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':localisation' => $localisation,
            ':description' => $description,
            ':surface_totale' => $surface_totale,
            ':prix' => $prix,
            ':nombre_unites' => $nombre_unites,
            ':status' => $status,
        ]);
    }

    /**
     * 🔹 Supprimer un programme immobilier
     */
    public function deleteProgramme(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM programme_immobilier WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * 🔹 Obtenir les statistiques des programmes
     */
    public function getStatistics(): array
    {
        $totalCount = $this->db->query("SELECT COUNT(*) as count FROM programme_immobilier")->fetch()['count'];
        $activeCount = $this->db->query("SELECT COUNT(*) as count FROM programme_immobilier WHERE status = 'en_cours'")->fetch()['count'];
        $planningCount = $this->db->query("SELECT COUNT(*) as count FROM programme_immobilier WHERE status = 'planning'")->fetch()['count'];

        return [
            'total' => $totalCount,
            'active' => $activeCount,
            'planning' => $planningCount,
        ];
    }
}
