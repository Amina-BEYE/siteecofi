<?php
// api.php - À appeler depuis votre site
require_once 'config.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch($action) {
    
    case 'ajouter_devis':
        $data = json_decode($_POST['data'], true);
        
        // Chercher ou créer le client
        $stmt = $pdo->prepare("SELECT id FROM clients WHERE email = ?");
        $stmt->execute([$data['client']['email']]);
        $client = $stmt->fetch();
        
        if (!$client) {
            $stmt = $pdo->prepare("INSERT INTO clients (nom, email, telephone) VALUES (?, ?, ?)");
            $stmt->execute([$data['client']['nom'], $data['client']['email'], $data['client']['telephone']]);
            $client_id = $pdo->lastInsertId();
        } else {
            $client_id = $client['id'];
        }
        
        // Créer le devis
        $numero = generateNumero('DEV');
        $stmt = $pdo->prepare("INSERT INTO devis (numero_devis, client_id, total_ht, total_ttc) VALUES (?, ?, ?, ?)");
        $stmt->execute([$numero, $client_id, $data['total_ht'], $data['total_ttc']]);
        $devis_id = $pdo->lastInsertId();
        
        // Ajouter les articles
        $stmt = $pdo->prepare("INSERT INTO devis_articles (devis_id, nom_article, prix_unitaire, quantite, total) VALUES (?, ?, ?, ?, ?)");
        foreach ($data['articles'] as $article) {
            $stmt->execute([$devis_id, $article['nom'], $article['prix'], $article['quantite'], $article['total']]);
        }
        
        echo json_encode(['success' => true, 'numero_devis' => $numero]);
        break;
        
    case 'ajouter_commande':
        $data = json_decode($_POST['data'], true);
        
        // Chercher ou créer le client
        $stmt = $pdo->prepare("SELECT id FROM clients WHERE email = ?");
        $stmt->execute([$data['client']['email']]);
        $client = $stmt->fetch();
        
        if (!$client) {
            $stmt = $pdo->prepare("INSERT INTO clients (nom, email, telephone, adresse) VALUES (?, ?, ?, ?)");
            $stmt->execute([$data['client']['nom'], $data['client']['email'], $data['client']['telephone'], $data['client']['adresse'] ?? '']);
            $client_id = $pdo->lastInsertId();
        } else {
            $client_id = $client['id'];
        }
        
        // Créer la commande
        $numero = generateNumero('CMD');
        $stmt = $pdo->prepare("
            INSERT INTO commandes (numero_commande, client_id, mode_paiement, frais_livraison, total_ht, total_ttc, code_promo, reduction, statut) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'en_attente')
        ");
        $stmt->execute([
            $numero, 
            $client_id, 
            $data['mode_paiement'], 
            $data['frais_livraison'] ?? 0,
            $data['total_ht'], 
            $data['total_ttc'], 
            $data['code_promo'] ?? null,
            $data['reduction'] ?? 0
        ]);
        $commande_id = $pdo->lastInsertId();
        
        // Ajouter les articles
        $stmt = $pdo->prepare("INSERT INTO commande_articles (commande_id, nom_article, prix_unitaire, quantite, total) VALUES (?, ?, ?, ?, ?)");
        foreach ($data['articles'] as $article) {
            $stmt->execute([$commande_id, $article['nom'], $article['prix'], $article['quantite'], $article['total']]);
        }
        
        // Ajouter la livraison si nécessaire
        if (isset($data['livraison'])) {
            $stmt = $pdo->prepare("
                INSERT INTO livraisons (commande_id, region, departement, commune, quartier, adresse, instructions, latitude, longitude) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $commande_id,
                $data['livraison']['region'],
                $data['livraison']['departement'],
                $data['livraison']['commune'],
                $data['livraison']['quartier'],
                $data['livraison']['adresse'] ?? '',
                $data['livraison']['instructions'] ?? '',
                $data['livraison']['latitude'] ?? null,
                $data['livraison']['longitude'] ?? null
            ]);
        }
        
        echo json_encode(['success' => true, 'numero_commande' => $numero]);
        break;
        
    case 'get_commandes':
        $stmt = $pdo->query("
            SELECT c.*, cl.nom as client_nom 
            FROM commandes c 
            JOIN clients cl ON c.client_id = cl.id 
            ORDER BY c.date_commande DESC 
            LIMIT 50
        ");
        echo json_encode($stmt->fetchAll());
        break;
        
    case 'get_devis':
        $stmt = $pdo->query("
            SELECT d.*, cl.nom as client_nom 
            FROM devis d 
            JOIN clients cl ON d.client_id = cl.id 
            ORDER BY d.date_creation DESC 
            LIMIT 50
        ");
        echo json_encode($stmt->fetchAll());
        break;
        
    case 'get_facture':
        $id = $_GET['id'];
        $stmt = $pdo->prepare("
            SELECT c.*, cl.*, l.* 
            FROM commandes c 
            JOIN clients cl ON c.client_id = cl.id 
            LEFT JOIN livraisons l ON c.id = l.commande_id 
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        $commande = $stmt->fetch();
        
        $stmt = $pdo->prepare("SELECT * FROM commande_articles WHERE commande_id = ?");
        $stmt->execute([$id]);
        $articles = $stmt->fetchAll();
        
        echo json_encode(['commande' => $commande, 'articles' => $articles]);
        break;
        
    default:
        echo json_encode(['error' => 'Action non reconnue']);
}