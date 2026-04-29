<?php

declare(strict_types=1);

// Désactiver l'affichage des erreurs pour éviter les warnings HTML
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Démarrer le buffer de sortie pour capturer tout output accidentel
ob_start();

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../Core/Database.php';

use App\Core\Database;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;

try {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception('ID devis invalide');
    }

    $devisId = (int) $_GET['id'];
    $pdo = Database::getConnection();

    // ── Logo ECOFI en base64 (seule méthode fiable avec Dompdf) ──
    $logoPath = realpath(__DIR__ . '/../../app/IMG/logo-ecofi.png');
    $logoBase64 = '';
    if ($logoPath && file_exists($logoPath)) {
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }

    // ── Récupération du devis ──
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
    $devis = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$devis) {
        throw new Exception('Devis introuvable');
    }

    // ── Récupération des lignes ──
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
    $lignes = $stmtLines->fetchAll(PDO::FETCH_ASSOC);

// ── Formatage des données ──
$numeroDevis = htmlspecialchars((string) $devis['numero_devis'], ENT_QUOTES, 'UTF-8');
$clientNom = htmlspecialchars((string) $devis['nom'], ENT_QUOTES, 'UTF-8');
$clientEmail = htmlspecialchars((string) $devis['email'], ENT_QUOTES, 'UTF-8');
$clientTel = htmlspecialchars((string) $devis['telephone'], ENT_QUOTES, 'UTF-8');
$dateCreation = htmlspecialchars((string) $devis['created_at'], ENT_QUOTES, 'UTF-8');

$tvaRate = 0.18;
$totalHtValue = (float) $devis['total_ht'];
$tvaValue = $totalHtValue * $tvaRate;
$totalTtcValue = $totalHtValue + $tvaValue;

$totalHt = number_format($totalHtValue, 0, ',', ' ');
$tva = number_format($tvaValue, 0, ',', ' ');
$totalTtc = number_format($totalTtcValue, 0, ',', ' ');

// ── Lignes du tableau ──
$rowsHtml = '';
foreach ($lignes as $i => $ligne) {
    $nomProduit = htmlspecialchars((string) $ligne['nom_produit'], ENT_QUOTES, 'UTF-8');
    $prixUnitaire = number_format((float) $ligne['prix_unitaire'], 0, ',', ' ');
    $quantite = (int) $ligne['quantite'];
    $totalLigne = number_format((float) $ligne['total_ligne'], 0, ',', ' ');
    $rowBg = ($i % 2 === 0) ? '#FFFFFF' : '#F7F9FC';

    $rowsHtml .= "
        <tr style=\"background:{$rowBg};\">
            <td class=\"td-produit\">{$nomProduit}</td>
            <td class=\"td-right\">{$prixUnitaire} FCFA</td>
            <td class=\"td-center\">{$quantite}</td>
            <td class=\"td-right td-total\">{$totalLigne} FCFA</td>
        </tr>
    ";
}

// ── Logo HTML ──
$logoHtml = $logoBase64 !== ''
    ? '<img src="' . $logoBase64 . '" class="logo" alt="Logo ECOFI">'
    : '<span class="logo-fallback">ECOFI</span>';

// ── Message commercial ──
$infoCommerciale = "Pour toute validation de devis, demande d'information complémentaire ou confirmation de commande, merci de contacter le service commercial ECOFI ou de vous déplacer directement à l'agence ECOFI.";

// ─────────────────────────────────────────────
// HTML DU PDF
// ─────────────────────────────────────────────
$html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Devis {$numeroDevis}</title>
    <style>

        @page {
                size: A4 portrait;
                margin: 0;
            }

            /* ── Reset & Base ── */
            * { margin: 0; padding: 0; box-sizing: border-box; }

            body {
                font-family: DejaVu Sans, Arial, sans-serif;
                font-size: 11.5px;
                color: #2D3748;
                background: #FFFFFF;
            }
        /* ── Reset & Base ── */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11.5px;
            color: #2D3748;
            background: #FFFFFF;
        }

        /* ── Bande supérieure tricolore ── */
        .stripe-top {
            width: 100%;
            height: 8px;
            background: #0B2A5A; /* bleu marine ECOFI */
        }
        .stripe-accent {
            width: 100%;
            height: 3px;
            background: #E8A020; /* or */
        }

        /* ── Wrapper principal ── */
        .page {
            padding: 28px 38px 20px 38px;
        }

        /* ── En-tête : table layout (compatible Dompdf) ── */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
        }
        .header-table td {
            border: none;
            vertical-align: top;
            padding: 0;
        }
        .cell-left  { width: 55%; }
        .cell-right { width: 45%; text-align: right; }

        /* Badge DEVIS */
        .devis-badge {
            display: inline-block;
            background: #0B2A5A;
            color: #FFFFFF;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 3px;
            padding: 7px 20px;
            border-radius: 3px;
            margin-bottom: 10px;
        }
        .devis-ref {
            font-size: 12.5px;
            color: #5A6A84;
            margin-bottom: 18px;
        }
        .devis-ref strong { color: #0B2A5A; }

        /* Infos client */
        .client-label {
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #E8A020;
            margin-bottom: 5px;
        }
        .client-name {
            font-size: 14px;
            font-weight: bold;
            color: #0B2A5A;
            margin-bottom: 4px;
        }
        .client-detail {
            font-size: 11px;
            color: #5A6A84;
            line-height: 1.7;
        }

        /* Logo & infos société */
        .logo {
            max-width: 120px;
            max-height: 65px;
            margin-bottom: 8px;
        }
        .logo-fallback {
            display: inline-block;
            font-size: 26px;
            font-weight: bold;
            color: #0B2A5A;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #0B2A5A;
            margin-bottom: 5px;
        }
        .company-detail {
            font-size: 10.5px;
            color: #5A6A84;
            line-height: 1.75;
        }
        .company-detail strong { color: #2D3748; }

        /* ── Séparateur ── */
        .divider {
            width: 100%;
            border: none;
            border-top: 1.5px solid #E2E8F0;
            margin: 0 0 20px 0;
        }

        /* ── Titre de section ── */
        .section-title {
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #E8A020;
            margin-bottom: 10px;
        }

        /* ── Tableau produits ── */
        table.products {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        table.products thead tr {
            background: #0B2A5A;
        }

        table.products thead th {
            color: #FFFFFF;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px 12px;
            text-align: left;
        }
        table.products thead th.th-right  { text-align: right; }
        table.products thead th.th-center { text-align: center; }

        table.products tbody tr td {
            padding: 9px 12px;
            font-size: 11.5px;
            color: #2D3748;
            border-bottom: 1px solid #EDF2F7;
        }

        .td-produit { font-weight: 600; }
        .td-right   { text-align: right; color: #5A6A84; }
        .td-center  { text-align: center; color: #5A6A84; }
        .td-total   { font-weight: bold; color: #0B2A5A !important; }

        /* ── Bloc totaux ── */
        .totaux-wrapper {
            width: 100%;
            margin-top: 0;
            margin-bottom: 22px;
        }
        table.totaux {
            width: 42%;
            margin-left: auto;
            border-collapse: collapse;
            border: 1px solid #E2E8F0;
        }
        table.totaux td {
            padding: 8px 14px;
            font-size: 11.5px;
            border-bottom: 1px solid #EDF2F7;
        }
        table.totaux .t-label { color: #5A6A84; text-align: left; }
        table.totaux .t-value { text-align: right; color: #2D3748; font-weight: 600; }

        /* Ligne TTC */
        table.totaux tr.ttc-row td {
            background: #0B2A5A;
            color: #FFFFFF;
            font-weight: bold;
            font-size: 13px;
            border-bottom: none;
        }
        table.totaux tr.ttc-row .t-value { color: #E8A020; }

        /* ── Info commerciale ── */
        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9.5px;
            color: #A0AEC0;
        }

        .info-box {
            position: fixed;
            bottom: 60px;
            left: 38px;
            right: 38px;
            background: #FFFBF0;
            border-left: 4px solid #E8A020;
            padding: 12px 16px;
            border-radius: 0 4px 4px 0;
            font-size: 10.5px;
            color: #5A6A84;
        }

        .page {
            padding: 28px 38px 120px 38px; /* IMPORTANT */
        }

        /* ── Bande inférieure ── */
        .stripe-bottom {
            width: 100%;
            height: 5px;
            background: #0B2A5A;
            margin-top: 18px;
        }

    </style>
</head>
<body>

    <!-- Bandes supérieures -->
    <div class="stripe-top"></div>
    <div class="stripe-accent"></div>

    <div class="page">

        <!-- ══ EN-TÊTE ══ -->
        <table class="header-table">
            <tr>
                <!-- Colonne gauche : badge + client -->
                <td class="cell-left">
                    <div class="devis-badge">DEVIS</div>
                    <div class="devis-ref">
                        N° <strong>{$numeroDevis}</strong>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        Date : <strong>{$dateCreation}</strong>
                    </div>

                    <div class="client-label">Établi pour</div>
                    <div class="client-name">{$clientNom}</div>
                    <div class="client-detail">
                        {$clientEmail}<br>
                        {$clientTel}
                    </div>
                </td>

                <!-- Colonne droite : logo + société -->
                <td class="cell-right">
                    {$logoHtml}<br>
                    <div class="company-detail">
                        <strong>Zac de Nginth, Thiès</strong><br>
                        service.ecofi01@gmail.com<br>
                        +221 339 98 50 72<br>
                        +221 710 39 75 75
                    </div>
                </td>
            </tr>
        </table>

        <hr class="divider">

        <!-- ══ TABLEAU DES PRODUITS ══ -->
        <div class="section-title">Détail du devis</div>

        <table class="products">
            <thead>
                <tr>
                    <th style="width:46%;">Produit / Prestation</th>
                    <th class="th-right"  style="width:20%;">Prix unitaire</th>
                    <th class="th-center" style="width:10%;">Qté</th>
                    <th class="th-right"  style="width:24%;">Total HT</th>
                </tr>
            </thead>
            <tbody>
                {$rowsHtml}
            </tbody>
        </table>

        <!-- ══ TOTAUX ══ -->
        <div class="totaux-wrapper">
            <table class="totaux">
                <tr>
                    <td class="t-label">Total HT</td>
                    <td class="t-value">{$totalHt} FCFA</td>
                </tr>
                <tr>
                    <td class="t-label">TVA (18%)</td>
                    <td class="t-value">{$tva} FCFA</td>
                </tr>
                <tr class="ttc-row">
                    <td class="t-label">Total TTC</td>
                    <td class="t-value">{$totalTtc} FCFA</td>
                </tr>
            </table>
        </div>

        <!-- ══ INFO COMMERCIALE ══ -->
        <div class="info-box">
            <strong>Information importante :</strong><br>
            {$infoCommerciale}
        </div>

        <!-- ══ PIED DE PAGE ══ -->
        <div class="footer">
            ECOFI &mdash; Devis N°{$numeroDevis} &mdash; Document généré automatiquement &mdash; Ne vaut pas facture
        </div>

    </div>

    <!-- Bande inférieure -->
   <!-- <div class="stripe-bottom"></div>-->

</body>
</html>
HTML;

// ── Génération Dompdf ──
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $pdfContent = $dompdf->output();

    if (empty($pdfContent) || strlen($pdfContent) < 100) {
        throw new Exception('Contenu PDF généré est vide ou invalide');
    }

    if (substr($pdfContent, 0, 4) !== '%PDF') {
        throw new Exception('Dompdf a retourné du contenu invalide (pas de signature PDF)');
    }

    // Nettoyer le buffer de sortie avant d'envoyer les headers
    ob_end_clean();

    header('Content-Type: application/pdf; charset=UTF-8');
    header('Content-Length: ' . strlen($pdfContent));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo $pdfContent;
    exit;

} catch (Exception $e) {
    // Nettoyer le buffer de sortie en cas d'erreur
    if (ob_get_level() > 0) {
        ob_end_clean();
    }

    http_response_code(500);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'error' => 'Erreur génération PDF',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}