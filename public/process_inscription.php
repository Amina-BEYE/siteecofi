<?php
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

$message = '';
$message_type = 'error';

// Vérifier la méthode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Valider et nettoyer les données
        $type = trim($_POST['type'] ?? '');
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $statut = trim($_POST['statut'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');
        $programme = trim($_POST['programme'] ?? '');
        $type_bien = trim($_POST['type_bien'] ?? '');
        $budget = trim($_POST['budget'] ?? '');
        $message_text = trim($_POST['message'] ?? '');
        $contact_email = isset($_POST['contact_email']) ? 1 : 0;
        $contact_tel = isset($_POST['contact_tel']) ? 1 : 0;
        $contact_whatsapp = isset($_POST['contact_whatsapp']) ? 1 : 0;
        $newsletter = isset($_POST['newsletter']) ? 1 : 0;

        // Validations
        if (empty($nom) || empty($email) || empty($telephone) || empty($statut) || empty($programme)) {
            throw new Exception('Veuillez remplir tous les champs obligatoires.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Adresse email invalide.');
        }

        if ($type !== 'contact' && (empty($type_bien) || empty($budget))) {
            throw new Exception('Veuillez spécifier le type de bien et le budget.');
        }

        // Se connecter à la base de données
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        if ($conn->connect_error) {
            throw new Exception('Erreur de connexion à la base de données.');
        }

        $conn->set_charset("utf8");

        // Vérifier si la table existe, sinon la créer
        $create_table_sql = "CREATE TABLE IF NOT EXISTS inscriptions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            type ENUM('programme', 'contact') NOT NULL,
            nom VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            telephone VARCHAR(20) NOT NULL,
            statut VARCHAR(50) NOT NULL,
            adresse TEXT,
            programme VARCHAR(255) NOT NULL,
            type_bien VARCHAR(50),
            budget VARCHAR(50),
            message LONGTEXT,
            contact_email TINYINT DEFAULT 0,
            contact_tel TINYINT DEFAULT 0,
            contact_whatsapp TINYINT DEFAULT 0,
            newsletter TINYINT DEFAULT 0,
            date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            statut_traitement ENUM('nouveau', 'en_cours', 'contacte', 'termine') DEFAULT 'nouveau'
        )";

        if (!$conn->query($create_table_sql)) {
            throw new Exception('Erreur lors de la création de la table.');
        }

        // Préparer la requête d'insertion
        $sql = "INSERT INTO inscriptions (
            type, nom, email, telephone, statut, adresse, 
            programme, type_bien, budget, message,
            contact_email, contact_tel, contact_whatsapp, newsletter
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception('Erreur de préparation de la requête: ' . $conn->error);
        }

        // Binder les paramètres
        $stmt->bind_param(
            'ssssssssssiiit',
            $type,
            $nom,
            $email,
            $telephone,
            $statut,
            $adresse,
            $programme,
            $type_bien,
            $budget,
            $message_text,
            $contact_email,
            $contact_tel,
            $contact_whatsapp,
            $newsletter
        );

        // Exécuter la requête
        if (!$stmt->execute()) {
            throw new Exception('Erreur lors de l\'insertion des données: ' . $stmt->error);
        }

        // Envoyer un email de confirmation (optionnel)
        $subject = $type === 'contact' ? 
            'Demande de contact - Résidence Les Palmiers' : 
            'Inscription au programme - Résidence Les Palmiers';

        $email_body = "Bonjour " . htmlspecialchars($nom) . ",\n\n";
        $email_body .= $type === 'contact' ? 
            "Nous avons bien reçu votre demande de contact.\n" :
            "Votre inscription a été enregistrée avec succès.\n";
        $email_body .= "Nous reviendrons vers vous très bientôt.\n\n";
        $email_body .= "Cordialement,\nL'équipe ECOFI";

        // Note: Vous devez avoir PHPMailer configuré ou utiliser mail()
        // Pour l'instant, on laisse juste un commentaire

        $message = $type === 'contact' ?
            'Votre demande a été enregistrée. Nous vous contacterons très bientôt.' :
            'Votre inscription a été confirmée. Merci de votre intérêt !';
        $message_type = 'success';

        $stmt->close();
        $conn->close();

    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = 'error';
    }
}

// Stocker le message en session
$_SESSION['message'] = $message;
$_SESSION['message_type'] = $message_type;

// Rediriger vers le formulaire d'inscription
$redirect_type = isset($_POST['type']) ? $_POST['type'] : 'programme';
header('Location: inscription.php?type=' . $redirect_type);
exit();
?>
