-- Schéma MySQL ECOFI - Configuration IMAP/SMTP par utilisateur

ALTER TABLE users
  ADD COLUMN IF NOT EXISTS email_address VARCHAR(180) NULL AFTER status,
  ADD COLUMN IF NOT EXISTS imap_host VARCHAR(180) NULL AFTER email_address,
  ADD COLUMN IF NOT EXISTS imap_port INT UNSIGNED NULL DEFAULT 993 AFTER imap_host,
  ADD COLUMN IF NOT EXISTS imap_encryption VARCHAR(10) NULL DEFAULT 'ssl' AFTER imap_port,
  ADD COLUMN IF NOT EXISTS imap_username VARCHAR(180) NULL AFTER imap_encryption,
  ADD COLUMN IF NOT EXISTS imap_password TEXT NULL AFTER imap_username,
  ADD COLUMN IF NOT EXISTS smtp_host VARCHAR(180) NULL AFTER imap_password,
  ADD COLUMN IF NOT EXISTS smtp_port INT UNSIGNED NULL DEFAULT 465 AFTER smtp_host,
  ADD COLUMN IF NOT EXISTS smtp_encryption VARCHAR(10) NULL DEFAULT 'ssl' AFTER smtp_port,
  ADD COLUMN IF NOT EXISTS smtp_username VARCHAR(180) NULL AFTER smtp_encryption,
  ADD COLUMN IF NOT EXISTS smtp_password TEXT NULL AFTER smtp_username;
