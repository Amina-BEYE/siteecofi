<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Core/Settings.php';
require_once __DIR__ . '/app/lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/app/lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/app/lib/PHPMailer/src/SMTP.php';

use App\Core\Database;
use App\Core\Settings;
use PHPMailer\PHPMailer\PHPMailer;

$sendTo = Settings::get('quote_email', Settings::get('contact_email'));

function clean_input(string $value): string
{
    $value = trim($value);
    $value = str_replace(["\r", "\n", "%0a", "%0d"], '', $value);
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function sendEcofiMail(string $to, string $subject, string $body, string $replyTo = '', string $altBody = ''): bool
{
    try {
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
        $mail->addAddress($to);

        if ($replyTo !== '' && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $mail->addReplyTo($replyTo);
        }

        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $body;
        $mail->AltBody = $altBody !== '' ? $altBody : strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $body));

        return $mail->send();
    } catch (Throwable $e) {
        error_log('[adhesion_mail] ' . $e->getMessage());
        return false;
    }
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function buildMailLayout(string $title, string $intro, string $contentHtml): string
{
    $fromName = h(Settings::get('smtp_from_name', 'ECOFI Construction'));
    $phoneFixed = h(Settings::get('phone_fixed'));
    $phoneMobile = h(Settings::get('phone_mobile'));
    $contactEmail = h(Settings::get('contact_email'));

    return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<body style="margin:0; padding:0; background:#f4f6f9; font-family:Arial, sans-serif; color:#344054;">
  <div style="max-width:640px; margin:0 auto; padding:28px 16px;">
    <div style="background:#ffffff; border:1px solid #eef2f6; border-radius:14px; overflow:hidden;">
      <div style="background:#111111; padding:26px 30px; color:#ffffff;">
        <div style="font-size:13px; font-weight:700; color:#ff8533; margin-bottom:8px;">{$fromName}</div>
        <h1 style="margin:0; font-size:24px; line-height:1.25;">{$title}</h1>
        <p style="margin:10px 0 0; color:rgba(255,255,255,.82); line-height:1.6;">{$intro}</p>
      </div>
      <div style="padding:26px 30px;">
        {$contentHtml}
      </div>
      <div style="background:#f8fafc; border-top:1px solid #eef2f6; padding:18px 30px; font-size:13px; color:#667085; line-height:1.6;">
        <strong style="color:#344054;">{$fromName}</strong><br>
        Téléphone : {$phoneFixed} / {$phoneMobile}<br>
        Email : {$contactEmail}
      </div>
    </div>
  </div>
</body>
</html>
HTML;
}

function buildInfoRows(array $rows): string
{
    $html = '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">';

    foreach ($rows as $label => $value) {
        $html .= '<tr>';
        $html .= '<td style="padding:11px 0; border-bottom:1px solid #eef2f6; color:#667085; font-size:13px; width:42%;">' . h((string) $label) . '</td>';
        $html .= '<td style="padding:11px 0; border-bottom:1px solid #eef2f6; color:#101828; font-size:14px; font-weight:700;">' . nl2br(h((string) $value)) . '</td>';
        $html .= '</tr>';
    }

    $html .= '</table>';

    return $html;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php#programme-immo');
    exit;
}

$fields = [
    'nom' => 'Nom',
    'prenom' => 'Prénom',
    'date_naissance' => 'Date de naissance',
    'lieu_naissance' => 'Lieu de naissance',
    'adresse' => 'Adresse',
    'telephone' => 'Téléphone',
    'cni' => 'N°CNI / Passeport',
    'email' => 'Email',
    'mode_paiement' => 'Mode de paiement',
];

$data = [];
$errors = [];

foreach ($fields as $key => $label) {
    $value = $_POST[$key] ?? '';
    $data[$key] = clean_input($value);
    if ($data[$key] === '') {
        $errors[] = "Le champ {$label} est requis.";
    }
}

$data['message'] = clean_input($_POST['message'] ?? '');

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Veuillez renseigner une adresse email valide.';
}

if (!preg_match('/^[0-9 +()\\-]{7,20}$/', $data['telephone'])) {
    $errors[] = 'Veuillez renseigner un numéro de téléphone valide.';
}

if ($errors) {
    $errorHtml = '<ul>';
    foreach ($errors as $error) {
        $errorHtml .= '<li>' . $error . '</li>';
    }
    $errorHtml .= '</ul>';
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Erreur d\'adhésion - ECOFI</title><link rel="stylesheet" href="style.css"></head><body><div class="container" style="padding:4rem 0; max-width:720px;"><h1>Erreur lors de l\'envoi</h1><p>Votre formulaire n\'a pas pu être envoyé pour les raisons suivantes :</p>' . $errorHtml . '<p><a href="index.php#programme-immo" class="submit-btn-elegant">Retour au formulaire</a></p></div></body></html>';
    exit;
}

try {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare('INSERT INTO adhesions (nom, prenom, date_naissance, lieu_naissance, adresse, telephone, cni, email, mode_paiement, message, status, created_at, updated_at) VALUES (:nom, :prenom, :date_naissance, :lieu_naissance, :adresse, :telephone, :cni, :email, :mode_paiement, :message, :status, NOW(), NOW())');
    $stmt->execute([
        ':nom' => $data['nom'],
        ':prenom' => $data['prenom'],
        ':date_naissance' => $data['date_naissance'],
        ':lieu_naissance' => $data['lieu_naissance'],
        ':adresse' => $data['adresse'],
        ':telephone' => $data['telephone'],
        ':cni' => $data['cni'],
        ':email' => $data['email'],
        ':mode_paiement' => $data['mode_paiement'],
        ':message' => $data['message'],
        ':status' => 'Nouveau',
    ]);
    $adhesionId = $pdo->lastInsertId();
} catch (Throwable $e) {
    error_log('[adhesion] Database error: ' . $e->getMessage());

    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Erreur base de données - ECOFI</title><link rel="stylesheet" href="style.css"></head><body><div class="container" style="padding:4rem 0; max-width:720px;"><h1>Erreur de base de données</h1><p>Impossible d\'enregistrer votre demande. Merci de réessayer plus tard.</p><p><a href="index.php#programme-immo" class="submit-btn-elegant">Retour au formulaire</a></p></div></body></html>';
    exit;
}

$adminSubject = 'Nouvelle adhésion - Programme ECOFI Construction';
$adminRows = [
    'ID demande' => (string) $adhesionId,
    'Nom complet' => "{$data['prenom']} {$data['nom']}",
    'Date de naissance' => $data['date_naissance'],
    'Lieu de naissance' => $data['lieu_naissance'],
    'Adresse' => $data['adresse'],
    'Téléphone' => $data['telephone'],
    'Email' => $data['email'],
    'CNI / Passeport' => $data['cni'],
    'Mode de paiement' => $data['mode_paiement'],
    'Message' => $data['message'] ?: 'Aucun message fourni',
    'Date de soumission' => date('Y-m-d H:i:s'),
];
$adminContent = '<p style="margin:0 0 18px; line-height:1.7;">Une nouvelle demande d’adhésion a été soumise depuis le site ECOFI.</p>';
$adminContent .= buildInfoRows($adminRows);
$adminContent .= '<p style="margin:22px 0 0;"><a href="mailto:' . h($data['email']) . '" style="display:inline-block; background:#ff8533; color:#111; padding:12px 18px; border-radius:999px; text-decoration:none; font-weight:800;">Répondre au demandeur</a></p>';
$adminMailHtml = buildMailLayout('Nouvelle adhésion programme immo', 'Un prospect vient de soumettre son dossier pour ' . h(Settings::get('program_title')) . '.', $adminContent);

$adminText = "Nouvelle adhésion - Programme ECOFI Construction\n\n";
foreach ($adminRows as $label => $value) {
    $adminText .= "{$label} : {$value}\n";
}

sendEcofiMail($sendTo, $adminSubject, $adminMailHtml, $data['email'], $adminText);

$clientSubject = 'Confirmation de votre demande d\'adhésion au programme immobilier ECOFI';
$clientRows = [
    'Programme' => Settings::get('program_title'),
    'Localisation' => Settings::get('program_location'),
    'Acompte' => Settings::get('program_deposit'),
    'Mensualité' => Settings::get('program_monthly_payment'),
    'Durée' => '24 mois',
    'Frais de dossier' => '25 000 F CFA',
    'Référence dossier' => '#' . $adhesionId,
];
$clientContent = '<p style="margin:0 0 18px; line-height:1.7;">Bonjour <strong>' . h($data['prenom'] . ' ' . $data['nom']) . '</strong>,</p>';
$clientContent .= '<p style="margin:0 0 18px; line-height:1.7;">Nous avons bien reçu votre demande d’adhésion au programme immobilier ECOFI Construction. Notre équipe va étudier votre dossier et vous recontactera prochainement.</p>';
$clientContent .= buildInfoRows($clientRows);
$clientContent .= '<div style="margin-top:22px; padding:16px 18px; background:#fff7ed; border:1px solid rgba(255,133,51,.28); border-radius:12px;">';
$clientContent .= '<strong style="display:block; color:#111; margin-bottom:10px;">Documents à préparer</strong>';
$clientContent .= '<ul style="margin:0; padding-left:20px; line-height:1.8;">';
$clientContent .= '<li>Copie CNI ou Passeport</li>';
$clientContent .= '<li>Numéro de téléphone valide</li>';
$clientContent .= '<li>Adresse complète</li>';
$clientContent .= '<li>Frais de dossier</li>';
$clientContent .= '<li>Justificatif de paiement, si disponible</li>';
$clientContent .= '</ul></div>';
$clientMailHtml = buildMailLayout('Demande d’adhésion reçue', 'Votre dossier pour le programme immobilier ECOFI a bien été enregistré.', $clientContent);

$clientText = "Bonjour {$data['prenom']} {$data['nom']},\n\n";
$clientText .= "Nous avons bien reçu votre demande d'adhésion au programme immobilier ECOFI Construction.\n\n";
foreach ($clientRows as $label => $value) {
    $clientText .= "{$label} : {$value}\n";
}
$clientText .= "\nDocuments à préparer : " . Settings::get('program_documents') . "\n\n";
$clientText .= "Cordialement,\n" . Settings::get('smtp_from_name', 'ECOFI Construction') . "\nTéléphone : " . Settings::get('phone_fixed') . " / " . Settings::get('phone_mobile') . "\nEmail : " . Settings::get('contact_email') . "\n";

sendEcofiMail($data['email'], $clientSubject, $clientMailHtml, $sendTo, $clientText);

header('Location: confirmation.html');
exit;
