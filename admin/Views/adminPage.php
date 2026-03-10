<?php

require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Controller.php';
require_once __DIR__ . '/../app/Repositories/CategoryRepository.php';
require_once __DIR__ . '/../app/Repositories/ProductRepository.php';
require_once __DIR__ . '/../app/Services/AdminService.php';
require_once __DIR__ . '/../app/Controllers/AdminController.php';

use App\Controllers\AdminController;

$controller = new AdminController();
$data = $controller->handle();

$message = $data['message'];
$messageType = $data['messageType'];
$categories = $data['categories'];
$products = $data['products'];
$editCategory = $data['editCategory'];
$editProduct = $data['editProduct'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin ECOFI</title>
    <link rel="stylesheet" href="stylePageAdmin.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            color: #111827;
        }
        .topbar {
            background: #111827;
            color: white;
            padding: 20px;
        }
        .topbar h1 {
            margin: 0;
            font-size: 28px;
        }
        .container {
            width: 95%;
            max-width: 1400px;
            margin: 24px auto;
        }
        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-weight: bold;
        }
        .alert.success {
            background: #dcfce7;
            color: #166534;
        }
        .alert.error {
            background: #fee2e2;
            color: #991b1b;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }
        .card {
            background: white;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        }
        .full {
            grid-column: 1 / -1;
        }
        h2 {
            margin-top: 0;
        }
        form {
            display: grid;
            gap: 12px;
        }
        .row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .row-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
        }
        input, textarea, select {
            width: 100%;
            box-sizing: border-box;
            padding: 11px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
        }
        textarea {
            min-height: 90px;
            resize: vertical;
        }
        .checkbox-line {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .checkbox-line input {
            width: auto;
        }
        .btn {
            border: none;
            border-radius: 8px;
            padding: 10px 14px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: #f97316;
            color: white;
        }
        .btn-secondary {
            background: #374151;
            color: white;
        }
        .btn-warning {
            background: #f59e0b;
            color: white;
        }
        .btn-danger {
            background: #dc2626;
            color: white;
        }
        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }
        table th, table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: top;
            font-size: 14px;
        }
        table th {
            background: #f9fafb;
        }
        .thumb {
            width: 70px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
            background: #e5e7eb;
        }
        @media (max-width: 1000px) {
            .grid, .row-2, .row-3 {
                grid-template-columns: 1fr;
            }
            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-shell">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <h2>ECOFI</h2>
                <p>Administration</p>
            </div>

            <nav class="sidebar-menu">
                <a href="dashboard.php" class="menu-item">
                    <span>📊</span>
                    <span>Tableau de bord</span>
                </a>

                <a href="auth.php" class="menu-item">
                    <span>🔐</span>
                    <span>Authentification & rôles</span>
                </a>

                <a href="clients.php" class="menu-item">
                    <span>👥</span>
                    <span>Clients & contacts</span>
                </a>

                <a href="products.php" class="menu-item active">
                    <span>📦</span>
                    <span>Produits & stock</span>
                </a>

                <a href="orders.php" class="menu-item">
                    <span>🧾</span>
                    <span>Commandes & factures</span>
                </a>

                <a href="employees.php" class="menu-item">
                    <span>🧑‍💼</span>
                    <span>Personnel</span>
                </a>

                <a href="notifications.php" class="menu-item">
                    <span>🔔</span>
                    <span>Notifications</span>
                </a>
            </nav>
        </aside>

        <!-- ZONE PRINCIPALE -->
        <div class="main-wrapper">
            <!-- TOPBAR -->
            <header class="topbar">
                <div class="topbar-left">
                    <h1>Tableau de bord</h1>
                </div>
                <div class="topbar-right">
                    <span class="welcome">Bienvenue : Admin ECOFI</span>
                    <div class="topbar-icons">
                        <span>🔔</span>
                        <span>👤</span>
                    </div>
                </div>
            </header>

            <!-- CONTENU -->
            <main class="main-content">
                <?php if ($message !== ''): ?>
                    <div class="alert <?= $messageType === 'error' ? 'error' : 'success' ?>">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <!-- CARDS STATS -->
                <section class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">📂</div>
                        <div>
                            <p>Catégories</p>
                            <h3><?= count($categories) ?></h3>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">📦</div>
                        <div>
                            <p>Produits</p>
                            <h3><?= count($products) ?></h3>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">✅</div>
                        <div>
                            <p>Produits actifs</p>
                            <h3><?= count(array_filter($products, fn($p) => (int)$p['actif'] === 1)) ?></h3>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">⭐</div>
                        <div>
                            <p>Total avis</p>
                            <h3><?= array_sum(array_map(fn($p) => (int)$p['nb_avis'], $products)) ?></h3>
                        </div>
                    </div>
                </section>

                <!-- FORMULAIRES -->
                <section class="content-grid">
                    <div class="card">
                        <h2><?= $editCategory ? 'Modifier une catégorie' : 'Ajouter une catégorie' ?></h2>

                        <form method="POST">
                            <input type="hidden" name="action" value="<?= $editCategory ? 'update_category' : 'add_category' ?>">
                            <?php if ($editCategory): ?>
                                <input type="hidden" name="id" value="<?= (int)$editCategory['id'] ?>">
                            <?php endif; ?>

                            <div>
                                <label>Nom de la catégorie</label>
                                <input type="text" name="nom" required value="<?= htmlspecialchars($editCategory['nom'] ?? '') ?>" placeholder="Ex: briques">
                            </div>

                            <div>
                                <label>Description</label>
                                <textarea name="description"><?= htmlspecialchars($editCategory['description'] ?? '') ?></textarea>
                            </div>

                            <div class="actions">
                                <button type="submit" class="btn btn-primary">
                                    <?= $editCategory ? 'Mettre à jour' : 'Ajouter' ?>
                                </button>
                                <?php if ($editCategory): ?>
                                    <a href="index.php" class="btn btn-secondary">Annuler</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <div class="card">
                        <h2><?= $editProduct ? 'Modifier un produit' : 'Ajouter un produit' ?></h2>

                        <form method="POST">
                            <input type="hidden" name="action" value="<?= $editProduct ? 'update_product' : 'add_product' ?>">
                            <?php if ($editProduct): ?>
                                <input type="hidden" name="id" value="<?= (int)$editProduct['id'] ?>">
                            <?php endif; ?>

                            <div class="row-2">
                                <div>
                                    <label>Catégorie</label>
                                    <select name="categorie_id" required>
                                        <option value="">Choisir</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= (int)$cat['id'] ?>" <?= $editProduct && (int)$editProduct['categorie_id'] === (int)$cat['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['nom']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label>Nom du produit</label>
                                    <input type="text" name="nom" required value="<?= htmlspecialchars($editProduct['nom'] ?? '') ?>">
                                </div>
                            </div>

                            <div>
                                <label>Description</label>
                                <textarea name="description"><?= htmlspecialchars($editProduct['description'] ?? '') ?></textarea>
                            </div>

                            <div class="row-2">
                                <div>
                                    <label>Prix</label>
                                    <input type="text" name="prix" value="<?= htmlspecialchars($editProduct['prix'] ?? '') ?>" placeholder="8500">
                                </div>
                                <div>
                                    <label>Ancien prix</label>
                                    <input type="text" name="ancien_prix" value="<?= htmlspecialchars($editProduct['ancien_prix'] ?? '') ?>" placeholder="9500">
                                </div>
                            </div>

                            <div class="row-3">
                                <div>
                                    <label>Image</label>
                                    <input type="text" name="image" value="<?= htmlspecialchars($editProduct['image'] ?? '') ?>" placeholder="IMG/pave1.jpeg">
                                </div>
                                <div>
                                    <label>Note</label>
                                    <input type="text" name="note" value="<?= htmlspecialchars($editProduct['note'] ?? '') ?>" placeholder="4.5">
                                </div>
                                <div>
                                    <label>Nombre d'avis</label>
                                    <input type="number" name="nb_avis" value="<?= htmlspecialchars($editProduct['nb_avis'] ?? 0) ?>">
                                </div>
                            </div>

                            <div class="row-2">
                                <div>
                                    <label>Type média</label>
                                    <select name="type_media">
                                        <option value="image" <?= ($editProduct['type_media'] ?? 'image') === 'image' ? 'selected' : '' ?>>image</option>
                                        <option value="video" <?= ($editProduct['type_media'] ?? '') === 'video' ? 'selected' : '' ?>>video</option>
                                    </select>
                                </div>
                                <div>
                                    <label>Source média</label>
                                    <input type="text" name="media_src" value="<?= htmlspecialchars($editProduct['media_src'] ?? '') ?>" placeholder="IMG/video2.mp4">
                                </div>
                            </div>

                            <label class="checkbox-line">
                                <input type="checkbox" name="actif" <?= !isset($editProduct['actif']) || (int)$editProduct['actif'] === 1 ? 'checked' : '' ?>>
                                Produit actif
                            </label>

                            <div class="actions">
                                <button type="submit" class="btn btn-primary">
                                    <?= $editProduct ? 'Mettre à jour' : 'Ajouter' ?>
                                </button>
                                <?php if ($editProduct): ?>
                                    <a href="index.php" class="btn btn-secondary">Annuler</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- TABLEAU CATEGORIES -->
                <section class="card table-card">
                    <h2>Liste des catégories</h2>

                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $cat): ?>
                                    <tr>
                                        <td><?= (int)$cat['id'] ?></td>
                                        <td><?= htmlspecialchars($cat['nom']) ?></td>
                                        <td><?= htmlspecialchars($cat['description']) ?></td>
                                        <td><?= htmlspecialchars($cat['created_at']) ?></td>
                                        <td>
                                            <div class="actions">
                                                <a class="btn btn-warning" href="?edit_category=<?= (int)$cat['id'] ?>">Modifier</a>

                                                <form method="POST" onsubmit="return confirm('Supprimer cette catégorie ?');">
                                                    <input type="hidden" name="action" value="delete_category">
                                                    <input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
                                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- TABLEAU PRODUITS -->
                <section class="card table-card">
                    <h2>Liste des produits</h2>

                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Nom</th>
                                    <th>Catégorie</th>
                                    <th>Prix</th>
                                    <th>Ancien prix</th>
                                    <th>Note</th>
                                    <th>Avis</th>
                                    <th>Média</th>
                                    <th>Actif</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $prod): ?>
                                    <tr>
                                        <td><?= (int)$prod['id'] ?></td>
                                        <td>
                                            <?php if (!empty($prod['image'])): ?>
                                                <img class="thumb" src="../<?= htmlspecialchars($prod['image']) ?>" alt="">
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($prod['nom']) ?></strong><br>
                                            <small><?= htmlspecialchars($prod['description']) ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($prod['categorie_nom']) ?></td>
                                        <td><?= htmlspecialchars((string)$prod['prix']) ?> FCFA</td>
                                        <td><?= htmlspecialchars((string)$prod['ancien_prix']) ?> FCFA</td>
                                        <td><?= htmlspecialchars((string)$prod['note']) ?></td>
                                        <td><?= (int)$prod['nb_avis'] ?></td>
                                        <td><span class="badge"><?= htmlspecialchars($prod['type_media']) ?></span></td>
                                        <td>
                                            <span class="status <?= (int)$prod['actif'] === 1 ? 'on' : 'off' ?>">
                                                <?= (int)$prod['actif'] === 1 ? 'Oui' : 'Non' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="actions">
                                                <a class="btn btn-warning" href="?edit_product=<?= (int)$prod['id'] ?>">Modifier</a>

                                                <form method="POST" onsubmit="return confirm('Supprimer ce produit ?');">
                                                    <input type="hidden" name="action" value="delete_product">
                                                    <input type="hidden" name="id" value="<?= (int)$prod['id'] ?>">
                                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>
</body>