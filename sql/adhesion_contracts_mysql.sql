-- Schéma MySQL ECOFI - Contrats des adhésions programme immobilier

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS adhesion_contracts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  adhesion_id INT UNSIGNED NOT NULL UNIQUE,
  contract_content LONGTEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_adhesion_contracts_adhesion
    FOREIGN KEY (adhesion_id) REFERENCES adhesions(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
