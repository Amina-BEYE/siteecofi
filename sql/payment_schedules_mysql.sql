-- Schéma MySQL ECOFI - Échéances de paiement programme immobilier
-- À importer dans phpMyAdmin sur la base utilisée par config/database.php.
-- Ce script est idempotent.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS payment_schedules (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  adhesion_id INT UNSIGNED NOT NULL,
  installment_number INT UNSIGNED NOT NULL,
  due_date DATE NOT NULL,
  amount DECIMAL(12,2) NOT NULL DEFAULT 0,
  status VARCHAR(30) NOT NULL DEFAULT 'pending',
  paid_at DATETIME NULL,
  payment_method VARCHAR(100) NULL,
  note TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_payment_schedule_installment (adhesion_id, installment_number),
  INDEX idx_payment_schedule_adhesion (adhesion_id),
  INDEX idx_payment_schedule_status (status),
  INDEX idx_payment_schedule_due_date (due_date),
  CONSTRAINT fk_payment_schedule_adhesion
    FOREIGN KEY (adhesion_id) REFERENCES adhesions(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
