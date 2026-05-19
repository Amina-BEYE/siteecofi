<?php

require_once __DIR__ . '/admin-functions.php';
require_admin();

$pdo = get_db_connection();
$stmt = $pdo->query('SELECT id, nom, prenom, date_naissance, lieu_naissance, adresse, telephone, email, cni, mode_paiement, status, message, created_at, updated_at FROM adhesions ORDER BY created_at DESC');
$adhesions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$filename = 'ecofi_adhesions_' . date('Ymd_His') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Nom', 'Prénom', 'Date de naissance', 'Lieu de naissance', 'Adresse', 'Téléphone', 'Email', 'N°CNI / Passeport', 'Mode de paiement', 'Statut', 'Message', 'Date de demande', 'Dernière mise à jour']);

foreach ($adhesions as $adhesion) {
    fputcsv($output, [
        $adhesion['id'],
        $adhesion['nom'],
        $adhesion['prenom'],
        $adhesion['date_naissance'],
        $adhesion['lieu_naissance'],
        $adhesion['adresse'],
        $adhesion['telephone'],
        $adhesion['email'],
        $adhesion['cni'],
        $adhesion['mode_paiement'],
        $adhesion['status'],
        $adhesion['message'],
        $adhesion['created_at'],
        $adhesion['updated_at'],
    ]);
}

fclose($output);
exit;
