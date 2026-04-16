<?php

declare(strict_types=1);

namespace App\Services;

use PDO;

/**
 * Service pour gérer les types de devis avec leurs champs spécifiques
 */
class QuoteTypeService
{
    private PDO $db;

    /**
     * Types de devis prédéfinis
     */
    private const DEFAULT_TYPES = [
        'standard' => [
            'label' => 'Produit/Service Standard',
            'description' => 'Produit ou service standard',
            'fields' => [
                ['name' => 'nom', 'label' => 'Nom du produit', 'type' => 'text', 'required' => true],
                ['name' => 'description', 'label' => 'Description (optionnel)', 'type' => 'textarea', 'required' => false],
                ['name' => 'quantite', 'label' => 'Quantité', 'type' => 'number', 'required' => true, 'min' => 1],
                ['name' => 'prix_unitaire', 'label' => 'Prix unitaire (FCFA)', 'type' => 'number', 'required' => true, 'min' => 0],
            ],
            'display_order' => 0,
        ],
        'gps_rental' => [
            'label' => 'Location de GPS',
            'description' => 'Service de location de GPS',
            'fields' => [
                ['name' => 'nom', 'label' => 'Modèle GPS', 'type' => 'text', 'required' => true],
                ['name' => 'description', 'label' => 'Description (optionnel)', 'type' => 'textarea', 'required' => false],
                ['name' => 'duree_jours', 'label' => 'Durée (jours)', 'type' => 'number', 'required' => true, 'min' => 1],
                ['name' => 'prix_par_jour', 'label' => 'Prix par jour (FCFA)', 'type' => 'number', 'required' => true, 'min' => 0],
                ['name' => 'prix_kilometre', 'label' => 'Prix par km (FCFA) - optionnel', 'type' => 'number', 'required' => false, 'min' => 0],
                ['name' => 'caution_montant', 'label' => 'Montant caution (FCFA) - optionnel', 'type' => 'number', 'required' => false, 'min' => 0],
                ['name' => 'assurance', 'label' => 'Assurance (FCFA) - optionnel', 'type' => 'number', 'required' => false, 'min' => 0],
                ['name' => 'conditions_specifiques', 'label' => 'Conditions spécifiques (optionnel)', 'type' => 'textarea', 'required' => false],
            ],
            'display_order' => 1,
        ],
    ];

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Récupère tous les types de devis disponibles
     */
    public function getAllTypes(): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, type_key, label, description, icon, form_schema, display_order
                FROM quote_types
                WHERE is_active = TRUE
                ORDER BY display_order ASC
            ");
            $stmt->execute();
            $types = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($types as &$type) {
                if ($type['form_schema']) {
                    $type['fields'] = json_decode($type['form_schema'], true)['fields'] ?? [];
                }
                unset($type['form_schema']);
            }

            return $types;
        } catch (\Exception $e) {
            return array_map(function ($key, $type) {
                return [
                    'type_key' => $key,
                    'label' => $type['label'],
                    'description' => $type['description'],
                    'fields' => $type['fields'],
                    'display_order' => $type['display_order'],
                ];
            }, array_keys(self::DEFAULT_TYPES), self::DEFAULT_TYPES);
        }
    }

    /**
     * Récupère un type de devis par sa clé
     */
    public function getTypeByKey(string $typeKey): ?array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, type_key, label, description, form_schema
                FROM quote_types
                WHERE type_key = :type_key AND is_active = TRUE
                LIMIT 1
            ");
            $stmt->execute([':type_key' => $typeKey]);
            $type = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($type && $type['form_schema']) {
                $type['fields'] = json_decode($type['form_schema'], true)['fields'] ?? [];
                unset($type['form_schema']);
            }

            return $type ?: null;
        } catch (\Exception $e) {
            return self::DEFAULT_TYPES[$typeKey] ?? null;
        }
    }

    /**
     * Valide les données d'une ligne de devis selon son type
     */
    public function validateLineItem(string $typeKey, array $itemData): array
    {
        $type = $this->getTypeByKey($typeKey);
        if (!$type) {
            return ['valid' => false, 'errors' => ['Type inconnu: ' . $typeKey], 'data' => []];
        }

        $errors = [];
        $validated = [];

        foreach ($type['fields'] as $field) {
            $fieldName = $field['name'];
            $value = $itemData[$fieldName] ?? null;

            // Validation requise
            if ($field['required']) {
                $isEmpty = false;
                
                if ($value === null || $value === '') {
                    $isEmpty = true;
                } elseif (is_string($value) && trim($value) === '') {
                    $isEmpty = true;
                } elseif (is_numeric($value) && $value == 0 && $field['type'] === 'number') {
                    if (isset($field['min']) && $field['min'] > 0) {
                        $isEmpty = true;
                    }
                }

                if ($isEmpty) {
                    $errors[] = "{$field['label']} est requis";
                    continue;
                }
            }

            if ($value === null || $value === '') {
                $validated[$fieldName] = null;
                continue;
            }

            // Validation selon le type
            switch ($field['type']) {
                case 'number':
                    if (!is_numeric($value)) {
                        $errors[] = "{$field['label']} doit être un nombre";
                        break;
                    }
                    $numValue = (float) $value;
                    if (isset($field['min']) && $numValue < $field['min']) {
                        $errors[] = "{$field['label']} doit être >= {$field['min']}";
                        break;
                    }
                    $validated[$fieldName] = $numValue;
                    break;

                case 'text':
                case 'textarea':
                    $validated[$fieldName] = trim((string) $value);
                    break;

                default:
                    $validated[$fieldName] = $value;
            }
        }

        return [
            'valid' => count($errors) === 0,
            'errors' => $errors,
            'data' => $validated,
        ];
    }

    /**
     * Calcule le total ligne d'un article selon son type
     */
    public function calculateLineTotal(string $typeKey, array $itemData): float
    {
        switch ($typeKey) {
            case 'gps_rental':
                $dureeJours = (float) ($itemData['duree_jours'] ?? 0);
                $prixParJour = (float) ($itemData['prix_par_jour'] ?? 0);
                $prixKm = (float) ($itemData['prix_kilometre'] ?? 0);
                $caution = (float) ($itemData['caution_montant'] ?? 0);
                $assurance = (float) ($itemData['assurance'] ?? 0);

                return ($dureeJours * $prixParJour) + ($prixKm * 0) + $caution + $assurance;

            case 'standard':
            default:
                $quantite = (float) ($itemData['quantite'] ?? 1);
                $prixUnitaire = (float) ($itemData['prix_unitaire'] ?? 0);
                return $quantite * $prixUnitaire;
        }
    }

    /**
     * Formate les données pour l'affichage
     */
    public function formatDataForDisplay(string $typeKey, array $itemData): array
    {
        $formatted = [];

        switch ($typeKey) {
            case 'gps_rental':
                if (!empty($itemData['nom'])) {
                    $formatted['Modèle'] = $itemData['nom'];
                }
                if (!empty($itemData['duree_jours'])) {
                    $formatted['Durée'] = $itemData['duree_jours'] . ' jours';
                }
                if (!empty($itemData['prix_par_jour'])) {
                    $formatted['Prix/jour'] = number_format((float) $itemData['prix_par_jour'], 0, ',', ' ') . ' FCFA';
                }
                if (!empty($itemData['prix_kilometre'])) {
                    $formatted['Prix/km'] = number_format((float) $itemData['prix_kilometre'], 0, ',', ' ') . ' FCFA';
                }
                if (!empty($itemData['caution_montant'])) {
                    $formatted['Caution'] = number_format((float) $itemData['caution_montant'], 0, ',', ' ') . ' FCFA';
                }
                if (!empty($itemData['assurance'])) {
                    $formatted['Assurance'] = number_format((float) $itemData['assurance'], 0, ',', ' ') . ' FCFA';
                }
                break;

            case 'standard':
            default:
                $formatted['Produit'] = $itemData['nom'] ?? '';
                $formatted['Description'] = $itemData['description'] ?? '';
                $formatted['Quantité'] = $itemData['quantite'] ?? 1;
                $formatted['Prix unitaire'] = number_format((float) ($itemData['prix_unitaire'] ?? 0), 0, ',', ' ') . ' FCFA';
                break;
        }

        return $formatted;
    }
}
