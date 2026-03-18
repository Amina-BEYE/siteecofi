<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../Core/Database.php';

use App\Core\Database;
use PDO;
use Throwable;

function jsonResponse(array $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function generateQuoteNumber(): string
{
    return 'DEV-' . date('Ymd-His') . '-' . random_int(1000, 9999);
}

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!is_array($input)) {
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

    if ($nom === '' || $email === '' || $telephone === '') {
        jsonResponse([
            'success' => false,
            'message' => 'Nom, email et téléphone sont obligatoires.'
        ], 422);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse([
            'success' => false,
            'message' => 'Adresse email invalide.'
        ], 422);
    }

    if (!is_array($items) || count($items) === 0) {
        jsonResponse([
            'success' => false,
            'message' => 'Le panier est vide.'
        ], 422);
    }

    $pdo = Database::getConnection();
    $pdo->beginTransaction();

    // Rechercher le client par email
    $stmtClient = $pdo->prepare("
        SELECT id
        FROM clients
        WHERE email = :email
        LIMIT 1
    ");
    $stmtClient->execute([':email' => $email]);
    $client = $stmtClient->fetch(PDO::FETCH_ASSOC);

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
    }

    // Calcul total
    $totalHt = 0.0;

    foreach ($items as $item) {
        $prix = (float) ($item['prix'] ?? 0);
        $quantite = max(1, (int) ($item['quantite'] ?? 1));
        $totalHt += $prix * $quantite;
    }

    $totalTtc = $totalHt;
    $numeroDevis = generateQuoteNumber();

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

    foreach ($items as $item) {
        $nomProduit = trim((string) ($item['nom'] ?? 'Produit sans nom'));
        $prixUnitaire = (float) ($item['prix'] ?? 0);
        $quantite = max(1, (int) ($item['quantite'] ?? 1));
        $totalLigne = $prixUnitaire * $quantite;

        // Important :
        // on utilise produit_id seulement si c'est un vrai id SQL
        $produitId = null;
        if (isset($item['produit_id']) && is_numeric($item['produit_id'])) {
            $produitId = (int) $item['produit_id'];
        }

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

    // TODO : envoyer un email de confirmation au client
    jsonResponse([
        'success' => true,
        'message' => 'Votre demande de devis a été envoyée avec succès ! Nous vous contacterons bientôt.'
    ]);
 /*    jsonResponse([
        'success' => true,
        'message' => 'Devis enregistré avec succès.',
        'devis_id' => $devisId,
        'numero_devis' => $numeroDevis,
        'pdf_url' => '/SITEECOFI/app/api/generate_quote_pdf.php?id=' . $devisId
    ]); */

} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    jsonResponse([
        'success' => false,
        'message' => 'Erreur serveur : ' . $e->getMessage()
    ], 500);
}
