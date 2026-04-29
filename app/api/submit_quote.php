<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Core/Database.php';

require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';

use App\Core\Database;
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
    if (defined('APP_URL') && APP_URL !== '') {
        return rtrim(APP_URL, '/');
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8888';

    return $scheme . '://' . $host . '/siteecofi';
}

function generateNumeroDevis(string $type): string
{
    $prefix = $type === 'location' ? 'LOC' : 'DEV';
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
        ':statut' => 'en_attente',
    ]);

    return (int) $pdo->lastInsertId();
}

function insertDevisLignes(PDO $pdo, int $devisId, array $items, string $type): void
{
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
        throw new RuntimeException('Impossible d’initialiser cURL.');
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

    $smtpUser = $_ENV['SMTP_USER'] ?? 'service.ecofi01@gmail.com';
    $smtpPass = $_ENV['SMTP_PASS'] ?? 'rocu nndd vkyu usaz';

    if ($smtpPass === '') {
        throw new RuntimeException('SMTP_PASS manquant.');
    }

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
<<<<<<< HEAD

    $mail->Username = 'service.ecofi01@gmail.com'; // TON EMAIL
    $mail->Password = 'vaeh oqzb fnfr sfbj'; // 🔥 mot de passe application

=======
    $mail->Username = $smtpUser;
    $mail->Password = $smtpPass;
>>>>>>> 59ab8a05f02de9ab0f7c452461dcbdb109dcbb43
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';

    return $mail;
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
    $pdf = fetchDevisPdfContent($devisId);

    $senderEmail = $_ENV['SMTP_USER'] ?? 'service.ecofi01@gmail.com';
    $senderName = $_ENV['MAIL_FROM_NAME'] ?? 'ECOFI';

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

    $mail->addStringAttachment(
        $pdf,
        'devis-' . $devis['numero_devis'] . '.pdf',
        'base64',
        'application/pdf'
    );

    $mail->send();
}

try {
    $input = getJsonInput();

    $type = trim((string) ($input['type'] ?? 'achat'));
    if (!in_array($type, ['achat', 'location'], true)) {
        $type = 'achat';
    }

    $nom = trim((string) ($input['nom'] ?? ''));
    $email = trim((string) ($input['email'] ?? ''));
    $telephone = trim((string) ($input['telephone'] ?? ''));
    $message = trim((string) ($input['message'] ?? ''));
    $items = $input['items'] ?? [];

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

    if (!is_array($items) || count($items) === 0) {
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

    sendQuoteEmail($devisId);

    echo json_encode([
        'success' => true,
        'message' => 'Votre demande de devis a bien été envoyée.',
        'devis_id' => $devisId,
        'numero_devis' => $numeroDevis,
        'type' => $type,
        'pdf_url' => getAppBaseUrl() . '/app/api/generate_quote_pdf.php?id=' . $devisId
    ], JSON_UNESCAPED_UNICODE);

    exit;

} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);

    exit;
}