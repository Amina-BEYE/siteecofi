<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../Core/Database.php';

use App\Core\Database;
use Dompdf\Dompdf;
use Dompdf\Options;
use PDO;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit('ID devis invalide');
}

$devisId = (int) $_GET['id'];
$pdo = Database::getConnection();

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
    exit('Devis introuvable');
}

$stmtLines = $pdo->prepare("
    SELECT nom_produit, prix_unitaire, quantite, total_ligne
    FROM devis_lignes
    WHERE devis_id = :devis_id
    ORDER BY id ASC
");
$stmtLines->execute([':devis_id' => $devisId]);
$lignes = $stmtLines->fetchAll(PDO::FETCH_ASSOC);

$html = '
<h1>Devis ' . htmlspecialchars($devis['numero_devis']) . '</h1>
<p><strong>Client :</strong> ' . htmlspecialchars($devis['nom']) . '</p>
<p><strong>Email :</strong> ' . htmlspecialchars($devis['email']) . '</p>
<p><strong>Téléphone :</strong> ' . htmlspecialchars($devis['telephone']) . '</p>
<p><strong>Date :</strong> ' . htmlspecialchars($devis['created_at']) . '</p>

<table width="100%" border="1" cellspacing="0" cellpadding="8">
    <thead>
        <tr>
            <th>Produit</th>
            <th>Prix unitaire</th>
            <th>Quantité</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>';

foreach ($lignes as $ligne) {
    $html .= '
        <tr>
            <td>' . htmlspecialchars($ligne['nom_produit']) . '</td>
            <td>' . number_format((float) $ligne['prix_unitaire'], 0, ',', ' ') . ' FCFA</td>
            <td>' . (int) $ligne['quantite'] . '</td>
            <td>' . number_format((float) $ligne['total_ligne'], 0, ',', ' ') . ' FCFA</td>
        </tr>';
}

$html .= '
    </tbody>
</table>

<p><strong>Total HT :</strong> ' . number_format((float) $devis['total_ht'], 0, ',', ' ') . ' FCFA</p>
<p><strong>Total TTC :</strong> ' . number_format((float) $devis['total_ttc'], 0, ',', ' ') . ' FCFA</p>
<p><strong>Notes :</strong> ' . nl2br(htmlspecialchars((string) $devis['notes'])) . '</p>
';

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream('devis-' . $devis['numero_devis'] . '.pdf', ['Attachment' => false]);
exit;