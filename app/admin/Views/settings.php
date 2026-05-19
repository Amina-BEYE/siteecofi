<?php

$settingGroups = $settingGroups ?? [];

$groupLabels = [
    'site' => ['label' => 'Identité du site', 'icon' => 'fa-building'],
    'contact' => ['label' => 'Contacts', 'icon' => 'fa-address-book'],
    'mail' => ['label' => 'Emails & SMTP', 'icon' => 'fa-envelope'],
    'programme_immo' => ['label' => 'Programme immobilier', 'icon' => 'fa-city'],
    'social' => ['label' => 'Réseaux sociaux', 'icon' => 'fa-share-nodes'],
];

function settingValue(array $setting): string
{
    return htmlspecialchars((string) ($setting['setting_value'] ?? ''), ENT_QUOTES, 'UTF-8');
}

function settingLabel(array $setting): string
{
    return htmlspecialchars((string) ($setting['setting_label'] ?? $setting['setting_key'] ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<style>
.settings-hero {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
    flex-wrap: wrap;
}

.settings-hero p {
    color: #667085;
    margin: 8px 0 0;
    max-width: 760px;
}

.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(420px, 100%), 1fr));
    gap: 20px;
    margin-top: 22px;
}

.settings-group {
    display: grid;
    gap: 16px;
}

.settings-group-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-bottom: 14px;
    border-bottom: 1px solid #eef2f7;
}

.settings-group-icon {
    width: 42px;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: rgba(255, 133, 51, .12);
    color: var(--accent-color);
    flex: 0 0 auto;
}

.settings-group-header h3 {
    margin: 0;
}

.settings-field {
    min-width: 0;
}

.settings-field label {
    display: block;
    margin-bottom: 8px;
    color: #344054;
    font-size: 13px;
    font-weight: 800;
    line-height: 1.35;
}

.settings-field small {
    display: block;
    margin-top: 6px;
    color: #98a2b3;
    font-size: 12px;
    overflow-wrap: anywhere;
}

.settings-actions {
    position: sticky;
    bottom: 0;
    z-index: 2;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 22px;
    padding: 16px 0 0;
    background: linear-gradient(180deg, rgba(248, 249, 250, 0), #f8f9fa 32%);
}

@media (max-width: 600px) {
    .settings-actions {
        position: static;
    }
}
</style>

<div class="card">
    <div class="settings-hero">
        <div>
            <h2>
                <i class="fas fa-sliders" style="color: var(--accent-color); margin-right: 10px;"></i>
                Paramétrage général
            </h2>
            <p>
                Centralisez ici les informations affichées en dur dans le site : identité ECOFI, contacts, email de réception, textes du programme immobilier et réseaux sociaux.
            </p>
        </div>
    </div>
</div>

<form method="post" action="adminPage.php?page=settings" data-loading-text="Enregistrement des paramètres...">
    <input type="hidden" name="action" value="save_settings">

    <div class="settings-grid">
        <?php foreach ($settingGroups as $groupName => $settings): ?>
            <?php
            $meta = $groupLabels[$groupName] ?? ['label' => ucfirst(str_replace('_', ' ', $groupName)), 'icon' => 'fa-gear'];
            ?>
            <section class="card settings-group">
                <div class="settings-group-header">
                    <span class="settings-group-icon">
                        <i class="fas <?= htmlspecialchars($meta['icon']) ?>"></i>
                    </span>
                    <h3><?= htmlspecialchars($meta['label']) ?></h3>
                </div>

                <?php foreach ($settings as $setting): ?>
                    <?php
                    $key = (string) ($setting['setting_key'] ?? '');
                    $fieldType = (string) ($setting['field_type'] ?? 'text');
                    $inputType = in_array($fieldType, ['email', 'url', 'number'], true) ? $fieldType : 'text';
                    ?>
                    <div class="settings-field">
                        <label for="setting-<?= htmlspecialchars($key) ?>"><?= settingLabel($setting) ?></label>

                        <?php if ($fieldType === 'textarea'): ?>
                            <textarea
                                id="setting-<?= htmlspecialchars($key) ?>"
                                name="settings[<?= htmlspecialchars($key) ?>]"
                                class="form-control"
                                rows="4"
                            ><?= settingValue($setting) ?></textarea>
                        <?php else: ?>
                            <input
                                id="setting-<?= htmlspecialchars($key) ?>"
                                name="settings[<?= htmlspecialchars($key) ?>]"
                                type="<?= htmlspecialchars($inputType) ?>"
                                class="form-control"
                                value="<?= settingValue($setting) ?>"
                            >
                        <?php endif; ?>

                        <small>Clé : <?= htmlspecialchars($key) ?></small>
                    </div>
                <?php endforeach; ?>
            </section>
        <?php endforeach; ?>
    </div>

    <div class="settings-actions">
        <button type="submit" class="btn">
            <i class="fas fa-save"></i>
            Enregistrer les paramètres
        </button>
    </div>
</form>
