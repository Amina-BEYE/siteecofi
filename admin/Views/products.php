<?php if (!empty($message)): ?>
    <div class="alert <?= $messageType === 'error' ? 'error' : 'success' ?>">
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

<section class="content-grid">
    <div class="card">
        <h2>Ajouter une catégorie</h2>
        <form method="POST" action="index.php?page=products">
            <input type="hidden" name="action" value="add_category">

            <div>
                <label>Nom de la catégorie</label>
                <input type="text" name="nom" required>
            </div>

            <div>
                <label>Description</label>
                <textarea name="description"></textarea>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Ajouter un produit</h2>
        <form method="POST" action="index.php?page=products">
            <input type="hidden" name="action" value="add_product">

            <div class="row-2">
                <div>
                    <label>Catégorie</label>
                    <select name="categorie_id" required>
                        <option value="">Choisir</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int)$cat['id'] ?>">
                                <?= htmlspecialchars($cat['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label>Nom du produit</label>
                    <input type="text" name="nom" required>
                </div>
            </div>

            <div>
                <label>Description</label>
                <textarea name="description"></textarea>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </div>
        </form>
    </div>
</section>

<section class="card table-card">
    <h2>Liste des produits</h2>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $prod): ?>
                    <tr>
                        <td><?= (int)$prod['id'] ?></td>
                        <td><?= htmlspecialchars($prod['nom']) ?></td>
                        <td><?= htmlspecialchars($prod['categorie_nom']) ?></td>
                        <td><?= htmlspecialchars((string)$prod['prix']) ?> FCFA</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>