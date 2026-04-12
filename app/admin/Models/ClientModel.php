<?php

require_once __DIR__ . '../../../Core/Database.php';

use App\Core\Database;

class ClientModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // 🔹 Récupérer tous les clients
    public function getAllClients(): array
    {
        $stmt = $this->db->query("
            SELECT id, nom, email, telephone, created_at
            FROM clients
            ORDER BY created_at DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 AJOUTER UN CLIENT (C'EST ÇA QUI MANQUE)
    public function addClient(string $nom, string $email, string $telephone): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO clients (nom, email, telephone)
            VALUES (:nom, :email, :telephone)
        ");

        return $stmt->execute([
            ':nom' => $nom,
            ':email' => $email,
            ':telephone' => $telephone,
        ]);
    }
}