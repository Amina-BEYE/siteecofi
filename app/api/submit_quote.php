<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Core/Settings.php';
require_once __DIR__ . '/../Core/MailTransportConfig.php';

require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';

use App\Core\Database;
use App\Core\MailTransportConfig;
use App\Core\Settings;
use PHPMailer\PHPMailer\PHPMailer;

function getJsonInput(): array
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (!is_array($data)) {
        throw new RuntimeException('JSON invalide ou vide.');
    }

    return $data;
}

function getAppBaseUrl(): string
{
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $isLocal = str_contains($host, 'localhost') || str_contains($host, '127.0.0.1') || str_contains($host, '::1');

    if (!$isLocal && defined('APP_URL') && APP_URL !== '') {
        return rtrim(APP_URL, '/');
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $host !== '' ? $host : 'localhost:8888';
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/siteecofi/app/api/submit_quote.php';
    $appPos = strpos($scriptName, '/app/api/');
    $basePath = $appPos !== false ? substr($scriptName, 0, $appPos) : '';

    return rtrim($scheme . '://' . $host . $basePath, '/');
}

function generateNumeroDevis(string $type): string
{
    $prefix = $type === 'location' ? 'LOC' : ($type === 'contact' ? 'CONT' : 'DEV');
    return $prefix . '-' . date('Ymd-His');
}

function findOrCreateClient(PDO $pdo, string $nom, string $email, string $telephone): int
{
    $stmt = $pdo->prepare("SELECT id FROM clients WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $existingId = $stmt->fetchColumn();

    if ($existingId) {
        $update = $pdo->prepare("
            UPDATE clients
            SET nom = :nom, telephone = :telephone
            WHERE id = :id
        ");
        $update->execute([
            ':nom' => $nom,
            ':telephone' => $telephone,
            ':id' => (int) $existingId,
        ]);

        return (int) $existingId;
    }

    $insert = $pdo->prepare("
        INSERT INTO clients (nom, email, telephone, created_at)
        VALUES (:nom, :email, :telephone, NOW())
    ");
    $insert->execute([
        ':nom' => $nom,
        ':email' => $email,
        ':telephone' => $telephone,
    ]);

    return (int) $pdo->lastInsertId();
}

function calculateTotalHt(array $items, string $type): float
{
    $total = 0.0;

    foreach ($items as $item) {
        if ($type === 'location') {
            $total += (float) ($item['total_location'] ?? 0);
        } else {
            $prix = (float) ($item['prix'] ?? 0);
            $quantite = (float) ($item['quantite'] ?? 1);
            $total += $prix * $quantite;
        }
    }

    return $total;
}

function createDevis(PDO $pdo, int $clientId, string $numeroDevis, string $message, array $items, string $type): int
{
    $totalHt = calculateTotalHt($items, $type);
    $totalTtc = $totalHt;

    $notes = $message;

    if ($type === 'location') {
        $notes = "[DEVIS LOCATION]\n" . $message;
    } elseif ($type === 'contact') {
        $notes = "[CONTACT]\nService: " . $message . "\nDemande traitée par Ecofi.";
    }

    $stmt = $pdo->prepare("
        INSERT INTO devis (
            client_id, numero_devis, total_ht, total_ttc,
            notes, statut, created_at
        ) VALUES (
            :client_id, :numero_devis, :total_ht, :total_ttc,
            :notes, :statut, NOW()
        )
    ");

    $stmt->execute([
        ':client_id' => $clientId,
        ':numero_devis' => $numeroDevis,
        ':total_ht' => $totalHt,
        ':total_ttc' => $totalTtc,
        ':notes' => $notes,
        ':statut' => 'contact' === $type ? 'traite' : 'en_attente',
    ]);

    return (int) $pdo->lastInsertId();
}

function insertDevisLignes(PDO $pdo, int $devisId, array $items, string $type): void
{
    if (empty($items) || $type === 'contact') return;

    $stmt = $pdo->prepare("
        INSERT INTO devis_lignes (
            devis_id, produit_id, nom_produit, quantite,
            prix_unitaire, total_ligne, created_at
        ) VALUES (
            :devis_id, :produit_id, :nom_produit, :quantite,
            :prix_unitaire, :total_ligne, NOW()
        )
    ");

    foreach ($items as $item) {
        $nomProduit = trim((string) ($item['nom'] ?? 'Article'));

        if ($type === 'location') {
            $quantite = max(1, (int) ($item['quantity'] ?? 1));
            $prixUnitaire = max(0, (float) ($item['price_per_period'] ?? 0));
            $totalLigne = max(0, (float) ($item['total_location'] ?? ($prixUnitaire * $quantite)));

            $periode = (string) ($item['period_text'] ?? '');
            $dateDebut = (string) ($item['startDate'] ?? '');
            $dateFin = (string) ($item['endDate'] ?? '');
            $caution = (float) ($item['caution'] ?? 0);

            $nomProduit .= " — Location {$periode}";

            if ($dateDebut !== '' && $dateFin !== '') {
                $nomProduit .= " du {$dateDebut} au {$dateFin}";
            }

            if ($caution > 0) {
                $nomProduit .= " — Caution : " . number_format($caution, 0, ',', ' ') . " FCFA";
            }
        } else {
            $quantite = max(1, (int) ($item['quantite'] ?? 1));
            $prixUnitaire = max(0, (float) ($item['prix'] ?? 0));
            $totalLigne = $quantite * $prixUnitaire;
        }

        $stmt->execute([
            ':devis_id' => $devisId,
            ':produit_id' => null,
            ':nom_produit' => $nomProduit,
            ':quantite' => $quantite,
            ':prix_unitaire' => $prixUnitaire,
            ':total_ligne' => $totalLigne,
        ]);
    }
}

function fetchDevisPdfContent(int $devisId): string
{
    $url = getAppBaseUrl() . '/app/api/generate_quote_pdf.php?id=' . urlencode((string) $devisId);

    $ch = curl_init($url);

    if ($ch === false) {
        throw new RuntimeException('Impossible d' . chr(8217) . 'initialiser cURL.');
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_FAILONERROR => false,
        CURLOPT_HTTPHEADER => ['Accept: application/pdf'],
    ]);

    $content = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);

    curl_close($ch);

    if ($content === false) {
        throw new RuntimeException("Erreur cURL lors de la récupération du PDF : {$curlErr}");
    }

    if ($httpCode !== 200) {
        throw new RuntimeException("PDF inaccessible — HTTP {$httpCode} — URL : {$url}");
    }

    if (substr($content, 0, 4) !== '%PDF') {
        throw new RuntimeException("Le fichier retourné n’est pas un PDF valide.");
    }

    return $content;
}

function fetchDevisForMail(PDO $pdo, int $devisId): array
{
    $stmt = $pdo->prepare("
        SELECT
            d.id,
            d.numero_devis,
            d.total_ttc,
            d.notes,
            c.nom AS client_nom,
            c.email AS client_email
        FROM devis d
        INNER JOIN clients c ON c.id = d.client_id
        WHERE d.id = :id
        LIMIT 1
    ");

    $stmt->execute([':id' => $devisId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        throw new RuntimeException("Devis introuvable.");
    }

    if (empty($row['client_email']) || !filter_var($row['client_email'], FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException("Email client invalide.");
    }

    return $row;
}

function buildMailer(): PHPMailer
{
    $mail = new PHPMailer(true);

    $smtpUser = MailTransportConfig::smtpUser();
    $smtpPass = MailTransportConfig::smtpPassword();

    if ($smtpPass === '') {
        throw new RuntimeException('SMTP_PASS manquant.');
    }

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

    return $mail;
}

function sendEcofiNotification(PDO $pdo, int $devisId, string $type, string $clientMessage = '', array $items = [], array $clientData = []): void
{
    $ecofiEmail = $_ENV['ECOFI_EMAIL'] ?? Settings::get('quote_email', Settings::get('contact_email'));
    $smtpUser = MailTransportConfig::smtpUser();
    $devis = fetchDevisForMail($pdo, $devisId);
    
    $mail = buildMailer();
    $mail->setFrom($smtpUser, Settings::get('smtp_from_name', 'ECOFI - Site web'));
    $mail->addAddress($ecofiEmail);
    $mail->addReplyTo($devis['client_email'], $devis['client_nom']);
    
    $subject = "🚨 NOUVEAU " . strtoupper($type) . " - " . $devis['numero_devis'];
    
    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px;'>
        <h2 style='color: #1a3a6b;'>Nouvelle demande reçue</h2>
        <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
            <h3>📋 " . ($type === 'contact' ? 'Contact' : 'Devis') . ": <strong>" . htmlspecialchars($devis['numero_devis']) . "</strong></h3>
            <p><strong>👤 Client:</strong> " . htmlspecialchars($devis['client_nom']) . " <br> 📧 " . htmlspecialchars($devis['client_email']) . "</p>
            <p><strong>📞 Téléphone:</strong> " . htmlspecialchars($clientData['telephone'] ?? 'N/A') . "</p>";
    
    if ($type !== 'contact') {
        $body .= "<p><strong>💰 Total:</strong> " . number_format($devis['total_ttc'], 0, ',', ' ') . " FCFA</p>";
    }
    
    $body .= "<p><strong>📂 Type:</strong> " . ucfirst($type) . "</p>
        </div>";

    // Réponse email de confirmation au client (uniquement pour le formulaire Contact)
    if ($type === 'contact') {
        $clientSubject = "✅ Confirmation - ECOFI a bien reçu votre demande";

        $clientBody = "
        <!DOCTYPE html>
        <html lang='fr'>
        <body style='font-family: Arial, sans-serif; background:#f4f6f9; padding:24px;'>
          <div style='max-width:560px; margin:auto; background:#fff; border-radius:10px; overflow:hidden; border:1px solid #eee;'>
            <div style='background:#ff8533; color:#fff; padding:18px 22px;'>
              <h2 style='margin:0; font-size:18px;'>✅ Demande reçue</h2>
              <p style='margin:6px 0 0; opacity:.9;'>ECOFI vous remercie pour votre message</p>
            </div>
            <div style='padding:22px; color:#374151;'>
              <p>Bonjour <strong>" . htmlspecialchars($devis['client_nom']) . "</strong>,</p>
              <p>Nous accusons réception de votre demande :</p>
              <div style='background:#fff3cd; border:1px solid #ffeeba; padding:14px; border-radius:8px; margin:14px 0;'>
                <p style='margin:0;'><strong>Service :</strong> " . nl2br(htmlspecialchars(trim($clientMessage))) . "</p>
              </div>
              <p>Notre équipe vous répondra dans les 24h.</p>
              <p style='margin-top:14px;'><strong>📞 " . htmlspecialchars(Settings::get('phone_mobile')) . "</strong> ou <strong>" . htmlspecialchars(Settings::get('phone_fixed')) . "</strong></p>
            </div>
            <div style='padding:14px 22px; font-size:12px; color:#888; text-align:center;'>ECOFI - Zac Nguinth, Thiès, Sénégal</div>
          </div>
        </body>
        </html>
        ";

        $body .= "<h3>💬 Message:</h3><div style='background: #e3f2fd; padding: 15px; border-left: 4px solid #1a3a6b;'>" . nl2br(htmlspecialchars($clientMessage)) . "</div>";
    }
    
    if (!empty($items)) {
        $body .= "<h3>🛒 Articles:</h3><table style='width:100%; border-collapse: collapse; margin-top: 10px;'>";
        $body .= "<tr style='background: #1a3a6b; color: white;'><th>Produit</th><th>Qté</th><th>Prix U.</th><th>Total</th></tr>";
        foreach ($items as $item) {
            $body .= "<tr style='border-bottom: 1px solid #eee;'>";
            $body .= "<td>" . htmlspecialchars($item['nom_produit'] ?? $item['nom'] ?? 'N/A') . "</td>";
            $body .= "<td>" . ($item['quantite'] ?? 1) . "</td>";
            $body .= "<td>" . number_format($item['prix_unitaire'] ?? $item['prix'] ?? 0, 0, ',', ' ') . " FCFA</td>";
            $body .= "<td style='font-weight: bold;'>" . number_format($item['total_ligne'] ?? $item['total'] ?? 0, 0, ',', ' ') . " FCFA</td>";
            $body .= "</tr>";
        }
        $body .= "</table>";
    }
    
    $pdfLink = getAppBaseUrl() . "/app/api/generate_quote_pdf.php?id=" . $devisId;
    $body .= "<p style='text-align: center; margin: 20px 0;'><a href='" . $pdfLink . "' style='background: #ff8533; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;'>📄 Voir PDF / Devis</a></p>";
    
    $body .= "</div>";
    
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    
    $mail->send();

    if ($type === 'contact' && isset($clientSubject, $clientBody)) {
        try {
            $clientMail = buildMailer();
            $clientMail->setFrom($smtpUser, 'ECOFI Service Client');
            $clientMail->addAddress($devis['client_email'], $devis['client_nom']);
            $clientMail->addReplyTo($ecofiEmail, 'ECOFI');
            $clientMail->isHTML(true);
            $clientMail->Subject = $clientSubject;
            $clientMail->Body = $clientBody;
            $clientMail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $clientBody));
            $clientMail->send();
        } catch (Throwable $e) {
            error_log('[submit_quote_client_mail] ' . $e->getMessage());
        }
    }
}

function buildEmailHtml(string $clientNom, string $numeroDevis, float $totalTtc): string
{
    $nom = htmlspecialchars($clientNom, ENT_QUOTES, 'UTF-8');
    $numero = htmlspecialchars($numeroDevis, ENT_QUOTES, 'UTF-8');
    $total = number_format($totalTtc, 0, ',', ' ') . ' FCFA';

    return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<body style="font-family: Arial, sans-serif; background:#f4f6f9; padding:24px;">
  <div style="max-width:580px; margin:auto; background:#fff; border-radius:8px; overflow:hidden;">
    <div style="background:#1a3a6b; padding:28px 32px;">
      <h1 style="color:#fff; margin:0;">ECOFI</h1>
      <p style="color:rgba(255,255,255,.7); margin:4px 0 0;">Solutions financières & industrielles</p>
    </div>
    <div style="padding:32px; color:#374151; font-size:14px; line-height:1.7;">
      <p>Bonjour <strong>{$nom}</strong>,</p>
      <p>Veuillez trouver ci-joint votre devis.</p>
      <div style="background:#f0f4ff; border-left:4px solid #1a3a6b; padding:14px 18px; margin:20px 0;">
        <strong>Devis N° {$numero}</strong><br>
        Montant total : <strong>{$total}</strong>
      </div>
      <p>Cordialement,<br><strong>L'équipe ECOFI</strong></p>
    </div>
  </div>
</body>
</html>
HTML;
}

function sendQuoteEmail(int $devisId): void
{
    $pdo = Database::getConnection();
    $devis = fetchDevisForMail($pdo, $devisId);
    $pdf = null;

    try {
        $pdf = fetchDevisPdfContent($devisId);
    } catch (Throwable $e) {
        error_log('[submit_quote_pdf_mail] ' . $e->getMessage());
    }

    $senderEmail = MailTransportConfig::smtpUser();
    $senderName = $_ENV['MAIL_FROM_NAME'] ?? Settings::get('smtp_from_name', 'ECOFI');

    $mail = buildMailer();
    $mail->setFrom($senderEmail, $senderName);
    $mail->addAddress($devis['client_email'], $devis['client_nom']);

    $mail->isHTML(true);
    $mail->Subject = 'Votre devis ' . $devis['numero_devis'] . ' — ECOFI';
    $mail->Body = buildEmailHtml(
        $devis['client_nom'],
        $devis['numero_devis'],
        (float) $devis['total_ttc']
    );

    $mail->AltBody = "Bonjour {$devis['client_nom']},\n\nVeuillez trouver ci-joint votre devis {$devis['numero_devis']}.\n\nCordialement,\nECOFI";

    if ($pdf !== null) {
        $mail->addStringAttachment(
            $pdf,
            'devis-' . $devis['numero_devis'] . '.pdf',
            'base64',
            'application/pdf'
        );
    }

    $mail->send();
}

try {
    $input = getJsonInput();

    $type = trim((string) ($input['type'] ?? 'achat'));
    if (!in_array($type, ['achat', 'location', 'contact'], true)) {
        $type = 'achat';
    }

    $nom = trim((string) ($input['nom'] ?? ''));
    $email = trim((string) ($input['email'] ?? ''));
    $telephone = trim((string) ($input['telephone'] ?? ''));
    $message = trim((string) ($input['message'] ?? ''));
    $items = $input['items'] ?? [];
    
    if ($type === 'contact') {
        $items = [];
        $message = trim((string) ($input['service'] ?? '')) . "\n\n" . $message;
    }

    if ($nom === '' || $email === '' || $telephone === '') {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Veuillez remplir les champs obligatoires.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Adresse email invalide.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($type !== 'contact' && (!is_array($items) || count($items) === 0)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Votre panier est vide.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $pdo = Database::getConnection();
    $pdo->beginTransaction();

    $clientId = findOrCreateClient($pdo, $nom, $email, $telephone);
    $numeroDevis = generateNumeroDevis($type);
    $devisId = createDevis($pdo, $clientId, $numeroDevis, $message, $items, $type);
    insertDevisLignes($pdo, $devisId, $items, $type);

    $pdo->commit();

    $mailErrors = [];

    try {
        sendEcofiNotification($pdo, $devisId, $type, $message, $items, ['telephone' => $telephone]);
    } catch (Throwable $e) {
        error_log('[submit_quote_admin_mail] ' . $e->getMessage());
        $mailErrors[] = 'notification_admin';
    }

    if ($type !== 'contact') {
        try {
            sendQuoteEmail($devisId);
        } catch (Throwable $e) {
            error_log('[submit_quote_client_mail] ' . $e->getMessage());
            $mailErrors[] = 'notification_client';
        }

        echo json_encode([
            'success' => true,
            'message' => empty($mailErrors)
                ? 'Votre demande de devis a bien été envoyée.'
                : 'Votre demande de devis est enregistrée. L’envoi email sera vérifié par notre équipe.',
            'devis_id' => $devisId,
            'numero_devis' => $numeroDevis,
            'type' => $type,
            'pdf_url' => getAppBaseUrl() . '/app/api/generate_quote_pdf.php?id=' . $devisId,
            'mail_sent' => empty($mailErrors)
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => true,
            'message' => empty($mailErrors)
                ? 'Votre message est bien envoyé ! Merci pour votre demande, nous vous répondons sous 24h.'
                : 'Votre demande est enregistrée. L’envoi email sera vérifié par notre équipe.',
            'devis_id' => $devisId,
            'numero_devis' => $numeroDevis,
            'mail_sent' => empty($mailErrors)
        ], JSON_UNESCAPED_UNICODE);
    }

    exit;
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('[submit_quote] ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Impossible d’enregistrer votre demande de devis. Merci de réessayer plus tard.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
