<?php
$mailMessages = $mailMessages ?? [];
$sentMessages = $sentMessages ?? [];
$selectedMail = $selectedMail ?? null;
$mailStats    = $mailStats    ?? ['total' => 0, 'mailbox' => 0, 'sent' => 0];
$mailError    = $mailError    ?? null;
$activeTab    = $activeTab    ?? 'inbox';
?>

<style>
/* ── Onglets ─────────────────────────────────────────────────────────────── */
.mb-tabs {
    display: flex;
    border-bottom: 2px solid #e5e7eb;
    margin-bottom: 1rem;
}
.mb-tab {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .65rem 1.25rem;
    font-size: .85rem;
    font-weight: 600;
    color: #6b7280;
    text-decoration: none;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    transition: color .15s, border-color .15s;
}
.mb-tab:hover { color: #111; }
.mb-tab.is-active        { color: #2563eb; border-bottom-color: #2563eb; }
.mb-tab.is-sent.is-active{ color: #059669; border-bottom-color: #059669; }
.mb-tab-count {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 18px; height: 18px; padding: 0 5px;
    border-radius: 20px; font-size: .68rem; font-weight: 700;
    background: #eff6ff; color: #2563eb;
}
.mb-tab.is-sent .mb-tab-count { background: #ecfdf5; color: #059669; }

/* ── Bouton actualiser ───────────────────────────────────────────────────── */
.mb-refresh-btn {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .4rem .9rem; border-radius: 6px; font-size: .82rem;
    font-weight: 600; text-decoration: none; white-space: nowrap;
    border: 1px solid #e5e7eb; background: #fff; color: #6b7280;
    transition: color .14s, border-color .14s, background .14s;
    flex-shrink: 0;
}
.mb-refresh-btn:hover { color: #2563eb; border-color: #2563eb; background: #eff6ff; }
.mb-refresh-btn.is-spinning i { animation: mbSpin .7s linear infinite; }
@keyframes mbSpin { to { transform: rotate(360deg); } }

/* ── Badge type mail ─────────────────────────────────────────────────────── */
.mb-badge-sent { background: #ecfdf5 !important; color: #059669 !important; }

/* ── Corps du message texte brut ─────────────────────────────────────────── */
.mail-body-plain {
    font-family: 'Courier New', monospace;
    font-size: .875rem;
    line-height: 1.8;
    white-space: pre-wrap;
    word-break: break-word;
    padding: 1.25rem 1.5rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    color: #1f2937;
    overflow-x: auto;
}
.mail-body-plain .mb-quote {
    display: block; border-left: 3px solid #d1d5db;
    padding-left: .75rem; color: #9ca3af; font-style: italic; margin: 2px 0;
}
.mail-body-plain a { color: #2563eb; text-decoration: underline; word-break: break-all; }

/* ── Corps HTML (iframe) ─────────────────────────────────────────────────── */
.mail-body-iframe-wrap {
    border: 1px solid #e5e7eb; border-radius: 8px;
    overflow: hidden; background: #fff;
}
.mail-body-iframe { width: 100%; min-height: 180px; border: none; display: block; }

/* ── Méta "À :" pour les envoyés ─────────────────────────────────────────── */
.mail-detail-head .message-meta { flex-wrap: wrap; gap: .4rem .75rem; }
</style>

<div class="mailbox-admin">

    <!-- HERO ──────────────────────────────────────────────────────────────── -->
    <section class="auth-hero newsletter-hero">
        <div>
            <span class="auth-kicker"><i class="fas fa-envelope-open-text"></i> Boîte utilisateur</span>
            <h2>Messagerie</h2>
            <p>Consultez vos emails via votre configuration IMAP et répondez avec votre SMTP personnel.</p>
        </div>
        <div class="auth-hero-metrics">
            <div>
                <strong><?= (int) ($mailStats['mailbox'] ?? 0) ?></strong>
                <span>Reçus</span>
            </div>
            <div>
                <strong><?= (int) ($mailStats['sent'] ?? 0) ?></strong>
                <span>Envoyés</span>
            </div>
        </div>
    </section>

    <?php if (!empty($mailError)): ?>
        <div class="alert error">
            <i class="fas fa-exclamation-triangle"></i>
            <?= htmlspecialchars($mailError) ?>
        </div>
    <?php endif; ?>

    <div class="mailbox-layout">

        <!-- ═══════════════════════════════════════════════════════════════
             LISTE
        ════════════════════════════════════════════════════════════════ -->
        <section class="card mailbox-list-card">

            <!-- Onglets -->
            <div class="mb-tabs">
                <a href="adminPage.php?page=messaging&tab=inbox<?= $selectedMail ? '&uid='.(int)($selectedMail['uid']??0) : '' ?>"
                   class="mb-tab <?= $activeTab === 'inbox' ? 'is-active' : '' ?>">
                    <i class="fas fa-inbox"></i> Reçus
                    <span class="mb-tab-count"><?= count($mailMessages) ?></span>
                </a>
                <a href="adminPage.php?page=messaging&tab=sent<?= $selectedMail ? '&uid='.(int)($selectedMail['uid']??0) : '' ?>"
                   class="mb-tab is-sent <?= $activeTab === 'sent' ? 'is-active' : '' ?>">
                    <i class="fas fa-paper-plane"></i> Envoyés
                    <span class="mb-tab-count"><?= count($sentMessages) ?></span>
                </a>
            </div>

            <!-- Toolbar -->
            <div class="admin-list-toolbar">
                <label class="admin-search-box">
                    <i class="fas fa-search"></i>
                    <input
                        type="search"
                        class="admin-search-input"
                        id="mbSearchInput"
                        placeholder="Rechercher par expéditeur ou sujet"
                        aria-label="Rechercher un email"
                    >
                </label>
                <a href="adminPage.php?page=messaging&tab=<?= htmlspecialchars($activeTab) ?><?= $selectedMail ? '&uid='.(int)($selectedMail['uid']??0) : '' ?>"
                   class="mb-refresh-btn" id="mbRefreshBtn" title="Actualiser">
                    <i class="fas fa-sync-alt"></i> Actualiser
                </a>
            </div>

            <!-- Messages -->
            <div class="message-list" id="mailMessagesList">
                <?php
                $isSentTab   = ($activeTab === 'sent');
                $currentList = $isSentTab ? $sentMessages : $mailMessages;
                ?>

                <?php if (!empty($currentList)): ?>
                    <?php foreach ($currentList as $mail): ?>
                        <?php
                        $uid      = (int) ($mail['uid'] ?? 0);
                        $isActive = ((int) ($selectedMail['uid'] ?? -1)) === $uid;
                        $contact  = $isSentTab ? ($mail['to'] ?? '-') : ($mail['from'] ?? '-');
                        ?>
                        <a
                            class="message-card mailbox-item <?= $isActive ? 'is-active' : '' ?>"
                            href="adminPage.php?page=messaging&tab=<?= $activeTab ?>&uid=<?= $uid ?>"
                            data-search="<?= htmlspecialchars($contact . ' ' . ($mail['subject'] ?? '') . ' ' . ($mail['excerpt'] ?? '')) ?>"
                        >
                            <div class="message-card-head">
                                <span class="badge <?= $isSentTab ? 'mb-badge-sent' : (!empty($mail['seen']) ? 'badge-info' : 'badge-warning') ?>">
                                    <?= $isSentTab ? 'Envoyé' : (!empty($mail['seen']) ? 'Lu' : 'Nouveau') ?>
                                </span>
                                <time><?= htmlspecialchars(mbFormatDate($mail['date'] ?? '')) ?></time>
                            </div>
                            <h3><?= htmlspecialchars($mail['subject'] ?? 'Sans objet') ?></h3>
                            <div class="message-meta">
                                <span>
                                    <i class="fas <?= $isSentTab ? 'fa-arrow-right' : 'fa-user' ?>"></i>
                                    <?= htmlspecialchars($contact) ?>
                                </span>
                            </div>
                            <p><?= htmlspecialchars($mail['excerpt'] ?? '') ?></p>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas <?= $isSentTab ? 'fa-paper-plane' : 'fa-inbox' ?>"></i>
                        <h3>Aucun email <?= $isSentTab ? 'envoyé' : 'reçu' ?></h3>
                        <p><?= $isSentTab ? 'Votre dossier Envoyés est vide.' : 'Vérifiez la configuration IMAP de votre utilisateur.' ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════════
             DÉTAIL
        ════════════════════════════════════════════════════════════════ -->
        <section class="card mailbox-detail-card">
            <?php if (!empty($selectedMail)): ?>
                <?php $isSentMail = ($activeTab === 'sent'); ?>

                <!-- En-tête -->
                <div class="mail-detail-head">
                    <span class="badge <?= $isSentMail ? 'mb-badge-sent' : 'badge-info' ?>">
                        <i class="fas <?= $isSentMail ? 'fa-paper-plane' : 'fa-inbox' ?>"></i>
                        <?= $isSentMail ? 'Email envoyé' : 'Email reçu' ?>
                    </span>
                    <h2><?= htmlspecialchars($selectedMail['subject'] ?? 'Sans objet') ?></h2>
                    <div class="message-meta">
                        <?php if ($isSentMail): ?>
                            <span><i class="fas fa-arrow-right"></i> <?= htmlspecialchars($selectedMail['to'] ?? '-') ?></span>
                        <?php else: ?>
                            <span><i class="fas fa-user"></i> <?= htmlspecialchars($selectedMail['from'] ?? '-') ?></span>
                        <?php endif; ?>
                        <span><i class="fas fa-clock"></i> <?= htmlspecialchars($selectedMail['date'] ?? '-') ?></span>
                    </div>
                </div>

                <!-- Corps du message -->
                <div class="mail-detail-body">
                    <?php
                    $rawBody = $selectedMail['body'] ?? '';
                    $isHtml  = (bool) preg_match('/<(html|body|div|p|br|table|span|a|img)\b/i', $rawBody);
                    ?>
                    <?php if ($isHtml): ?>
                        <div class="mail-body-iframe-wrap">
                            <iframe id="mbBodyFrame" class="mail-body-iframe"
                                    sandbox="allow-same-origin" scrolling="auto"
                                    title="Contenu du message"></iframe>
                        </div>
                        <script>
                        (function(){
                            var f = document.getElementById('mbBodyFrame');
                            if (!f) return;
                            var d = f.contentDocument || f.contentWindow.document;
                            d.open();
                            d.write(<?= json_encode($rawBody, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>);
                            d.close();
                            function resize(){ f.style.height = (d.body.scrollHeight + 32) + 'px'; }
                            f.onload = resize;
                            setTimeout(resize, 200);
                        })();
                        </script>
                    <?php else: ?>
                        <div class="mail-body-plain"><?= mbFormatPlainText($rawBody) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Répondre (uniquement sur les reçus) -->
                <?php if (!$isSentMail): ?>
                    <button type="button" class="btn reply-toggle-btn" onclick="toggleReplyForm()">
                        <i class="fas fa-reply"></i> Répondre
                    </button>

                    <form
                        method="post"
                        action="adminPage.php?page=messaging&tab=inbox&uid=<?= (int) ($selectedMail['uid'] ?? 0) ?>"
                        class="admin-form reply-form"
                        id="replyForm"
                        style="display:none;"
                    >
                        <input type="hidden" name="uid" value="<?= (int) ($selectedMail['uid'] ?? 0) ?>">
                        <div class="form-group">
                            <label for="reply_body">Votre réponse</label>
                            <textarea id="reply_body" name="body" class="form-control" rows="7"
                                      placeholder="Votre réponse..." required></textarea>
                        </div>
                        <div style="display:flex;gap:.6rem;flex-wrap:wrap;">
                            <button type="submit" class="btn">
                                <i class="fas fa-paper-plane"></i> Envoyer
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="toggleReplyForm()">
                                <i class="fas fa-times"></i> Annuler
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

            <?php else: ?>
                <div class="empty-state mailbox-detail-empty">
                    <i class="fas fa-envelope"></i>
                    <h3>Sélectionnez un email</h3>
                    <p>Le détail complet du message et le formulaire de réponse s'afficheront ici.</p>
                </div>
            <?php endif; ?>
        </section>

    </div>
</div>

<script>
/* Afficher / masquer le formulaire de réponse */
function toggleReplyForm() {
    var form = document.getElementById('replyForm');
    if (!form) return;
    var open = form.style.display === 'none' || form.style.display === '';
    form.style.display = open ? 'block' : 'none';
    if (open) {
        var ta = form.querySelector('textarea');
        if (ta) { ta.focus(); form.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); }
    }
}

/* Actualiser : icône tourne au clic */
(function(){
    var btn = document.getElementById('mbRefreshBtn');
    if (btn) btn.addEventListener('click', function(){ btn.classList.add('is-spinning'); });
})();

/* Recherche en temps réel */
(function(){
    var inp  = document.getElementById('mbSearchInput');
    var list = document.getElementById('mailMessagesList');
    if (!inp || !list) return;
    inp.addEventListener('input', function(){
        var q = this.value.trim().toLowerCase();
        list.querySelectorAll('.mb-item').forEach(function(el){
            el.style.display = (!q || (el.dataset.search || '').toLowerCase().includes(q)) ? '' : 'none';
        });
    });
})();
</script>

<?php
/* Formate la date pour la liste */
function mbFormatDate(string $raw): string
{
    if ($raw === '') return '';
    try {
        $dt  = new DateTime($raw);
        $now = new DateTime();
        if ($dt->format('Y-m-d') === $now->format('Y-m-d')) return $dt->format('H:i');
        $m = ['jan','fév','mar','avr','mai','jun','jul','aoû','sep','oct','nov','déc'];
        if ($dt->format('Y') === $now->format('Y')) return $dt->format('j') . ' ' . $m[(int)$dt->format('n') - 1];
        return $dt->format('d/m/Y');
    } catch (Throwable $e) { return $raw; }
}

/* Formate un corps texte brut */
function mbFormatPlainText(string $text): string
{
    $output = '';
    foreach (explode("\n", $text) as $line) {
        $e = htmlspecialchars($line, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $output .= preg_match('/^(&gt;|>)/', $e)
            ? '<span class="mb-quote">' . $e . "</span>\n"
            : $e . "\n";
    }
    return preg_replace('~(https?://[^\s<>"\']+)~i',
        '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>', $output);
}
?>