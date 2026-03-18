<?php

require_once __DIR__ . '../../../Core/Database.php';

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

        return $stmt->fetchAll();
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

        return $stmt->fetchAll();
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
}