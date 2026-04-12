<?php
session_start();

require_once __DIR__ . '/../../Core/Router.php';

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
    <link rel="stylesheet" href="/SITEECOFI/app/admin/asserts/css/stylePageAdmin.css">

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

    <script>
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
    </script>
</body>
</html>