#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")" && pwd)"
TS="$(date +%Y%m%d_%H%M%S)"
BACKUP="$ROOT/backup_$TS"

echo "Création backup dans $BACKUP ..."
mkdir -p "$BACKUP"
cp -a "$ROOT/." "$BACKUP/"

echo "Structure cible : app/{Controllers,Core,Services,Models,Views} public admin/Views admin/assets config vendor"
mkdir -p "$ROOT/app/Controllers" "$ROOT/app/Core" "$ROOT/app/Services" "$ROOT/app/Models" "$ROOT/app/Views"
mkdir -p "$ROOT/public"
mkdir -p "$ROOT/admin/Views/partials" "$ROOT/admin/assets/css" "$ROOT/admin/assets/js" "$ROOT/admin/assets/img"
mkdir -p "$ROOT/config"

# Déplace les fichiers existants s'ils existent (sans écraser)
mv_if_exists() {
  src="$1"; dst="$2"
  if [ -e "$src" ]; then
    echo "Déplacement $src -> $dst"
    mkdir -p "$(dirname "$dst")"
    if [ -e "$dst" ]; then
      echo "  (cible existe déjà : $dst) -> skip"
    else
      mv "$src" "$dst"
    fi
  fi
}

# exemples de déplacements sûrs (adaptables)
mv_if_exists "$ROOT/admin/index.php" "$ROOT/admin/index.php"
mv_if_exists "$ROOT/admin/Views" "$ROOT/admin/Views"
mv_if_exists "$ROOT/app/Controllers/AdminController.php" "$ROOT/app/Controllers/AdminController.php"
mv_if_exists "$ROOT/public/index.php" "$ROOT/public/index.php"
mv_if_exists "$ROOT/public/app.js" "$ROOT/public/app.js"
mv_if_exists "$ROOT/stylePageAdmin.css" "$ROOT/admin/assets/css/stylePageAdmin.css"

# Crée composer.json si absent
if [ ! -f "$ROOT/composer.json" ]; then
  echo "Création composer.json"
  cat > "$ROOT/composer.json" <<'JSON'
{
  "name": "ecofi/site",
  "autoload": {
    "psr-4": {
      "App\\\": "app/"
    }
  },
  "require": {}
}
JSON
fi

# fichier de config minimal
if [ ! -f "$ROOT/config/config.php" ]; then
  echo "Création config/config.php"
  cat > "$ROOT/config/config.php" <<'PHP'
<?php
return [
    'db' => [
        'dsn' => 'mysql:host=127.0.0.1;dbname=siteecofi;charset=utf8mb4',
        'user' => 'root',
        'pass' => 'root'
    ],
    'admin' => [
        'user' => 'admin@example.com',
        'pass' => 'admin'
    ]
];
PHP
fi

# Core Controller (si absent)
if [ ! -f "$ROOT/app/Core/Controller.php" ]; then
  echo "Création app/Core/Controller.php"
  cat > "$ROOT/app/Core/Controller.php" <<'PHP'
<?php
namespace App\Core;
class Controller {
    protected function clean($value): string {
        return is_array($value) ? '' : trim(strip_tags((string)$value));
    }
    protected function toNullableDecimal($value) {
        $v = $this->clean($value);
        if ($v === '') return null;
        $v = str_replace([' ', ','], ['', '.'], $v);
        return is_numeric($v) ? (float)$v : null;
    }
}
PHP
fi

# Database singleton
if [ ! -f "$ROOT/app/Core/Database.php" ]; then
  echo "Création app/Core/Database.php"
  cat > "$ROOT/app/Core/Database.php" <<'PHP'
<?php
namespace App\Core;
class Database {
    private static ?\PDO $pdo = null;
    public static function getInstance(): \PDO {
        if (self::$pdo !== null) return self::$pdo;
        $config = require __DIR__ . '/../../config/config.php';
        $db = $config['db'];
        $options = [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC];
        self::$pdo = new \PDO($db['dsn'],$db['user'],$db['pass'],$options);
        return self::$pdo;
    }
}
PHP
fi

# AdminService minimal (si absent)
if [ ! -f "$ROOT/app/Services/AdminService.php" ]; then
  echo "Création app/Services/AdminService.php"
  cat > "$ROOT/app/Services/AdminService.php" <<'PHP'
<?php
namespace App\Services;
use App\Core\Database;
use PDO;
class AdminService {
    private PDO $pdo;
    public function __construct(){ $this->pdo = Database::getInstance(); }
    public function getCategories(): array {
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY id DESC");
        return $stmt->fetchAll();
    }
    public function getProducts(): array {
        $stmt = $this->pdo->query("SELECT p.*, c.nom AS categorie_nom FROM produits p LEFT JOIN categories c ON p.categorie_id = c.id ORDER BY p.id DESC");
        return $stmt->fetchAll();
    }
    // autres méthodes CRUD peuvent être ajoutées ensuite
}
PHP
fi

# Front controller admin (si absent)
if [ ! -f "$ROOT/admin/index.php" ]; then
  echo "Création admin/index.php"
  cat > "$ROOT/admin/index.php" <<'PHP'
<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();
use App\Controllers\AdminController;
$controller = new AdminController();
$data = $controller->handle();
extract($data, EXTR_SKIP);
require __DIR__ . '/Views/admin_layout.php';
PHP
fi

# Layout admin safe template (écrase si manquant)
if [ ! -f "$ROOT/admin/Views/admin_layout.php" ]; then
  echo "Création admin/Views/admin_layout.php"
  cat > "$ROOT/admin/Views/admin_layout.php" <<'PHP'
<?php
$currentPage = $currentPage ?? 'dashboard';
$pageTitle = $pageTitle ?? 'Administration ECOFI';
$view = $view ?? 'dashboardv2.php';
$baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$viewsDir = realpath(__DIR__);
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($pageTitle) ?></title>
<link rel="stylesheet" href="<?= htmlspecialchars($baseUrl) ?>/admin/assets/css/stylePageAdmin.css">
</head>
<body>
<div class="dashboard-shell">
  <aside id="sidebar"><?php if(file_exists($viewsDir.'/partials/sidebar.php')) include $viewsDir.'/partials/sidebar.php'; else echo '<div class=card>Sidebar manquante</div>'; ?></aside>
  <div class="main-wrapper">
    <?php if(file_exists($viewsDir.'/partials/topbar.php')) include $viewsDir.'/partials/topbar.php'; ?>
    <main class="main-content">
      <?php if(!empty($message)): ?><div class="alert <?= ($messageType ?? '') === 'error' ? 'error' : 'success' ?>"><?= htmlspecialchars($message) ?></div><?php endif; ?>
      <?php $vp = realpath($viewsDir.'/'.$view); if($vp && str_starts_with($vp,$viewsDir.DIRECTORY_SEPARATOR)) include $vp; else echo '<div class=card><h2>Vue introuvable</h2></div>'; ?>
    </main>
  </div>
</div>
<script src="<?= htmlspecialchars($baseUrl) ?>/admin/assets/js/admin.js" defer></script>
</body>
</html>
PHP
fi

# placeholder partials
if [ ! -f "$ROOT/admin/Views/partials/sidebar.php" ]; then
  cat > "$ROOT/admin/Views/partials/sidebar.php" <<'PHP'
<?php $currentPage = $currentPage ?? 'dashboard'; ?>
<nav class="admin-sidebar-nav"><div class="brand"><a href="?page=dashboard">ECOFI Admin</a></div>
<ul class="nav-list">
<li class="<?= $currentPage==='dashboard'?'active':'' ?>"><a href="?page=dashboard">Tableau de bord</a></li>
<li class="<?= $currentPage==='products'?'active':'' ?>"><a href="?page=products">Produits</a></li>
<li class="<?= $currentPage==='users'?'active':'' ?>"><a href="?page=users">Utilisateurs</a></li>
<li class="<?= $currentPage==='settings'?'active':'' ?>"><a href="?page=settings">Paramètres</a></li>
</ul></nav>
PHP
fi

if [ ! -f "$ROOT/admin/Views/partials/topbar.php" ]; then
  cat > "$ROOT/admin/Views/partials/topbar.php" <<'PHP'
<header class="admin-topbar"><button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button>
<div class="topbar-title"><?= htmlspecialchars($pageTitle ?? 'Admin') ?></div></header>
PHP
fi

# placeholder admin.js + css if absent
if [ ! -f "$ROOT/admin/assets/js/admin.js" ]; then
  cat > "$ROOT/admin/assets/js/admin.js" <<'JS'
function toggleSidebar(){const sb=document.getElementById('sidebar'); if(sb) sb.classList.toggle('open');}
document.addEventListener('keydown', e=>{ if(e.key==='Escape'){ const sb=document.getElementById('sidebar'); if(sb) sb.classList.remove('open'); }});
JS
fi

if [ ! -f "$ROOT/admin/assets/css/stylePageAdmin.css" ]; then
  cat > "$ROOT/admin/assets/css/stylePageAdmin.css" <<'CSS'
/* style minimal admin */
body{font-family:Arial,Helvetica,sans-serif;background:#f5f7fb;margin:0}
.dashboard-shell{display:flex;min-height:100vh}
.sidebar{width:240px;background:#0b1220;color:#fff;padding:18px}
.main-wrapper{flex:1}
.admin-topbar{padding:12px 18px;border-bottom:1px solid #eee}
.main-content{padding:20px}
.card{background:#fff;padding:14px;border-radius:8px}
.alert.success{background:#dcfce7;padding:10px;border-radius:8px;margin-bottom:12px}
.alert.error{background:#fee2e2;padding:10px;border-radius:8px;margin-bottom:12px}
CSS
fi

echo "Réorganisation terminée. Backup : $BACKUP"
echo "Vérifiez les fichiers, puis exécutez 'composer dump-autoload' si nécessaire."
echo "Si vous voulez que je génère d'autres fichiers CRUD/migrations, dites‑le."
exit 0
