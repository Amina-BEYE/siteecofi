<?php

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../Core/Database.php';

use App\Core\Database;

class OrderModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAllOrders(): array
    {
        $sql = "
            SELECT 
                c.id,
                c.numero_commande,
                c.total_ht,
                c.reduction,
                c.frais_livraison,
                c.total_ttc,
                c.mode_paiement,
                c.statut_paiement,
                c.statut_commande,
                c.adresse_livraison,
                c.region,
                c.departement,
                c.commune,
                c.quartier,
                c.created_at,
                cl.nom AS client_nom,
                cl.email AS client_email,
                cl.telephone AS client_telephone
            FROM commandes c
            INNER JOIN clients cl ON cl.id = c.client_id
            ORDER BY c.created_at DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($orders as &$order) {
            $order['lignes'] = $this->getOrderLines((int) $order['id']);
        }

        return $orders;
    }

    public function getAllQuotes(): array
    {
        $sql = "
            SELECT 
                d.id,
                d.numero_devis,
                d.total_ht,
                d.total_ttc,
                d.notes,
                d.statut,
                d.created_at,
                cl.nom AS client_nom,
                cl.email AS client_email,
                cl.telephone AS client_telephone
            FROM devis d
            INNER JOIN clients cl ON cl.id = d.client_id
            ORDER BY d.created_at DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($quotes as &$quote) {
            $quote['lignes'] = $this->getQuoteLines((int) $quote['id']);
        }

        return $quotes;
    }

    public function getOrderLines(int $commandeId): array
    {
        $sql = "
            SELECT 
                id,
                nom_produit,
                prix_unitaire,
                quantite,
                total_ligne
            FROM commande_lignes
            WHERE commande_id = :commande_id
            ORDER BY id ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':commande_id', $commandeId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getQuoteLines(int $devisId): array
    {
        $sql = "
            SELECT 
                id,
                nom_produit,
                prix_unitaire,
                quantite,
                total_ligne
            FROM devis_lignes
            WHERE devis_id = :devis_id
            ORDER BY id ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':devis_id', $devisId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function updateQuoteStatus(int $quoteId, string $status): bool
    {
        $allowed = ['en_attente', 'accepte', 'refuse', 'expire'];

        if ($quoteId <= 0 || !in_array($status, $allowed, true)) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE devis
            SET statut = :statut
            WHERE id = :id
        ");

        return $stmt->execute([
            ':statut' => $status,
            ':id' => $quoteId,
        ]);
    }

    public function validateQuote(int $quoteId): bool
    {
        if ($quoteId <= 0) {
            return false;
        }

        $this->db->beginTransaction();

        try {
            $quoteStmt = $this->db->prepare("
                SELECT id, client_id, total_ht, total_ttc, notes
                FROM devis
                WHERE id = :id
                LIMIT 1
            ");
            $quoteStmt->execute([':id' => $quoteId]);
            $quote = $quoteStmt->fetch(PDO::FETCH_ASSOC);

            if (!$quote) {
                $this->db->rollBack();
                return false;
            }

            $existingStmt = $this->db->prepare("
                SELECT id
                FROM commandes
                WHERE devis_id = :devis_id
                LIMIT 1
            ");
            $existingStmt->execute([':devis_id' => $quoteId]);
            $existingOrderId = (int) $existingStmt->fetchColumn();

            if ($existingOrderId <= 0) {
                $orderNumber = 'CMD-' . date('Ymd-His') . '-' . random_int(100, 999);

                $orderStmt = $this->db->prepare("
                    INSERT INTO commandes (
                        numero_commande,
                        client_id,
                        devis_id,
                        total_ht,
                        total_ttc,
                        notes,
                        status,
                        statut,
                        statut_commande,
                        statut_paiement,
                        created_at
                    ) VALUES (
                        :numero_commande,
                        :client_id,
                        :devis_id,
                        :total_ht,
                        :total_ttc,
                        :notes,
                        'en_attente',
                        'en_attente',
                        'en_attente',
                        'non_paye',
                        NOW()
                    )
                ");
                $orderStmt->execute([
                    ':numero_commande' => $orderNumber,
                    ':client_id' => (int) $quote['client_id'],
                    ':devis_id' => $quoteId,
                    ':total_ht' => (float) $quote['total_ht'],
                    ':total_ttc' => (float) $quote['total_ttc'],
                    ':notes' => $quote['notes'],
                ]);

                $orderId = (int) $this->db->lastInsertId();

                $lines = $this->getQuoteLines($quoteId);
                $lineStmt = $this->db->prepare("
                    INSERT INTO commande_lignes (
                        commande_id,
                        nom_produit,
                        quantite,
                        prix_unitaire,
                        total_ligne,
                        created_at
                    ) VALUES (
                        :commande_id,
                        :nom_produit,
                        :quantite,
                        :prix_unitaire,
                        :total_ligne,
                        NOW()
                    )
                ");

                foreach ($lines as $line) {
                    $lineStmt->execute([
                        ':commande_id' => $orderId,
                        ':nom_produit' => $line['nom_produit'] ?? 'Article',
                        ':quantite' => (int) ($line['quantite'] ?? 1),
                        ':prix_unitaire' => (float) ($line['prix_unitaire'] ?? 0),
                        ':total_ligne' => (float) ($line['total_ligne'] ?? 0),
                    ]);
                }
            }

            $this->updateQuoteStatus($quoteId, 'accepte');
            $this->db->commit();

            return true;
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            error_log('[OrderModel] validateQuote error: ' . $exception->getMessage());
            return false;
        }
    }

    public function updateOrderStatus(int $orderId, string $status): bool
    {
        $allowed = ['en_attente', 'en_cours', 'livre', 'annule', 'refuse'];

        if ($orderId <= 0 || !in_array($status, $allowed, true)) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE commandes
            SET status = :status,
                statut = :status,
                statut_commande = :status
            WHERE id = :id
        ");

        return $stmt->execute([
            ':status' => $status,
            ':id' => $orderId,
        ]);
    }

    public function updatePaymentStatus(int $orderId, string $status): bool
    {
        $allowed = ['non_paye', 'partiel', 'paie'];

        if ($orderId <= 0 || !in_array($status, $allowed, true)) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE commandes
            SET statut_paiement = :status
            WHERE id = :id
        ");

        return $stmt->execute([
            ':status' => $status,
            ':id' => $orderId,
        ]);
    }
}
