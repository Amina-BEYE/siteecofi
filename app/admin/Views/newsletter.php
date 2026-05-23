<?php
$subscribers = $subscribers ?? [];
$newsletterStats = $newsletterStats ?? ['total' => 0, 'active_total' => 0, 'recent_total' => 0];
$interests = [
    '' => 'Tous les abonnés actifs',
    'programme' => 'Programme immobilier',
    'terrain' => 'Terrain / Parcelle',
    'chantier' => 'Suivi chantier',
    'investissement' => 'Investissement',
];
?>

<div class="newsletter-admin">
    <section class="auth-hero newsletter-hero">
        <div>
            <span class="auth-kicker"><i class="fas fa-bullhorn"></i> Newsletter ECOFI</span>
            <h2>Abonnés & campagnes</h2>
            <p>Retrouvez les personnes inscrites aux alertes et envoyez vos communications programmes en quelques clics.</p>
        </div>
        <div class="auth-hero-metrics">
            <div><strong><?= (int) ($newsletterStats['total'] ?? 0) ?></strong><span>Total</span></div>
            <div><strong><?= (int) ($newsletterStats['active_total'] ?? 0) ?></strong><span>Actifs</span></div>
            <div><strong><?= (int) ($newsletterStats['recent_total'] ?? 0) ?></strong><span>30 jours</span></div>
        </div>
    </section>

    <div class="newsletter-layout">
        <section class="card campaign-card">
            <div class="section-heading">
                <div>
                    <h2><i class="fas fa-paper-plane"></i> Envoyer une campagne</h2>
                    <p>Le message sera envoyé aux abonnés actifs selon le ciblage choisi.</p>
                </div>
            </div>

            <form method="post" action="adminPage.php?page=newsletter" class="admin-form" data-loading-text="Envoi de la campagne..." enctype="multipart/form-data">
                <input type="hidden" name="action" value="send_campaign">

                <div class="form-group">
                    <label for="campaign_subject">Objet</label>
                    <input type="text" id="campaign_subject" name="subject" class="form-control" placeholder="Nouveau programme disponible..." required>
                </div>

                <div class="form-group">
                    <label for="campaign_interest">Ciblage</label>
                    <select id="campaign_interest" name="interest" class="form-control">
                        <?php foreach ($interests as $value => $label): ?>
                            <option value="<?= htmlspecialchars($value) ?>"><?= htmlspecialchars($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="campaign_content">Message</label>
                    <textarea id="campaign_content" name="content" class="form-control" rows="8" placeholder="Bonjour, ECOFI vous informe..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="campaign_attachment">Pièce jointe</label>
                    <input
                        type="file"
                        id="campaign_attachment"
                        name="campaign_attachment"
                        class="form-control"
                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.txt"
                    >
                    <small>Formats acceptés : PDF, image, Word, Excel, TXT. Taille max : 10 Mo.</small>
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-envelope"></i>
                    Envoyer la campagne
                </button>
            </form>
        </section>

        <section class="card subscribers-card">
            <div class="section-heading">
                <div>
                    <h2><i class="fas fa-users"></i> Inscrits newsletter</h2>
                    <p>Recherche par nom, email, téléphone ou intérêt.</p>
                </div>
            </div>

            <div class="admin-list-toolbar">
                <label class="admin-search-box">
                    <i class="fas fa-search"></i>
                    <input
                        type="search"
                        class="admin-search-input"
                        data-admin-search
                        data-target="#newsletterTable tbody tr.subscriber-row"
                        placeholder="Rechercher un inscrit"
                        aria-label="Rechercher un inscrit newsletter"
                    >
                </label>
            </div>

            <div class="table-container">
                <table id="newsletterTable" class="modern-table">
                    <thead>
                        <tr>
                            <th>Contact</th>
                            <th>Intérêt</th>
                            <th>Statut</th>
                            <th>Inscription</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($subscribers)): ?>
                            <?php foreach ($subscribers as $subscriber): ?>
                                <tr class="subscriber-row" data-search="<?= htmlspecialchars(($subscriber['name'] ?? '') . ' ' . ($subscriber['email'] ?? '') . ' ' . ($subscriber['phone'] ?? '') . ' ' . ($subscriber['interest'] ?? '') . ' ' . ($subscriber['status'] ?? '')) ?>">
                                    <td>
                                        <strong><?= htmlspecialchars($subscriber['name'] ?? '-') ?></strong>
                                        <small><?= htmlspecialchars($subscriber['email'] ?? '-') ?></small>
                                        <small><?= htmlspecialchars($subscriber['phone'] ?? '-') ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($interests[$subscriber['interest'] ?? ''] ?? ($subscriber['interest'] ?? '-')) ?></td>
                                    <td>
                                        <span class="badge <?= ($subscriber['status'] ?? '') === 'active' ? 'badge-success' : 'badge-danger' ?>">
                                            <?= ($subscriber['status'] ?? '') === 'active' ? 'Actif' : 'Désinscrit' ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($subscriber['created_at'] ?? '-') ?></td>
                                    <td>
                                        <form method="post" action="adminPage.php?page=newsletter" class="inline-form">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="subscriber_id" value="<?= (int) ($subscriber['id'] ?? 0) ?>">
                                            <input type="hidden" name="status" value="<?= ($subscriber['status'] ?? '') === 'active' ? 'unsubscribed' : 'active' ?>">
                                            <button type="submit" class="btn btn-outline">
                                                <?= ($subscriber['status'] ?? '') === 'active' ? 'Désactiver' : 'Réactiver' ?>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">Aucun inscrit newsletter pour le moment.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
