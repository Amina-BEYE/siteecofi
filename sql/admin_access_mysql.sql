-- Schéma MySQL ECOFI - Gestion des accès admin par profil via app_features
-- À importer dans phpMyAdmin sur la base utilisée par config/database.php.
-- Ce script est idempotent.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS app_features (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role_key VARCHAR(50) NOT NULL,
  page_key VARCHAR(80) NOT NULL,
  can_access TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_app_features (role_key, page_key),
  INDEX idx_app_features_role (role_key),
  INDEX idx_app_features_page (page_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS app_roles (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role_key VARCHAR(50) NOT NULL UNIQUE,
  role_label VARCHAR(120) NOT NULL,
  is_system TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO app_roles (role_key, role_label, is_system)
VALUES
('admin', 'Administrateur', 1),
('manager', 'Manager', 1),
('agent', 'Agent', 1)
ON DUPLICATE KEY UPDATE role_label = VALUES(role_label);

INSERT INTO app_features (role_key, page_key, can_access)
VALUES
('admin', 'dashboard', 1),
('admin', 'auth', 1),
('admin', 'access-control', 1),
('admin', 'clients', 1),
('admin', 'products', 1),
('admin', 'orders', 1),
('admin', 'programme-immo', 1),
('admin', 'payment-schedules', 1),
('admin', 'settings', 1),
('admin', 'employees', 1),
('admin', 'notifications', 1),

('manager', 'dashboard', 1),
('manager', 'auth', 0),
('manager', 'access-control', 0),
('manager', 'clients', 1),
('manager', 'products', 1),
('manager', 'orders', 1),
('manager', 'programme-immo', 1),
('manager', 'payment-schedules', 1),
('manager', 'settings', 0),
('manager', 'employees', 1),
('manager', 'notifications', 1),

('agent', 'dashboard', 1),
('agent', 'auth', 0),
('agent', 'access-control', 0),
('agent', 'clients', 1),
('agent', 'products', 0),
('agent', 'orders', 1),
('agent', 'programme-immo', 1),
('agent', 'payment-schedules', 1),
('agent', 'settings', 0),
('agent', 'employees', 0),
('agent', 'notifications', 1)
ON DUPLICATE KEY UPDATE
  role_key = VALUES(role_key),
  page_key = VALUES(page_key);
