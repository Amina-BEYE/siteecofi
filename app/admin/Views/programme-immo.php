<?php

function immoValue($value): string
{
    $value = trim((string) ($value ?? ''));
    return htmlspecialchars($value !== '' ? $value : '-', ENT_QUOTES, 'UTF-8');
}

$statusOptions = ['Nouveau', 'En cours', 'Validé', 'Refusé'];
$selectedAdhesion = $selectedAdhesion ?? null;
$selectedNotes = $selectedNotes ?? [];
$stats = $stats ?? [];
?>

<style>
.immo-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(150px, 100%), 1fr));
    gap: 14px;
    margin: 18px 0 22px;
}

.immo-stat {
    padding: 16px;
    border: 1px solid #eef2f7;
    border-radius: 12px;
    background: #fff;
    min-width: 0;
}

.immo-stat span {
    display: block;
    color: #667085;
    font-size: 12px;
    margin-bottom: 8px;
    line-height: 1.3;
}

.immo-stat strong {
    color: #0f172a;
    font-size: 24px;
    line-height: 1.1;
}

.immo-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.4fr) minmax(340px, .8fr);
    gap: 20px;
    align-items: start;
}

.immo-table th,
.immo-table td {
    font-size: 13px;
    padding: 10px 12px;
    vertical-align: top;
    line-height: 1.45;
    overflow-wrap: anywhere;
}

.immo-badge {
    display: inline-flex;
    align-items: center;
    min-width: 88px;
    max-width: 100%;
    justify-content: center;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 800;
    line-height: 1.2;
    text-align: center;
    white-space: normal;
}

.immo-badge--nouveau {
    background: #fef3c7;
    color: #92400e;
}

.immo-badge--en-cours {
    background: #dbeafe;
    color: #1d4ed8;
}

.immo-badge--valide {
    background: #dcfce7;
    color: #166534;
}

.immo-badge--refuse {
    background: #fee2e2;
    color: #b91c1c;
}

.immo-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    min-width: 0;
}

.immo-btn,
.immo-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 34px;
    padding: 8px 11px;
    border: 0;
    border-radius: 8px;
    background: #2563eb;
    color: #fff;
    text-decoration: none;
    font-size: 12px;
    font-weight: 800;
    cursor: pointer;
    line-height: 1.2;
    max-width: 100%;
    min-width: 0;
    text-align: center;
    white-space: normal;
}

.immo-btn.danger {
    background: #dc2626;
}

.immo-link.secondary,
.immo-btn.secondary {
    background: #475467;
}

.detail-list {
    display: grid;
    gap: 10px;
    min-width: 0;
}

.detail-row {
    display: grid;
    grid-template-columns: minmax(112px, 140px) minmax(0, 1fr);
    gap: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eef2f7;
    min-width: 0;
}

.detail-row dt {
    color: #667085;
    font-size: 12px;
    font-weight: 800;
    line-height: 1.35;
}

.detail-row dd {
    margin: 0;
    color: #101828;
    font-size: 13px;
    line-height: 1.45;
    min-width: 0;
    overflow-wrap: anywhere;
}

.immo-form {
    display: grid;
    gap: 10px;
    margin-top: 16px;
}

.immo-form select,
.immo-form textarea {
    width: 100%;
    min-width: 0;
    border: 1px solid #d0d5dd;
    border-radius: 8px;
    padding: 10px 12px;
    font: inherit;
}

.note-list {
    display: grid;
    gap: 10px;
    margin-top: 14px;
}

.note-item {
    padding: 12px;
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid #eef2f7;
    min-width: 0;
}

.note-item strong {
    display: block;
    font-size: 12px;
    color: #344054;
    margin-bottom: 5px;
    line-height: 1.35;
}

.note-item p {
    margin: 0;
    color: #475467;
    font-size: 13px;
    line-height: 1.5;
    white-space: pre-wrap;
    overflow-wrap: anywhere;
}

@media (max-width: 1100px) {
    .immo-layout {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 620px) {
    .immo-table {
        min-width: 760px;
    }

    .detail-row {
        grid-template-columns: 1fr;
        gap: 4px;
    }

    .immo-btn,
    .immo-link {
        width: 100%;
    }
}
</style>

<div class="card">
    <div style="display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap; align-items:flex-start;">
        <div>
            <h2 style="margin-bottom:8px;">Programme Immo</h2>
            <p style="color:#667085; margin:0;">Suivi des adhésions au programme immobilier ECOFI Construction.</p>
        </div>
    </div>

    <div class="immo-summary">
        <div class="immo-stat"><span>Total</span><strong><?= (int) ($stats['total'] ?? 0) ?></strong></div>
        <div class="immo-stat"><span>Nouveau</span><strong><?= (int) ($stats['Nouveau'] ?? 0) ?></strong></div>
        <div class="immo-stat"><span>En cours</span><strong><?= (int) ($stats['En cours'] ?? 0) ?></strong></div>
        <div class="immo-stat"><span>Validé</span><strong><?= (int) ($stats['Validé'] ?? 0) ?></strong></div>
        <div class="immo-stat"><span>Refusé</span><strong><?= (int) ($stats['Refusé'] ?? 0) ?></strong></div>
    </div>
</div>

<div class="immo-layout">
    <div class="card">
        <h3 style="margin-bottom:14px;">Adhésions reçues</h3>

        <?php if (empty($adhesions)): ?>
            <p>Aucune adhésion trouvée.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table immo-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom complet</th>
                            <th>Contact</th>
                            <th>CNI / Passeport</th>
                            <th>Paiement</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($adhesions as $adhesion): ?>
                            <?php
                            $status = (string) ($adhesion['status'] ?? 'Nouveau');
                            $badgeClass = strtolower(str_replace([' ', 'é'], ['-', 'e'], $status));
                            ?>
                            <tr>
                                <td>#<?= (int) ($adhesion['id'] ?? 0) ?></td>
                                <td><?= immoValue(($adhesion['prenom'] ?? '') . ' ' . ($adhesion['nom'] ?? '')) ?></td>
                                <td>
                                    <?= immoValue($adhesion['email'] ?? '') ?><br>
                                    <small><?= immoValue($adhesion['telephone'] ?? '') ?></small>
                                </td>
                                <td><?= immoValue($adhesion['cni'] ?? '') ?></td>
                                <td><?= immoValue($adhesion['mode_paiement'] ?? '') ?></td>
                                <td><span class="immo-badge immo-badge--<?= htmlspecialchars($badgeClass) ?>"><?= immoValue($status) ?></span></td>
                                <td><?= immoValue($adhesion['created_at'] ?? '') ?></td>
                                <td>
                                    <div class="immo-actions">
                                        <a class="immo-link" href="adminPage.php?page=programme-immo&id=<?= (int) ($adhesion['id'] ?? 0) ?>">Voir</a>
                                        <form method="post" action="adminPage.php?page=programme-immo" data-loading-text="Suppression de l’adhésion..." onsubmit="return confirm('Supprimer cette adhésion ?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="adhesion_id" value="<?= (int) ($adhesion['id'] ?? 0) ?>">
                                            <button type="submit" class="immo-btn danger">Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <aside class="card">
        <?php if (!$selectedAdhesion): ?>
            <h3 style="margin-bottom:10px;">Dossier adhésion</h3>
            <p style="color:#667085; margin:0;">Sélectionnez une adhésion pour voir les détails, changer le statut ou ajouter une note.</p>
        <?php else: ?>
            <h3 style="margin-bottom:14px;">Dossier #<?= (int) $selectedAdhesion['id'] ?></h3>

            <dl class="detail-list">
                <div class="detail-row"><dt>Nom complet</dt><dd><?= immoValue(($selectedAdhesion['prenom'] ?? '') . ' ' . ($selectedAdhesion['nom'] ?? '')) ?></dd></div>
                <div class="detail-row"><dt>Naissance</dt><dd><?= immoValue(($selectedAdhesion['date_naissance'] ?? '') . ' à ' . ($selectedAdhesion['lieu_naissance'] ?? '')) ?></dd></div>
                <div class="detail-row"><dt>Adresse</dt><dd><?= immoValue($selectedAdhesion['adresse'] ?? '') ?></dd></div>
                <div class="detail-row"><dt>Téléphone</dt><dd><?= immoValue($selectedAdhesion['telephone'] ?? '') ?></dd></div>
                <div class="detail-row"><dt>Email</dt><dd><?= immoValue($selectedAdhesion['email'] ?? '') ?></dd></div>
                <div class="detail-row"><dt>CNI/Passeport</dt><dd><?= immoValue($selectedAdhesion['cni'] ?? '') ?></dd></div>
                <div class="detail-row"><dt>Paiement</dt><dd><?= immoValue($selectedAdhesion['mode_paiement'] ?? '') ?></dd></div>
                <div class="detail-row"><dt>Message</dt><dd><?= nl2br(immoValue($selectedAdhesion['message'] ?? '')) ?></dd></div>
            </dl>

            <form method="post" action="adminPage.php?page=programme-immo&id=<?= (int) $selectedAdhesion['id'] ?>" class="immo-form" data-loading-text="Mise à jour de l’adhésion...">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="adhesion_id" value="<?= (int) $selectedAdhesion['id'] ?>">
                <label for="immo-status">Statut du dossier</label>
                <select id="immo-status" name="status">
                    <?php foreach ($statusOptions as $status): ?>
                        <option value="<?= htmlspecialchars($status) ?>" <?= ($selectedAdhesion['status'] ?? '') === $status ? 'selected' : '' ?>>
                            <?= htmlspecialchars($status) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="immo-btn">Mettre à jour</button>
            </form>

            <form method="post" action="adminPage.php?page=programme-immo&id=<?= (int) $selectedAdhesion['id'] ?>" class="immo-form" data-loading-text="Ajout de la note...">
                <input type="hidden" name="action" value="add_note">
                <input type="hidden" name="adhesion_id" value="<?= (int) $selectedAdhesion['id'] ?>">
                <label for="immo-note">Note interne</label>
                <textarea id="immo-note" name="note" rows="4" required></textarea>
                <button type="submit" class="immo-btn secondary">Ajouter la note</button>
            </form>

            <div class="note-list">
                <?php if (empty($selectedNotes)): ?>
                    <p style="color:#667085;">Aucune note interne.</p>
                <?php else: ?>
                    <?php foreach ($selectedNotes as $note): ?>
                        <div class="note-item">
                            <strong><?= immoValue($note['admin_name'] ?? 'Administrateur') ?> - <?= immoValue($note['created_at'] ?? '') ?></strong>
                            <p><?= immoValue($note['note'] ?? '') ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </aside>
</div>
