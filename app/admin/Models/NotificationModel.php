<?php

require_once __DIR__ . '/../../Core/Database.php';

use App\Core\Database;

class NotificationModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * 🔹 Récupérer toutes les inscriptions
     */
    public function getAllInscriptions(): array
    {
        try {
            $stmt = $this->db->query("
                SELECT id, type, nom, email, telephone, statut, programme,
                       type_bien, budget, message, contact_email, contact_tel,
                       contact_whatsapp, newsletter, date_inscription, statut_traitement
                FROM inscriptions
                ORDER BY date_inscription DESC
            ");

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Table n'existe pas encore, créer un tableau vide
            return [];
        }
    }

    /**
     * 🔹 Récupérer les inscriptions par statut
     */
    public function getInscriptionsByStatus(string $status): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, type, nom, email, telephone, statut, programme,
                       type_bien, budget, message, contact_email, contact_tel,
                       contact_whatsapp, newsletter, date_inscription, statut_traitement
                FROM inscriptions
                WHERE statut_traitement = :status
                ORDER BY date_inscription DESC
            ");

            $stmt->execute([':status' => $status]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * 🔹 Récupérer une inscription par ID
     */
    public function getInscriptionById(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, type, nom, email, telephone, statut, adresse, programme,
                       type_bien, budget, message, contact_email, contact_tel,
                       contact_whatsapp, newsletter, date_inscription, statut_traitement
                FROM inscriptions
                WHERE id = :id
            ");

            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * 🔹 Mettre à jour le statut d'une inscription
     */
    public function updateInscriptionStatus(int $id, string $status): bool
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE inscriptions
                SET statut_traitement = :status
                WHERE id = :id
            ");

            return $stmt->execute([
                ':id' => $id,
                ':status' => $status,
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 🔹 Supprimer une inscription
     */
    public function deleteInscription(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM inscriptions WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 🔹 Obtenir les statistiques
     */
    public function getStatistics(): array
    {
        try {
            $total = $this->db->query("SELECT COUNT(*) as count FROM inscriptions")->fetch()['count'];
            $new = $this->db->query("SELECT COUNT(*) as count FROM inscriptions WHERE statut_traitement = 'nouveau'")->fetch()['count'];
            $contacted = $this->db->query("SELECT COUNT(*) as count FROM inscriptions WHERE statut_traitement = 'contacte'")->fetch()['count'];

            return [
                'total' => $total,
                'new' => $new,
                'contacted' => $contacted,
            ];
        } catch (Exception $e) {
            return [
                'total' => 0,
                'new' => 0,
                'contacted' => 0,
            ];
        }
    }
}
