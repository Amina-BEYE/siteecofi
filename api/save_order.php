<?php
// api/save_order.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['client_nom']) || !isset($data['client_telephone']) || !isset($data['produit'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

$pdo = connectDB();

try {
    $stmt = $pdo->prepare("
        INSERT INTO commandes (client_nom, client_email, client_telephone, produit, quantite, prix, status)
        VALUES (:nom, :email, :telephone, :produit, :quantite, :prix, 'en_attente')
    ");
    
    $stmt->execute([
        ':nom' => $data['client_nom'],
        ':email' => $data['client_email'] ?? '',
        ':telephone' => $data['client_telephone'],
        ':produit' => $data['produit'],
        ':quantite' => $data['quantite'] ?? 1,
        ':prix' => $data['prix'] ?? '0 FCFA'
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Commande enregistrée avec succès',
        'commande_id' => $pdo->lastInsertId()
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
?>