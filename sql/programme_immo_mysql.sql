-- Schéma MySQL ECOFI - Programme immobilier + accès admin
-- À importer dans phpMyAdmin sur la base utilisée par config/database.php.
-- Ce script est idempotent: il peut être relancé sans supprimer les données.

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
  INDEX idx_adhesions_status (status),
  INDEX idx_adhesions_created_at (created_at)
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
  INDEX idx_admin_notes_admin_id (admin_id),
  CONSTRAINT fk_admin_notes_adhesion
    FOREIGN KEY (adhesion_id) REFERENCES adhesions(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Compte admin par défaut:
-- email: admin@ecofi.sn
-- mot de passe: admin123
INSERT INTO users (fullname, email, password, role, status)
VALUES (
  'Administrateur ECOFI',
  'admin@ecofi.sn',
  '$2y$12$8jR7UG6V.bszEsG3573f2.LGzaHEjH4dgf5hj4ILDGh1rzHgYvuIG',
  'admin',
  'active'
)
ON DUPLICATE KEY UPDATE
  fullname = VALUES(fullname),
  role = VALUES(role),
  status = VALUES(status);

INSERT INTO admins (fullname, email, password, role, status)
VALUES (
  'Administrateur ECOFI',
  'admin@ecofi.sn',
  '$2y$12$8jR7UG6V.bszEsG3573f2.LGzaHEjH4dgf5hj4ILDGh1rzHgYvuIG',
  'admin',
  'active'
)
ON DUPLICATE KEY UPDATE
  fullname = VALUES(fullname),
  role = VALUES(role),
  status = VALUES(status);

SET FOREIGN_KEY_CHECKS = 1;
