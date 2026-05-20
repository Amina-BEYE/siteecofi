<?php
$paymentClients = $paymentClients ?? [];
$scheduleStats = $scheduleStats ?? [];
$statusFilter = $statusFilter ?? null;

function paymentValue($value): string { return htmlspecialchars(trim((string) $value) ?: '-', ENT_QUOTES, 'UTF-8'); }
function paymentMoney($value): string { return number_format((float) $value, 0, ',', ' ') . ' F CFA'; }
function paymentDate($value): string { $ts = strtotime((string) $value); return $ts ? date('d/m/Y', $ts) : '-'; }
?>

<div class="payment-page">
    <div class="card payment-hero-card">
        <div>
            <span class="payment-kicker"><i class="fas fa-calendar-check"></i> Programme immobilier</span>
            <h2>Échéances de paiement</h2>
            <p>Suivez chaque client adhérent, ouvrez le détail des mensualités, relancez par mail et exportez la facture.</p>
        </div>
        <a class="btn btn-outline" href="adminPage.php?page=programme-immo"><i class="fas fa-building"></i> Adhésions</a>
    </div>

    <div class="payment-stats">
        <div class="payment-stat"><span>Total échéances</span><strong><?= (int) ($scheduleStats['total'] ?? 0) ?></strong></div>
        <div class="payment-stat"><span>Payées</span><strong><?= (int) ($scheduleStats['paid'] ?? 0) ?></strong><small><?= paymentMoney($scheduleStats['amount_paid'] ?? 0) ?></small></div>
        <div class="payment-stat"><span>En attente</span><strong><?= (int) ($scheduleStats['pending'] ?? 0) ?></strong><small><?= paymentMoney($scheduleStats['amount_pending'] ?? 0) ?></small></div>
        <div class="payment-stat danger"><span>En retard</span><strong><?= (int) ($scheduleStats['late'] ?? 0) ?></strong></div>
    </div>

    <div class="payment-filters">
        <?php foreach ([null => 'Tous', 'pending' => 'À venir', 'late' => 'En retard', 'paid' => 'Payés'] as $key => $label): ?>
            <?php $active = $statusFilter === $key || ($statusFilter === null && $key === null); ?>
            <a class="payment-filter <?= $active ? 'active' : '' ?>" href="adminPage.php?page=payment-schedules<?= $key ? '&status=' . urlencode($key) : '' ?>"><?= htmlspecialchars($label) ?></a>
        <?php endforeach; ?>
    </div>

    <div class="admin-list-toolbar">
        <label class="admin-search-box">
            <i class="fas fa-search"></i>
            <input
                type="search"
                class="admin-search-input"
                data-admin-search
                data-target="#paymentClientGrid .payment-client-card"
                placeholder="Rechercher par nom, prénom, email ou téléphone"
                aria-label="Rechercher un échéancier"
            >
        </label>
    </div>

    <div class="payment-client-grid" id="paymentClientGrid">
        <?php foreach ($paymentClients as $client): ?>
            <?php $modalId = 'payment-modal-' . (int) $client['adhesion_id']; ?>
            <article class="card payment-client-card">
                <div class="payment-client-head">
                    <div>
                        <h3><?= paymentValue(($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? '')) ?></h3>
                        <p><?= paymentValue($client['email'] ?? '') ?> · <?= paymentValue($client['telephone'] ?? '') ?></p>
                    </div>
                    <?php if (($client['late_count'] ?? 0) > 0): ?>
                        <span class="payment-badge late"><?= (int) $client['late_count'] ?> retard</span>
                    <?php else: ?>
                        <span class="payment-badge pending">Suivi actif</span>
                    <?php endif; ?>
                </div>

                <div class="payment-client-metrics">
                    <div><span>Payé</span><strong><?= paymentMoney($client['paid_amount'] ?? 0) ?></strong></div>
                    <div><span>Reste</span><strong><?= paymentMoney($client['pending_amount'] ?? 0) ?></strong></div>
                    <div><span>Prochaine échéance</span><strong><?= paymentDate($client['next_due_date'] ?? '') ?></strong></div>
                </div>

                <div class="payment-client-actions">
                    <button type="button" class="btn" onclick="openPaymentDialog('<?= $modalId ?>')"><i class="fas fa-eye"></i> Voir</button>
                    <form method="post" action="adminPage.php?page=payment-schedules" data-loading-text="Envoi de la relance...">
                        <input type="hidden" name="action" value="send_reminder">
                        <input type="hidden" name="adhesion_id" value="<?= (int) $client['adhesion_id'] ?>">
                        <button type="submit" class="btn btn-outline"><i class="fas fa-envelope"></i> Relancer</button>
                    </form>
                    <a class="btn btn-outline" href="../../../app/api/export_payment_invoice_pdf.php?adhesion_id=<?= (int) $client['adhesion_id'] ?>" target="_blank">
                        <i class="fas fa-file-pdf"></i> Export facture
                    </a>
                </div>
            </article>

            <div class="payment-dialog" id="<?= $modalId ?>" aria-hidden="true">
                <div class="payment-dialog-panel">
                    <button type="button" class="payment-dialog-close" onclick="closePaymentDialog('<?= $modalId ?>')">&times;</button>
                    <h3>Mensualités - <?= paymentValue(($client['prenom'] ?? '') . ' ' . ($client['nom'] ?? '')) ?></h3>
                    <div class="payment-month-list">
                        <?php foreach ($client['schedules'] as $schedule): ?>
                            <?php
                            $isPaid = ($schedule['status'] ?? '') === 'paid';
                            $isLate = !$isPaid && strtotime((string) ($schedule['due_date'] ?? '')) < strtotime(date('Y-m-d'));
                            ?>
                            <div class="payment-month-row">
                                <div>
                                    <strong>Mois <?= (int) $schedule['installment_number'] ?> / 24</strong>
                                    <span><?= paymentDate($schedule['due_date'] ?? '') ?> · <?= paymentMoney($schedule['amount'] ?? 0) ?></span>
                                </div>
                                <div class="payment-month-action">
                                    <span class="payment-badge <?= $isPaid ? 'paid' : ($isLate ? 'late' : 'pending') ?>"><?= $isPaid ? 'Payé' : ($isLate ? 'Retard' : 'À venir') ?></span>
                                    <?php if (!$isPaid): ?>
                                        <form method="post" action="adminPage.php?page=payment-schedules" data-loading-text="Validation du paiement...">
                                            <input type="hidden" name="action" value="mark_paid">
                                            <input type="hidden" name="schedule_id" value="<?= (int) $schedule['id'] ?>">
                                            <button type="submit" class="btn">Marquer payé</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function openPaymentDialog(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
}
function closePaymentDialog(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
}
</script>
