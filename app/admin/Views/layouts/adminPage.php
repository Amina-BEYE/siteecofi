<?php
// adminPage.php
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

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
    
    <style>
        /* ===== VARIABLES GLOBALES ECOFI ===== */
        :root {
            --primary-color: #333333;
            --accent-color: #FF8533;
            --light-color: #f8f9fa;
            --dark-color: #333333;
            --gray-color: #666666;
            --transition: all 0.3s ease;
            --shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 15px 30px rgba(0, 0, 0, 0.15);
            --radius: 8px;
            --sidebar-width: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--light-color);
            color: var(--dark-color);
            overflow-x: hidden;
        }

        /* ========== SIDEBAR STYLES ========== */
        .dashboard-shell {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: var(--transition);
            box-shadow: var(--shadow);
            z-index: 100;
        }

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--accent-color);
            border-radius: 10px;
        }

        .sidebar-brand {
            padding: 30px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
            text-align: center;
        }

        .logo-container {
            margin-bottom: 15px;
        }

        .sidebar-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 50%;
            background: white;
            padding: 10px;
            transition: var(--transition);
        }

        .sidebar-logo:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(255, 133, 51, 0.3);
        }

        .sidebar-brand h2 {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent-color) 0%, #ff6b1a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 5px;
        }

        .sidebar-brand p {
            font-size: 12px;
            opacity: 0.7;
            letter-spacing: 2px;
        }

        .sidebar-menu {
            padding: 0 15px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 12px;
            transition: var(--transition);
            font-weight: 500;
            font-size: 14px;
        }

        .menu-item i {
            width: 20px;
            font-size: 18px;
        }

        .menu-item:hover {
            background: rgba(255, 133, 51, 0.2);
            color: var(--accent-color);
            transform: translateX(5px);
        }

        .menu-item.active {
            background: linear-gradient(135deg, var(--accent-color), #ff6b1a);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 133, 51, 0.3);
        }

        /* ========== MAIN CONTENT ========== */
        .main-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: var(--light-color);
        }

        /* ========== TOPBAR STYLES ========== */
        .topbar {
            background: white;
            padding: 15px 30px;
            border-bottom: 1px solid #e1e8ed;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            position: sticky;
            top: 0;
            z-index: 99;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* Icône de page dans la topbar */
        .page-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--accent-color), #ff6b1a);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .page-icon i {
            font-size: 1.3rem;
            color: white;
        }

        .page-icon:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(255, 133, 51, 0.3);
        }

        .menu-toggle {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--primary-color);
            cursor: pointer;
            display: none;
            transition: var(--transition);
            width: 40px;
            height: 40px;
            border-radius: 8px;
        }

        .menu-toggle:hover {
            background: var(--light-color);
            color: var(--accent-color);
        }

        .topbar-left h1 {
            font-size: 22px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color), #555);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .welcome {
            color: var(--gray-color);
            font-size: 14px;
            font-weight: 500;
        }

        .topbar-icons {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .topbar-icons i {
            font-size: 1.2rem;
            color: var(--gray-color);
            cursor: pointer;
            transition: var(--transition);
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .topbar-icons i:hover {
            color: var(--accent-color);
            background: rgba(255, 133, 51, 0.1);
            transform: translateY(-2px);
        }

        /* ========== MAIN CONTENT AREA ========== */
        .main-content {
            padding: 30px;
        }

        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-weight: 500;
            animation: slideDown 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert.success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert.error {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: var(--transition);
            margin-bottom: 25px;
            border: 1px solid #eef2f6;
        }

        .card:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: var(--transition);
            border: 1px solid #eef2f6;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
            border-color: var(--accent-color);
        }

        .stat-icon {
            width: 65px;
            height: 65px;
            background: linear-gradient(135deg, var(--accent-color), #ff6b1a);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
        }

        .stat-info h3 {
            font-size: 13px;
            color: var(--gray-color);
            margin-bottom: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-info p {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
        }

        /* Tables */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: var(--light-color);
            padding: 14px;
            text-align: left;
            font-weight: 600;
            color: var(--primary-color);
            border-bottom: 2px solid #e1e8ed;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #eef2f6;
            color: #34495e;
        }

        tr:hover {
            background: #fafbfc;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            background: linear-gradient(135deg, var(--accent-color), #ff6b1a);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 133, 51, 0.3);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--accent-color);
            color: var(--accent-color);
        }

        .btn-outline:hover {
            background: var(--accent-color);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }

        .btn-danger:hover {
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
        }

        /* Form elements */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #eef2f6;
            border-radius: 10px;
            font-size: 14px;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(255, 133, 51, 0.1);
        }

        /* Grid layouts */
        .row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 1000;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
            }

            .menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .row {
                grid-template-columns: 1fr;
            }

            .topbar {
                padding: 12px 20px;
            }

            .topbar-left h1 {
                font-size: 18px;
            }

            .welcome {
                display: none;
            }

            .main-content {
                padding: 20px;
            }

            .page-icon {
                width: 35px;
                height: 35px;
            }

            .page-icon i {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .topbar-left h1 {
                font-size: 16px;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                font-size: 22px;
            }

            .stat-info p {
                font-size: 22px;
            }
        }

        /* ========== ANIMATIONS ========== */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.3s ease;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99;
        }

        .sidebar-overlay.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="dashboard-shell">
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <div class="main-wrapper">
            <?php include __DIR__ . '/topbar.php'; ?>

            <main class="main-content fade-in">
                <?php if (!empty($message)): ?>
                    <div class="alert <?= ($messageType ?? '') === 'error' ? 'error' : 'success' ?>">
                        <i class="fas <?= ($messageType ?? '') === 'error' ? 'fa-exclamation-triangle' : 'fa-check-circle' ?>"></i>
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <?php
                $viewPath = __DIR__ . '' . $view;
                if (file_exists($viewPath)) {
                    include $viewPath;
                } else {
                    echo "<div class='card'><h2><i class='fas fa-exclamation-triangle' style='color: var(--accent-color);'></i> Vue introuvable</h2><p>Le fichier suivant n'existe pas : " . htmlspecialchars($viewPath) . "</p></div>";
                }
                ?>
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