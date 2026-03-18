<aside class="sidebar">
    <div class="sidebar-brand">
        <h2>ECOFI</h2>
        <p>Administration</p>
    </div>

    <nav class="sidebar-menu">
        <a href="index.php?page=dashboard" class="menu-item <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i>
            <span>Tableau de bord</span>
        </a>

        <a href="index.php?page=auth" class="menu-item <?= $currentPage === 'auth' ? 'active' : '' ?>">
            <i class="fas fa-user-shield"></i>
            <span>Authentification & rôles</span>
        </a>

        <a href="index.php?page=clients" class="menu-item <?= $currentPage === 'clients' ? 'active' : '' ?>">
            <i class="fas fa-users"></i>
            <span>Clients & contacts</span>
        </a>

        <a href="index.php?page=products" class="menu-item <?= $currentPage === 'products' ? 'active' : '' ?>">
            <i class="fas fa-box"></i>
            <span>Produits & stock</span>
        </a>

        <a href="index.php?page=orders" class="menu-item <?= $currentPage === 'orders' ? 'active' : '' ?>">
            <i class="fas fa-file-invoice"></i>
            <span>Commandes & factures</span>
        </a>

        <a href="index.php?page=employees" class="menu-item <?= $currentPage === 'employees' ? 'active' : '' ?>">
            <i class="fas fa-user-tie"></i>
            <span>Personnel</span>
        </a>

        <a href="index.php?page=notifications" class="menu-item <?= $currentPage === 'notifications' ? 'active' : '' ?>">
            <i class="fas fa-bell"></i>
            <span>Notifications</span>
        </a>
    </nav>
</aside>