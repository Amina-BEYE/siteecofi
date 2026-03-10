<?php
$currentPage = $currentPage ?? 'dashboard';
$pageTitle = $pageTitle ?? 'Administration ECOFI';
$view = $view ?? 'dashboardv2.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="../stylePageAdmin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="dashboard-shell">
        <aside class="sidebar" id="sidebar">
            <?php include __DIR__ . '/partials/sidebar.php'; ?>
        </aside>

        <div class="main-wrapper">
            <?php include __DIR__ . '/partials/topbar.php'; ?>

            <main class="main-content">
                <?php
                $viewPath = __DIR__ . '/' . $view;
                if (file_exists($viewPath)) {
                    include $viewPath;
                } else {
                    echo "<div class='card'><h2>Vue introuvable</h2></div>";
                }
                ?>
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
        }

        document.addEventListener('click', function (e) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.menu-toggle');

            if (window.innerWidth <= 900) {
                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            }
        });
    </script>
</body>
</html>