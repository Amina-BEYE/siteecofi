<?php

require_once __DIR__ . '/admin-functions.php';
require_admin();

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$status = trim($_POST['status'] ?? '');
$note = trim($_POST['note'] ?? '');

$allowedStatuses = ['Nouveau', 'En cours', 'Validé', 'Refusé'];
if ($id <= 0 || !in_array($status, $allowedStatuses, true)) {
    header('Location: view-demande.php?id=' . $id);
    exit;
}

try {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('UPDATE adhesions SET status = :status, updated_at = NOW() WHERE id = :id');
    $stmt->execute([':status' => $status, ':id' => $id]);

    if ($note !== '') {
        $noteStmt = $pdo->prepare('INSERT INTO admin_notes (adhesion_id, admin_id, note, created_at) VALUES (:adhesion_id, :admin_id, :note, NOW())');
        $noteStmt->execute([
            ':adhesion_id' => $id,
            ':admin_id' => $_SESSION['admin_id'],
            ':note' => $note,
        ]);
    }

    flash_message('Statut et note enregistrés avec succès.');
} catch (Throwable $exception) {
    flash_message('Une erreur est survenue lors de la mise à jour.');
}

header('Location: view-demande.php?id=' . $id . '&success=1');
exit;
