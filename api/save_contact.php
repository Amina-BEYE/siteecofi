<?php
// api/save_contact.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['nom']) || !isset($data['email']) || !isset($data['message'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

$pdo = connectDB();

try {
    $stmt = $pdo->prepare("
        INSERT INTO demandes_contact (nom, email, telephone, service, message, status)
        VALUES (:nom, :email, :telephone, :service, :message, 'nouveau')
    ");
    
    $stmt->execute([
        ':nom' => $data['nom'],
        ':email' => $data['email'],
        ':telephone' => $data['telephone'] ?? '',
        ':service' => $data['service'] ?? '',
        ':message' => $data['message']
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Demande enregistrée avec succès',
        'demande_id' => $pdo->lastInsertId()
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
?>