<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../Core/Database.php';

// Charge PHPMailer si tu l'as installé manuellement.
// Ajuste ces chemins si besoin.
require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';

use App\Core\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailerException;

/**
 * Lit le JSON envoyé par fetch(...)
 */
function getJsonInput(): array
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (!is_array($data)) {
        throw new RuntimeException('JSON invalide ou vide.');
    }

    return $data;
}

/**
 * Génère un numéro de devis simple.
 */
function generateNumeroDevis(): string
{
    return 'DEV-' . date('Ymd-His');
}

/**
 * Crée ou met à jour un client par email.
 */
function findOrCreateClient(PDO $pdo, string $nom, string $email, string $telephone): int
{
    $stmt = $pdo->prepare("
        SELECT id
        FROM clients
        WHERE email = :email
        LIMIT 1
    ");
    $stmt->execute([':email' => $email]);
    $existingId = $stmt->fetchColumn();

    if ($existingId) {
        $update = $pdo->prepare("
            UPDATE clients
            SET nom = :nom,
                telephone = :telephone
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

/**
 * Crée l'entête du devis.
 */
function createDevis(PDO $pdo, int $clientId, string $numeroDevis, string $message, array $items): int
{
    $totalHt = 0.0;

    foreach ($items as $item) {
        $prix = (float) ($item['prix'] ?? 0);
        $quantite = (float) ($item['quantite'] ?? 1);
        $totalHt += $prix * $quantite;
    }

    $tva = 0.0;
    $totalTtc = $totalHt + $tva;

    $stmt = $pdo->prepare("
        INSERT INTO devis (
            client_id,
            numero_devis,
            total_ht,
            total_ttc,
            notes,
            statut,
            created_at
        ) VALUES (
            :client_id,
            :numero_devis,
            :total_ht,
            :total_ttc,
            :notes,
            :statut,
            NOW()
        )
    ");

    $stmt->execute([
        ':client_id' => $clientId,
        ':numero_devis' => $numeroDevis,
        ':total_ht' => $totalHt,
        ':total_ttc' => $totalTtc,
        ':notes' => $message,
        ':statut' => 'en_attente',
    ]);

    return (int) $pdo->lastInsertId();
}

/**
 * Valide l'id produit.
 * Retourne null si la valeur n'est pas un entier positif acceptable.
 */
function sanitizeProduitId(mixed $value): ?int
{
    if ($value === null || $value === '' || $value === false) {
        return null;
    }

    if (is_string($value)) {
        $value = trim($value);
        if ($value === '') {
            return null;
        }
    }

    if (!is_numeric($value)) {
        return null;
    }

    // On convertit d'abord en string pour contrôler proprement.
    $stringValue = (string) $value;

    // Pas de décimaux, pas de notation scientifique, pas de valeurs bizarres.
    if (!preg_match('/^\d+$/', $stringValue)) {
        return null;
    }

    $intValue = (int) $stringValue;

    if ($intValue <= 0) {
        return null;
    }

    return $intValue;
}

/**
 * Crée les lignes du devis.
 * Si produit_id n'est pas valide, on enregistre NULL.
 */
function insertDevisLignes(PDO $pdo, int $devisId, array $items): void
{
    $stmt = $pdo->prepare("
        INSERT INTO devis_lignes (
            devis_id,
            produit_id,
            nom_produit,
            quantite,
            prix_unitaire,
            total_ligne,
            created_at
        ) VALUES (
            :devis_id,
            :produit_id,
            :nom_produit,
            :quantite,
            :prix_unitaire,
            :total_ligne,
            NOW()
        )
    ");

    foreach ($items as $item) {
        //$produitId = sanitizeProduitId($item['id'] ?? null);
        $produitId = null;
        $nomProduit = trim((string) ($item['nom'] ?? 'Article'));
        $quantite = max(1, (int) ($item['quantite'] ?? 1));
        $prixUnitaire = max(0, (float) ($item['prix'] ?? 0));
        $totalLigne = $quantite * $prixUnitaire;

        $stmt->execute([
            ':devis_id' => $devisId,
            ':produit_id' => $produitId,
            ':nom_produit' => $nomProduit,
            ':quantite' => $quantite,
            ':prix_unitaire' => $prixUnitaire,
            ':total_ligne' => $totalLigne,
        ]);
    }
}

/**
 * Récupère le PDF généré.
 */
function fetchDevisPdfContent(int $devisId): string
{
    $baseUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost:8888', '/');
    $url = $baseUrl . '/SITEECOFI/app/api/generate_quote_pdf.php?id=' . $devisId;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_FAILONERROR    => false,
    ]);

    $content = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if ($content === false || $httpCode !== 200) {
        throw new RuntimeException(
            "PDF inaccessible pour le devis #$devisId — HTTP $httpCode — $curlErr"
        );
    }

    return $content;
}

/**
 * Charge les infos devis/client pour l'email.
 */
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
        throw new RuntimeException("Devis introuvable en base : id=$devisId");
    }

    if (
        empty($row['client_email']) ||
        !filter_var($row['client_email'], FILTER_VALIDATE_EMAIL)
    ) {
        throw new RuntimeException(
            "Email client invalide pour le devis #$devisId : {$row['client_email']}"
        );
    }

    return $row;
}

/**
 * Construit l'instance mailer.
 */
function buildMailer(): PHPMailer
{
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    $mail->Username = 'fallrago@gmail.com'; // TON EMAIL
    $mail->Password = 'pkht vzuz txtb hasb'; // 🔥 mot de passe application

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';

    return $mail;
}



/**
 * Corps HTML de l'email.
 */
function buildEmailHtml(string $clientNom, string $numeroDevis, float $totalTtc): string
{
    $nom    = htmlspecialchars($clientNom, ENT_QUOTES, 'UTF-8');
    $numero = htmlspecialchars($numeroDevis, ENT_QUOTES, 'UTF-8');
    $total  = number_format($totalTtc, 0, ',', ' ') . ' FCFA';

    return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
    .wrapper { max-width: 580px; margin: 32px auto; background: #ffffff; border-radius: 8px; overflow: hidden; }
    .header { background: #1a3a6b; padding: 28px 32px; }
    .header h1 { color: #ffffff; font-size: 22px; margin: 0; }
    .header p { color: rgba(255,255,255,0.65); font-size: 12px; margin: 4px 0 0; }
    .body { padding: 32px; color: #374151; font-size: 14px; line-height: 1.7; }
    .highlight { background: #f0f4ff; border-left: 4px solid #1a3a6b; padding: 14px 18px; border-radius: 4px; margin: 20px 0; }
    .footer { background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 18px 32px; font-size: 11px; color: #9ca3af; text-align: center; }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="header">
      <h1>ECOFI</h1>
      <p>Solutions financières &amp; industrielles</p>
    </div>
    <div class="body">
      <p>Bonjour <strong>{$nom}</strong>,</p>
      <p>Veuillez trouver ci-joint votre devis.</p>
      <div class="highlight">
        <strong>Devis N° {$numero}</strong><br>
        Montant total TTC : <strong>{$total}</strong>
      </div>
      <p>Cordialement,<br><strong>L'équipe ECOFI</strong></p>
    </div>
    <div class="footer">
      ECOFI SARL
    </div>
  </div>
</body>
</html>
HTML;
}

/**
 * Envoie le devis par email.
 */
function sendQuoteEmail(int $devisId): void
{
    $pdo = Database::getConnection();
    $devis = fetchDevisForMail($pdo, $devisId);
    $pdf = fetchDevisPdfContent($devisId);

    $senderEmail = $_ENV['SMTP_USER'] ?? 'service.ecofi01@gmail.com';
    $senderName  = $_ENV['MAIL_FROM_NAME'] ?? 'ECOFI';

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

    $mail->AltBody = sprintf(
        "Bonjour %s,\n\nVeuillez trouver ci-joint votre devis %s.\n\nCordialement,\nECOFI",
        $devis['client_nom'],
        $devis['numero_devis']
    );

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
        ]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Adresse email invalide.'
        ]);
        exit;
    }

    if (!is_array($items) || count($items) === 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Votre panier est vide.'
        ]);
        exit;
    }

    $pdo = Database::getConnection();
    $pdo->beginTransaction();

    $clientId = findOrCreateClient($pdo, $nom, $email, $telephone);
    $numeroDevis = generateNumeroDevis();
    $devisId = createDevis($pdo, $clientId, $numeroDevis, $message, $items);
    insertDevisLignes($pdo, $devisId, $items);

    $pdo->commit();

    sendQuoteEmail($devisId);

    echo json_encode([
        'success' => true,
        'message' => 'Votre demande de devis a bien été envoyée.',
        'devis_id' => $devisId,
        'numero_devis' => $numeroDevis,
        'pdf_url' => '/SITEECOFI/app/api/generate_quote_pdf.php?id=' . $devisId
    ]);
    exit;
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}