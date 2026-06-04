<?php
// sidebar.php
$currentPage = $currentPage ?? 'dashboard';
require_once __DIR__ . '/../Models/AccessControlModel.php';

$accessModel = new AccessControlModel();
$adminRole = (string) ($_SESSION['admin_role'] ?? 'agent');
$menuPages = $accessModel->getAccessiblePages($adminRole);

$menuGroups = [
    'Pilotage' => ['dashboard'],
    'Gestion commerciale' => ['clients', 'orders', 'programme-immo', 'actualites', 'payment-schedules'],
    'Catalogue' => ['products'],
    'Gestion personnel' => ['employees'],
    'Administration' => ['newsletter', 'messaging', 'auth', 'access-control', 'settings', 'profile', 'notifications'],
];
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="logo-container">
            <img src="../../IMG/logo-ecofi.png" alt="ECOFI Logo" class="sidebar-logo">
        </div>
        <h2>ECOFI</h2>
        <p>Administration</p>
    </div>

    <nav class="sidebar-menu">
        <a href="../../../index.php" class="menu-item menu-item--site">
            <i class="fas fa-arrow-left"></i>
            <span>Retour au site</span>
        </a>

        <?php foreach ($menuGroups as $groupLabel => $pageKeys): ?>
            <?php
            $groupItems = array_intersect_key($menuPages, array_flip($pageKeys));
            if (empty($groupItems)) {
                continue;
            }
            ?>
            <div class="menu-group">
                <div class="menu-group-title"><?= htmlspecialchars($groupLabel) ?></div>

                <?php foreach ($pageKeys as $pageKey): ?>
                    <?php if (empty($menuPages[$pageKey])) continue; ?>
                    <?php $pageConfig = $menuPages[$pageKey]; ?>
                    <a href="adminPage.php?page=<?= htmlspecialchars($pageKey) ?>" class="menu-item <?= $currentPage === $pageKey ? 'active' : '' ?>">
                        <i class="fas <?= htmlspecialchars($pageConfig['icon'] ?? 'fa-circle') ?>"></i>
                        <span><?= htmlspecialchars($pageConfig['label'] ?? $pageKey) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <?php
        $groupedPages = array_merge(...array_values($menuGroups));
        $ungroupedPages = array_diff_key($menuPages, array_flip($groupedPages));
        ?>
        <?php if (!empty($ungroupedPages)): ?>
            <div class="menu-group">
                <div class="menu-group-title">Autres</div>
                <?php foreach ($ungroupedPages as $pageKey => $pageConfig): ?>
                    <a href="adminPage.php?page=<?= htmlspecialchars($pageKey) ?>" class="menu-item <?= $currentPage === $pageKey ? 'active' : '' ?>">
                        <i class="fas <?= htmlspecialchars($pageConfig['icon'] ?? 'fa-circle') ?>"></i>
                        <span><?= htmlspecialchars($pageConfig['label'] ?? $pageKey) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </nav>
</aside>
