<?php

namespace App\Core;

use PDO;
use Throwable;

class Settings
{
    private static ?array $settings = null;

    private static array $defaults = [
        'company_name' => 'ECOFI',
        'company_full_name' => 'Etablissement de Conseils sur le Foncier et l’Immobilier',
        'site_title' => 'Ecofi - Etablissement de conseils sur le foncier et l\'immobilier',
        'site_description' => 'ECOFI transforme vos projets immobiliers en réalités durables.',
        'contact_email' => 'service.ecofi01@gmail.com',
        'quote_email' => 'service.ecofi01@gmail.com',
        'phone_fixed' => '33 998 50 72',
        'phone_mobile' => '71 039 75 75',
        'address' => 'Thiès, Nguinth 2ème tranche, Sénégal',
        'smtp_from_name' => 'ECOFI Construction',
        'smtp_host' => 'smtp.gmail.com',
        'smtp_port' => '587',
        'program_title' => 'Terrains de 200 m² à Berokh Extension',
        'program_subtitle' => 'Derrière chez Sophie, en face des 3000 logements sociaux.',
        'program_location' => 'Berokh Extension',
        'program_surface' => '200 m²',
        'program_deposit' => '200 000 F CFA',
        'program_monthly_payment' => '50 000 F CFA pendant 24 mois',
        'program_documents' => 'Copie CNI ou Passeport, téléphone valide, adresse complète, frais de dossier, justificatif de paiement si disponible.',
        'facebook_url' => '#',
        'instagram_url' => '#',
        'linkedin_url' => '#',
        'twitter_url' => '#',
        'youtube_url' => '#',
        'tiktok_url' => '#',
    ];

    public static function get(string $key, ?string $fallback = null): string
    {
        $settings = self::all();
        return (string) ($settings[$key] ?? $fallback ?? self::$defaults[$key] ?? '');
    }

    public static function all(): array
    {
        if (self::$settings !== null) {
            return self::$settings;
        }

        self::$settings = self::$defaults;

        try {
            $db = Database::getConnection();

            if (!self::tableExists($db)) {
                return self::$settings;
            }

            $stmt = $db->query("
                SELECT setting_key, setting_value
                FROM general_settings
                WHERE is_active = 1
            ");

            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $key = (string) ($row['setting_key'] ?? '');

                if ($key !== '') {
                    self::$settings[$key] = (string) ($row['setting_value'] ?? '');
                }
            }
        } catch (Throwable $e) {
            error_log('[Settings] fallback defaults: ' . $e->getMessage());
        }

        return self::$settings;
    }

    private static function tableExists(PDO $db): bool
    {
        try {
            $stmt = $db->query("SHOW TABLES LIKE 'general_settings'");
            return (bool) $stmt->fetchColumn();
        } catch (Throwable $e) {
            return false;
        }
    }
}
