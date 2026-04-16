<?php
function formatPrice($value): string
{
    return number_format((float) $value, 0, ',', ' ') . ' FCFA';
}

function normalizeStatus(string $value): string
{
    $value = strtolower(trim($value));
    $value = str_replace(['é', 'è', 'ê', 'ë', 'à', 'á', 'â', 'ä', 'ù', 'û', 'ü', 'ô', 'ö', 'î', 'ï', 'ç', ' ', '-'], ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'a', 'u', 'u', 'u', 'o', 'o', 'i', 'i', 'c', '_', '_'], $value);

    $map = [
        'enattente' => 'en_attente',
        'en_cours' => 'en_cours',
        'encours' => 'en_cours',
        'accepte' => 'accepte',
        'accepte' => 'accepte',
        'acceptes' => 'accepte',
        'refuse' => 'refuse',
        'refusee' => 'refuse',
        'refuses' => 'refuse',
        'expire' => 'expire',
        'expiree' => 'expire',
        'expirees' => 'expire',
        'livre' => 'livre',
        'livree' => 'livre',
        'livres' => 'livre',
        'paie' => 'paie',
        'paye' => 'paie',
        'payee' => 'paie',
        'payed' => 'paie',
    ];

    return $map[$value] ?? ($value !== '' ? $value : 'inconnu');
}

$orderStatusCounts = [];
foreach ($orders as $order) {
    $status = normalizeStatus($order['statut_commande'] ?? 'inconnu');
    $orderStatusCounts[$status] = ($orderStatusCounts[$status] ?? 0) + 1;
}

$quoteStatusCounts = [];
foreach ($quotes as $quote) {
    $status = normalizeStatus($quote['statut'] ?? 'inconnu');
    $quoteStatusCounts[$status] = ($quoteStatusCounts[$status] ?? 0) + 1;
}

$availableOrderStatuses = [
    'toutes' => 'Toutes',
    'en_attente' => 'En attente',
    'en_cours' => 'En cours',
    'accepte' => 'Acceptées',
    'refuse' => 'Refusées',
        'expire' => 'Expirés',
        'livre' => 'Livrées',
        'paie' => 'Payées',
        'inconnu' => 'Autres',
];

$availableQuoteStatuses = [
    'toutes' => 'Toutes',
    'en_attente' => 'En attente',
    'accepte' => 'Acceptés',
    'refuse' => 'Refusés',
    'expire' => 'Expirés',
    'inconnu' => 'Autres',
];
?>

<style>
.status-card {
    display: inline-flex;
    flex-direction: column;
    justify-content: space-between;
    width: 190px;
    min-height: 110px;
    padding: 18px;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 10px 30px rgba(15, 23, 42, .08);
    margin: 0 12px 18px 0;
}

.status-card h3 {
    margin: 0 0 8px;
    font-size: 13px;
    color: #344054;
}

.status-card p {
    font-size: 24px;
    font-weight: 700;
    margin: 0;
    color: #0f172a;
}

.filter-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin: 16px 0 14px;
}

.filter-button {
    border: 1px solid #d1d5db;
    background: #f8fafc;
    color: #344054;
    padding: 8px 12px;
    border-radius: 999px;
    cursor: pointer;
    font-size: 12px;
}

.admin-table,
.admin-table th,
.admin-table td {
    font-size: 13px;
}

.admin-table th {
    padding: 10px 12px;
}

.admin-table td {
    padding: 10px 12px;
}

.filter-button.active {
    background: #2563eb;
    border-color: #2563eb;
    color: #fff;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 96px;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .02em;
    white-space: nowrap;
}

.status-badge--en_attente {
    background: #fef3c7;
    color: #92400e;
}

.status-badge--en_cours {
    background: #dbeafe;
    color: #1d4ed8;
}

.status-badge--accepte {
    background: #dcfce7;
    color: #166534;
}

.status-badge--refuse {
    background: #fee2e2;
    color: #b91c1c;
}

.status-badge--expire,
.status-badge--expiree,
.status-badge--expirees {
    background: #fde68a;
    color: #78350f;
}

.status-badge--livre {
    background: #d1fae5;
    color: #065f46;
}

.status-badge--paie,
.status-badge--paye,
.status-badge--payee {
    background: #e0f2fe;
    color: #075985;
}

.status-badge--inconnu {
    background: #e2e8f0;
    color: #334155;
}

.table-section {
    margin-top: 20px;
}

@media (max-width: 900px) {
    .status-card {
        width: calc(50% - 10px);
    }
}

@media (max-width: 600px) {
    .status-card {
        width: 100%;
    }
}
</style>

<div class="card">
    <div style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:16px; align-items:center;">
        <div>
            <h2 style="margin-bottom:8px;">Commandes</h2>
            <p style="color:#667085; margin:0;">Visualisez et traitez les commandes selon leur statut.</p>
        </div>
    </div>

    <div style="display:flex; flex-wrap:wrap; gap:16px; margin-top:20px;">
        <div class="status-card">
            <h3>Total commandes</h3>
            <p><?= count($orders) ?></p>
        </div>
        <?php foreach ($availableOrderStatuses as $statusKey => $statusLabel): ?>
            <?php if ($statusKey === 'toutes') continue; ?>
            <div class="status-card">
                <h3><?= htmlspecialchars($statusLabel) ?></h3>
                <p><?= $orderStatusCounts[$statusKey] ?? 0 ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="filter-bar">
        <?php foreach ($availableOrderStatuses as $statusKey => $statusLabel): ?>
            <button type="button" class="filter-button<?= $statusKey === 'toutes' ? ' active' : '' ?>" data-target="orders" data-status="<?= htmlspecialchars($statusKey) ?>">
                <?= htmlspecialchars($statusLabel) ?>
            </button>
        <?php endforeach; ?>
    </div>

    <?php if (empty($orders)): ?>
        <p>Aucune commande trouvée.</p>
    <?php else: ?>
        <div class="table-section">
            <div class="table-responsive">
                <table class="admin-table" id="ordersTable">
                    <thead>
                        <tr>
                            <th>N° commande</th>
                            <th>Client</th>
                            <th>Contact</th>
                            <th>Total TTC</th>
                            <th>Paiement</th>
                            <th>Statut paiement</th>
                            <th>Statut commande</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <?php $orderStatus = normalizeStatus($order['statut_commande'] ?? 'inconnu'); ?>
                            <tr data-status="<?= htmlspecialchars($orderStatus) ?>">
                                <td><?= htmlspecialchars($order['numero_commande']) ?></td>
                                <td><?= htmlspecialchars($order['client_nom']) ?></td>
                                <td>
                                    <?= htmlspecialchars($order['client_email']) ?><br>
                                    <small><?= htmlspecialchars($order['client_telephone']) ?></small>
                                </td>
                                <td><?= htmlspecialchars(formatPrice($order['total_ttc'])) ?></td>
                                <td><?= htmlspecialchars($order['mode_paiement']) ?></td>
                                <td><?= htmlspecialchars($order['statut_paiement']) ?></td>
                                <td>
                                    <span class="status-badge status-badge--<?= htmlspecialchars($orderStatus) ?>">
                                        <?= htmlspecialchars($order['statut_commande']) ?: 'Inconnu' ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($order['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="card table-section" style="margin-top:30px;">
    <div style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:16px; align-items:center;">
        <div>
            <h2 style="margin-bottom:8px;">Demandes de devis</h2>
            <p style="color:#667085; margin:0;">Filtrez les devis par statut pour un suivi rapide.</p>
        </div>
    </div>

    <div style="display:flex; flex-wrap:wrap; gap:16px; margin-top:20px;">
        <div class="status-card">
            <h3>Total devis</h3>
            <p><?= count($quotes) ?></p>
        </div>
        <?php foreach ($availableQuoteStatuses as $statusKey => $statusLabel): ?>
            <?php if ($statusKey === 'toutes') continue; ?>
            <div class="status-card">
                <h3><?= htmlspecialchars($statusLabel) ?></h3>
                <p><?= $quoteStatusCounts[$statusKey] ?? 0 ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="filter-bar">
        <?php foreach ($availableQuoteStatuses as $statusKey => $statusLabel): ?>
            <button type="button" class="filter-button<?= $statusKey === 'toutes' ? ' active' : '' ?>" data-target="quotes" data-status="<?= htmlspecialchars($statusKey) ?>">
                <?= htmlspecialchars($statusLabel) ?>
            </button>
        <?php endforeach; ?>
    </div>

    <?php if (empty($quotes)): ?>
        <p>Aucune demande de devis trouvée.</p>
    <?php else: ?>
        <div class="table-section">
            <div class="table-responsive">
                <table class="admin-table" id="quotesTable">
                    <thead>
                        <tr>
                            <th>N° devis</th>
                            <th>Client</th>
                            <th>Contact</th>
                            <th>Total HT</th>
                            <th>Total TTC</th>
                            <th>Statut</th>
                            <th>Notes</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quotes as $quote): ?>
                            <?php $quoteStatus = normalizeStatus($quote['statut'] ?? 'inconnu'); ?>
                            <tr data-status="<?= htmlspecialchars($quoteStatus) ?>">
                                <td><?= htmlspecialchars($quote['numero_devis']) ?></td>
                                <td><?= htmlspecialchars($quote['client_nom']) ?></td>
                                <td>
                                    <?= htmlspecialchars($quote['client_email']) ?><br>
                                    <small><?= htmlspecialchars($quote['client_telephone']) ?></small>
                                </td>
                                <td><?= htmlspecialchars(formatPrice($quote['total_ht'])) ?></td>
                                <td><?= htmlspecialchars(formatPrice($quote['total_ttc'])) ?></td>
                                <td>
                                    <span class="status-badge status-badge--<?= htmlspecialchars($quoteStatus) ?>">
                                        <?= htmlspecialchars($quote['statut'] ?: 'Inconnu') ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($quote['notes'] ?? '') ?></td>
                                <td><?= htmlspecialchars($quote['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.querySelectorAll('.filter-button').forEach(button => {
        button.addEventListener('click', function () {
            const target = this.dataset.target;
            const status = this.dataset.status;
            document.querySelectorAll('.filter-button[data-target="' + target + '"]').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const table = document.getElementById(target + 'Table');
            if (!table) return;

            table.querySelectorAll('tbody tr').forEach(row => {
                const rowStatus = row.dataset.status || 'inconnu';
                if (status === 'toutes' || rowStatus === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>