<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Core/Settings.php';
require_once __DIR__ . '/../Core/MailTransportConfig.php';
require_once __DIR__ . '/../admin/Models/NewsletterModel.php';
require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';

use App\Core\MailTransportConfig;
use App\Core\Settings;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

function jsonResponse(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function inputData(): array
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    if (str_contains($contentType, 'application/json')) {
        $json = file_get_contents('php://input');
        $data = json_decode((string) $json, true);

        return is_array($data) ? $data : [];
    }

    return $_POST;
}

function cleanValue(?string $value): string
{
    return trim((string) $value);
}

function sendNewsletterNotification(
    string $name,
    string $phone,
    string $email,
    string $interest
): void {
    $smtpUser = MailTransportConfig::smtpUser();
    $smtpPass = MailTransportConfig::smtpPassword();
    $smtpHost = MailTransportConfig::smtpHost();
    $smtpPort = MailTransportConfig::smtpPort();
    $smtpEncryption = MailTransportConfig::smtpEncryption();

    if ($smtpUser === '' || $smtpPass === '' || $smtpHost === '') {
        throw new RuntimeException('Configuration SMTP incomplète.');
    }

    $to = Settings::get('quote_email', Settings::get('contact_email'));

    if (!$to || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('Email destinataire non configuré ou invalide.');
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUser;
        $mail->Password = $smtpPass;
        $mail->Port = $smtpPort;
        $mail->CharSet = 'UTF-8';

        if ($smtpEncryption === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($smtpEncryption === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mail->setFrom($smtpUser, MailTransportConfig::fromName());
        $mail->addAddress($to);
        $mail->addBCC($email);
        $mail->addReplyTo($email, $name);

        $mail->isHTML(true);
        $mail->Subject = 'Nouvelle inscription newsletter ECOFI';


        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $safePhone = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
        $safeEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $safeInterest = htmlspecialchars($interest, ENT_QUOTES, 'UTF-8');

        $mail->Body = "
            <h2>Nouvelle inscription newsletter ECOFI</h2>
            <p><strong>Nom :</strong> {$safeName}</p>
            <p><strong>Téléphone :</strong> {$safePhone}</p>
            <p><strong>Email :</strong> {$safeEmail}</p>
            <p><strong>Intérêt :</strong> {$safeInterest}</p>
        ";

        $mail->AltBody =
            "Nouvelle inscription newsletter ECOFI\n"
            . "Nom : {$name}\n"
            . "Téléphone : {$phone}\n"
            . "Email : {$email}\n"
            . "Intérêt : {$interest}";

        $mail->send();
    } catch (MailException $e) {
        throw new RuntimeException('Erreur envoi email : ' . $e->getMessage());
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse([
            'success' => false,
            'message' => 'Méthode non autorisée.'
        ], 405);
    }

    $data = inputData();

    $name = cleanValue($data['name'] ?? $data['nom'] ?? '');
    $phone = cleanValue($data['phone'] ?? $data['telephone'] ?? '');
    $email = cleanValue($data['email'] ?? '');
    $interest = cleanValue($data['interest'] ?? $data['interet'] ?? 'programme');

    if ($name === '') {
        jsonResponse([
            'success' => false,
            'message' => 'Le nom est obligatoire.'
        ], 422);
    }

    if ($phone === '') {
        jsonResponse([
            'success' => false,
            'message' => 'Le téléphone est obligatoire.'
        ], 422);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse([
            'success' => false,
            'message' => 'Adresse email invalide.'
        ], 422);
    }

    $model = new NewsletterModel();

    // 1. Insertion en base
    $inserted = $model->subscribe($name, $phone, $email, $interest);

    if (!$inserted) {
        throw new RuntimeException('Impossible d’enregistrer l’inscription en base de données.');
    }

    // 2. Envoi email
    $mailSent = true;
    $mailWarning = null;

    try {
        sendNewsletterNotification($name, $phone, $email, $interest);
    } catch (Throwable $e) {
        $mailSent = false;
        $mailWarning = $e->getMessage();
        error_log('[newsletter_mail_error] ' . $e->getMessage());
    }

    jsonResponse([
        'success' => true,
        'message' => 'Inscription confirmée avec succès.',
        'mail_sent' => $mailSent,
        'mail_warning' => $mailWarning
    ]);

} catch (Throwable $e) {
    error_log('[newsletter_subscribe_error] ' . $e->getMessage());

    jsonResponse([
        'success' => false,
        'message' => 'Erreur lors de l’inscription : ' . $e->getMessage()
    ], 500);
}