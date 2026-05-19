<?php

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../Core/Database.php';

use App\Core\Database;

class SettingsModel
{
    private PDO $db;

    private array $defaultSettings = [
        ['site', 'company_name', 'Nom de l’entreprise', 'ECOFI', 'text'],
        ['site', 'company_full_name', 'Nom complet', 'Etablissement de Conseils sur le Foncier et l’Immobilier', 'text'],
        ['site', 'site_title', 'Titre du site', 'ECOFI Construction', 'text'],
        ['site', 'site_description', 'Description courte', 'ECOFI transforme vos projets immobiliers en réalités durables.', 'textarea'],
        ['contact', 'contact_email', 'Email principal', 'service.ecofi01@gmail.com', 'email'],
        ['contact', 'quote_email', 'Email réception devis', 'service.ecofi01@gmail.com', 'email'],
        ['contact', 'phone_fixed', 'Téléphone fixe', '33 998 50 72', 'text'],
        ['contact', 'phone_mobile', 'Téléphone mobile', '71 039 75 75', 'text'],
        ['contact', 'address', 'Adresse', 'Thiès, Nguinth 2ème tranche, Sénégal', 'textarea'],
        ['mail', 'smtp_from_name', 'Nom expéditeur email', 'ECOFI Construction', 'text'],
        ['mail', 'smtp_host', 'Serveur SMTP', 'smtp.gmail.com', 'text'],
        ['mail', 'smtp_port', 'Port SMTP', '587', 'number'],
        ['programme_immo', 'program_title', 'Titre programme immo', 'Terrains de 200 m² à Berokh Extension', 'text'],
        ['programme_immo', 'program_subtitle', 'Sous-titre programme immo', 'Derrière chez Sophie, en face des 3000 logements sociaux.', 'textarea'],
        ['programme_immo', 'program_location', 'Localisation', 'Berokh Extension', 'text'],
        ['programme_immo', 'program_surface', 'Surface terrain', '200 m²', 'text'],
        ['programme_immo', 'program_deposit', 'Acompte', '200 000 F CFA', 'text'],
        ['programme_immo', 'program_monthly_payment', 'Mensualité', '50 000 F CFA pendant 24 mois', 'text'],
        ['programme_immo', 'program_documents', 'Documents à préparer', 'Copie CNI ou Passeport, téléphone valide, adresse complète, frais de dossier, justificatif de paiement si disponible.', 'textarea'],
        ['social', 'facebook_url', 'Facebook', '#', 'url'],
        ['social', 'instagram_url', 'Instagram', '#', 'url'],
        ['social', 'linkedin_url', 'LinkedIn', '#', 'url'],
        ['social', 'twitter_url', 'Twitter/X', '#', 'url'],
        ['social', 'youtube_url', 'YouTube', '#', 'url'],
        ['social', 'tiktok_url', 'TikTok', '#', 'url'],
    ];

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function ensureDefaults(): void
    {
        $this->ensureTable();

        $sql = "
            INSERT INTO general_settings (`group_name`, `setting_key`, `setting_label`, `setting_value`, `field_type`, `is_active`)
            VALUES (:group_name, :setting_key, :setting_label, :setting_value, :field_type, 1)
            ON DUPLICATE KEY UPDATE
                setting_label = VALUES(setting_label),
                field_type = VALUES(field_type),
                is_active = 1
        ";

        $stmt = $this->db->prepare($sql);

        foreach ($this->defaultSettings as [$groupName, $key, $label, $value, $fieldType]) {
            $stmt->execute([
                ':group_name' => $groupName,
                ':setting_key' => $key,
                ':setting_label' => $label,
                ':setting_value' => $value,
                ':field_type' => $fieldType,
            ]);
        }
    }

    private function ensureTable(): void
    {
        $this->db->exec("
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function getGroupedSettings(): array
    {
        $this->ensureDefaults();

        $stmt = $this->db->query("
            SELECT id, group_name, setting_key, setting_label, setting_value, field_type, is_active, updated_at
            FROM general_settings
            WHERE is_active = 1
            ORDER BY group_name ASC, id ASC
        ");

        $groups = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $setting) {
            $groups[$setting['group_name']][] = $setting;
        }

        return $groups;
    }

    public function updateSettings(array $settings): bool
    {
        $this->ensureDefaults();

        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare("
                UPDATE general_settings
                SET setting_value = :setting_value, updated_at = NOW()
                WHERE setting_key = :setting_key
            ");

            foreach ($settings as $key => $value) {
                $key = trim((string) $key);

                if ($key === '') {
                    continue;
                }

                $stmt->execute([
                    ':setting_key' => $key,
                    ':setting_value' => is_array($value) ? '' : trim((string) $value),
                ]);
            }

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollBack();
            error_log('[SettingsModel] updateSettings error: ' . $e->getMessage());
            return false;
        }
    }
}
