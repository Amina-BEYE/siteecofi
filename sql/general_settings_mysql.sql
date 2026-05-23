-- Schéma MySQL ECOFI - Paramétrage général admin
-- À importer dans phpMyAdmin sur la base utilisée par config/database.php.
-- Ce script est idempotent: il peut être relancé sans supprimer les données.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS general_settings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  group_name VARCHAR(80) NOT NULL,
  setting_key VARCHAR(120) NOT NULL,
  setting_label VARCHAR(180) NOT NULL,
  setting_value TEXT NULL,
  field_type VARCHAR(30) NOT NULL DEFAULT 'text',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_general_settings_key (setting_key),
  INDEX idx_general_settings_group (group_name),
  INDEX idx_general_settings_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO general_settings (group_name, setting_key, setting_label, setting_value, field_type, is_active)
VALUES
('site', 'company_name', 'Nom de l’entreprise', 'ECOFI', 'text', 1),
('site', 'company_full_name', 'Nom complet', 'Etablissement de Conseils sur le Foncier et l’Immobilier', 'text', 1),
('site', 'site_title', 'Titre du site', 'ECOFI Construction', 'text', 1),
('site', 'site_description', 'Description courte', 'ECOFI transforme vos projets immobiliers en réalités durables.', 'textarea', 1),

('contact', 'contact_email', 'Email principal', 'service.ecofi01@gmail.com', 'email', 1),
('contact', 'quote_email', 'Email réception devis', 'service.ecofi01@gmail.com', 'email', 1),
('contact', 'phone_fixed', 'Téléphone fixe', '33 998 50 72', 'text', 1),
('contact', 'phone_mobile', 'Téléphone mobile', '71 039 75 75', 'text', 1),
('contact', 'address', 'Adresse', 'Thiès, Nguinth 2ème tranche, Sénégal', 'textarea', 1),

('mail', 'smtp_from_name', 'Nom expéditeur email', 'ECOFI Construction', 'text', 1),
('mail', 'smtp_host', 'Serveur SMTP', 'smtp.gmail.com', 'text', 1),
('mail', 'smtp_port', 'Port SMTP', '587', 'number', 1),
('mail', 'smtp_user', 'Utilisateur SMTP', '', 'text', 1),
('mail', 'smtp_password', 'Mot de passe SMTP', '', 'password', 1),
('mail', 'smtp_encryption', 'Sécurité SMTP', 'tls', 'text', 1),
('mail', 'imap_host', 'Serveur IMAP réception', '', 'text', 1),
('mail', 'imap_port', 'Port IMAP', '993', 'number', 1),
('mail', 'imap_flags', 'Options IMAP', '/imap/ssl', 'text', 1),
('mail', 'imap_user', 'Utilisateur IMAP', '', 'text', 1),

('programme_immo', 'program_title', 'Titre programme immo', 'Terrains de 200 m² à Berokh Extension', 'text', 1),
('programme_immo', 'program_subtitle', 'Sous-titre programme immo', 'Derrière chez Sophie, en face des 3000 logements sociaux.', 'textarea', 1),
('programme_immo', 'program_location', 'Localisation', 'Berokh Extension', 'text', 1),
('programme_immo', 'program_surface', 'Surface terrain', '200 m²', 'text', 1),
('programme_immo', 'program_deposit', 'Acompte', '200 000 F CFA', 'text', 1),
('programme_immo', 'program_monthly_payment', 'Mensualité', '50 000 F CFA pendant 24 mois', 'text', 1),
('programme_immo', 'program_documents', 'Documents à préparer', 'Copie CNI ou Passeport, téléphone valide, adresse complète, frais de dossier, justificatif de paiement si disponible.', 'textarea', 1),

('social', 'facebook_url', 'Facebook', '#', 'url', 1),
('social', 'instagram_url', 'Instagram', '#', 'url', 1),
('social', 'linkedin_url', 'LinkedIn', '#', 'url', 1),
('social', 'twitter_url', 'Twitter/X', '#', 'url', 1),
('social', 'youtube_url', 'YouTube', '#', 'url', 1),
('social', 'tiktok_url', 'TikTok', '#', 'url', 1)
ON DUPLICATE KEY UPDATE
  group_name = VALUES(group_name),
  setting_label = VALUES(setting_label),
  field_type = VALUES(field_type),
  is_active = VALUES(is_active);
