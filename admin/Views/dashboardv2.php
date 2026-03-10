<section class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-box"></i></div>
        <div>
            <p>Produits</p>
            <h3><?= count($products ?? []) ?></h3>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-folder"></i></div>
        <div>
            <p>Catégories</p>
            <h3><?= count($categories ?? []) ?></h3>
        </div>
    </div>
</section>

<div class="card">
    <h2>Bienvenue dans l’administration ECOFI</h2>
    <p>Utilisez le menu à gauche pour naviguer entre les modules.</p>
</div>