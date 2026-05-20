<?php
declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../admin/Models/PaymentScheduleModel.php';

use Dompdf\Dompdf;

$adhesionId = (int) ($_GET['adhesion_id'] ?? 0);
if ($adhesionId <= 0) {
    http_response_code(400);
    exit('Dossier invalide.');
}

$model = new PaymentScheduleModel();
$client = null;
foreach ($model->getClientSummaries() as $item) {
    if ((int) $item['adhesion_id'] === $adhesionId) {
        $client = $item;
        break;
    }
}

if (!$client) {
    http_response_code(404);
    exit('Dossier introuvable.');
}

$rows = '';
foreach ($client['schedules'] as $schedule) {
    $rows .= '<tr><td>Mois ' . (int) $schedule['installment_number'] . '</td><td>' . htmlspecialchars((string) $schedule['due_date']) . '</td><td>' . number_format((float) $schedule['amount'], 0, ',', ' ') . ' F CFA</td><td>' . (($schedule['status'] ?? '') === 'paid' ? 'Payé' : 'En attente') . '</td></tr>';
}

$name = htmlspecialchars(trim(($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? '')));
$html = '<html><body style="font-family:DejaVu Sans,Arial,sans-serif;color:#222">'
    . '<h1 style="color:#ff8533">Facture échéancier ECOFI</h1>'
    . '<p><strong>Client :</strong> ' . $name . '<br><strong>Email :</strong> ' . htmlspecialchars((string) $client['email']) . '<br><strong>Téléphone :</strong> ' . htmlspecialchars((string) $client['telephone']) . '</p>'
    . '<p><strong>Payé :</strong> ' . number_format((float) $client['paid_amount'], 0, ',', ' ') . ' F CFA<br><strong>Reste :</strong> ' . number_format((float) $client['pending_amount'], 0, ',', ' ') . ' F CFA</p>'
    . '<table width="100%" cellspacing="0" cellpadding="8" style="border-collapse:collapse"><thead><tr style="background:#111;color:#fff"><th align="left">Mois</th><th align="left">Échéance</th><th align="left">Montant</th><th align="left">Statut</th></tr></thead><tbody>' . $rows . '</tbody></table>'
    . '</body></html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4');
$dompdf->render();
$dompdf->stream('facture-echeancier-' . $adhesionId . '.pdf', ['Attachment' => false]);
