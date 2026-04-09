<?php
// sidebar.php
$currentPage = $currentPage ?? 'dashboard';
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="logo-container">
            <img src="/SITEECOFI/app/IMG/logo-ecofi.png" alt="ECOFI Logo" class="sidebar-logo">
        </div>
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