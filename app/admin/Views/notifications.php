<?php
$message = $message ?? null;
$messageType = $messageType ?? 'success';
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

<!-- Statistiques des Notifications -->
<section class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-bell"></i></div>
        <div class="stat-info">
            <h3>Notifications système</h3>
            <p>0</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="stat-info">
            <h3>Alertes</h3>
            <p>0</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-info-circle"></i></div>
        <div class="stat-info">
            <h3>Informations</h3>
            <p>0</p>
        </div>
    </div>
</section>

<!-- Liste des Notifications Système -->
<section class="row">
    <div class="card">
        <h2 style="margin-bottom: 20px;">
            <i class="fas fa-bell"></i> Notifications Système
        </h2>

        <div style="text-align: center; padding: 40px; color: #999;">
            <i class="fas fa-bell" style="font-size: 48px; margin-bottom: 20px; display: block;"></i>
            <p>Aucune notification système pour le moment.</p>
            <p style="font-size: 14px; margin-top: 10px; color: #666;">
                Les inscriptions et demandes de contact sont maintenant gérées dans la section <strong>"Inscriptions"</strong>.
            </p>
        </div>
    </div>
</section>

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

<script>
function closeDialog() {
    document.getElementById('dialogOverlay').style.display = 'none';
}
</script>
