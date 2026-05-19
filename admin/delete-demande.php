<?php

require_once __DIR__ . '/admin-functions.php';
require_admin();

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($id <= 0) {
    header('Location: dashboard.php');
    exit;
}

try {
    $pdo = get_db_connection();
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('DELETE FROM admin_notes WHERE adhesion_id = :id');
    $stmt->execute([':id' => $id]);

    $stmt = $pdo->prepare('DELETE FROM adhesions WHERE id = :id');
    $stmt->execute([':id' => $id]);

    $pdo->commit();
    flash_message('La demande a été supprimée.');
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    flash_message('Impossible de supprimer la demande.');
}

header('Location: dashboard.php');
exit;
