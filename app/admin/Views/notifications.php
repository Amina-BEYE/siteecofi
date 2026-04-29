<?php
$message = $message ?? null;
$messageType = $messageType ?? 'success';
$inscriptions = $inscriptions ?? [];
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

<!-- Statistiques -->
<section class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-bell"></i></div>
        <div class="stat-info">
            <h3>Inscriptions totales</h3>
            <p><?= count($inscriptions) ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-hourglass-start"></i></div>
        <div class="stat-info">
            <h3>En attente</h3>
            <p><?= count(array_filter($inscriptions, fn($i) => $i['statut_traitement'] === 'nouveau')) ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
            <h3>Traitées</h3>
            <p><?= count(array_filter($inscriptions, fn($i) => $i['statut_traitement'] === 'termine')) ?></p>
        </div>
    </div>
</section>

<!-- Liste des Notifications/Inscriptions -->
<section class="row">
    <div class="card">
        <h2 style="margin-bottom: 20px;">
            <i class="fas fa-inbox"></i> Inscriptions & Demandes
        </h2>

        <?php if (empty($inscriptions)): ?>
            <div style="text-align: center; padding: 40px; color: #999;">
                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 20px; display: block;"></i>
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
                                    <span style="padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;
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
                                <td style="padding: 12px; text-align: center; font-size: 14px; color: #666;">
                                    <?= date('d/m/Y', strtotime($inscription['date_inscription'])) ?>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <div style="display: flex; gap: 10px; justify-content: center;">
                                        <button 
                                            type="button" 
                                            class="btn-detail"
                                            onclick="openInscriptionModal(<?= htmlspecialchars(json_encode($inscription), ENT_QUOTES) ?>)"
                                            style="background: #3b82f6; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                            <i class="fas fa-eye"></i> Détails
                                        </button>
                                        <form method="POST" action="adminPage.php?page=notifications" style="display: inline;">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="id" value="<?= $inscription['id'] ?>">
                                            <input type="hidden" name="status" value="contacte">
                                            <button type="submit" style="background: #10b981; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                                <i class="fas fa-check"></i> Contacté
                                            </button>
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
</section>

<!-- Modal pour les détails -->
<div id="inscriptionModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;">Détails de l'inscription</h3>
            <button onclick="closeInscriptionModal()" style="background: none; border: none; font-size: 24px; cursor: pointer;">×</button>
        </div>
        <div id="modalContent"></div>
    </div>
</div>

<script>
function openInscriptionModal(data) {
    const modal = document.getElementById('inscriptionModal');
    const content = document.getElementById('modalContent');
    
    let html = `
        <div style="display: grid; gap: 15px;">
            <div>
                <label style="font-weight: 600; color: #666;">Nom:</label>
                <p>${htmlEscape(data.nom)}</p>
            </div>
            <div>
                <label style="font-weight: 600; color: #666;">Email:</label>
                <p><a href="mailto:${htmlEscape(data.email)}" style="color: #d97706;">${htmlEscape(data.email)}</a></p>
            </div>
            <div>
                <label style="font-weight: 600; color: #666;">Téléphone:</label>
                <p><a href="tel:${htmlEscape(data.telephone)}" style="color: #d97706;">${htmlEscape(data.telephone)}</a></p>
            </div>
            <div>
                <label style="font-weight: 600; color: #666;">Statut:</label>
                <p>${htmlEscape(data.statut)}</p>
            </div>
            <div>
                <label style="font-weight: 600; color: #666;">Type:</label>
                <p>${data.type === 'programme' ? '📋 Inscription au programme' : '📞 Demande de contact'}</p>
            </div>
            <div>
                <label style="font-weight: 600; color: #666;">Programme:</label>
                <p>${htmlEscape(data.programme)}</p>
            </div>
    `;
    
    if (data.type_bien) {
        html += `
            <div>
                <label style="font-weight: 600; color: #666;">Type de bien:</label>
                <p>${htmlEscape(data.type_bien)}</p>
            </div>
        `;
    }
    
    if (data.budget) {
        html += `
            <div>
                <label style="font-weight: 600; color: #666;">Budget:</label>
                <p>${htmlEscape(data.budget)}</p>
            </div>
        `;
    }
    
    if (data.message) {
        html += `
            <div>
                <label style="font-weight: 600; color: #666;">Message:</label>
                <p style="background: #f9fafb; padding: 10px; border-radius: 4px;">${htmlEscape(data.message)}</p>
            </div>
        `;
    }
    
    html += `
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
    `;
    
    if (data.contact_email) html += `<div style="text-align: center;"><i class="fas fa-envelope" style="color: #d97706;"></i> Email</div>`;
    if (data.contact_tel) html += `<div style="text-align: center;"><i class="fas fa-phone" style="color: #d97706;"></i> Téléphone</div>`;
    if (data.contact_whatsapp) html += `<div style="text-align: center;"><i class="fab fa-whatsapp" style="color: #d97706;"></i> WhatsApp</div>`;
    
    html += `
            </div>
        </div>
    `;
    
    content.innerHTML = html;
    modal.style.display = 'flex';
}

function closeInscriptionModal() {
    document.getElementById('inscriptionModal').style.display = 'none';
}

function htmlEscape(text) {
    if (!text) return '';
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Fermer le modal en cliquant en dehors
document.getElementById('inscriptionModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeInscriptionModal();
    }
});
</script>
