<?php

function immoValue($value): string
{
    $value = trim((string) ($value ?? ''));
    return htmlspecialchars($value !== '' ? $value : '—', ENT_QUOTES, 'UTF-8');
}

function immoInitials($prenom, $nom): string
{
    $p = mb_strtoupper(mb_substr(trim($prenom), 0, 1));
    $n = mb_strtoupper(mb_substr(trim($nom), 0, 1));
    return htmlspecialchars($p . $n, ENT_QUOTES, 'UTF-8');
}

$statusOptions    = ['Nouveau', 'En cours', 'Validé', 'Refusé'];
$selectedAdhesion = $selectedAdhesion ?? null;
$selectedContract = $selectedContract ?? null;
$selectedNotes    = $selectedNotes    ?? [];
$stats            = $stats            ?? [];

$badgeMap = [
    'Nouveau'  => 'nouveau',
    'En cours' => 'en-cours',
    'Validé'   => 'valide',
    'Refusé'   => 'refuse',
];

// URL de base pour les actions admin
$baseUrl = 'adminPage.php?page=programme-immo';
// URL de base pour l'export PDF — à adapter selon votre structure
$pdfBase = '/app/api/export_adhesion_contract_pdf.php';
?>

<style>
*, *::before, *::after { box-sizing: border-box; }

/* ── Page ── */
.immo-page { display: grid; gap: 1.25rem; }

/* ── Card ── */
.immo-card {
    background    : var(--color-background-primary, #fff);
    border        : 0.5px solid var(--color-border-tertiary, #e5e7eb);
    border-radius : var(--border-radius-lg, 12px);
    padding       : 1.25rem;
}

/* ── Page header ── */
.immo-page-title { font-size: 18px; font-weight: 500; margin-bottom: 4px; color: var(--color-text-primary); }
.immo-page-sub   { font-size: 13px; color: var(--color-text-secondary, #667085); margin: 0; }

/* ── Stats ── */
.immo-stats {
    display               : grid;
    grid-template-columns : repeat(5, 1fr);
    gap                   : 10px;
    margin-top            : 1rem;
}
.immo-stat {
    background    : var(--color-background-secondary, #f8fafc);
    border-radius : var(--border-radius-md, 8px);
    padding       : .875rem 1rem;
}
.immo-stat__label { font-size: 12px; color: var(--color-text-secondary); margin-bottom: 6px; }
.immo-stat__val   { font-size: 22px; font-weight: 500; }
.immo-stat__val--total    { color: var(--color-text-primary); }
.immo-stat__val--nouveau  { color: #185FA5; }
.immo-stat__val--en-cours { color: #854F0B; }
.immo-stat__val--valide   { color: #3B6D11; }
.immo-stat__val--refuse   { color: #A32D2D; }

/* ── Section title ── */
.immo-section-title { font-size: 15px; font-weight: 500; margin-bottom: 1rem; color: var(--color-text-primary); }

/* ── Toolbar ── */
.immo-toolbar {
    display       : flex;
    align-items   : center;
    gap           : 8px;
    background    : var(--color-background-secondary, #f8fafc);
    border        : 0.5px solid var(--color-border-tertiary, #e5e7eb);
    border-radius : var(--border-radius-md, 8px);
    padding       : 8px 12px;
    margin-bottom : 1rem;
}
.immo-toolbar i     { font-size: 15px; color: var(--color-text-secondary); }
.immo-toolbar input { border: none; background: transparent; font: inherit; font-size: 13px; color: var(--color-text-primary); flex: 1; outline: none; }

/* ── Table ── */
.immo-table-wrap { overflow-x: auto; }
.immo-table { width: 100%; border-collapse: collapse; }
.immo-table thead th {
    font-size      : 11px;
    font-weight    : 500;
    text-transform : uppercase;
    letter-spacing : .5px;
    color          : var(--color-text-secondary);
    padding        : 0 10px 10px;
    text-align     : left;
    border-bottom  : 0.5px solid var(--color-border-tertiary);
    white-space    : nowrap;
}
.immo-table tbody td {
    font-size      : 13px;
    padding        : 11px 10px;
    border-bottom  : 0.5px solid var(--color-border-tertiary);
    vertical-align : middle;
    color          : var(--color-text-primary);
}
.immo-table tbody tr:last-child td { border-bottom: none; }
.immo-table tbody tr:hover td      { background: var(--color-background-secondary); }
.col-id   { color: var(--color-text-secondary) !important; white-space: nowrap; }
.col-name { font-weight: 500; }
.col-sub  { font-size: 12px; color: var(--color-text-secondary); margin-top: 2px; }
.col-date { font-size: 12px; color: var(--color-text-secondary); white-space: nowrap; }

/* ── Badge ── */
.immo-badge {
    display       : inline-flex;
    align-items   : center;
    gap           : 4px;
    padding       : 3px 10px;
    border-radius : 999px;
    font-size     : 11px;
    font-weight   : 500;
    white-space   : nowrap;
}
.immo-badge--nouveau  { background: #E6F1FB; color: #0C447C; }
.immo-badge--en-cours { background: #FAEEDA; color: #633806; }
.immo-badge--valide   { background: #EAF3DE; color: #27500A; }
.immo-badge--refuse   { background: #FCEBEB; color: #791F1F; }

/* ── Action buttons ── */
.immo-actions  { display: flex; gap: 6px; align-items: center; }
.immo-btn-icon {
    display        : inline-flex;
    align-items    : center;
    gap            : 4px;
    padding        : 5px 10px;
    border-radius  : var(--border-radius-md, 8px);
    font           : inherit;
    font-size      : 12px;
    cursor         : pointer;
    border         : 0.5px solid var(--color-border-secondary, #d0d5dd);
    background     : transparent;
    color          : var(--color-text-primary);
    text-decoration: none;
    white-space    : nowrap;
}
.immo-btn-icon:hover        { background: var(--color-background-secondary); }
.immo-btn-icon.danger       { color: #A32D2D; border-color: #F09595; }
.immo-btn-icon.danger:hover { background: #FCEBEB; }

/* ══════════════════════════════════════
   DIALOG
══════════════════════════════════════ */
#immo-dialog-overlay {
    position        : fixed;
    inset           : 0;
    background      : rgba(15, 23, 42, .4);
    display         : flex;
    align-items     : center;
    justify-content : center;
    padding         : 20px;
    z-index         : 9999;
    opacity         : 0;
    pointer-events  : none;
    transition      : opacity 180ms ease;
}
#immo-dialog-overlay.open {
    opacity        : 1;
    pointer-events : all;
}

#immo-dialog {
    background    : var(--color-background-primary, #fff);
    border        : 0.5px solid var(--color-border-tertiary, #e5e7eb);
    border-radius : var(--border-radius-lg, 12px);
    width         : 100%;
    max-width     : 600px;
    max-height    : calc(100dvh - 40px);
    overflow-y    : auto;
    transform     : translateY(14px) scale(.98);
    transition    : transform 180ms ease;
    display       : flex;
    flex-direction: column;
}
#immo-dialog-overlay.open #immo-dialog {
    transform: translateY(0) scale(1);
}

/* Dialog header */
.dlg-header {
    display        : flex;
    align-items    : flex-start;
    justify-content: space-between;
    gap            : 12px;
    padding        : 1.25rem 1.25rem .75rem;
    border-bottom  : 0.5px solid var(--color-border-tertiary);
    position       : sticky;
    top            : 0;
    background     : var(--color-background-primary);
    z-index        : 2;
    border-radius  : var(--border-radius-lg, 12px) var(--border-radius-lg, 12px) 0 0;
}
.dlg-avatar {
    width          : 46px;
    height         : 46px;
    border-radius  : 50%;
    background     : #E6F1FB;
    display        : flex;
    align-items    : center;
    justify-content: center;
    font-size      : 15px;
    font-weight    : 500;
    color          : #0C447C;
    flex-shrink    : 0;
}
.dlg-name   { font-size: 16px; font-weight: 500; color: var(--color-text-primary); line-height: 1.3; }
.dlg-sub    { font-size: 12px; color: var(--color-text-secondary); margin-top: 2px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.dlg-close  {
    width          : 32px;
    height         : 32px;
    flex-shrink    : 0;
    border-radius  : var(--border-radius-md, 8px);
    border         : 0.5px solid var(--color-border-tertiary);
    background     : transparent;
    cursor         : pointer;
    display        : flex;
    align-items    : center;
    justify-content: center;
    color          : var(--color-text-secondary);
    font-size      : 16px;
}
.dlg-close:hover { background: var(--color-background-secondary); }

/* Dialog body */
.dlg-body    { padding: 1rem 1.25rem; flex: 1; }
.dlg-section { font-size: 11px; font-weight: 500; text-transform: uppercase; letter-spacing: .5px; color: var(--color-text-secondary); margin-bottom: .625rem; }
.dlg-hr      { border: none; border-top: 0.5px solid var(--color-border-tertiary); margin: 1rem 0; }

.dlg-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.dlg-field-key { font-size: 11px; color: var(--color-text-secondary); display: flex; align-items: center; gap: 5px; }
.dlg-field-val { font-size: 13px; color: var(--color-text-primary); margin-top: 3px; line-height: 1.45; overflow-wrap: anywhere; }

.dlg-message {
    background    : var(--color-background-secondary);
    border-radius : var(--border-radius-md, 8px);
    padding       : .75rem;
    font-size     : 13px;
    color         : var(--color-text-primary);
    line-height   : 1.55;
    white-space   : pre-wrap;
}

/* Status update inline dans dialog */
.dlg-status-form { display: grid; gap: 8px; }
.dlg-select {
    width         : 100%;
    border        : 0.5px solid var(--color-border-secondary, #d0d5dd);
    border-radius : var(--border-radius-md, 8px);
    padding       : 8px 10px;
    font          : inherit;
    font-size     : 13px;
    background    : var(--color-background-primary);
    color         : var(--color-text-primary);
}
.dlg-textarea {
    width         : 100%;
    border        : 0.5px solid var(--color-border-secondary, #d0d5dd);
    border-radius : var(--border-radius-md, 8px);
    padding       : 8px 10px;
    font          : inherit;
    font-size     : 13px;
    background    : var(--color-background-primary);
    color         : var(--color-text-primary);
    resize        : vertical;
}
.dlg-label { font-size: 12px; font-weight: 500; color: var(--color-text-secondary); display: block; margin-bottom: 5px; }

/* Contract block */
.dlg-contract-block {
    background    : var(--color-background-secondary);
    border-radius : var(--border-radius-md, 8px);
    padding       : 1rem;
}
.dlg-contract-title {
    font-size     : 13px;
    font-weight   : 500;
    margin-bottom : .75rem;
    display       : flex;
    align-items   : center;
    gap           : 6px;
    color         : var(--color-text-primary);
}

/* Timeline notes */
.dlg-tl      { display: grid; gap: 0; }
.dlg-tl-item { display: flex; gap: 10px; padding: 8px 0; position: relative; }
.dlg-tl-dot  { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; margin-top: 5px; background: #185FA5; }
.dlg-tl-line { position: absolute; left: 3.5px; top: 21px; bottom: -8px; width: 0.5px; background: var(--color-border-tertiary); }
.dlg-tl-item:last-child .dlg-tl-line { display: none; }
.dlg-tl-meta { font-size: 11px; color: var(--color-text-secondary); margin-bottom: 3px; }
.dlg-tl-text { font-size: 13px; color: var(--color-text-primary); line-height: 1.45; }

/* Dialog footer */
.dlg-footer {
    padding        : .875rem 1.25rem;
    display        : flex;
    gap            : 8px;
    border-top     : 0.5px solid var(--color-border-tertiary);
    position       : sticky;
    bottom         : 0;
    background     : var(--color-background-primary);
    z-index        : 2;
    border-radius  : 0 0 var(--border-radius-lg, 12px) var(--border-radius-lg, 12px);
    flex-wrap      : wrap;
}
.dlg-btn {
    display        : inline-flex;
    align-items    : center;
    justify-content: center;
    gap            : 6px;
    padding        : 7px 14px;
    border-radius  : var(--border-radius-md, 8px);
    font           : inherit;
    font-size      : 13px;
    font-weight    : 500;
    cursor         : pointer;
    text-decoration: none;
    white-space    : nowrap;
}
.dlg-btn-ghost   { background: transparent; border: 0.5px solid var(--color-border-secondary, #d0d5dd); color: var(--color-text-primary); }
.dlg-btn-ghost:hover { background: var(--color-background-secondary); }
.dlg-btn-primary { background: var(--color-text-primary); border: none; color: var(--color-background-primary); flex: 1; }
.dlg-btn-primary:hover { opacity: .88; }
.dlg-btn-danger  { background: transparent; border: 0.5px solid #F09595; color: #A32D2D; }
.dlg-btn-danger:hover { background: #FCEBEB; }

/* Dialog loading / error */
.dlg-loading {
    padding    : 3rem 1.25rem;
    text-align : center;
    font-size  : 13px;
    color      : var(--color-text-secondary);
}

/* Badges dans dialog */
.dlg-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 500; }
.dlg-badge--nouveau  { background: #E6F1FB; color: #0C447C; }
.dlg-badge--en-cours { background: #FAEEDA; color: #633806; }
.dlg-badge--valide   { background: #EAF3DE; color: #27500A; }
.dlg-badge--refuse   { background: #FCEBEB; color: #791F1F; }

/* Export row */
.dlg-export-row { display: flex; gap: 8px; margin-top: 8px; }
.dlg-btn-export {
    display        : inline-flex;
    align-items    : center;
    justify-content: center;
    gap            : 6px;
    flex           : 1;
    padding        : 7px 10px;
    border-radius  : var(--border-radius-md, 8px);
    font           : inherit;
    font-size      : 12px;
    cursor         : pointer;
    border         : 0.5px solid var(--color-border-secondary, #d0d5dd);
    background     : transparent;
    color          : var(--color-text-primary);
    text-decoration: none;
}
.dlg-btn-export:hover { background: var(--color-background-primary); }

/* Note list dans dialog */
.dlg-note-list { display: grid; gap: 8px; }
.dlg-note {
    background    : var(--color-background-secondary);
    border-radius : var(--border-radius-md, 8px);
    padding       : .75rem;
}
.dlg-note__meta { font-size: 11px; font-weight: 500; color: var(--color-text-secondary); margin-bottom: 4px; }
.dlg-note__text { font-size: 13px; color: var(--color-text-primary); line-height: 1.5; white-space: pre-wrap; overflow-wrap: anywhere; }

/* ── Responsive ── */
@media (max-width: 900px) {
    .immo-stats { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 600px) {
    .immo-stats   { grid-template-columns: repeat(2, 1fr); }
    .dlg-grid     { grid-template-columns: 1fr; }
    .dlg-footer   { flex-direction: column; }
    .dlg-btn-primary { flex: unset; }
}
</style>

<!-- ══ Dialog overlay ══ -->
<div id="immo-dialog-overlay" aria-hidden="true">
    <div id="immo-dialog" role="dialog" aria-modal="true" aria-labelledby="dlg-name">
        <div class="dlg-loading">
            <i class="fas fa-circle-notch fa-spin" aria-hidden="true"></i> Chargement…
        </div>
    </div>
</div>

<!-- ══ Page ══ -->
<div class="immo-page">

    <!-- Header + stats -->
    <div class="immo-card">
        <p class="immo-page-title">Programme Immo</p>
        <p class="immo-page-sub">Suivi des adhésions au programme immobilier ECOFI Construction.</p>

        <div class="immo-stats">
            <div class="immo-stat">
                <div class="immo-stat__label">Total</div>
                <div class="immo-stat__val immo-stat__val--total"><?= (int)($stats['total'] ?? 0) ?></div>
            </div>
            <div class="immo-stat">
                <div class="immo-stat__label">Nouveau</div>
                <div class="immo-stat__val immo-stat__val--nouveau"><?= (int)($stats['Nouveau'] ?? 0) ?></div>
            </div>
            <div class="immo-stat">
                <div class="immo-stat__label">En cours</div>
                <div class="immo-stat__val immo-stat__val--en-cours"><?= (int)($stats['En cours'] ?? 0) ?></div>
            </div>
            <div class="immo-stat">
                <div class="immo-stat__label">Validé</div>
                <div class="immo-stat__val immo-stat__val--valide"><?= (int)($stats['Validé'] ?? 0) ?></div>
            </div>
            <div class="immo-stat">
                <div class="immo-stat__label">Refusé</div>
                <div class="immo-stat__val immo-stat__val--refuse"><?= (int)($stats['Refusé'] ?? 0) ?></div>
            </div>
        </div>
    </div>

    <!-- Table pleine largeur -->
    <div class="immo-card">
        <p class="immo-section-title">Adhésions reçues</p>

        <div class="immo-toolbar">
            <i class="fas fa-search" aria-hidden="true"></i>
            <input
                type="search"
                data-admin-search
                data-target="#adhesionsTable tbody tr"
                placeholder="Rechercher par nom, prénom, email, téléphone ou CNI…"
                aria-label="Rechercher une adhésion"
            >
        </div>

        <?php if (empty($adhesions)): ?>
            <p style="color:var(--color-text-secondary);font-size:13px;">Aucune adhésion trouvée.</p>
        <?php else: ?>
            <div class="immo-table-wrap">
                <table class="immo-table" id="adhesionsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom complet</th>
                            <th>Contact</th>
                            <th>CNI / Passeport</th>
                            <th>Paiement</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($adhesions as $adhesion):
                            $status     = (string)($adhesion['status'] ?? 'Nouveau');
                            $badgeClass = $badgeMap[$status] ?? 'nouveau';
                        ?>
                        <tr>
                            <td class="col-id">#<?= (int)($adhesion['id'] ?? 0) ?></td>
                            <td class="col-name"><?= immoValue(($adhesion['prenom'] ?? '') . ' ' . ($adhesion['nom'] ?? '')) ?></td>
                            <td>
                                <?= immoValue($adhesion['email'] ?? '') ?>
                                <div class="col-sub"><?= immoValue($adhesion['telephone'] ?? '') ?></div>
                            </td>
                            <td><?= immoValue($adhesion['cni'] ?? '') ?></td>
                            <td><?= immoValue($adhesion['mode_paiement'] ?? '') ?></td>
                            <td>
                                <span class="immo-badge immo-badge--<?= htmlspecialchars($badgeClass) ?>">
                                    <?= immoValue($status) ?>
                                </span>
                            </td>
                            <td class="col-date"><?= immoValue($adhesion['created_at'] ?? '') ?></td>
                            <td>
                                <div class="immo-actions">
                                    <button
                                        class="immo-btn-icon"
                                        data-dialog-open="<?= (int)($adhesion['id'] ?? 0) ?>"
                                        aria-label="Voir le dossier #<?= (int)($adhesion['id'] ?? 0) ?>"
                                    >
                                        <i class="fas fa-eye" aria-hidden="true"></i> Voir
                                    </button>
                                    <form
                                        method="post"
                                        action="<?= htmlspecialchars($baseUrl) ?>"
                                        data-loading-text="Suppression…"
                                        onsubmit="return confirm('Supprimer cette adhésion ?');"
                                        style="display:contents"
                                    >
                                        <input type="hidden" name="action"      value="delete">
                                        <input type="hidden" name="adhesion_id" value="<?= (int)($adhesion['id'] ?? 0) ?>">
                                        <button type="submit" class="immo-btn-icon danger" title="Supprimer">
                                            <i class="fas fa-trash" aria-hidden="true"></i>
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

</div><!-- /.immo-page -->

<script>
(function () {
    'use strict';

    /* ── Config ── */
    const BASE_URL = 'adminPage.php?page=programme-immo';
    const PDF_URL  = '<?= htmlspecialchars($pdfBase, ENT_QUOTES) ?>';

    const STATUS_OPTIONS = <?= json_encode($statusOptions, JSON_UNESCAPED_UNICODE) ?>;

    const BADGE_MAP = {
        'Nouveau' : 'nouveau',
        'En cours': 'en-cours',
        'Validé'  : 'valide',
        'Refusé'  : 'refuse',
    };

    /* ── DOM ── */
    const overlay = document.getElementById('immo-dialog-overlay');
    const dialog  = document.getElementById('immo-dialog');

    /* ── Helpers ── */
    function esc(str) {
        return String(str ?? '—')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function initials(p, n) {
        return (String(p || '')[0] ?? '').toUpperCase()
             + (String(n || '')[0] ?? '').toUpperCase();
    }

    function statusOptionsHtml(current) {
        return STATUS_OPTIONS
            .map(s => `<option value="${esc(s)}"${s === current ? ' selected' : ''}>${esc(s)}</option>`)
            .join('');
    }

    /* ── Open / close ── */
    function openDialog(id) {
        overlay.style.display = 'flex';
        requestAnimationFrame(() => overlay.classList.add('open'));
        overlay.removeAttribute('aria-hidden');
        document.body.style.overflow = 'hidden';

        dialog.innerHTML = '<div class="dlg-loading"><i class="fas fa-circle-notch fa-spin" aria-hidden="true"></i> Chargement…</div>';

        fetch(`${BASE_URL}&action=get_json&id=${encodeURIComponent(id)}`)
            .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(({ adhesion: a, notes, contract }) => render(a, notes ?? [], contract ?? {}))
            .catch(err => {
                dialog.innerHTML = `<div class="dlg-loading" style="color:var(--color-text-danger,#b91c1c)">
                    <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                    Erreur lors du chargement du dossier.<br>
                    <small>${esc(err.message)}</small>
                </div>`;
            });
    }

    function closeDialog() {
        overlay.classList.remove('open');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        setTimeout(() => { overlay.style.display = 'none'; }, 200);
    }

    /* ── Render dialog content ── */
    function render(a, notes, contract) {
        const id      = parseInt(a.id, 10);
        const bc      = BADGE_MAP[a.status] ?? 'nouveau';
        const initls  = initials(a.prenom, a.nom);
        const pdfHref = `${PDF_URL}?adhesion_id=${id}`;

        /* Notes timeline */
        const notesHtml = notes.length
            ? '<div class="dlg-tl">' + notes.map((n, i) => `
                <div class="dlg-tl-item">
                    <div style="display:flex;flex-direction:column;align-items:center">
                        <div class="dlg-tl-dot"></div>
                        ${i < notes.length - 1 ? '<div class="dlg-tl-line"></div>' : ''}
                    </div>
                    <div>
                        <div class="dlg-tl-meta">${esc(n.admin_name ?? 'Administrateur')} · ${esc(n.created_at ?? '')}</div>
                        <div class="dlg-tl-text">${esc(n.note ?? '')}</div>
                    </div>
                </div>`).join('') + '</div>'
            : '<p style="font-size:13px;color:var(--color-text-secondary)">Aucune note interne.</p>';

        /* Contract content */
        const contractContent = esc(contract.contract_content ?? '');

        dialog.innerHTML = `
        <!-- Header -->
        <div class="dlg-header">
            <div style="display:flex;align-items:center;gap:12px">
                <div class="dlg-avatar">${esc(initls)}</div>
                <div>
                    <div class="dlg-name" id="dlg-name">${esc((a.prenom ?? '') + ' ' + (a.nom ?? ''))}</div>
                    <div class="dlg-sub">
                        Dossier #${id}
                        <span class="dlg-badge dlg-badge--${bc}">${esc(a.status ?? 'Nouveau')}</span>
                    </div>
                </div>
            </div>
            <button class="dlg-close" id="dlg-close-btn" aria-label="Fermer">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="dlg-body">

            <!-- Infos personnelles -->
            <p class="dlg-section">Informations personnelles</p>
            <div class="dlg-grid">
                <div>
                    <div class="dlg-field-key"><i class="fas fa-birthday-cake" aria-hidden="true" style="font-size:11px"></i> Date de naissance</div>
                    <div class="dlg-field-val">${esc(a.date_naissance ?? '—')}</div>
                </div>
                <div>
                    <div class="dlg-field-key"><i class="fas fa-map-marker-alt" aria-hidden="true" style="font-size:11px"></i> Lieu de naissance</div>
                    <div class="dlg-field-val">${esc(a.lieu_naissance ?? '—')}</div>
                </div>
                <div>
                    <div class="dlg-field-key"><i class="fas fa-id-card" aria-hidden="true" style="font-size:11px"></i> CNI / Passeport</div>
                    <div class="dlg-field-val">${esc(a.cni ?? '—')}</div>
                </div>
                <div>
                    <div class="dlg-field-key"><i class="fas fa-home" aria-hidden="true" style="font-size:11px"></i> Adresse</div>
                    <div class="dlg-field-val">${esc(a.adresse ?? '—')}</div>
                </div>
                <div>
                    <div class="dlg-field-key"><i class="fas fa-phone" aria-hidden="true" style="font-size:11px"></i> Téléphone</div>
                    <div class="dlg-field-val">${esc(a.telephone ?? '—')}</div>
                </div>
                <div>
                    <div class="dlg-field-key"><i class="fas fa-envelope" aria-hidden="true" style="font-size:11px"></i> Email</div>
                    <div class="dlg-field-val">${esc(a.email ?? '—')}</div>
                </div>
                <div>
                    <div class="dlg-field-key"><i class="fas fa-credit-card" aria-hidden="true" style="font-size:11px"></i> Mode de paiement</div>
                    <div class="dlg-field-val">${esc(a.mode_paiement ?? '—')}</div>
                </div>
            </div>

            <!-- Message -->
            <hr class="dlg-hr">
            <p class="dlg-section">Message</p>
            <div class="dlg-message">${esc(a.message ?? '—')}</div>

            <!-- Statut -->
            <hr class="dlg-hr">
            <p class="dlg-section">Statut du dossier</p>
            <form id="dlg-status-form" class="dlg-status-form">
                <input type="hidden" name="action"      value="update_status">
                <input type="hidden" name="adhesion_id" value="${id}">
                <select name="status" class="dlg-select">${statusOptionsHtml(a.status)}</select>
                <button type="submit" class="dlg-btn dlg-btn-ghost" style="width:100%">
                    <i class="fas fa-check" aria-hidden="true"></i> Mettre à jour le statut
                </button>
            </form>

            <!-- Contrat -->
            <hr class="dlg-hr">
            <div class="dlg-contract-block">
                <div class="dlg-contract-title">
                    <i class="fas fa-file-alt" aria-hidden="true"></i> Contrat client
                </div>
                <form id="dlg-contract-form">
                    <input type="hidden" name="action"      value="save_contract">
                    <input type="hidden" name="adhesion_id" value="${id}">
                    <label class="dlg-label" for="dlg-contract-content">Contenu du contrat</label>
                    <textarea id="dlg-contract-content" name="contract_content" rows="6" class="dlg-textarea">${contractContent}</textarea>
                    <button type="submit" class="dlg-btn dlg-btn-ghost" style="width:100%;margin-top:8px">
                        <i class="fas fa-save" aria-hidden="true"></i> Enregistrer le contrat
                    </button>
                </form>
                <div class="dlg-export-row">
                    <a class="dlg-btn-export" href="${pdfHref}" target="_blank" rel="noopener">
                        <i class="fas fa-download" aria-hidden="true"></i> Exporter PDF
                    </a>
                    <button class="dlg-btn-export" id="dlg-send-contract-btn" data-id="${id}">
                        <i class="fas fa-envelope" aria-hidden="true"></i> Envoyer par mail
                    </button>
                </div>
            </div>

            <!-- Notes -->
            <hr class="dlg-hr">
            <p class="dlg-section">Notes internes</p>
            ${notesHtml}

            <!-- Ajouter note -->
            <form id="dlg-note-form" style="margin-top:1rem;display:grid;gap:8px">
                <input type="hidden" name="action"      value="add_note">
                <input type="hidden" name="adhesion_id" value="${id}">
                <label class="dlg-label" for="dlg-note-text">Nouvelle note</label>
                <textarea id="dlg-note-text" name="note" rows="3" class="dlg-textarea" placeholder="Ajouter une note interne…" required></textarea>
                <button type="submit" class="dlg-btn dlg-btn-ghost" style="width:100%">
                    <i class="fas fa-plus" aria-hidden="true"></i> Ajouter la note
                </button>
            </form>

        </div>

        <!-- Footer -->
        <div class="dlg-footer">
            <button class="dlg-btn dlg-btn-danger" id="dlg-delete-btn" data-id="${id}">
                <i class="fas fa-trash" aria-hidden="true"></i> Supprimer
            </button>
            <button class="dlg-btn dlg-btn-primary" onclick="closeDialogPublic()">
                <i class="fas fa-times" aria-hidden="true"></i> Fermer
            </button>
        </div>`;

        /* ── Bind events après injection ── */
        document.getElementById('dlg-close-btn').addEventListener('click', closeDialog);

        /* Statut */
        document.getElementById('dlg-status-form').addEventListener('submit', function (e) {
            e.preventDefault();
            postForm(new FormData(this), 'Statut mis à jour.', 'Erreur lors de la mise à jour.');
        });

        /* Contrat */
        document.getElementById('dlg-contract-form').addEventListener('submit', function (e) {
            e.preventDefault();
            postForm(new FormData(this), 'Contrat enregistré.', 'Erreur lors de l\'enregistrement.');
        });

        /* Envoyer contrat */
        document.getElementById('dlg-send-contract-btn').addEventListener('click', function () {
            const fd = new FormData();
            fd.set('action', 'send_contract');
            fd.set('adhesion_id', this.dataset.id);
            postForm(fd, 'Contrat envoyé par mail.', 'Erreur lors de l\'envoi.');
        });

        /* Note */
        document.getElementById('dlg-note-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const fd = new FormData(this);
            postForm(fd, 'Note ajoutée.', 'Erreur lors de l\'ajout de la note.', () => {
                /* Recharger le dialog pour afficher la nouvelle note */
                openDialog(id);
            });
        });

        /* Supprimer */
        document.getElementById('dlg-delete-btn').addEventListener('click', function () {
            if (!confirm('Supprimer définitivement cette adhésion ?')) return;
            const fd = new FormData();
            fd.set('action', 'delete');
            fd.set('adhesion_id', this.dataset.id);
            postForm(fd, null, 'Erreur lors de la suppression.', () => {
                closeDialog();
                /* Retirer la ligne du tableau sans rechargement */
                const row = document.querySelector(`[data-dialog-open="${id}"]`)?.closest('tr');
                if (row) row.remove();
            });
        });
    }

    /* ── POST helper ── */
    function postForm(formData, successMsg, errorMsg, callback) {
        fetch(BASE_URL, { method: 'POST', body: formData })
            .then(r => {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                if (successMsg) showToast(successMsg, 'success');
                if (callback) callback();
            })
            .catch(() => showToast(errorMsg, 'error'));
    }

    /* ── Toast notifications ── */
    function showToast(msg, type) {
        const existing = document.getElementById('immo-toast');
        if (existing) existing.remove();

        const t = document.createElement('div');
        t.id = 'immo-toast';
        Object.assign(t.style, {
            position      : 'fixed',
            bottom        : '24px',
            right         : '24px',
            background    : type === 'success' ? '#EAF3DE' : '#FCEBEB',
            color         : type === 'success' ? '#27500A'  : '#791F1F',
            border        : `0.5px solid ${type === 'success' ? '#97C459' : '#F09595'}`,
            borderRadius  : '10px',
            padding       : '12px 18px',
            fontSize      : '13px',
            fontWeight    : '500',
            zIndex        : '10000',
            maxWidth      : '320px',
            lineHeight    : '1.4',
            boxShadow     : 'none',
            transition    : 'opacity 200ms ease',
        });
        t.textContent = msg;
        document.body.appendChild(t);
        setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 200); }, 3000);
    }

    /* ── Global pour le bouton fermer dans le footer ── */
    window.closeDialogPublic = closeDialog;

    /* ── Listeners globaux ── */
    overlay.addEventListener('click', e => { if (e.target === overlay) closeDialog(); });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && overlay.classList.contains('open')) closeDialog();
    });
    document.addEventListener('click', e => {
        const btn = e.target.closest('[data-dialog-open]');
        if (btn) openDialog(btn.dataset.dialogOpen);
    });

})();
</script>