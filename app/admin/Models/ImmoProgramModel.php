<?php

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../Core/Database.php';

use App\Core\Database;

class ImmoProgramModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAllAdhesions(): array
    {
        $stmt = $this->db->query("
            SELECT *
            FROM adhesions
            ORDER BY created_at DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStats(): array
    {
        $stats = [
            'total' => 0,
            'Nouveau' => 0,
            'En cours' => 0,
            'Validé' => 0,
            'Refusé' => 0,
        ];

        $stmt = $this->db->query("
            SELECT status, COUNT(*) AS total
            FROM adhesions
            GROUP BY status
        ");

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $status = $row['status'] ?? 'Nouveau';
            $count = (int) ($row['total'] ?? 0);
            $stats[$status] = $count;
            $stats['total'] += $count;
        }

        return $stats;
    }

    public function getAdhesionById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM adhesions
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);

        $adhesion = $stmt->fetch(PDO::FETCH_ASSOC);

        return $adhesion ?: null;
    }

    public function getNotes(int $adhesionId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                n.*,
                COALESCE(u.fullname, a.fullname, 'Administrateur') AS admin_name
            FROM admin_notes n
            LEFT JOIN users u ON u.id = n.admin_id
            LEFT JOIN admins a ON a.id = n.admin_id
            WHERE n.adhesion_id = :adhesion_id
            ORDER BY n.created_at DESC
        ");
        $stmt->execute([':adhesion_id' => $adhesionId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $allowed = ['Nouveau', 'En cours', 'Validé', 'Refusé'];

        if ($id <= 0 || !in_array($status, $allowed, true)) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE adhesions
            SET status = :status,
                updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':status' => $status,
            ':id' => $id,
        ]);
    }

    public function addNote(int $adhesionId, ?int $adminId, string $note): bool
    {
        if ($adhesionId <= 0 || trim($note) === '') {
            return false;
        }

        $sql = "
            INSERT INTO admin_notes (adhesion_id, admin_id, note, created_at)
            VALUES (:adhesion_id, :admin_id, :note, NOW())
        ";

        try {
            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':adhesion_id' => $adhesionId,
                ':admin_id' => $adminId,
                ':note' => trim($note),
            ]);
        } catch (Throwable $exception) {
            error_log('[ImmoProgramModel] addNote with admin_id failed: ' . $exception->getMessage());

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':adhesion_id' => $adhesionId,
                ':admin_id' => null,
                ':note' => trim($note),
            ]);
        }
    }

    public function deleteAdhesion(int $id): bool
    {
        if ($id <= 0) {
            return false;
        }

        $stmt = $this->db->prepare("
            DELETE FROM adhesions
            WHERE id = :id
        ");

        return $stmt->execute([':id' => $id]);
    }
}
