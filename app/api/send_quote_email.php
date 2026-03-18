<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../Core/Database.php';

use App\Core\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PDO;

function buildPdfFile(int $devisId): string
{
    return 'http://localhost:8888/SITEECOFI/app/api/generate_quote_pdf.php?id=' . $devisId;
}

function sendQuoteEmail(int $devisId): bool
{
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("
        SELECT 
            d.numero_devis,
            d.id,
            c.nom,
            c.email
        FROM devis d
        INNER JOIN clients c ON c.id = d.client_id
        WHERE d.id = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $devisId]);
    $devis = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$devis) {
        return false;
    }

    $pdfContent = file_get_contents(buildPdfFile($devisId));
    if ($pdfContent === false) {
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'service.ecofi01@gmail.com';
        $mail->Password = 'TON_MOT_DE_PASSE_APPLICATION';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('service.ecofi01@gmail.com', 'ECOFI');
        $mail->addAddress($devis['email'], $devis['nom']);
        $mail->addAddress('service.ecofi01@gmail.com', 'ECOFI');

        $mail->isHTML(true);
        $mail->Subject = 'Votre devis ' . $devis['numero_devis'];
        $mail->Body = '
            <p>Bonjour ' . htmlspecialchars($devis['nom']) . ',</p>
            <p>Votre demande de devis a bien été enregistrée.</p>
            <p>Veuillez trouver ci-joint votre devis <strong>' . htmlspecialchars($devis['numero_devis']) . '</strong>.</p>
            <p>Cordialement,<br>ECOFI</p>
        ';

        $mail->addStringAttachment(
            $pdfContent,
            'devis-' . $devis['numero_devis'] . '.pdf',
            'base64',
            'application/pdf'
        );

        return $mail->send();

    } catch (Exception $e) {
        return false;
    }
}