<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Core/Database.php';

use App\Core\Database;

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!is_array($input)) {
        throw new RuntimeException('JSON invalide.');
    }

    $type = $input['type'] ?? '';
    $lineId = (int) ($input['line_id'] ?? 0);
    $quantity = (int) ($input['quantity'] ?? 0);

    if (!in_array($type, ['order', 'quote'], true)) {
        throw new RuntimeException('Type invalide.');
    }

    if ($lineId <= 0) {
        throw new RuntimeException('ID ligne invalide.');
    }

    if ($quantity <= 0) {
        throw new RuntimeException('La quantité doit être supérieure à 0.');
    }

    $pdo = Database::getConnection();
    $pdo->beginTransaction();

    if ($type === 'quote') {
        $stmt = $pdo->prepare("
            UPDATE devis_lignes
            SET quantite = :quantite,
                total_ligne = prix_unitaire * :quantite
            WHERE id = :id
        ");
        $stmt->execute([
            ':quantite' => $quantity,
            ':id' => $lineId,
        ]);

        $stmt = $pdo->prepare("
            SELECT devis_id
            FROM devis_lignes
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $lineId]);
        $parentId = (int) $stmt->fetchColumn();

        $stmt = $pdo->prepare("
            UPDATE devis
            SET total_ht = (
                SELECT COALESCE(SUM(total_ligne), 0)
                FROM devis_lignes
                WHERE devis_id = :id
            ),
            total_ttc = (
                SELECT COALESCE(SUM(total_ligne), 0)
                FROM devis_lignes
                WHERE devis_id = :id
            )
            WHERE id = :id
        ");
        $stmt->execute([':id' => $parentId]);

    } else {
        $stmt = $pdo->prepare("
            UPDATE commande_lignes
            SET quantite = :quantite,
                total_ligne = prix_unitaire * :quantite
            WHERE id = :id
        ");
        $stmt->execute([
            ':quantite' => $quantity,
            ':id' => $lineId,
        ]);

        $stmt = $pdo->prepare("
            SELECT commande_id
            FROM commande_lignes
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $lineId]);
        $parentId = (int) $stmt->fetchColumn();

        $stmt = $pdo->prepare("
            UPDATE commandes
            SET total_ttc = (
                SELECT COALESCE(SUM(total_ligne), 0)
                FROM commande_lignes
                WHERE commande_id = :id
            )
            WHERE id = :id
        ");
        $stmt->execute([':id' => $parentId]);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Quantité mise à jour.'
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}