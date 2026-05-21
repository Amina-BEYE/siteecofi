<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../../Core/Router.php';
require_once __DIR__ . '/../Models/AccessControlModel.php';

if (!empty($_SESSION['admin_role'])) {
    (new AccessControlModel())->loadSessionFeatures((string) $_SESSION['admin_role']);
}

$page = $_GET['page'] ?? 'dashboard';

$router = new Router();
$data = $router->resolve($page);

$currentPage = $data['currentPage'] ?? 'dashboard';
$pageTitle   = $data['pageTitle'] ?? 'Administration ECOFI';
$view        = $data['view'] ?? 'dashboardv2.php';

extract($data);

// sécurisation
$view = ltrim($view, '/\\');
$viewPath = __DIR__ . '/' . $view;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="stylePageAdmin.css">

    <script>

    function toggleUserMenu(event) {
        event.stopPropagation();

        const dropdown = document.getElementById('userDropdown');
        const trigger = document.querySelector('.user-trigger');

        if (dropdown) {
            const isOpen = dropdown.classList.contains('show');
            dropdown.classList.toggle('show');

            if (trigger) {
                trigger.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
            }
        }
    }

    document.addEventListener('click', function (event) {
        const userMenu = document.querySelector('.user-menu');
        const dropdown = document.getElementById('userDropdown');
        const trigger = document.querySelector('.user-trigger');

        if (!userMenu || !dropdown) return;

        if (!userMenu.contains(event.target)) {
            dropdown.classList.remove('show');

            if (trigger) {
                trigger.setAttribute('aria-expanded', 'false');
            }
        }
    });

    function closeDialog() {
        const dialog = document.getElementById('dialogOverlay');
        if (dialog) {
            dialog.style.display = 'none';
        }
    }
    </script>
</head>
<body>
 <?php include __DIR__ . '/sidebar.php'; ?>

        <div class="main-wrapper">
            <?php include __DIR__ . '/topbar.php'; ?>

            <main class="main-content">
                <?php if (!empty($message)): ?>
                    <div class="alert <?= $messageType === 'error' ? 'error' : 'success' ?>">
                        <i class="fas <?= $messageType === 'error' ? 'fa-exclamation-triangle' : 'fa-check-circle' ?>"></i>
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <?php if (file_exists($viewPath)): ?>
                    <?php include $viewPath; ?>
                <?php else: ?>
                    <div class="card">
                        <h2>
                            <i class="fas fa-exclamation-triangle" style="color: var(--accent-color);"></i>
                            Vue introuvable
                        </h2>
                        <p>Le fichier suivant n'existe pas : <?= htmlspecialchars($viewPath) ?></p>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>


    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    <div class="page-loading-overlay" id="pageLoadingOverlay" aria-live="polite" aria-hidden="true">
        <div class="page-loader">
            <span class="loader-spinner"></span>
            <span id="pageLoadingText">Traitement en cours...</span>
        </div>
    </div>

    <script>
        const pageLoadingOverlay = document.getElementById('pageLoadingOverlay');
        const pageLoadingText = document.getElementById('pageLoadingText');

        function showPageLoader(message = 'Traitement en cours...') {
            if (!pageLoadingOverlay) return;
            if (pageLoadingText) {
                pageLoadingText.textContent = message;
            }
            pageLoadingOverlay.classList.add('is-visible');
            pageLoadingOverlay.setAttribute('aria-hidden', 'false');
        }

        function hidePageLoader() {
            if (!pageLoadingOverlay) return;
            pageLoadingOverlay.classList.remove('is-visible');
            pageLoadingOverlay.setAttribute('aria-hidden', 'true');
        }

        window.addEventListener('pageshow', hidePageLoader);

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar) {
                sidebar.classList.toggle('active');
                if (overlay) {
                    overlay.classList.toggle('active');
                }
                
                if (sidebar.classList.contains('active')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            }
        }

        document.querySelectorAll('.menu-item').forEach(link => {
            link.addEventListener('click', () => {
                showPageLoader('Chargement de la page...');

                if (window.innerWidth <= 768) {
                    setTimeout(() => {
                        toggleSidebar();
                    }, 150);
                }
            });
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                if (sidebar) {
                    sidebar.classList.remove('active');
                }
                if (overlay) {
                    overlay.classList.remove('active');
                }
                document.body.style.overflow = '';
            }
        });

        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function () {
                if (form.dataset.noLoader === 'true') return;

                const submitter = document.activeElement && document.activeElement.matches('button[type="submit"], input[type="submit"]')
                    ? document.activeElement
                    : form.querySelector('button[type="submit"], input[type="submit"]');

                form.classList.add('is-submitting');

                if (submitter) {
                    submitter.classList.add('is-loading');
                    submitter.disabled = true;
                }

                showPageLoader(form.dataset.loadingText || 'Enregistrement en cours...');
            });
        });

        function normalizeAdminSearch(value) {
            return (value || '')
                .toString()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .trim();
        }

        function applyAdminSearch(input) {
            const targetSelector = input.dataset.target || '';
            const items = targetSelector ? document.querySelectorAll(targetSelector) : [];
            const query = normalizeAdminSearch(input.value);
            let visibleCount = 0;

            items.forEach(item => {
                const table = item.closest('table');
                const activeStatus = table ? (table.dataset.adminStatusFilter || 'toutes') : 'toutes';
                const itemStatus = item.dataset.status || 'inconnu';
                const matchesStatus = activeStatus === 'toutes' || itemStatus === activeStatus;
                const haystack = normalizeAdminSearch((item.dataset.search || '') + ' ' + item.textContent);
                const matchesSearch = !query || haystack.includes(query);
                const shouldShow = matchesStatus && matchesSearch;

                item.style.display = shouldShow ? '' : 'none';
                if (shouldShow) {
                    visibleCount++;
                }
            });

            const emptyId = input.dataset.emptyId;
            const emptyState = emptyId ? document.getElementById(emptyId) : null;
            if (emptyState) {
                emptyState.classList.toggle('is-visible', visibleCount === 0 && items.length > 0);
            }
        }

        window.applyAdminSearch = applyAdminSearch;

        document.querySelectorAll('[data-admin-search]').forEach(input => {
            const emptyState = document.createElement('p');
            emptyState.className = 'admin-search-empty';
            emptyState.textContent = 'Aucun résultat pour cette recherche.';
            emptyState.id = 'admin-search-empty-' + Math.random().toString(36).slice(2);
            input.dataset.emptyId = emptyState.id;

            const toolbar = input.closest('.admin-list-toolbar');
            if (toolbar && toolbar.parentNode) {
                toolbar.parentNode.insertBefore(emptyState, toolbar.nextSibling);
            }

            input.addEventListener('input', () => applyAdminSearch(input));
        });

        document.querySelectorAll('a[href]').forEach(link => {
            link.addEventListener('click', function () {
                const href = link.getAttribute('href') || '';
                const isExternal = link.target === '_blank' || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript:');

                if (isExternal) return;

                if (href.includes('adminPage.php') || href.includes('logout.php')) {
                    showPageLoader('Chargement...');
                }
            });
        });
    </script>
</body>
</html>
