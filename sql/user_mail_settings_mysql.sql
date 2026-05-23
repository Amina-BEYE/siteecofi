-- Schéma MySQL ECOFI - Configuration mail IMAP/SMTP par utilisateur

CREATE TABLE IF NOT EXISTS user_mail_settings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL UNIQUE,
  email_address VARCHAR(180) NOT NULL,
  imap_host VARCHAR(180) NULL,
  imap_port INT UNSIGNED NULL DEFAULT 993,
  imap_encryption VARCHAR(10) NULL DEFAULT 'ssl',
  imap_username VARCHAR(180) NULL,
  imap_password_encrypted TEXT NULL,
  smtp_host VARCHAR(180) NULL,
  smtp_port INT UNSIGNED NULL DEFAULT 465,
  smtp_encryption VARCHAR(10) NULL DEFAULT 'ssl',
  smtp_username VARCHAR(180) NULL,
  smtp_password_encrypted TEXT NULL,
  config_source VARCHAR(40) NOT NULL DEFAULT 'manual',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_user_mail_settings_email (email_address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
