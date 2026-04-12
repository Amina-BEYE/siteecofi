<?php
$pageTitle = $pageTitle ?? 'Administration ECOFI';
$adminName = $_SESSION['user_name'] ?? 'Admin ECOFI';
?>

<header class="topbar">
    <div class="topbar-left">
        <button class="menu-toggle" type="button" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>

        <div class="page-icon">
            <i class="fas fa-chart-line"></i>
        </div>

        <h1><?= htmlspecialchars($pageTitle) ?></h1>
    </div>

    <div class="topbar-right">
        <span class="welcome">Bienvenue : <?= htmlspecialchars($adminName) ?></span>

        <div class="topbar-icons">
            <button class="icon-btn" type="button" aria-label="Notifications">
                <i class="fas fa-bell"></i>
            </button>

            <div class="user-menu">
                <button
                    class="icon-btn user-trigger"
                    type="button"
                    onclick="toggleUserMenu(event)"
                    aria-label="Menu utilisateur"
                    aria-expanded="false"
                    aria-controls="userDropdown"
                >
                    <i class="fas fa-user-circle"></i>
                </button>

                <div class="dropdown-menu" id="userDropdown">
                    <div class="dropdown-header">
                        <strong><?= htmlspecialchars($adminName) ?></strong>
                        <small>Compte administrateur</small>
                    </div>

                    <a href="adminPage.php?page=profile" class="dropdown-item">
                        <i class="fas fa-user"></i>
                        <span>Mon profil</span>
                    </a>

                    <a href="adminPage.php?page=settings" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>

                    <div class="dropdown-divider"></div>

                    <a href="logout.php" class="dropdown-item logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Se déconnecter</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>