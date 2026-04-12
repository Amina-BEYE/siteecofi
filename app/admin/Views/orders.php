<?php
function formatPrice($value): string
{
    return number_format((float) $value, 0, ',', ' ') . ' FCFA';
}
?>

<div class="card">
    <h2>Commandes</h2>

    <?php if (empty($orders)): ?>
        <p>Aucune commande trouvée.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>N° commande</th>
                        <th>Client</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Total TTC</th>
                        <th>Paiement</th>
                        <th>Statut paiement</th>
                        <th>Statut commande</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['numero_commande']) ?></td>
                            <td><?= htmlspecialchars($order['client_nom']) ?></td>
                            <td><?= htmlspecialchars($order['client_email']) ?></td>
                            <td><?= htmlspecialchars($order['client_telephone']) ?></td>
                            <td><?= htmlspecialchars(formatPrice($order['total_ttc'])) ?></td>
                            <td><?= htmlspecialchars($order['mode_paiement']) ?></td>
                            <td><?= htmlspecialchars($order['statut_paiement']) ?></td>
                            <td><?= htmlspecialchars($order['statut_commande']) ?></td>
                            <td><?= htmlspecialchars($order['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="card" style="margin-top: 24px;">
    <h2>Demandes de devis</h2>

    <?php if (empty($quotes)): ?>
        <p>Aucune demande de devis trouvée.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>N° devis</th>
                        <th>Client</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Total HT</th>
                        <th>Total TTC</th>
                        <th>Statut</th>
                        <th>Notes</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quotes as $quote): ?>
                        <tr>
                            <td><?= htmlspecialchars($quote['numero_devis']) ?></td>
                            <td><?= htmlspecialchars($quote['client_nom']) ?></td>
                            <td><?= htmlspecialchars($quote['client_email']) ?></td>
                            <td><?= htmlspecialchars($quote['client_telephone']) ?></td>
                            <td><?= htmlspecialchars(formatPrice($quote['total_ht'])) ?></td>
                            <td><?= htmlspecialchars(formatPrice($quote['total_ttc'])) ?></td>
                            <td><?= htmlspecialchars($quote['statut']) ?></td>
                            <td><?= htmlspecialchars($quote['notes'] ?? '') ?></td>
                            <td><?= htmlspecialchars($quote['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>