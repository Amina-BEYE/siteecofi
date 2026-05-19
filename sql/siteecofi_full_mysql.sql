-- Schéma MySQL complet ECOFI
-- À importer dans phpMyAdmin sur la base utilisée par config/database.php.
-- Compatible avec le code PHP actuel du site. Ne supprime pas les données.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(50) NOT NULL DEFAULT 'admin',
  status VARCHAR(50) NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(50) NOT NULL DEFAULT 'admin',
  status VARCHAR(50) NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS adhesions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  date_naissance DATE NOT NULL,
  lieu_naissance VARCHAR(150) NOT NULL,
  adresse VARCHAR(255) NOT NULL,
  telephone VARCHAR(30) NOT NULL,
  cni VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  mode_paiement VARCHAR(100) NOT NULL,
  message TEXT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'Nouveau',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_adhesions_email (email),
  INDEX idx_adhesions_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE adhesions
  ADD COLUMN IF NOT EXISTS nom VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS prenom VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS date_naissance DATE NULL,
  ADD COLUMN IF NOT EXISTS lieu_naissance VARCHAR(150) NULL,
  ADD COLUMN IF NOT EXISTS adresse VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS telephone VARCHAR(30) NULL,
  ADD COLUMN IF NOT EXISTS cni VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS email VARCHAR(150) NULL,
  ADD COLUMN IF NOT EXISTS mode_paiement VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS message TEXT NULL,
  ADD COLUMN IF NOT EXISTS status VARCHAR(50) NOT NULL DEFAULT 'Nouveau',
  ADD COLUMN IF NOT EXISTS created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN IF NOT EXISTS updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

CREATE TABLE IF NOT EXISTS admin_notes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  adhesion_id INT UNSIGNED NOT NULL,
  admin_id INT UNSIGNED NULL,
  note TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_admin_notes_adhesion_id (adhesion_id),
  CONSTRAINT fk_admin_notes_adhesion
    FOREIGN KEY (adhesion_id) REFERENCES adhesions(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS clients (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  telephone VARCHAR(30) NOT NULL,
  adresse TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_inscription DATETIME NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(120) NOT NULL UNIQUE,
  description TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS produits (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  categorie_id INT UNSIGNED NOT NULL,
  nom VARCHAR(180) NOT NULL,
  description TEXT NULL,
  prix DECIMAL(15,2) NOT NULL DEFAULT 0,
  ancien_prix DECIMAL(15,2) NULL,
  image VARCHAR(255) NULL,
  note DECIMAL(3,2) NULL,
  nb_avis INT UNSIGNED NOT NULL DEFAULT 0,
  type_media VARCHAR(20) NOT NULL DEFAULT 'image',
  media_src VARCHAR(255) NULL,
  actif TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_produits_categorie_id (categorie_id),
  CONSTRAINT fk_produits_categorie
    FOREIGN KEY (categorie_id) REFERENCES categories(id)
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS devis (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  numero_devis VARCHAR(50) NOT NULL UNIQUE,
  client_id INT UNSIGNED NOT NULL,
  total_ht DECIMAL(15,2) NOT NULL DEFAULT 0,
  total_ttc DECIMAL(15,2) NOT NULL DEFAULT 0,
  notes TEXT NULL,
  statut VARCHAR(50) NOT NULL DEFAULT 'en_attente',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_creation DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  date_expiration DATE NULL,
  INDEX idx_devis_client_id (client_id),
  INDEX idx_devis_statut (statut),
  CONSTRAINT fk_devis_client
    FOREIGN KEY (client_id) REFERENCES clients(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS devis_lignes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  devis_id INT UNSIGNED NOT NULL,
  produit_id INT UNSIGNED NULL,
  nom_produit VARCHAR(255) NOT NULL,
  quantite INT UNSIGNED NOT NULL DEFAULT 1,
  prix_unitaire DECIMAL(15,2) NOT NULL DEFAULT 0,
  total_ligne DECIMAL(15,2) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_devis_lignes_devis_id (devis_id),
  CONSTRAINT fk_devis_lignes_devis
    FOREIGN KEY (devis_id) REFERENCES devis(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS devis_articles (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  devis_id INT UNSIGNED NOT NULL,
  nom_article VARCHAR(255) NOT NULL,
  description TEXT NULL,
  prix_unitaire DECIMAL(15,2) NOT NULL DEFAULT 0,
  quantite INT UNSIGNED NOT NULL DEFAULT 1,
  total DECIMAL(15,2) NOT NULL DEFAULT 0,
  CONSTRAINT fk_devis_articles_devis
    FOREIGN KEY (devis_id) REFERENCES devis(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS commandes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  numero_commande VARCHAR(50) NOT NULL UNIQUE,
  client_id INT UNSIGNED NOT NULL,
  devis_id INT UNSIGNED NULL,
  mode_paiement VARCHAR(80) NULL,
  frais_livraison DECIMAL(15,2) NOT NULL DEFAULT 0,
  total_ht DECIMAL(15,2) NOT NULL DEFAULT 0,
  total_ttc DECIMAL(15,2) NOT NULL DEFAULT 0,
  code_promo VARCHAR(50) NULL,
  reduction DECIMAL(15,2) NOT NULL DEFAULT 0,
  notes TEXT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'en_attente',
  statut VARCHAR(50) NOT NULL DEFAULT 'en_attente',
  statut_commande VARCHAR(50) NOT NULL DEFAULT 'en_attente',
  statut_paiement VARCHAR(50) NOT NULL DEFAULT 'non_paye',
  adresse_livraison TEXT NULL,
  region VARCHAR(100) NULL,
  departement VARCHAR(100) NULL,
  commune VARCHAR(100) NULL,
  quartier VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_commande DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_commandes_client_id (client_id),
  CONSTRAINT fk_commandes_client
    FOREIGN KEY (client_id) REFERENCES clients(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_commandes_devis
    FOREIGN KEY (devis_id) REFERENCES devis(id)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS commande_lignes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  commande_id INT UNSIGNED NOT NULL,
  produit_id INT UNSIGNED NULL,
  nom_produit VARCHAR(255) NOT NULL,
  quantite INT UNSIGNED NOT NULL DEFAULT 1,
  prix_unitaire DECIMAL(15,2) NOT NULL DEFAULT 0,
  total_ligne DECIMAL(15,2) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_commande_lignes_commande_id (commande_id),
  CONSTRAINT fk_commande_lignes_commande
    FOREIGN KEY (commande_id) REFERENCES commandes(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS commande_articles (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  commande_id INT UNSIGNED NOT NULL,
  nom_article VARCHAR(255) NOT NULL,
  description TEXT NULL,
  prix_unitaire DECIMAL(15,2) NOT NULL DEFAULT 0,
  quantite INT UNSIGNED NOT NULL DEFAULT 1,
  total DECIMAL(15,2) NOT NULL DEFAULT 0,
  CONSTRAINT fk_commande_articles_commande
    FOREIGN KEY (commande_id) REFERENCES commandes(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS livraisons (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  commande_id INT UNSIGNED NOT NULL UNIQUE,
  region VARCHAR(100) NOT NULL,
  departement VARCHAR(100) NOT NULL,
  commune VARCHAR(100) NOT NULL,
  quartier VARCHAR(255) NOT NULL,
  adresse TEXT NULL,
  instructions TEXT NULL,
  latitude DECIMAL(10,8) NULL,
  longitude DECIMAL(11,8) NULL,
  date_livraison DATE NULL,
  statut VARCHAR(50) NOT NULL DEFAULT 'en_preparation',
  CONSTRAINT fk_livraisons_commande
    FOREIGN KEY (commande_id) REFERENCES commandes(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS demandes_contact (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL,
  telephone VARCHAR(30) NULL,
  type VARCHAR(50) NOT NULL DEFAULT 'contact',
  service VARCHAR(150) NULL,
  message TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_demandes_contact_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quote_types (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  type_key VARCHAR(80) NOT NULL UNIQUE,
  label VARCHAR(150) NOT NULL,
  description TEXT NULL,
  icon VARCHAR(80) NULL,
  form_schema JSON NULL,
  display_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (fullname, email, password, role, status)
VALUES (
  'Administrateur ECOFI',
  'admin@ecofi.sn',
  '$2y$12$8jR7UG6V.bszEsG3573f2.LGzaHEjH4dgf5hj4ILDGh1rzHgYvuIG',
  'admin',
  'active'
)
ON DUPLICATE KEY UPDATE fullname = VALUES(fullname), role = VALUES(role), status = VALUES(status);

INSERT INTO admins (fullname, email, password, role, status)
VALUES (
  'Administrateur ECOFI',
  'admin@ecofi.sn',
  '$2y$12$8jR7UG6V.bszEsG3573f2.LGzaHEjH4dgf5hj4ILDGh1rzHgYvuIG',
  'admin',
  'active'
)
ON DUPLICATE KEY UPDATE fullname = VALUES(fullname), role = VALUES(role), status = VALUES(status);

INSERT INTO quote_types (type_key, label, description, icon, display_order, is_active)
VALUES
  ('standard', 'Produit/Service Standard', 'Produit ou service standard', 'box', 0, 1),
  ('gps_rental', 'Location de GPS', 'Service de location de GPS', 'map-pin', 1, 1)
ON DUPLICATE KEY UPDATE
  label = VALUES(label),
  description = VALUES(description),
  icon = VALUES(icon),
  display_order = VALUES(display_order),
  is_active = VALUES(is_active);

SET FOREIGN_KEY_CHECKS = 1;
