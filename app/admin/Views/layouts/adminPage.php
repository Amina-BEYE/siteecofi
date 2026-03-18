<?php
$currentPage = $currentPage ?? 'dashboard';
$pageTitle = $pageTitle ?? 'Administration ECOFI';
$view = $view ?? '/dashboardv2.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <link rel="stylesheet" href="/SITEECOFI/app/admin/asserts/css/stylePageAdmin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
</head>
<body>
    <div class="dashboard-shell">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <h2>ECOFI</h2>
                <p>Administration</p>
            </div>

            <nav class="sidebar-menu">
                <a href="/SITEECOFI/app/admin/admin.php?page=dashboard" class="menu-item <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i>
                    <span>Tableau de bord</span>
                </a>

                <a href="/SITEECOFI/app/admin/admin.php?page=auth" class="menu-item <?= $currentPage === 'auth' ? 'active' : '' ?>">
                    <i class="fas fa-user-shield"></i>
                    <span>Authentification & rôles</span>
                </a>

                <a href="/SITEECOFI/app/admin/admin.php?page=clients" class="menu-item <?= $currentPage === 'clients' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i>
                    <span>Clients & contacts</span>
                </a>

                <a href="/SITEECOFI/app/admin/admin.php?page=products" class="menu-item <?= $currentPage === 'products' ? 'active' : '' ?>">
                    <i class="fas fa-box"></i>
                    <span>Produits & stock</span>
                </a>

                <a href="/SITEECOFI/app/admin/admin.php?page=orders" class="menu-item <?= $currentPage === 'orders' ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice"></i>
                    <span>Commandes & factures</span>
                </a>

                <a href="/SITEECOFI/app/admin/admin.php?page=employees" class="menu-item <?= $currentPage === 'employees' ? 'active' : '' ?>">
                    <i class="fas fa-user-tie"></i>
                    <span>Personnel</span>
                </a>

                <a href="/SITEECOFI/app/admin/admin.php?page=notifications" class="menu-item <?= $currentPage === 'notifications' ? 'active' : '' ?>">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                </a>
            </nav>
        </aside>

        <div class="main-wrapper">
            <?php
            $topbarFile = __DIR__ . '/topbar.php';
            if (file_exists($topbarFile)) {
                include $topbarFile;
            } else {
                echo '<header class="admin-topbar"><h1>' . htmlspecialchars($pageTitle) . '</h1></header>';
            }
            ?>

            <main class="main-content">
                <?php if (!empty($message)): ?>
                    <div class="alert <?= ($messageType ?? '') === 'error' ? 'error' : 'success' ?>">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <?php
                $viewPath = __DIR__ . '' . $view;
                print_r($viewPath);
                if (file_exists($viewPath)) {
                    include $viewPath;
                } else {
                    echo "<div class='card'><h2>Vue introuvable</h2></div>";
                }
                ?>
            </main>
        </div>
    </div>


</body>
</html>