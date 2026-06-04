<?php
/** @var array $actualites */
?>
<div class="card">
    <div class="card-header">
        <h2>Gestion des actualités</h2>
        <p>Ajoutez des informations de la page Actualités et publiez-les directement.</p>
    </div>

    <form method="post" class="card-form" data-loading-text="Ajout de l'actualité...">
        <input type="hidden" name="action" value="add_actualite">

        <div class="form-grid">
            <div class="form-group">
                <label for="title">Titre</label>
                <input id="title" name="title" type="text" required placeholder="Titre de l'actualité">
            </div>

            <div class="form-group">
                <label for="subtitle">Sous-titre</label>
                <input id="subtitle" name="subtitle" type="text" placeholder="Petit sous-titre / accroche">
            </div>

            <div class="form-group">
                <label for="category">Catégorie</label>
                <input id="category" name="category" type="text" placeholder="Ex: Terrain, Projet, Actualité" value="Actualité">
            </div>

            <div class="form-group">
                <label for="status">Statut</label>
                <select id="status" name="status">
                    <option value="published">Publié</option>
                    <option value="draft">Brouillon</option>
                </select>
            </div>

            <div class="form-group">
                <label for="published_at">Date de publication</label>
                <input id="published_at" name="published_at" type="date">
            </div>

            <div class="form-group">
                <label for="image">Image (URL ou chemin)</label>
                <input id="image" name="image" type="text" placeholder="app/IMG/mon-image.jpg">
            </div>

            <div class="form-group">
                <label for="video">Vidéo (URL ou chemin)</label>
                <input id="video" name="video" type="text" placeholder="app/IMG/ma-video.mp4">
            </div>

            <div class="form-group form-group-full">
                <label for="content">Contenu / description</label>
                <textarea id="content" name="content" rows="5" required placeholder="Description détaillée de l'actualité..."></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Ajouter l'actualité</button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h2>Liste des actualités</h2>
        <p>Visualisez les actualités déjà enregistrées en base.</p>
    </div>

    <?php if (empty($actualites)): ?>
        <p>Aucune actualité enregistrée pour le moment.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($actualites as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['id']) ?></td>
                            <td><?= htmlspecialchars($item['title']) ?></td>
                            <td><?= htmlspecialchars($item['category']) ?></td>
                            <td><?= htmlspecialchars(ucfirst($item['status'])) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($item['published_at'] ?? 'now'))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
