<?php

declare(strict_types=1);

// Charge les dépendances Composer
require_once __DIR__ . '/../../vendor/autoload.php';

// Connexion base de données
require_once __DIR__ . '/../Core/Database.php';

use App\Core\Database;
use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    exit('ID devis invalide');
}

$devisId = (int) $_GET['id'];
$pdo = Database::getConnection();

// Récupération du devis
$stmt = $pdo->prepare("
    SELECT 
        d.id,
        d.numero_devis,
        d.total_ht,
        d.total_ttc,
        d.notes,
        d.statut,
        d.created_at,
        c.nom,
        c.email,
        c.telephone
    FROM devis d
    INNER JOIN clients c ON c.id = d.client_id
    WHERE d.id = :id
    LIMIT 1
");
$stmt->execute([':id' => $devisId]);
$devis = $stmt->fetch(\PDO::FETCH_ASSOC);

if (!$devis) {
    http_response_code(404);
    exit('Devis introuvable');
}

// Récupération des lignes du devis
$stmtLines = $pdo->prepare("
    SELECT 
        nom_produit,
        prix_unitaire,
        quantite,
        total_ligne
    FROM devis_lignes
    WHERE devis_id = :devis_id
    ORDER BY id ASC
");
$stmtLines->execute([':devis_id' => $devisId]);
$lignes = $stmtLines->fetchAll(\PDO::FETCH_ASSOC);

// Sécurisation / formatage
$numeroDevis = htmlspecialchars((string) $devis['numero_devis'], ENT_QUOTES, 'UTF-8');
$clientNom = htmlspecialchars((string) $devis['nom'], ENT_QUOTES, 'UTF-8');
$clientEmail = htmlspecialchars((string) $devis['email'], ENT_QUOTES, 'UTF-8');
$clientTelephone = htmlspecialchars((string) $devis['telephone'], ENT_QUOTES, 'UTF-8');
$dateCreation = htmlspecialchars((string) $devis['created_at'], ENT_QUOTES, 'UTF-8');

// TVA Sénégal
$tvaRate = 0.18;

$totalHtValue = (float) $devis['total_ht'];
$tvaValue = $totalHtValue * $tvaRate;
$totalTtcValue = $totalHtValue + $tvaValue;

$totalHt = number_format($totalHtValue, 0, ',', ' ');
$tva = number_format($tvaValue, 0, ',', ' ');
$totalTtc = number_format($totalTtcValue, 0, ',', ' ');

// Construction des lignes HTML
$rowsHtml = '';

foreach ($lignes as $ligne) {
    $nomProduit = htmlspecialchars((string) $ligne['nom_produit'], ENT_QUOTES, 'UTF-8');
    $prixUnitaire = number_format((float) $ligne['prix_unitaire'], 0, ',', ' ');
    $quantite = (int) $ligne['quantite'];
    $totalLigne = number_format((float) $ligne['total_ligne'], 0, ',', ' ');

    $rowsHtml .= "
        <tr>
            <td>{$nomProduit}</td>
            <td>{$prixUnitaire} FCFA</td>
            <td>{$quantite}</td>
            <td>{$totalLigne} FCFA</td>
        </tr>
    ";
}

// Message commercial fixe
$infoCommerciale = "Pour toute validation de devis, demande d'information complémentaire ou confirmation de commande, merci de contacter le service commercial ECOFI ou de vous déplacer directement à l'agence ECOFI.";

// HTML complet du PDF
$html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Devis {$numeroDevis}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 13px;
            color: #222;
            margin: 30px;
        }

        .header {
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0 0 8px 0;
            color: #1a3a6b;
            font-size: 24px;
        }

        .header p {
            margin: 4px 0;
        }

        .section-title {
            margin-top: 25px;
            margin-bottom: 10px;
            font-size: 16px;
            color: #1a3a6b;
            border-bottom: 1px solid #ddd;
            padding-bottom: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px 8px;
            text-align: left;
        }

        th {
            background: #f3f6fb;
        }

        .totaux {
            margin-top: 20px;
            width: 100%;
        }

        .totaux p {
            margin: 6px 0;
            font-size: 14px;
        }

        .info-commerciale {
            margin-top: 20px;
            padding: 14px;
            background: #fafafa;
            border: 1px solid #e5e5e5;
            line-height: 1.6;
        }

        .footer {
            margin-top: 40px;
            font-size: 11px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DEVIS {$numeroDevis}</h1>
        <p><strong>Client :</strong> {$clientNom}</p>
        <p><strong>Email :</strong> {$clientEmail}</p>
        <p><strong>Téléphone :</strong> {$clientTelephone}</p>
        <p><strong>Date :</strong> {$dateCreation}</p>
    </div>

    <div class="section-title">Détail du devis</div>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Prix unitaire</th>
                <th>Quantité</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            {$rowsHtml}
        </tbody>
    </table>

    <div class="totaux">
        <p><strong>Total HT :</strong> {$totalHt} FCFA</p>
        <p><strong>TVA (18%) :</strong> {$tva} FCFA</p>
        <p><strong>Total TTC :</strong> {$totalTtc} FCFA</p>
    </div>

    <div class="info-commerciale">
        <strong>Information importante :</strong><br>
        {$infoCommerciale}
    </div>

    <div class="footer">
        ECOFI — Devis généré automatiquement
    </div>
</body>
</html>
HTML;

// Configuration Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Affichage dans le navigateur
$dompdf->stream('devis-' . $devis['numero_devis'] . '.pdf', [
    'Attachment' => false
]);

exit;