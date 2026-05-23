<?php

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../Core/Database.php';
require_once __DIR__ . '/../../Core/Settings.php';
require_once __DIR__ . '/../../Core/MailTransportConfig.php';
require_once __DIR__ . '/../../lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../lib/PHPMailer/src/SMTP.php';

use App\Core\Database;
use App\Core\MailTransportConfig;
use App\Core\Settings;
use PHPMailer\PHPMailer\PHPMailer;

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

    public function ensureContractTable(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS adhesion_contracts (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                adhesion_id INT UNSIGNED NOT NULL UNIQUE,
                contract_content LONGTEXT NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_adhesion_contracts_adhesion
                    FOREIGN KEY (adhesion_id) REFERENCES adhesions(id)
                    ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function getContract(int $adhesionId): ?array
    {
        $this->ensureContractTable();
        $this->ensureContract($adhesionId);

        $stmt = $this->db->prepare("SELECT * FROM adhesion_contracts WHERE adhesion_id = :adhesion_id LIMIT 1");
        $stmt->execute([':adhesion_id' => $adhesionId]);
        $contract = $stmt->fetch(PDO::FETCH_ASSOC);

        return $contract ?: null;
    }

    public function saveContract(int $adhesionId, string $content): bool
    {
        $this->ensureContractTable();
        if ($adhesionId <= 0 || trim($content) === '') {
            return false;
        }

        $stmt = $this->db->prepare("
            INSERT INTO adhesion_contracts (adhesion_id, contract_content)
            VALUES (:adhesion_id, :content)
            ON DUPLICATE KEY UPDATE contract_content = VALUES(contract_content), updated_at = NOW()
        ");

        return $stmt->execute([
            ':adhesion_id' => $adhesionId,
            ':content' => trim($content),
        ]);
    }

    public function sendContractByEmail(int $adhesionId): bool
    {
        $adhesion = $this->getAdhesionById($adhesionId);
        $contract = $this->getContract($adhesionId);

        if (!$adhesion || !$contract || empty($adhesion['email'])) {
            return false;
        }

        $smtpUser = MailTransportConfig::smtpUser();
        $smtpPass = MailTransportConfig::smtpPassword();

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = MailTransportConfig::smtpHost();
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUser;
        $mail->Password = $smtpPass;
        $mail->SMTPSecure = MailTransportConfig::smtpEncryption() === 'ssl'
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = MailTransportConfig::smtpPort();
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($smtpUser, MailTransportConfig::fromName());
        $mail->addAddress($adhesion['email'], trim(($adhesion['prenom'] ?? '') . ' ' . ($adhesion['nom'] ?? '')));
        $mail->isHTML(true);
        $mail->Subject = 'Votre contrat programme immobilier ECOFI';
        $mail->Body = '<p>Bonjour,</p><p>Veuillez trouver ci-dessous votre contrat ECOFI.</p><div style="white-space:pre-line;border:1px solid #eee;padding:16px">' . htmlspecialchars($contract['contract_content']) . '</div>';

        return $mail->send();
    }

    private function ensureContract(int $adhesionId): void
    {
        $adhesion = $this->getAdhesionById($adhesionId);
        if (!$adhesion) {
            return;
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM adhesion_contracts WHERE adhesion_id = :adhesion_id");
        $stmt->execute([':adhesion_id' => $adhesionId]);
        if ((int) $stmt->fetchColumn() > 0) {
            return;
        }

        $content = "CONTRAT D'ADHÉSION AU PROGRAMME IMMOBILIER ECOFI\n\n"
            . "Client : " . trim(($adhesion['prenom'] ?? '') . ' ' . ($adhesion['nom'] ?? '')) . "\n"
            . "CNI/Passeport : " . ($adhesion['cni'] ?? '') . "\n"
            . "Programme : " . Settings::get('program_title') . "\n"
            . "Localisation : " . Settings::get('program_location') . "\n"
            . "Acompte : " . Settings::get('program_deposit') . "\n"
            . "Mensualité : " . Settings::get('program_monthly_payment') . "\n\n"
            . "Le client s'engage à respecter les échéances de paiement et les conditions du programme.";

        $this->saveContract($adhesionId, $content);
    }
}
