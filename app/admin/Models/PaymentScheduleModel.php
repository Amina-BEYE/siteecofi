<?php

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../Core/Database.php';
require_once __DIR__ . '/../../Core/Settings.php';
require_once __DIR__ . '/../../lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../lib/PHPMailer/src/SMTP.php';

use App\Core\Database;
use App\Core\Settings;
use PHPMailer\PHPMailer\PHPMailer;

class PaymentScheduleModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function ensureTable(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS payment_schedules (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                adhesion_id INT UNSIGNED NOT NULL,
                installment_number INT UNSIGNED NOT NULL,
                due_date DATE NOT NULL,
                amount DECIMAL(12,2) NOT NULL DEFAULT 0,
                status VARCHAR(30) NOT NULL DEFAULT 'pending',
                paid_at DATETIME NULL,
                payment_method VARCHAR(100) NULL,
                note TEXT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_payment_schedule_installment (adhesion_id, installment_number),
                INDEX idx_payment_schedule_adhesion (adhesion_id),
                INDEX idx_payment_schedule_status (status),
                INDEX idx_payment_schedule_due_date (due_date),
                CONSTRAINT fk_payment_schedule_adhesion
                    FOREIGN KEY (adhesion_id) REFERENCES adhesions(id)
                    ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function ensureSchedulesForAllAdhesions(): void
    {
        $this->ensureTable();

        $stmt = $this->db->query("
            SELECT id, created_at
            FROM adhesions
            ORDER BY created_at ASC
        ");

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $adhesion) {
            $this->ensureScheduleForAdhesion((int) $adhesion['id'], (string) ($adhesion['created_at'] ?? 'now'));
        }
    }

    public function ensureScheduleForAdhesion(int $adhesionId, string $startDate = 'now'): void
    {
        if ($adhesionId <= 0) {
            return;
        }

        $this->ensureTable();

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM payment_schedules WHERE adhesion_id = :adhesion_id");
        $countStmt->execute([':adhesion_id' => $adhesionId]);

        if ((int) $countStmt->fetchColumn() > 0) {
            return;
        }

        $amount = $this->monthlyAmount();
        $base = new DateTimeImmutable($startDate ?: 'now');
        $firstDueDate = $base->modify('first day of next month');

        $stmt = $this->db->prepare("
            INSERT INTO payment_schedules (adhesion_id, installment_number, due_date, amount, status)
            VALUES (:adhesion_id, :installment_number, :due_date, :amount, 'pending')
        ");

        for ($month = 1; $month <= 24; $month++) {
            $dueDate = $firstDueDate->modify('+' . ($month - 1) . ' months');

            $stmt->execute([
                ':adhesion_id' => $adhesionId,
                ':installment_number' => $month,
                ':due_date' => $dueDate->format('Y-m-d'),
                ':amount' => $amount,
            ]);
        }
    }

    public function getStats(): array
    {
        $this->ensureSchedulesForAllAdhesions();

        $stats = [
            'total' => 0,
            'paid' => 0,
            'pending' => 0,
            'late' => 0,
            'amount_paid' => 0.0,
            'amount_pending' => 0.0,
        ];

        $stmt = $this->db->query("
            SELECT
                status,
                COUNT(*) AS count_items,
                COALESCE(SUM(amount), 0) AS total_amount
            FROM payment_schedules
            GROUP BY status
        ");

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $status = (string) ($row['status'] ?? 'pending');
            $count = (int) ($row['count_items'] ?? 0);
            $amount = (float) ($row['total_amount'] ?? 0);

            $stats['total'] += $count;

            if ($status === 'paid') {
                $stats['paid'] += $count;
                $stats['amount_paid'] += $amount;
            } else {
                $stats['pending'] += $count;
                $stats['amount_pending'] += $amount;
            }
        }

        $lateStmt = $this->db->query("
            SELECT COUNT(*)
            FROM payment_schedules
            WHERE status <> 'paid' AND due_date < CURDATE()
        ");
        $stats['late'] = (int) $lateStmt->fetchColumn();

        return $stats;
    }

    public function getSchedules(?string $status = null): array
    {
        $this->ensureSchedulesForAllAdhesions();

        $params = [];
        $where = '';

        if ($status === 'paid') {
            $where = "WHERE ps.status = 'paid'";
        } elseif ($status === 'late') {
            $where = "WHERE ps.status <> 'paid' AND ps.due_date < CURDATE()";
        } elseif ($status === 'pending') {
            $where = "WHERE ps.status <> 'paid' AND ps.due_date >= CURDATE()";
        }

        $stmt = $this->db->prepare("
            SELECT
                ps.*,
                a.nom,
                a.prenom,
                a.email,
                a.telephone,
                a.status AS adhesion_status
            FROM payment_schedules ps
            INNER JOIN adhesions a ON a.id = ps.adhesion_id
            {$where}
            ORDER BY ps.due_date ASC, a.nom ASC, a.prenom ASC
        ");
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClientSummaries(?string $status = null): array
    {
        $rows = $this->getSchedules($status);
        $clients = [];

        foreach ($rows as $row) {
            $adhesionId = (int) ($row['adhesion_id'] ?? 0);
            if (!isset($clients[$adhesionId])) {
                $clients[$adhesionId] = [
                    'adhesion_id' => $adhesionId,
                    'nom' => $row['nom'] ?? '',
                    'prenom' => $row['prenom'] ?? '',
                    'email' => $row['email'] ?? '',
                    'telephone' => $row['telephone'] ?? '',
                    'total_amount' => 0.0,
                    'paid_amount' => 0.0,
                    'pending_amount' => 0.0,
                    'paid_count' => 0,
                    'pending_count' => 0,
                    'late_count' => 0,
                    'next_due_date' => null,
                    'schedules' => [],
                ];
            }

            $amount = (float) ($row['amount'] ?? 0);
            $isPaid = ($row['status'] ?? '') === 'paid';
            $isLate = !$isPaid && strtotime((string) ($row['due_date'] ?? '')) < strtotime(date('Y-m-d'));

            $clients[$adhesionId]['total_amount'] += $amount;
            $clients[$adhesionId]['schedules'][] = $row;

            if ($isPaid) {
                $clients[$adhesionId]['paid_amount'] += $amount;
                $clients[$adhesionId]['paid_count']++;
            } else {
                $clients[$adhesionId]['pending_amount'] += $amount;
                $clients[$adhesionId]['pending_count']++;
                if ($isLate) {
                    $clients[$adhesionId]['late_count']++;
                }
                if ($clients[$adhesionId]['next_due_date'] === null || $row['due_date'] < $clients[$adhesionId]['next_due_date']) {
                    $clients[$adhesionId]['next_due_date'] = $row['due_date'];
                }
            }
        }

        return array_values($clients);
    }

    public function markPaid(int $scheduleId, string $paymentMethod = '', string $note = ''): bool
    {
        if ($scheduleId <= 0) {
            return false;
        }

        $this->ensureTable();

        $stmt = $this->db->prepare("
            UPDATE payment_schedules
            SET status = 'paid',
                paid_at = NOW(),
                payment_method = :payment_method,
                note = :note,
                updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id' => $scheduleId,
            ':payment_method' => trim($paymentMethod) ?: null,
            ':note' => trim($note) ?: null,
        ]);
    }

    public function markPending(int $scheduleId): bool
    {
        if ($scheduleId <= 0) {
            return false;
        }

        $this->ensureTable();

        $stmt = $this->db->prepare("
            UPDATE payment_schedules
            SET status = 'pending',
                paid_at = NULL,
                payment_method = NULL,
                updated_at = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([':id' => $scheduleId]);
    }

    public function sendReminder(int $adhesionId): bool
    {
        $clients = $this->getClientSummaries();
        $client = null;

        foreach ($clients as $item) {
            if ((int) $item['adhesion_id'] === $adhesionId) {
                $client = $item;
                break;
            }
        }

        if (!$client || empty($client['email'])) {
            return false;
        }

        $smtpUser = $_ENV['SMTP_USER'] ?? Settings::get('contact_email');
        $smtpPass = $_ENV['SMTP_PASS'] ?? 'rocu nndd vkyu usaz';

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = Settings::get('smtp_host', 'smtp.gmail.com');
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUser;
        $mail->Password = $smtpPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = (int) Settings::get('smtp_port', '587');
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($smtpUser, Settings::get('smtp_from_name', 'ECOFI Construction'));
        $mail->addAddress($client['email'], trim(($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? '')));
        $mail->isHTML(true);
        $mail->Subject = 'Relance paiement mensualité - ECOFI';

        $mail->Body = '<p>Bonjour <strong>' . htmlspecialchars(trim(($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? ''))) . '</strong>,</p>'
            . '<p>Nous vous rappelons que votre dossier programme immobilier présente des échéances en attente.</p>'
            . '<p><strong>Montant restant :</strong> ' . number_format((float) $client['pending_amount'], 0, ',', ' ') . ' F CFA</p>'
            . '<p>Merci de contacter ECOFI pour régulariser votre mensualité.</p>';

        return $mail->send();
    }

    private function monthlyAmount(): float
    {
        $value = Settings::get('program_monthly_payment', '50000');
        preg_match('/\d[\d\s]*/', (string) $value, $matches);
        $digits = preg_replace('/[^0-9]/', '', $matches[0] ?? '');

        return $digits !== '' ? (float) $digits : 50000.0;
    }
}
