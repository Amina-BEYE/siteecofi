<?php
$categories  = $categories ?? [];
$products    = $products ?? [];
$message     = $message ?? null;
$messageType = $messageType ?? 'success';
?>

<?php if (!empty($message)): ?>
    <div class="alert <?= $messageType === 'error' ? 'error' : 'success' ?>">
        <i class="fas <?= $messageType === 'error' ? 'fa-exclamation-triangle' : 'fa-check-circle' ?>"></i>
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<section class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-folder"></i></div>
        <div>
            <p>Catégories</p>
            <h3><?= count($categories) ?></h3>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-box"></i></div>
        <div>
            <p>Produits</p>
            <h3><?= count($products) ?></h3>
        </div>
    </div>
</section>

<section class="row">
    <div class="card">
        <h2>Ajouter une catégorie</h2>

        <form method="POST" action="adminPage.php?page=products">
            <input type="hidden" name="action" value="add_category">

            <div class="form-group">
                <label for="category_nom">Nom de la catégorie</label>
                <input type="text" id="category_nom" name="nom" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="category_description">Description</label>
                <textarea id="category_description" name="description" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn">
                <i class="fas fa-plus"></i>
                Ajouter
            </button>
        </form>
    </div>

    <div class="card">
        <h2>Ajouter un produit</h2>

        <form method="POST" action="adminPage.php?page=products">
            <input type="hidden" name="action" value="add_product">

            <div class="form-group">
                <label for="categorie_id">Catégorie</label>
                <select id="categorie_id" name="categorie_id" class="form-control" required>
                    <option value="">Choisir</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int) $cat['id'] ?>">
                            <?= htmlspecialchars($cat['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="product_nom">Nom du produit</label>
                <input type="text" id="product_nom" name="nom" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="product_description">Description</label>
                <textarea id="product_description" name="description" class="form-control"></textarea>
            </div>

            <div class="form-group">
                <label for="product_prix">Prix</label>
                <input type="number" id="product_prix" name="prix" class="form-control" min="0" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="ancien_prix">Ancien prix</label>
                <input type="number" id="ancien_prix" name="ancien_prix" class="form-control" min="0" step="0.01">
            </div>

            <div class="form-group">
                <label for="image">Image</label>
                <input type="text" id="image" name="image" class="form-control" placeholder="nom-image.jpg ou URL">
            </div>

            <div class="form-group">
                <label for="note">Note</label>
                <input type="number" id="note" name="note" class="form-control" min="0" max="5" step="0.01">
            </div>

            <div class="form-group">
                <label for="nb_avis">Nombre d’avis</label>
                <input type="number" id="nb_avis" name="nb_avis" class="form-control" min="0" value="0">
            </div>

            <div class="form-group">
                <label for="type_media">Type de média</label>
                <select id="type_media" name="type_media" class="form-control">
                    <option value="image">Image</option>
                    <option value="video">Vidéo</option>
                </select>
            </div>

            <div class="form-group">
                <label for="media_src">Source média</label>
                <input type="text" id="media_src" name="media_src" class="form-control" placeholder="URL ou chemin du média">
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="actif" value="1" checked>
                    Produit actif
                </label>
            </div>

            <button type="submit" class="btn">
                <i class="fas fa-plus"></i>
                Ajouter
            </button>
        </form>
    </div>
</section>

<section class="card">
    <h2>Liste des produits</h2>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $prod): ?>
                        <tr>
                            <td><?= (int) $prod['id'] ?></td>
                            <td><?= htmlspecialchars($prod['nom']) ?></td>
                            <td><?= htmlspecialchars($prod['categorie_nom']) ?></td>
                            <td><?= htmlspecialchars((string) $prod['prix']) ?> FCFA</td>
                            <td>
                                <?php if ((int) $prod['actif'] === 1): ?>
                                    <span class="badge badge-success">Actif</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactif</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Aucun produit trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>