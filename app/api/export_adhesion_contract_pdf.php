<?php
declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../admin/Models/ImmoProgramModel.php';

use Dompdf\Dompdf;

$adhesionId = (int) ($_GET['adhesion_id'] ?? 0);
if ($adhesionId <= 0) {
    http_response_code(400);
    exit('Dossier invalide.');
}

$model = new ImmoProgramModel();
$adhesion = $model->getAdhesionById($adhesionId);
$contract = $model->getContract($adhesionId);

if (!$adhesion || !$contract) {
    http_response_code(404);
    exit('Contrat introuvable.');
}

$name = htmlspecialchars(trim(($adhesion['prenom'] ?? '') . ' ' . ($adhesion['nom'] ?? '')));
$content = nl2br(htmlspecialchars((string) $contract['contract_content']));

$html = '<html><body style="font-family:DejaVu Sans,Arial,sans-serif;color:#222;line-height:1.6">'
    . '<h1 style="color:#ff8533">Contrat programme immobilier ECOFI</h1>'
    . '<p><strong>Client :</strong> ' . $name . '<br><strong>Dossier :</strong> #' . $adhesionId . '</p>'
    . '<div style="border:1px solid #ddd;padding:18px;border-radius:8px">' . $content . '</div>'
    . '</body></html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4');
$dompdf->render();
$dompdf->stream('contrat-adhesion-' . $adhesionId . '.pdf', ['Attachment' => false]);
