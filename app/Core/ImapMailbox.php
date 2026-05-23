<?php
$mailMessages = $mailMessages ?? [];
$sentMessages = $sentMessages ?? [];
$selectedMail = $selectedMail ?? null;
$mailStats    = $mailStats    ?? ['total' => 0, 'mailbox' => 0, 'sent' => 0];
$mailError    = $mailError    ?? null;
$activeTab    = $activeTab    ?? 'inbox'; // 'inbox' | 'sent'
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
/* ══════════════════════════════════════════════════════════
   VARIABLES
══════════════════════════════════════════════════════════ */
.mb-wrap {
    --mb-bg:          #f4f5f7;
    --mb-surface:     #ffffff;
    --mb-border:      #e2e4e9;
    --mb-text:        #1c1e26;
    --mb-muted:       #7a7f96;
    --mb-accent:      #2563eb;
    --mb-accent-soft: #eff4ff;
    --mb-sent:        #059669;
    --mb-sent-soft:   #ecfdf5;
    --mb-danger:      #dc2626;
    --mb-radius:      10px;
    --mb-shadow:      0 1px 3px rgba(0,0,0,.06), 0 1px 8px rgba(0,0,0,.04);

    font-family: 'DM Sans', sans-serif;
    color: var(--mb-text);
    background: var(--mb-bg);
}

/* ══════════════════════════════════════════════════════════
   HERO
══════════════════════════════════════════════════════════ */
.mb-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    padding: 1.75rem 2rem;
    background: var(--mb-surface);
    border-bottom: 1px solid var(--mb-border);
    flex-wrap: wrap;
}
.mb-hero-left .mb-kicker {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .09em;
    text-transform: uppercase;
    color: var(--mb-accent);
    margin-bottom: .45rem;
}
.mb-hero-left h1 {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0 0 .2rem;
    letter-spacing: -.02em;
}
.mb-hero-left p { font-size: .85rem; color: var(--mb-muted); margin: 0; }

.mb-hero-stats { display: flex; gap: 1rem; flex-shrink: 0; }
.mb-stat {
    text-align: center;
    padding: .65rem 1.1rem;
    background: var(--mb-bg);
    border-radius: var(--mb-radius);
    border: 1px solid var(--mb-border);
    min-width: 72px;
}
.mb-stat strong { display: block; font-size: 1.4rem; font-weight: 700; line-height: 1; margin-bottom: .15rem; }
.mb-stat span   { font-size: .7rem; color: var(--mb-muted); text-transform: uppercase; letter-spacing: .07em; font-weight: 600; }
.mb-stat.is-inbox strong { color: var(--mb-accent); }
.mb-stat.is-sent  strong { color: var(--mb-sent); }

/* ══════════════════════════════════════════════════════════
   ALERTE
══════════════════════════════════════════════════════════ */
.mb-alert {
    display: flex; align-items: center; gap: .75rem;
    padding: .85rem 1.5rem; margin: 1rem 1.5rem 0;
    background: #fef2f2; border: 1px solid #fecaca;
    border-radius: var(--mb-radius);
    color: var(--mb-danger); font-size: .875rem; font-weight: 500;
}

/* ══════════════════════════════════════════════════════════
   LAYOUT
══════════════════════════════════════════════════════════ */
.mb-layout {
    display: grid;
    grid-template-columns: 340px 1fr;
    height: calc(100vh - 118px);
    overflow: hidden;
}

/* ══════════════════════════════════════════════════════════
   PANNEAU LISTE
══════════════════════════════════════════════════════════ */
.mb-panel-list {
    display: flex; flex-direction: column;
    background: var(--mb-surface);
    border-right: 1px solid var(--mb-border);
    overflow: hidden;
}

/* Onglets */
.mb-tabs {
    display: grid; grid-template-columns: 1fr 1fr;
    border-bottom: 1px solid var(--mb-border);
    flex-shrink: 0;
}
.mb-tab {
    display: flex; align-items: center; justify-content: center; gap: .45rem;
    padding: .85rem .5rem;
    font-size: .82rem; font-weight: 600;
    color: var(--mb-muted);
    text-decoration: none;
    border-bottom: 2px solid transparent;
    transition: color .14s, border-color .14s, background .14s;
}
.mb-tab:hover { color: var(--mb-text); background: var(--mb-bg); }
.mb-tab.is-active          { color: var(--mb-accent); border-bottom-color: var(--mb-accent); }
.mb-tab.is-sent.is-active  { color: var(--mb-sent);   border-bottom-color: var(--mb-sent); }

.mb-count {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 18px; height: 18px; padding: 0 4px;
    border-radius: 20px; font-size: .68rem; font-weight: 700;
    background: var(--mb-accent-soft); color: var(--mb-accent);
}
.mb-tab.is-sent .mb-count { background: var(--mb-sent-soft); color: var(--mb-sent); }

/* Toolbar */
.mb-toolbar {
    display: flex; align-items: center; gap: .5rem;
    padding: .65rem .875rem;
    border-bottom: 1px solid var(--mb-border);
    background: var(--mb-bg);
    flex-shrink: 0;
}
.mb-search-wrap { flex: 1; position: relative; }
.mb-search-wrap i {
    position: absolute; left: .65rem; top: 50%; transform: translateY(-50%);
    color: var(--mb-muted); font-size: .78rem; pointer-events: none;
}
.mb-search {
    width: 100%; padding: .48rem .75rem .48rem 2rem;
    border: 1px solid var(--mb-border); border-radius: 6px;
    font-size: .81rem; background: var(--mb-surface); color: var(--mb-text);
    font-family: inherit; box-sizing: border-box;
    transition: border-color .14s, box-shadow .14s;
}
.mb-search:focus { outline: none; border-color: var(--mb-accent); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }

.mb-btn-refresh {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 6px;
    border: 1px solid var(--mb-border); background: var(--mb-surface);
    color: var(--mb-muted); text-decoration: none; font-size: .82rem;
    transition: color .14s, border-color .14s, background .14s; flex-shrink: 0;
}
.mb-btn-refresh:hover { color: var(--mb-accent); border-color: var(--mb-accent); background: var(--mb-accent-soft); }
.mb-btn-refresh.is-spinning i { animation: mbSpin .7s linear infinite; }
@keyframes mbSpin { to { transform: rotate(360deg); } }

/* Liste */
.mb-list { flex: 1; overflow-y: auto; padding: .4rem; }
.mb-list::-webkit-scrollbar { width: 4px; }
.mb-list::-webkit-scrollbar-thumb { background: var(--mb-border); border-radius: 4px; }

.mb-item {
    display: block; padding: .8rem .9rem; border-radius: 8px;
    text-decoration: none; color: inherit; margin-bottom: 2px;
    transition: background .12s; position: relative;
}
.mb-item:hover { background: var(--mb-bg); }
.mb-item.is-active           { background: var(--mb-accent-soft); box-shadow: inset 3px 0 0 var(--mb-accent); }
.mb-item.is-active.is-sent-item { background: var(--mb-sent-soft);   box-shadow: inset 3px 0 0 var(--mb-sent); }

.mb-item-head {
    display: flex; align-items: center;
    justify-content: space-between; gap: .5rem; margin-bottom: .28rem;
}
.mb-item-from {
    font-size: .81rem; font-weight: 600;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1;
    display: flex; align-items: center; gap: .35rem;
}
.mb-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--mb-accent); flex-shrink: 0;
}
.mb-item-date {
    font-size: .7rem; color: var(--mb-muted);
    white-space: nowrap; font-family: 'DM Mono', monospace; flex-shrink: 0;
}
.mb-item-subject {
    font-size: .81rem; font-weight: 500; margin-bottom: .22rem;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.mb-item-excerpt {
    font-size: .75rem; color: var(--mb-muted); line-height: 1.45;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}

.mb-empty {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; padding: 3rem 1.5rem; text-align: center;
    color: var(--mb-muted); gap: .75rem;
}
.mb-empty i { font-size: 2rem; opacity: .35; }
.mb-empty p { font-size: .83rem; margin: 0; max-width: 200px; line-height: 1.5; }

/* ══════════════════════════════════════════════════════════
   PANNEAU DÉTAIL
══════════════════════════════════════════════════════════ */
.mb-panel-detail {
    display: flex; flex-direction: column;
    overflow: hidden; background: var(--mb-bg);
}
.mb-detail-empty {
    flex: 1; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: var(--mb-muted); gap: .9rem; text-align: center; padding: 2rem;
}
.mb-detail-empty i    { font-size: 2.5rem; opacity: .2; }
.mb-detail-empty h3   { font-size: 1rem; font-weight: 600; margin: 0; color: var(--mb-text); }
.mb-detail-empty p    { font-size: .83rem; margin: 0; max-width: 260px; line-height: 1.55; }

/* Header détail */
.mb-detail-header {
    padding: 1.4rem 1.75rem 1.2rem;
    background: var(--mb-surface);
    border-bottom: 1px solid var(--mb-border);
    flex-shrink: 0;
}
.mb-detail-badge {
    display: inline-flex; align-items: center; gap: .35rem;
    font-size: .7rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .08em; padding: .22rem .6rem; border-radius: 20px; margin-bottom: .65rem;
}
.mb-detail-badge.is-inbox { background: var(--mb-accent-soft); color: var(--mb-accent); }
.mb-detail-badge.is-sent  { background: var(--mb-sent-soft);   color: var(--mb-sent); }

.mb-detail-subject {
    font-size: 1.15rem; font-weight: 600; letter-spacing: -.015em;
    margin: 0 0 .7rem; line-height: 1.3;
}
.mb-detail-meta { display: flex; flex-wrap: wrap; gap: .4rem 1.25rem; }
.mb-meta-item {
    display: flex; align-items: center; gap: .38rem;
    font-size: .79rem; color: var(--mb-muted);
}
.mb-meta-item i    { width: 13px; text-align: center; opacity: .7; }
.mb-meta-item strong { color: var(--mb-text); font-weight: 500; }

/* Corps */
.mb-detail-body-wrap {
    flex: 1; overflow-y: auto; padding: 1.4rem 1.75rem;
}
.mb-detail-body-wrap::-webkit-scrollbar { width: 4px; }
.mb-detail-body-wrap::-webkit-scrollbar-thumb { background: var(--mb-border); border-radius: 4px; }

.mb-iframe-wrap {
    background: #fff; border: 1px solid var(--mb-border);
    border-radius: var(--mb-radius); overflow: hidden; box-shadow: var(--mb-shadow);
}
.mb-iframe { width: 100%; min-height: 200px; border: none; display: block; }

.mb-plain {
    background: var(--mb-surface); border: 1px solid var(--mb-border);
    border-radius: var(--mb-radius); padding: 1.4rem 1.5rem;
    font-family: 'DM Mono', monospace; font-size: .83rem;
    line-height: 1.85; white-space: pre-wrap; word-break: break-word;
    color: var(--mb-text); box-shadow: var(--mb-shadow);
}
.mb-plain .mb-quote {
    display: block; border-left: 3px solid var(--mb-border);
    padding-left: .7rem; color: var(--mb-muted); font-style: italic; margin: 2px 0;
}
.mb-plain a { color: var(--mb-accent); text-decoration: underline; word-break: break-all; }

/* Zone réponse */
.mb-reply-zone {
    border-top: 1px solid var(--mb-border);
    background: var(--mb-surface); flex-shrink: 0;
}
.mb-reply-toggle {
    display: flex; align-items: center; gap: .5rem;
    width: 100%; padding: .85rem 1.75rem;
    font-size: .84rem; font-weight: 600; color: var(--mb-accent);
    cursor: pointer; border: none; background: none; font-family: inherit;
    text-align: left; transition: background .13s;
}
.mb-reply-toggle:hover { background: var(--mb-accent-soft); }
.mb-reply-chevron { margin-left: auto; font-size: .72rem; transition: transform .2s; }
.mb-reply-chevron.is-open { transform: rotate(180deg); }

.mb-reply-form { padding: 0 1.75rem 1.4rem; display: none; }
.mb-reply-form.is-open { display: block; }

.mb-textarea {
    width: 100%; min-height: 110px; padding: .85rem 1rem;
    border: 1px solid var(--mb-border); border-radius: var(--mb-radius);
    font-family: 'DM Sans', sans-serif; font-size: .875rem; line-height: 1.6;
    color: var(--mb-text); background: var(--mb-bg); resize: vertical;
    transition: border-color .14s, box-shadow .14s; box-sizing: border-box; margin-bottom: .7rem;
}
.mb-textarea:focus {
    outline: none; border-color: var(--mb-accent);
    box-shadow: 0 0 0 3px rgba(37,99,235,.1); background: var(--mb-surface);
}
.mb-reply-actions { display: flex; gap: .55rem; }

.mb-btn {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .52rem 1rem; border-radius: 7px;
    font-size: .82rem; font-weight: 600; font-family: inherit;
    cursor: pointer; border: none; transition: opacity .14s, transform .1s;
}
.mb-btn:active { transform: scale(.97); }
.mb-btn-primary { background: var(--mb-accent); color: #fff; }
.mb-btn-primary:hover { opacity: .88; }
.mb-btn-ghost {
    background: var(--mb-bg); color: var(--mb-muted);
    border: 1px solid var(--mb-border);
}
.mb-btn-ghost:hover { background: var(--mb-border); color: var(--mb-text); }

/* ══════════════════════════════════════════════════════════
   RESPONSIVE
══════════════════════════════════════════════════════════ */
@media (max-width: 860px) {
    .mb-layout { grid-template-columns: 1fr; height: auto; }
    .mb-panel-list { border-right: none; border-bottom: 1px solid var(--mb-border); max-height: 48vh; }
    .mb-panel-detail { min-height: 55vh; }
}
</style>

<div class="mb-wrap">

    <!-- HERO ──────────────────────────────────────────────────────────── -->
    <header class="mb-hero">
        <div class="mb-hero-left">
            <div class="mb-kicker"><i class="fas fa-envelope-open-text"></i> Boîte utilisateur</div>
            <h1>Messagerie</h1>
            <p>Consultez et répondez à vos emails via IMAP / SMTP.</p>
        </div>
        <div class="mb-hero-stats">
            <div class="mb-stat is-inbox">
                <strong><?= (int) ($mailStats['mailbox'] ?? 0) ?></strong>
                <span>Reçus</span>
            </div>
            <div class="mb-stat is-sent">
                <strong><?= (int) ($mailStats['sent'] ?? 0) ?></strong>
                <span>Envoyés</span>
            </div>
        </div>
    </header>

    <?php if (!empty($mailError)): ?>
        <div class="mb-alert">
            <i class="fas fa-exclamation-triangle"></i>
            <?= htmlspecialchars($mailError) ?>
        </div>
    <?php endif; ?>

    <!-- LAYOUT ────────────────────────────────────────────────────────── -->
    <div class="mb-layout">

        <!-- LISTE ─────────────────────────────────────────────────────── -->
        <aside class="mb-panel-list">

            <!-- Onglets -->
            <div class="mb-tabs" role="tablist">
                <a href="adminPage.php?page=messaging&tab=inbox<?= !empty($selectedMail) ? '&uid='.(int)($selectedMail['uid']??0) : '' ?>"
                   class="mb-tab <?= $activeTab==='inbox' ? 'is-active' : '' ?>"
                   role="tab" aria-selected="<?= $activeTab==='inbox' ? 'true' : 'false' ?>">
                    <i class="fas fa-inbox"></i> Reçus
                    <span class="mb-count"><?= count($mailMessages) ?></span>
                </a>
                <a href="adminPage.php?page=messaging&tab=sent<?= !empty($selectedMail) ? '&uid='.(int)($selectedMail['uid']??0) : '' ?>"
                   class="mb-tab is-sent <?= $activeTab==='sent' ? 'is-active' : '' ?>"
                   role="tab" aria-selected="<?= $activeTab==='sent' ? 'true' : 'false' ?>">
                    <i class="fas fa-paper-plane"></i> Envoyés
                    <span class="mb-count"><?= count($sentMessages) ?></span>
                </a>
            </div>

            <!-- Toolbar -->
            <div class="mb-toolbar">
                <div class="mb-search-wrap">
                    <i class="fas fa-search"></i>
                    <input type="search" class="mb-search" id="mbSearch" placeholder="Rechercher…" aria-label="Rechercher">
                </div>
                <a href="adminPage.php?page=messaging&tab=<?= htmlspecialchars($activeTab) ?><?= !empty($selectedMail) ? '&uid='.(int)($selectedMail['uid']??0) : '' ?>"
                   class="mb-btn-refresh" id="mbRefreshBtn" title="Actualiser" aria-label="Actualiser">
                    <i class="fas fa-sync-alt"></i>
                </a>
            </div>

            <!-- Messages -->
            <div class="mb-list" id="mbList" role="list">
                <?php
                $isSentTab   = $activeTab === 'sent';
                $currentList = $isSentTab ? $sentMessages : $mailMessages;
                ?>
                <?php if (!empty($currentList)): ?>
                    <?php foreach ($currentList as $mail): ?>
                        <?php
                        $uid      = (int)($mail['uid'] ?? 0);
                        $isActive = ((int)($selectedMail['uid'] ?? -1)) === $uid;
                        $isSeen   = !empty($mail['seen']);
                        $contact  = $isSentTab ? ($mail['to'] ?? '-') : ($mail['from'] ?? '-');
                        ?>
                        <a class="mb-item <?= $isActive ? 'is-active' : '' ?> <?= $isSentTab ? 'is-sent-item' : '' ?>"
                           href="adminPage.php?page=messaging&tab=<?= $activeTab ?>&uid=<?= $uid ?>"
                           role="listitem"
                           data-search="<?= htmlspecialchars($contact.' '.($mail['subject']??'').' '.($mail['excerpt']??'')) ?>">
                            <div class="mb-item-head">
                                <span class="mb-item-from">
                                    <?php if (!$isSeen && !$isSentTab): ?><span class="mb-dot"></span><?php endif; ?>
                                    <?= htmlspecialchars($contact) ?>
                                </span>
                                <time class="mb-item-date"><?= htmlspecialchars(mbFormatDate($mail['date'] ?? '')) ?></time>
                            </div>
                            <div class="mb-item-subject"><?= htmlspecialchars($mail['subject'] ?? 'Sans objet') ?></div>
                            <div class="mb-item-excerpt"><?= htmlspecialchars($mail['excerpt'] ?? '') ?></div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="mb-empty">
                        <i class="fas <?= $isSentTab ? 'fa-paper-plane' : 'fa-inbox' ?>"></i>
                        <p><?= $isSentTab ? 'Aucun email envoyé.' : 'Aucun email reçu.<br>Vérifiez la config IMAP.' ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </aside>

        <!-- DÉTAIL ────────────────────────────────────────────────────── -->
        <main class="mb-panel-detail">
            <?php if (!empty($selectedMail)): ?>
                <?php $isSentMail = ($activeTab === 'sent'); ?>

                <div class="mb-detail-header">
                    <div class="mb-detail-badge <?= $isSentMail ? 'is-sent' : 'is-inbox' ?>">
                        <i class="fas <?= $isSentMail ? 'fa-paper-plane' : 'fa-inbox' ?>"></i>
                        <?= $isSentMail ? 'Email envoyé' : 'Email reçu' ?>
                    </div>
                    <h2 class="mb-detail-subject"><?= htmlspecialchars($selectedMail['subject'] ?? 'Sans objet') ?></h2>
                    <div class="mb-detail-meta">
                        <?php if ($isSentMail): ?>
                            <div class="mb-meta-item">
                                <i class="fas fa-arrow-right"></i>
                                <span>À&nbsp;<strong><?= htmlspecialchars($selectedMail['to'] ?? '-') ?></strong></span>
                            </div>
                        <?php else: ?>
                            <div class="mb-meta-item">
                                <i class="fas fa-user"></i>
                                <span>De&nbsp;<strong><?= htmlspecialchars($selectedMail['from'] ?? '-') ?></strong></span>
                            </div>
                        <?php endif; ?>
                        <div class="mb-meta-item">
                            <i class="fas fa-clock"></i>
                            <span><?= htmlspecialchars($selectedMail['date'] ?? '-') ?></span>
                        </div>
                    </div>
                </div>

                <div class="mb-detail-body-wrap">
                    <?php
                    $rawBody = $selectedMail['body'] ?? '';
                    $isHtml  = (bool) preg_match('/<(html|body|div|p|br|table|span|a|img)\b/i', $rawBody);
                    ?>
                    <?php if ($isHtml): ?>
                        <div class="mb-iframe-wrap">
                            <iframe id="mbBodyFrame" class="mb-iframe" sandbox="allow-same-origin" scrolling="auto" title="Contenu du message"></iframe>
                        </div>
                        <script>
                        (function(){
                            var f=document.getElementById('mbBodyFrame');
                            if(!f)return;
                            var d=f.contentDocument||f.contentWindow.document;
                            d.open();d.write(<?= json_encode($rawBody, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>);d.close();
                            function r(){f.style.height=(d.body.scrollHeight+32)+'px';}
                            f.onload=r;setTimeout(r,200);
                        })();
                        </script>
                    <?php else: ?>
                        <div class="mb-plain"><?= mbFormatPlainText($rawBody) ?></div>
                    <?php endif; ?>
                </div>

                <?php if (!$isSentMail): ?>
                <div class="mb-reply-zone">
                    <button type="button" class="mb-reply-toggle" id="mbReplyToggle"
                            onclick="mbToggleReply()" aria-expanded="false">
                        <i class="fas fa-reply"></i>
                        Répondre
                        <i class="fas fa-chevron-down mb-reply-chevron" id="mbChevron"></i>
                    </button>
                    <div class="mb-reply-form" id="mbReplyForm">
                        <form method="post" action="adminPage.php?page=messaging&tab=inbox&uid=<?= (int)($selectedMail['uid']??0) ?>">
                            <input type="hidden" name="uid" value="<?= (int)($selectedMail['uid']??0) ?>">
                            <textarea name="body" id="mbReplyBody" class="mb-textarea"
                                      placeholder="Écrivez votre réponse…" required></textarea>
                            <div class="mb-reply-actions">
                                <button type="submit" class="mb-btn mb-btn-primary">
                                    <i class="fas fa-paper-plane"></i> Envoyer
                                </button>
                                <button type="button" class="mb-btn mb-btn-ghost" onclick="mbToggleReply()">
                                    <i class="fas fa-times"></i> Annuler
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="mb-detail-empty">
                    <i class="fas fa-envelope"></i>
                    <h3>Sélectionnez un email</h3>
                    <p>Le contenu du message s'affichera ici.</p>
                </div>
            <?php endif; ?>
        </main>

    </div>
</div>

<script>
/* Actualiser */
(function(){
    var btn=document.getElementById('mbRefreshBtn');
    if(btn) btn.addEventListener('click',function(){btn.classList.add('is-spinning');});
})();

/* Répondre */
function mbToggleReply(){
    var form=document.getElementById('mbReplyForm');
    var chev=document.getElementById('mbChevron');
    var tog=document.getElementById('mbReplyToggle');
    if(!form)return;
    var open=form.classList.toggle('is-open');
    if(chev) chev.classList.toggle('is-open',open);
    if(tog)  tog.setAttribute('aria-expanded', open?'true':'false');
    if(open){
        var ta=document.getElementById('mbReplyBody');
        if(ta) ta.focus();
    }
}

/* Recherche */
(function(){
    var inp=document.getElementById('mbSearch');
    var list=document.getElementById('mbList');
    if(!inp||!list)return;
    inp.addEventListener('input',function(){
        var q=this.value.trim().toLowerCase();
        list.querySelectorAll('.mb-item').forEach(function(el){
            el.style.display=(!q||(el.dataset.search||'').toLowerCase().includes(q))?'':'none';
        });
    });
})();
</script>

<?php
function mbFormatPlainText(string $text): string
{
    $lines=$output='';
    foreach(explode("\n",$text) as $line){
        $e=htmlspecialchars($line,ENT_QUOTES|ENT_HTML5,'UTF-8');
        $output.=preg_match('/^(&gt;|>)/',$e)
            ?'<span class="mb-quote">'.$e."</span>\n"
            :$e."\n";
    }
    return preg_replace('~(https?://[^\s<>"\']+)~i','<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',$output);
}

function mbFormatDate(string $raw): string
{
    if($raw==='')return '';
    try{
        $dt=new DateTime($raw);$now=new DateTime();
        if($dt->format('Y-m-d')===$now->format('Y-m-d'))return $dt->format('H:i');
        $m=['jan','fév','mar','avr','mai','jun','jul','aoû','sep','oct','nov','déc'];
        if($dt->format('Y')===$now->format('Y'))return $dt->format('j').' '.$m[(int)$dt->format('n')-1];
        return $dt->format('d/m/Y');
    }catch(Throwable){return $raw;}
}
?>