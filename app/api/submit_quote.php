<?php

declare(strict_types=1);

ob_start();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../Core/Database.php';

use App\Core\Database;
use PDO;
use Throwable;

function logDebug(string $message, $data = null): void
{
    $logFile = __DIR__ . '/debug.log';

    $entry = '[' . date('Y-m-d H:i:s') . '] ' . $message;

    if ($data !== null) {
        if (is_array($data) || is_object($data)) {
            $entry .= ' | ' . print_r($data, true);
        } else {
            $entry .= ' | ' . $data;
        }
    }

    file_put_contents($logFile, $entry . PHP_EOL, FILE_APPEND);
}

function jsonResponse(array $data, int $status = 200): void
{
    http_response_code($status);

    if (ob_get_length()) {
        ob_clean();
    }

    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function generateQuoteNumber(): string
{
    return 'DEV-' . date('Ymd-His') . '-' . random_int(1000, 9999);
}

try {
    $rawInput = file_get_contents('php://input');
    logDebug('RAW INPUT', $rawInput);

    $input = json_decode($rawInput, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        logDebug('JSON ERROR', json_last_error_msg());

        jsonResponse([
            'success' => false,
            'message' => 'JSON invalide.',
            'error' => json_last_error_msg(),
            'raw_input' => $rawInput
        ], 400);
    }

    logDebug('PARSED INPUT', $input);

    if (!is_array($input)) {
        logDebug('INVALID INPUT TYPE', gettype($input));

        jsonResponse([
            'success' => false,
            'message' => 'Données invalides.'
        ], 400);
    }

    $nom = trim((string) ($input['nom'] ?? ''));
    $email = trim((string) ($input['email'] ?? ''));
    $telephone = trim((string) ($input['telephone'] ?? ''));
    $message = trim((string) ($input['message'] ?? ''));
    $items = $input['items'] ?? [];

    logDebug('CLIENT DATA', [
        'nom' => $nom,
        'email' => $email,
        'telephone' => $telephone,
        'message' => $message
    ]);

    logDebug('ITEMS', $items);

    if ($nom === '' || $email === '' || $telephone === '') {
        logDebug('VALIDATION ERROR', 'Nom, email et téléphone obligatoires');

        jsonResponse([
            'success' => false,
            'message' => 'Nom, email et téléphone sont obligatoires.'
        ], 422);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        logDebug('VALIDATION ERROR', 'Adresse email invalide : ' . $email);

        jsonResponse([
            'success' => false,
            'message' => 'Adresse email invalide.'
        ], 422);
    }

    if (!is_array($items) || count($items) === 0) {
        logDebug('VALIDATION ERROR', 'Panier vide ou items invalide');

        jsonResponse([
            'success' => false,
            'message' => 'Le panier est vide.'
        ], 422);
    }

    $pdo = Database::getConnection();
    $pdo->beginTransaction();

    logDebug('DB', 'Transaction démarrée');

    // Rechercher le client par email
    $stmtClient = $pdo->prepare("
        SELECT id
        FROM clients
        WHERE email = :email
        LIMIT 1
    ");
    $stmtClient->execute([':email' => $email]);
    $client = $stmtClient->fetch(PDO::FETCH_ASSOC);

    logDebug('CLIENT SEARCH RESULT', $client);

    if ($client) {
        $clientId = (int) $client['id'];

        $updateClient = $pdo->prepare("
            UPDATE clients
            SET nom = :nom, telephone = :telephone
            WHERE id = :id
        ");
        $updateClient->execute([
            ':nom' => $nom,
            ':telephone' => $telephone,
            ':id' => $clientId
        ]);

        logDebug('CLIENT UPDATED', [
            'client_id' => $clientId,
            'nom' => $nom,
            'telephone' => $telephone
        ]);
    } else {
        $insertClient = $pdo->prepare("
            INSERT INTO clients (nom, email, telephone)
            VALUES (:nom, :email, :telephone)
        ");
        $insertClient->execute([
            ':nom' => $nom,
            ':email' => $email,
            ':telephone' => $telephone
        ]);

        $clientId = (int) $pdo->lastInsertId();

        logDebug('CLIENT INSERTED', [
            'client_id' => $clientId,
            'nom' => $nom,
            'email' => $email,
            'telephone' => $telephone
        ]);
    }

    // Calcul total
    $totalHt = 0.0;

    foreach ($items as $index => $item) {
        logDebug('ITEM LOOP #' . $index, $item);

        $prix = (float) ($item['prix'] ?? 0);
        $quantite = max(1, (int) ($item['quantite'] ?? 1));
        $totalHt += $prix * $quantite;
    }

    $totalTtc = $totalHt;
    $numeroDevis = generateQuoteNumber();

    logDebug('DEVIS TOTALS', [
        'total_ht' => $totalHt,
        'total_ttc' => $totalTtc,
        'numero_devis' => $numeroDevis
    ]);

    // Insertion devis
    $insertDevis = $pdo->prepare("
        INSERT INTO devis (numero_devis, client_id, total_ht, total_ttc, notes, statut)
        VALUES (:numero_devis, :client_id, :total_ht, :total_ttc, :notes, :statut)
    ");
    $insertDevis->execute([
        ':numero_devis' => $numeroDevis,
        ':client_id' => $clientId,
        ':total_ht' => $totalHt,
        ':total_ttc' => $totalTtc,
        ':notes' => $message !== '' ? $message : null,
        ':statut' => 'en_attente'
    ]);

    $devisId = (int) $pdo->lastInsertId();

    logDebug('DEVIS INSERTED', [
        'devis_id' => $devisId,
        'numero_devis' => $numeroDevis
    ]);

    // Insertion lignes devis
    $insertLine = $pdo->prepare("
        INSERT INTO devis_lignes (
            devis_id,
            produit_id,
            nom_produit,
            prix_unitaire,
            quantite,
            total_ligne
        )
        VALUES (
            :devis_id,
            :produit_id,
            :nom_produit,
            :prix_unitaire,
            :quantite,
            :total_ligne
        )
    ");

    foreach ($items as $index => $item) {
        $nomProduit = trim((string) ($item['nom'] ?? 'Produit sans nom'));
        $prixUnitaire = (float) ($item['prix'] ?? 0);
        $quantite = max(1, (int) ($item['quantite'] ?? 1));
        $totalLigne = $prixUnitaire * $quantite;

        $produitId = null;
        if (isset($item['produit_id']) && is_numeric($item['produit_id'])) {
            $produitId = (int) $item['produit_id'];
        }

        logDebug('INSERT LIGNE #' . $index, [
            'devis_id' => $devisId,
            'produit_id' => $produitId,
            'nom_produit' => $nomProduit,
            'prix_unitaire' => $prixUnitaire,
            'quantite' => $quantite,
            'total_ligne' => $totalLigne
        ]);

        $insertLine->bindValue(':devis_id', $devisId, PDO::PARAM_INT);

        if ($produitId !== null) {
            $insertLine->bindValue(':produit_id', $produitId, PDO::PARAM_INT);
        } else {
            $insertLine->bindValue(':produit_id', null, PDO::PARAM_NULL);
        }

        $insertLine->bindValue(':nom_produit', $nomProduit);
        $insertLine->bindValue(':prix_unitaire', $prixUnitaire);
        $insertLine->bindValue(':quantite', $quantite, PDO::PARAM_INT);
        $insertLine->bindValue(':total_ligne', $totalLigne);
        $insertLine->execute();
    }

    $pdo->commit();
    logDebug('DB', 'Transaction commit OK');

    jsonResponse([
        'success' => true,
        'message' => 'Votre demande de devis a été envoyée avec succès ! Nous vous contacterons bientôt.',
        'devis_id' => $devisId,
        'numero_devis' => $numeroDevis
    ]);

} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
        logDebug('DB', 'Transaction rollback');
    }

    logDebug('ERROR', [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);

    jsonResponse([
        'success' => false,
        'message' => 'Erreur serveur.',
        'error' => $e->getMessage()
    ], 500);
}
