<?php
$users = $users ?? [];
$roles = $roles ?? [];
?>

<section class="card">
    <div class="section-header">
        <div>
            <h2>Gestion du personnel</h2>
            <p>Liste des comptes internes rattachés à l’administration.</p>
        </div>
        <a class="btn btn-outline" href="adminPage.php?page=auth">
            <i class="fas fa-user-shield"></i>
            Gérer les accès
        </a>
    </div>

    <div class="admin-list-toolbar">
        <label class="admin-search-box">
            <i class="fas fa-search"></i>
            <input
                type="search"
                class="admin-search-input"
                data-admin-search
                data-target="#employeesTable tbody tr"
                placeholder="Rechercher par nom, prénom, email ou profil"
                aria-label="Rechercher un membre du personnel"
            >
        </label>
    </div>

    <div class="table-container">
        <table id="employeesTable">
            <thead>
                <tr>
                    <th>Nom complet</th>
                    <th>Email</th>
                    <th>Profil</th>
                    <th>Statut</th>
                    <th>Date création</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <?php
                        $roleKey = (string) ($user['role'] ?? '');
                        $status = (string) ($user['status'] ?? '');
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($user['fullname'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($user['email'] ?? '-') ?></td>
                            <td>
                                <span class="badge badge-info"><?= htmlspecialchars($roles[$roleKey] ?? $roleKey ?: '-') ?></span>
                            </td>
                            <td>
                                <?php if ($status === 'active'): ?>
                                    <span class="badge badge-success">Actif</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Suspendu</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($user['created_at'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Aucun membre du personnel trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
