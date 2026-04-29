<?php
$message = $message ?? null;
$messageType = $messageType ?? 'success';
$inscriptions = $inscriptions ?? [];
$quotes = $quotes ?? [];
$users = $users ?? [];
?>

<?php if (!empty($message)): ?>
    <div class="dialog-overlay" id="dialogOverlay">
        <div class="dialog-box <?= $messageType === 'error' ? 'error' : 'success' ?>">
            <div class="dialog-icon">
                <i class="fas <?= $messageType === 'error' ? 'fa-exclamation-triangle' : 'fa-check-circle' ?>"></i>
            </div>
            <h3>
                <?= $messageType === 'error' ? 'Erreur' : 'Succès' ?>
            </h3>
            <p><?= htmlspecialchars($message) ?></p>
            <div class="dialog-actions">
                <button class="btn" onclick="closeDialog()">OK</button>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Statistiques Globales -->
<section class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-user-plus"></i></div>
        <div class="stat-info">
            <h3>Nouveaux clients inscrits</h3>
            <p><?= count($inscriptions) ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-file-invoice-dollar"></i></div>
        <div class="stat-info">
            <h3>Demandes de devis</h3>
            <p><?= count($quotes) ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <h3>Nouveaux comptes</h3>
            <p><?= count($users) ?></p>
        </div>
    </div>
</section>

<!-- Liste des Inscriptions -->
<section class="row">
    <div class="card">
        <h2 style="margin-bottom: 20px;">
            <i class="fas fa-user-plus"></i> Nouveaux Clients Inscrits
        </h2>

        <?php if (empty($inscriptions)): ?>
            <div style="text-align: center; padding: 40px; color: #999;">
                <i class="fas fa-user-plus" style="font-size: 48px; margin-bottom: 20px; display: block;"></i>
                <p>Aucune inscription pour le moment.</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            <th style="padding: 12px; text-align: left;">Nom</th>
                            <th style="padding: 12px; text-align: left;">Email</th>
                            <th style="padding: 12px; text-align: left;">Téléphone</th>
                            <th style="padding: 12px; text-align: center;">Type</th>
                            <th style="padding: 12px; text-align: center;">Statut</th>
                            <th style="padding: 12px; text-align: center;">Date</th>
                            <th style="padding: 12px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inscriptions as $inscription): ?>
                            <tr style="border-bottom: 1px solid #ddd;">
                                <td style="padding: 12px;">
                                    <strong><?= htmlspecialchars($inscription['nom']) ?></strong>
                                </td>
                                <td style="padding: 12px;">
                                    <a href="mailto:<?= htmlspecialchars($inscription['email']) ?>" style="color: #d97706;">
                                        <?= htmlspecialchars($inscription['email']) ?>
                                    </a>
                                </td>
                                <td style="padding: 12px;">
                                    <a href="tel:<?= str_replace(' ', '', $inscription['telephone']) ?>" style="color: #d97706;">
                                        <?= htmlspecialchars($inscription['telephone']) ?>
                                    </a>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <span style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
                                        background-color: <?php
                                            echo $inscription['type'] === 'programme' ? '#fef3c7' : '#dbeafe';
                                        ?>;
                                        color: <?php
                                            echo $inscription['type'] === 'programme' ? '#b45309' : '#1e40af';
                                        ?>;">
                                        <?= $inscription['type'] === 'programme' ? '📋 Inscription' : '📞 Contact' ?>
                                    </span>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <span style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
                                        background-color: <?php
                                            echo $inscription['statut_traitement'] === 'nouveau' ? '#fecaca' :
                                                 ($inscription['statut_traitement'] === 'en_cours' ? '#fbbf24' :
                                                  ($inscription['statut_traitement'] === 'contacte' ? '#d1fae5' : '#a7f3d0'));
                                        ?>;
                                        color: <?php
                                            echo $inscription['statut_traitement'] === 'nouveau' ? '#991b1b' :
                                                 ($inscription['statut_traitement'] === 'en_cours' ? '#92400e' :
                                                  ($inscription['statut_traitement'] === 'contacte' ? '#065f46' : '#065f46'));
                                        ?>;">
                                        <?= ucfirst(str_replace('_', ' ', $inscription['statut_traitement'])) ?>
                                    </span>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <?= date('d/m/Y', strtotime($inscription['date_inscription'])) ?>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <button type="button"
                                            onclick="openInscriptionModal(<?= htmlspecialchars(json_encode($inscription), ENT_QUOTES) ?>)"
                                            style="background: #3b82f6; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; margin-right: 5px;">
                                        <i class="fas fa-eye"></i> Voir
                                    </button>
                                    <form method="POST" action="adminPage.php?page=inscriptions" style="display: inline;">
                                        <input type="hidden" name="action" value="delete_inscription">
                                        <input type="hidden" name="id" value="<?= $inscription['id'] ?>">
                                        <button type="submit"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette inscription ?')"
                                                style="background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">
                                            <i class="fas fa-trash"></i> Suppr
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Section 2: Nouvelles demandes de devis -->
<section class="row">
    <div class="card">
        <h2 style="margin-bottom: 20px;">
            <i class="fas fa-file-invoice-dollar"></i> Nouvelles Demandes de Devis
        </h2>

        <?php if (empty($quotes)): ?>
            <div style="text-align: center; padding: 40px; color: #999;">
                <i class="fas fa-file-invoice-dollar" style="font-size: 48px; margin-bottom: 20px; display: block;"></i>
                <p>Aucune demande de devis pour le moment.</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            <th style="padding: 12px; text-align: left;">N° Devis</th>
                            <th style="padding: 12px; text-align: left;">Client</th>
                            <th style="padding: 12px; text-align: left;">Email</th>
                            <th style="padding: 12px; text-align: center;">Montant TTC</th>
                            <th style="padding: 12px; text-align: center;">Statut</th>
                            <th style="padding: 12px; text-align: center;">Date de demande</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quotes as $quote): ?>
                            <tr style="border-bottom: 1px solid #ddd;">
                                <td style="padding: 12px;">
                                    <strong style="color: #d97706;"><?= htmlspecialchars($quote['numero_devis']) ?></strong>
                                </td>
                                <td style="padding: 12px;">
                                    <strong><?= htmlspecialchars($quote['client_nom']) ?></strong>
                                </td>
                                <td style="padding: 12px;">
                                    <a href="mailto:<?= htmlspecialchars($quote['client_email']) ?>" style="color: #d97706;">
                                        <?= htmlspecialchars($quote['client_email']) ?>
                                    </a>
                                </td>
                                <td style="padding: 12px; text-align: center; font-weight: bold;">
                                    <?= number_format($quote['total_ttc'], 0, ',', ' ') ?> €
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <span style="padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;
                                        background-color: <?php
                                            echo $quote['statut'] === 'en_attente' ? '#fbbf24' :
                                                 ($quote['statut'] === 'accepte' ? '#d1fae5' :
                                                  ($quote['statut'] === 'refuse' ? '#fecaca' : '#e5e7eb'));
                                        ?>;
                                        color: <?php
                                            echo $quote['statut'] === 'en_attente' ? '#92400e' :
                                                 ($quote['statut'] === 'accepte' ? '#065f46' :
                                                  ($quote['statut'] === 'refuse' ? '#991b1b' : '#374151'));
                                        ?>;">
                                        <?= ucfirst(str_replace('_', ' ', $quote['statut'])) ?>
                                    </span>
                                </td>
                                <td style="padding: 12px; text-align: center; font-size: 14px; color: #666;">
                                    <?= date('d/m/Y H:i', strtotime($quote['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Section 3: Nouveaux comptes utilisateurs -->
<section class="row">
    <div class="card">
        <h2 style="margin-bottom: 20px;">
            <i class="fas fa-users"></i> Nouveaux Comptes Utilisateurs
        </h2>

        <?php if (empty($users)): ?>
            <div style="text-align: center; padding: 40px; color: #999;">
                <i class="fas fa-users" style="font-size: 48px; margin-bottom: 20px; display: block;"></i>
                <p>Aucun nouveau compte utilisateur pour le moment.</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            <th style="padding: 12px; text-align: left;">Nom complet</th>
                            <th style="padding: 12px; text-align: left;">Email</th>
                            <th style="padding: 12px; text-align: center;">Rôle</th>
                            <th style="padding: 12px; text-align: center;">Statut</th>
                            <th style="padding: 12px; text-align: center;">Date d'inscription</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr style="border-bottom: 1px solid #ddd;">
                                <td style="padding: 12px;">
                                    <strong><?= htmlspecialchars($user['fullname']) ?></strong>
                                </td>
                                <td style="padding: 12px;">
                                    <a href="mailto:<?= htmlspecialchars($user['email']) ?>" style="color: #d97706;">
                                        <?= htmlspecialchars($user['email']) ?>
                                    </a>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <span style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
                                        background-color: <?php
                                            echo $user['role'] === 'admin' ? '#fef3c7' :
                                                 ($user['role'] === 'manager' ? '#dbeafe' : '#f3e8ff');
                                        ?>;
                                        color: <?php
                                            echo $user['role'] === 'admin' ? '#b45309' :
                                                 ($user['role'] === 'manager' ? '#1e40af' : '#6b21a8');
                                        ?>;">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <span style="padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;
                                        background-color: <?php
                                            echo $user['status'] === 'active' ? '#d1fae5' :
                                                 ($user['status'] === 'inactive' ? '#fecaca' : '#fbbf24');
                                        ?>;
                                        color: <?php
                                            echo $user['status'] === 'active' ? '#065f46' :
                                                 ($user['status'] === 'inactive' ? '#991b1b' : '#92400e');
                                        ?>;">
                                        <?= ucfirst($user['status']) ?>
                                    </span>
                                </td>
                                <td style="padding: 12px; text-align: center; font-size: 14px; color: #666;">
                                    <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal pour les détails d'inscription -->
<div id="inscriptionModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 20px; border-radius: 8px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;">Détails de l'inscription</h3>
            <button onclick="closeInscriptionModal()" style="background: none; border: none; font-size: 24px; cursor: pointer;">×</button>
        </div>
        <div id="inscriptionDetails"></div>
        <div style="margin-top: 20px; text-align: right;">
            <button onclick="closeInscriptionModal()" style="background: #6b7280; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">Fermer</button>
        </div>
    </div>
</div>

<script>
function openInscriptionModal(data) {
    const modal = document.getElementById('inscriptionModal');
    const details = document.getElementById('inscriptionDetails');

    let content = `
        <div style="margin-bottom: 15px;">
            <strong>Nom:</strong> ${data.nom}
        </div>
        <div style="margin-bottom: 15px;">
            <strong>Email:</strong> <a href="mailto:${data.email}" style="color: #3b82f6;">${data.email}</a>
        </div>
        <div style="margin-bottom: 15px;">
            <strong>Téléphone:</strong> <a href="tel:${data.telephone.replace(/\s/g, '')}" style="color: #3b82f6;">${data.telephone}</a>
        </div>
        <div style="margin-bottom: 15px;">
            <strong>Type:</strong> ${data.type === 'programme' ? '📋 Inscription au programme' : '📞 Demande de contact'}
        </div>
        <div style="margin-bottom: 15px;">
            <strong>Statut:</strong> ${data.statut_traitement.replace('_', ' ').toUpperCase()}
        </div>
        <div style="margin-bottom: 15px;">
            <strong>Date:</strong> ${new Date(data.date_inscription).toLocaleDateString('fr-FR')}
        </div>
    `;

    if (data.message) {
        content += `
            <div style="margin-bottom: 15px;">
                <strong>Message:</strong><br>
                <div style="background: #f3f4f6; padding: 10px; border-radius: 4px; margin-top: 5px;">
                    ${data.message.replace(/\n/g, '<br>')}
                </div>
            </div>
        `;
    }

    if (data.adresse) {
        content += `
            <div style="margin-bottom: 15px;">
                <strong>Adresse:</strong> ${data.adresse}
            </div>
        `;
    }

    if (data.societe) {
        content += `
            <div style="margin-bottom: 15px;">
                <strong>Société:</strong> ${data.societe}
            </div>
        `;
    }

    if (data.contact_whatsapp !== null) {
        content += `
            <div style="margin-bottom: 15px;">
                <strong>Contact WhatsApp:</strong> ${data.contact_whatsapp ? 'Oui' : 'Non'}
            </div>
        `;
    }

    if (data.newsletter !== null) {
        content += `
            <div style="margin-bottom: 15px;">
                <strong>Newsletter:</strong> ${data.newsletter ? 'Oui' : 'Non'}
            </div>
        `;
    }

    // Formulaire de mise à jour du statut
    content += `
        <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #e5e7eb;">
            <strong>Mettre à jour le statut:</strong>
            <form method="POST" action="adminPage.php?page=inscriptions" style="margin-top: 10px;">
                <input type="hidden" name="action" value="update_inscription_status">
                <input type="hidden" name="id" value="${data.id}">
                <select name="status" style="padding: 8px; border: 1px solid #d1d5db; border-radius: 4px; margin-right: 10px;">
                    <option value="nouveau" ${data.statut_traitement === 'nouveau' ? 'selected' : ''}>Nouveau</option>
                    <option value="en_cours" ${data.statut_traitement === 'en_cours' ? 'selected' : ''}>En cours</option>
                    <option value="contacte" ${data.statut_traitement === 'contacte' ? 'selected' : ''}>Contacté</option>
                    <option value="termine" ${data.statut_traitement === 'termine' ? 'selected' : ''}>Terminé</option>
                </select>
                <button type="submit" style="background: #10b981; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">
                    <i class="fas fa-save"></i> Mettre à jour
                </button>
            </form>
        </div>
    `;

    details.innerHTML = content;
    modal.style.display = 'flex';
}

function closeInscriptionModal() {
    document.getElementById('inscriptionModal').style.display = 'none';
}

// Fermer la modal en cliquant en dehors
document.getElementById('inscriptionModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeInscriptionModal();
    }
});
</script>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.stat-info h3 {
    margin: 0;
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
}

.stat-info p {
    margin: 5px 0 0 0;
    font-size: 28px;
    font-weight: bold;
    color: #1f2937;
}

.row {
    margin-bottom: 30px;
}

.card {
    background: white;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.dialog-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
}

.dialog-box {
    background: white;
    border-radius: 8px;
    padding: 30px;
    max-width: 400px;
    width: 90%;
    text-align: center;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.dialog-box.error {
    border-left: 4px solid #ef4444;
}

.dialog-box.success {
    border-left: 4px solid #10b981;
}

.dialog-icon {
    font-size: 48px;
    margin-bottom: 15px;
}

.dialog-box.error .dialog-icon {
    color: #ef4444;
}

.dialog-box.success .dialog-icon {
    color: #10b981;
}

.dialog-actions {
    margin-top: 20px;
}

.btn {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

.btn:hover {
    background: #2563eb;
}
</style>