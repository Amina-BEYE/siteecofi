BEGIN;

-- Table des clients
CREATE TABLE clients (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    adresse TEXT,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT unique_email UNIQUE (email)
);

-- Table des devis
CREATE TABLE devis (
    id SERIAL PRIMARY KEY,
    numero_devis VARCHAR(50) UNIQUE NOT NULL,
    client_id INTEGER NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_expiration DATE,
    statut VARCHAR(20) DEFAULT 'en_attente' CHECK (statut IN ('en_attente', 'accepte', 'refuse', 'converti')),
    total_ht DECIMAL(15,2) DEFAULT 0,
    total_ttc DECIMAL(15,2) DEFAULT 0,
    notes TEXT,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);

-- Table des articles du devis
CREATE TABLE devis_articles (
    id SERIAL PRIMARY KEY,
    devis_id INTEGER NOT NULL,
    nom_article VARCHAR(255) NOT NULL,
    description TEXT,
    prix_unitaire DECIMAL(15,2) NOT NULL,
    quantite INTEGER NOT NULL,
    total DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (devis_id) REFERENCES devis(id) ON DELETE CASCADE
);

-- Table des commandes
CREATE TABLE commandes (
    id SERIAL PRIMARY KEY,
    numero_commande VARCHAR(50) UNIQUE NOT NULL,
    client_id INTEGER NOT NULL,
    devis_id INTEGER NULL,
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(20) DEFAULT 'en_attente' CHECK (statut IN ('en_attente', 'confirmee', 'livree', 'annulee')),
    mode_paiement VARCHAR(50),
    frais_livraison DECIMAL(15,2) DEFAULT 0,
    total_ht DECIMAL(15,2) DEFAULT 0,
    total_ttc DECIMAL(15,2) DEFAULT 0,
    code_promo VARCHAR(50),
    reduction DECIMAL(5,2) DEFAULT 0,
    notes TEXT,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (devis_id) REFERENCES devis(id) ON DELETE SET NULL
);

-- Table des articles de commande
CREATE TABLE commande_articles (
    id SERIAL PRIMARY KEY,
    commande_id INTEGER NOT NULL,
    nom_article VARCHAR(255) NOT NULL,
    description TEXT,
    prix_unitaire DECIMAL(15,2) NOT NULL,
    quantite INTEGER NOT NULL,
    total DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
);

-- Table des livraisons
CREATE TABLE livraisons (
    id SERIAL PRIMARY KEY,
    commande_id INTEGER NOT NULL UNIQUE,
    region VARCHAR(100) NOT NULL,
    departement VARCHAR(100) NOT NULL,
    commune VARCHAR(100) NOT NULL,
    quartier VARCHAR(255) NOT NULL,
    adresse TEXT,
    instructions TEXT,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    date_livraison DATE,
    statut VARCHAR(20) DEFAULT 'en_preparation' CHECK (statut IN ('en_preparation', 'expediee', 'livree', 'retard')),
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
);

-- Table des utilisateurs (pour le dashboard)
CREATE TABLE utilisateurs (
    id SERIAL PRIMARY KEY,
    nom_utilisateur VARCHAR(50) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role VARCHAR(20) DEFAULT 'gestionnaire' CHECK (role IN ('admin', 'gestionnaire')),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insérer un utilisateur par défaut (mot de passe: admin123)
INSERT INTO utilisateurs (nom_utilisateur, mot_de_passe, email, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ecofiservice01@gmail.com.sn', 'admin');

COMMIT;