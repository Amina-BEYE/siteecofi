<header class="topbar">
    <div class="topbar-left">
        <button class="menu-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <h1><?= htmlspecialchars($pageTitle ?? 'Administration ECOFI') ?></h1>
    </div>

    <div class="topbar-right">
        <span class="welcome">Bienvenue : Admin ECOFI</span>
        <div class="topbar-icons">
            <i class="fas fa-bell"></i>
            <i class="fas fa-user-circle"></i>
        </div>
    </div>
</header>