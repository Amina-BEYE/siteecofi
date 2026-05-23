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

class NewsletterModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->ensureTable();
    }

    public function subscribe(string $name, string $phone, string $email, string $interest): bool
    {
        $name = trim($name);
        $phone = trim($phone);
        $email = trim(strtolower($email));
        $interest = trim($interest) !== '' ? trim($interest) : 'programme';

        if ($name === '' || $phone === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $stmt = $this->db->prepare("
        INSERT INTO newletter 
            (name, phone, email, interest, status, created_at, updated_at)
        VALUES 
            (:name, :phone, :email, :interest, 'active', NOW(), NOW())
        ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            phone = VALUES(phone),
            interest = VALUES(interest),
            status = 'active',
            updated_at = NOW()
    ");

        return $stmt->execute([
            ':name' => $name,
            ':phone' => $phone,
            ':email' => $email,
            ':interest' => $interest,
        ]);
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT id, name, phone, email, interest, status, created_at, updated_at
            FROM newletter
            ORDER BY created_at DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveSubscribers(): array
    {
        $stmt = $this->db->query("
            SELECT id, name, phone, email, interest, status, created_at
            FROM newletter
            WHERE status = 'active'
            ORDER BY created_at DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus(int $id, string $status): bool
    {
        if (!in_array($status, ['active', 'unsubscribed'], true)) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE newletter
            SET status = :status, updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':status' => $status,
            ':id' => $id,
        ]);
    }

    public function sendCampaign(string $subject, string $content, ?string $interest = null, array $attachments = []): array
    {
        $subject = trim($subject);
        $content = trim($content);
        $interest = trim((string) $interest);

        if ($subject === '' || $content === '') {
            return ['success' => false, 'sent' => 0, 'failed' => 0, 'message' => 'Objet et message obligatoires.'];
        }

        $subscribers = $this->getCampaignRecipients($interest !== '' ? $interest : null);
        if (empty($subscribers)) {
            return ['success' => false, 'sent' => 0, 'failed' => 0, 'message' => 'Aucun abonné actif pour cette campagne.'];
        }

        $smtpPass = MailTransportConfig::smtpPassword();
        if ($smtpPass === '') {
            return ['success' => false, 'sent' => 0, 'failed' => count($subscribers), 'message' => 'Mot de passe SMTP non configuré.'];
        }

        $sent = 0;
        $failed = 0;

        foreach ($subscribers as $subscriber) {
            try {
                $this->sendCampaignMail($subscriber, $subject, $content, $attachments);
                $sent++;
            } catch (Throwable $e) {
                $failed++;
                error_log('[NewsletterModel] campaign mail: ' . $e->getMessage());
            }
        }

        return [
            'success' => $sent > 0,
            'sent' => $sent,
            'failed' => $failed,
            'message' => $sent > 0
                ? "Campagne envoyée à {$sent} abonné(s)."
                : 'Aucun email n’a pu être envoyé.',
        ];
    }

    public function getStats(): array
    {
        $stmt = $this->db->query("
            SELECT
                COUNT(*) AS total,
                SUM(status = 'active') AS active_total,
                SUM(created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) AS recent_total
            FROM newletter
        ");

        $stats = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'total' => (int) ($stats['total'] ?? 0),
            'active_total' => (int) ($stats['active_total'] ?? 0),
            'recent_total' => (int) ($stats['recent_total'] ?? 0),
        ];
    }

    private function getCampaignRecipients(?string $interest): array
    {
        if ($interest === null) {
            return $this->getActiveSubscribers();
        }

        $stmt = $this->db->prepare("
            SELECT id, name, phone, email, interest, status, created_at
            FROM newletter
            WHERE status = 'active' AND interest = :interest
            ORDER BY created_at DESC
        ");
        $stmt->execute([':interest' => $interest]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function sendCampaignMail(array $subscriber, string $subject, string $content, array $attachments = []): void
    {
        $smtpUser = MailTransportConfig::smtpUser();
        $mail = new PHPMailer(true);
        $name = trim((string) ($subscriber['name'] ?? ''));
        $email = trim((string) ($subscriber['email'] ?? ''));

        $mail->isSMTP();
        $mail->Host = MailTransportConfig::smtpHost();
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUser;
        $mail->Password = MailTransportConfig::smtpPassword();
        $port = MailTransportConfig::smtpPort();
        $encryption = MailTransportConfig::smtpEncryption();
        $mail->SMTPSecure = $encryption === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $port;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($smtpUser, MailTransportConfig::fromName());
        $mail->addAddress($email, $name);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = '<div style="font-family:Arial,sans-serif;line-height:1.6;color:#1f2937">'
            . '<p>Bonjour ' . htmlspecialchars($name !== '' ? $name : 'cher abonné') . ',</p>'
            . '<div>' . nl2br(htmlspecialchars($content)) . '</div>'
            . '<p style="margin-top:24px;color:#667085">ECOFI - Programmes immobiliers</p>'
            . '</div>';
        $mail->AltBody = "Bonjour " . ($name !== '' ? $name : 'cher abonné') . "\n\n" . $content . "\n\nECOFI";

        foreach ($attachments as $attachment) {
            $path = (string) ($attachment['path'] ?? '');
            $name = (string) ($attachment['name'] ?? 'piece-jointe');
            if ($path !== '' && is_file($path)) {
                $mail->addAttachment($path, $name);
            }
        }

        $mail->send();
    }

    private function ensureTable(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS newletter (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(150) NOT NULL,
                phone VARCHAR(40) NOT NULL,
                email VARCHAR(180) NOT NULL UNIQUE,
                interest VARCHAR(80) NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'active',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_newletter_status (status),
                INDEX idx_newletter_interest (interest),
                INDEX idx_newletter_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }
}
