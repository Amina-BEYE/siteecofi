<?php
session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Core/Settings.php';

use App\Core\Settings;
require_once __DIR__ . '/app/admin/Models/ImmoProgramModel.php';
require_once __DIR__ . '/app/admin/Models/ActualitesModel.php';

if (!class_exists('ImmoProgramModel')){
    $programmes = [];
} else {
    $model = new ImmoProgramModel();
    // ImmoProgramModel does not expose getAllProgrammes(); avoid fatal call
    $programmes = [];
    if (method_exists($model, 'getAllProgrammes')) {
        try {
            $programmes = $model->getAllProgrammes();
        } catch (Throwable $e) {
            $programmes = [];
        }
    }
}

$actualitesModel = new ActualitesModel();
$actualites = $actualitesModel->getPublishedActualites();

$site_title = 'Actualités - ' . Settings::get('site_title');
$email_contact = Settings::get('contact_email');
$tel_fixe = Settings::get('phone_fixed');
$tel_mobile = Settings::get('phone_mobile');
$facebook_url = Settings::get('facebook_url');
$instagram_url = Settings::get('instagram_url');
$linkedin_url = Settings::get('linkedin_url');
$twitter_url = Settings::get('twitter_url');
$youtube_url = Settings::get('youtube_url');
$tiktok_url = Settings::get('tiktok_url');
$program_title = Settings::get('program_title');
$program_subtitle = Settings::get('program_subtitle');
$program_location = Settings::get('program_location');
$program_surface = Settings::get('program_surface');
$program_deposit = Settings::get('program_deposit');
$program_monthly_payment = Settings::get('program_monthly_payment');
$mail_contact_url = 'https://mail.google.com/mail/?view=cm&fs=1&to=' . rawurlencode($email_contact) . '&su=Contact%20ECOFI&body=Bonjour%20ECOFI,%0D%0A%0D%0AJe%20vous%20contacte%20au%20sujet%20de...';
$tel_fixe_href = 'tel:+221' . preg_replace('/\D+/', '', $tel_fixe);
$tel_mobile_href = 'tel:+221' . preg_replace('/\D+/', '', $tel_mobile);

// Date actuelle formatée
$today = date('d/m/Y');
$today_full = date('l d F Y', time());

function fmtDate($value): string {
    if (!$value) return '';
    $ts = strtotime($value);
    if (!$ts) return (string)$value;
    return date('d/m/Y', $ts);
}

function esc($s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function statusLabel($status): string {
    $s = strtolower((string)$status);
    return match ($s) {
        'en_cours', 'en cours' => 'En cours',
        'planning', 'planification' => 'Planification',
        'termine', 'terminé' => 'Terminé',
        default => ucfirst($status),
    };
}

function statusClass($status): string {
    $s = strtolower((string)$status);
    return match ($s) {
        'en_cours', 'en cours' => 'status-active',
        'planning', 'planification' => 'status-pending',
        'termine', 'terminé' => 'status-done',
        default => 'status-done',
    };
}

// Données des terrains (à brancher sur DB ensuite)
$terrains = [
    [
        'titre'      => 'Terrain viabilisé – Nguinth Extension',
        'surface'    => '300',
        'prix'       => '8 500 000',
        'localisation'=> 'Nguinth, Thiès',
        'type'       => 'Résidentiel',
        'disponible' => true,
        'date'       => '10/05/2026',
        'img'        => 'app/IMG/terrain1.jpeg',
        'desc'       => 'Terrain titré et borné, accès voie bitumée, eau et électricité disponibles. Idéal pour maison individuelle ou immeuble R+2.',
        'bornage'    => true,
        'titre_foncier' => true,
    ],
    [
        'titre'      => 'Parcelle à bâtir – Cité Lamy',
        'surface'    => '200',
        'prix'       => '5 000 000',
        'localisation'=> 'Cité Lamy, Thiès',
        'type'       => 'Résidentiel',
        'disponible' => true,
        'date'       => '02/05/2026',
        'img'        => 'app/IMG/terraain2.jpg',
        'desc'       => 'Parcelle plane dans quartier calme. Proche écoles et marchés. Titre foncier disponible. Financement possible.',
        'bornage'    => true,
        'titre_foncier' => true,
    ],
    [
        'titre'      => 'Zone commerciale – RN1 Thiès',
        'surface'    => '500',
        'prix'       => '22 000 000',
        'localisation'=> 'Route Nationale 1, Thiès',
        'type'       => 'Commercial',
        'disponible' => true,
        'date'       => '15/04/2026',
        'img'        => 'app/IMG/terrain3.jpeg',
        'desc'       => 'Terrain en façade sur route principale, très fort passage. Parfait pour commerce, station-service ou immeuble mixte.',
        'bornage'    => true,
        'titre_foncier' => false,
    ],

     [
        'titre'      => 'Zone commerciale – RN1 Thiès',
        'surface'    => '500',
        'prix'       => '22 000 000',
        'localisation'=> 'Route Nationale 1, Thiès',
        'type'       => 'Commercial',
        'disponible' => true,
        'date'       => '15/04/2026',
        'img'        => 'app/IMG/terrain4.jpeg',
        'desc'       => 'Terrain en façade sur route principale, très fort passage. Parfait pour commerce, station-service ou immeuble mixte.',
        'bornage'    => true,
        'titre_foncier' => false,
    ],

     [
        'titre'      => 'Zone commerciale – RN1 Thiès',
        'surface'    => '500',
        'prix'       => '22 000 000',
        'localisation'=> 'Route Nationale 1, Thiès',
        'type'       => 'Commercial',
        'disponible' => true,
        'date'       => '15/04/2026',
        'img'        => 'app/IMG/terrain5.jpeg',
        'desc'       => 'Terrain en façade sur route principale, très fort passage. Parfait pour commerce, station-service ou immeuble mixte.',
        'bornage'    => true,
        'titre_foncier' => false,
    ],

     [
        'titre'      => 'Zone commerciale – RN1 Thiès',
        'surface'    => '500',
        'prix'       => '22 000 000',
        'localisation'=> 'Route Nationale 1, Thiès',
        'type'       => 'Commercial',
        'disponible' => true,
        'date'       => '15/04/2026',
        'img'        => 'app/IMG/terrain6.jpeg',
        'desc'       => 'Terrain en façade sur route principale, très fort passage. Parfait pour commerce, station-service ou immeuble mixte.',
        'bornage'    => true,
        'titre_foncier' => false,
    ],
];

// Projets en cours
$projets = [
    [
        'titre'      => 'Résidence Les Palmiers',
        'type'       => 'Immeuble R+3',
        'localisation'=> 'Nguinth, Thiès',
        'avancement' => 65,
        'debut'      => 'Janvier 2026',
        'livraison'  => 'Décembre 2026',
        'desc'       => '12 appartements T3 et T4, parkings couverts, groupe électrogène, sécurité 24h/24. Financement échelonné disponible.',
        'img'        => 'app/IMG/chantier.jpg',
        'video'      => 'app/IMG/amenage1.mp4',
        'poster'     => 'app/IMG/chantier.jpg',
        'etapes'     => ['Fondations ✓', 'Gros œuvre ✓', 'Toiture (en cours)', 'Finitions', 'Livraison'],
        'etape_actuelle' => 2,
    ],
    [
        'titre'      => 'Villa Duplex Keur Salam',
        'type'       => 'Villa R+1',
        'localisation'=> 'Keur Salam, Thiès',
        'avancement' => 40,
        'debut'      => 'Mars 2026',
        'livraison'  => 'Février 2027',
        'desc'       => 'Villa moderne 4 chambres, 2 salons, jardin paysagé, piscine optionnelle. Sur mesure pour la famille.',
        'img'        => 'app/IMG/plan.jpeg',
        'video'      => 'app/IMG/amenage3.mp4',
        'poster'     => 'app/IMG/ma.jpg',
        'etapes'     => ['Terrassement ✓', 'Fondations ✓', 'Gros œuvre (en cours)', 'Second œuvre', 'Livraison'],
        'etape_actuelle' => 2,
    ],
    [
        'titre'      => 'Immeuble Commercial Darou',
        'type'       => 'Immeuble R+2',
        'localisation'=> 'Centre-ville Thiès',
        'avancement' => 15,
        'debut'      => 'Avril 2026',
        'livraison'  => 'Juin 2027',
        'desc'       => 'Rez-de-chaussée commercial, bureaux aux étages, façade moderne. Excellent emplacement en cœur de ville.',
        'img'        => 'app/IMG/drone.jpg',
        'video'      => null,
        'poster'     => null,
        'etapes'     => ['Fondations (en cours)', 'Gros œuvre', 'Toiture', 'Finitions', 'Livraison'],
        'etape_actuelle' => 0,
    ],
];

// Médias galerie chantier
$mediaChantier = [
    ['type' => 'video', 'src' => 'app/IMG/amenage1.mp4', 'poster' => 'app/IMG/chantier.jpg', 'titre' => 'Avancement gros œuvre – Les Palmiers'],
    ['type' => 'image', 'src' => 'app/IMG/chantier.jpg', 'titre' => 'Suivi qualité chantier'],
    ['type' => 'video', 'src' => 'app/IMG/amenage3.mp4', 'poster' => 'app/IMG/ma.jpg', 'titre' => 'Finitions et second œuvre'],
    ['type' => 'image', 'src' => 'app/IMG/drone.jpg', 'titre' => 'Vue aérienne du site – Drone ECOFI'],
    ['type' => 'image', 'src' => 'app/IMG/plan.jpeg', 'titre' => 'Plans architecturaux validés'],
    ['type' => 'video', 'src' => 'app/IMG/VID.mp4', 'poster' => 'app/IMG/plan.jpeg', 'titre' => 'Projection 3D – Rendu final'],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc($site_title); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="actualites.css">
    <link rel="icon" href="../app/IMG/logo-ecofi.png" type="image/png">

 
</head>
<body>

<!-- ===== HEADER  ===== -->
<header>
    <div class="top-navbar">
        <div class="container">
            <div class="contact-info">
                <a href="<?= esc($mail_contact_url); ?>" target="_blank">
                    <i class="fas fa-envelope"></i> <?= esc($email_contact); ?>
                </a>
                <a href="<?= esc($tel_fixe_href); ?>"><i class="fas fa-phone"></i> <?= esc($tel_fixe); ?></a>
                <a href="<?= esc($tel_mobile_href); ?>"><i class="fas fa-mobile-alt"></i> <?= esc($tel_mobile); ?></a>
            </div>
            <div class="social-links">
                <a href="https://www.facebook.com/profile.php?id=61584334332565&mibextid=ZbWKwL" class="footer-social-icon"><i class="fab fa-facebook-f"></i></a>
                <a href="https://www.instagram.com/ecofiservice?igsh=MTVnY2xwcGFicm00Zw==" class="footer-social-icon"><i class="fab fa-instagram"></i></a>
                <a href="#" class="footer-social-icon"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="footer-social-icon"><i class="fab fa-twitter"></i></a>
                <a href="https://www.tiktok.com/@ecofi.service.01?_r=1&_t=ZS-93Hkr11ak5K" class="footer-social-icon"><i class="fab fa-tiktok"></i></a>
                <a link="stylesheet" href= "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" class="footer-social-icon"><i class="fab fa-youtube"></i></a>
                <a link="stylesheet" href= "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" class="footer-social-icon"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
    </div>
    <div class="main-navbar">
        <div class="container">
            <div class="logo">
                <img src="app/IMG/logo-ecofi.png" alt="ECOFI" class="custom-logo">
                <div class="logo-definition">
                    <div class="definition-main">Etablissement de Conseils</div>
                    <div class="definition-sub">sur le Foncier et l'Immobilier</div>
                </div>
            </div>
            <div class="nav-search-container">
                <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
                <nav id="mainNav">
                    <ul>
                        <li><a href="index.php#accueil">Accueil</a></li>
                        <li><a href="index.php#apropos">À propos</a></li>
                        <li><a href="actualites.php" class="active">Actualités</a></li>
                        <li><a href="index.php#services">Services</a></li>
                        <li><a href="index.php#contact">Contact</a></li>
                    </ul>
                </nav>
                <div class="search-container">
                    <button class="search-toggle" id="searchToggle"><i class="fas fa-search"></i></button>
                    <div class="search-box" id="searchBox">
                        <input type="text" id="searchInput" placeholder="Rechercher un produit...">
                        <div class="search-results" id="searchResults"></div>
                    </div>
                </div>
                <div class="cart-wrapper">
                    <div class="cart-icon" onclick="toggleCart(event)">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Panier</span>
                        <span class="cart-count" id="cartCount">0</span>
                        <span class="cart-total-header" id="cartHeaderTotal">0 FCFA</span>
                    </div>
                    <div class="cart-dropdown" id="cartDropdown">
                        <div class="cart-header">
                            <span><i class="fas fa-shopping-cart" style="color:var(--accent-color)"></i> Mon Panier</span>
                            <span id="cartItemCount">0 article(s)</span>
                        </div>
                        <div class="cart-items" id="cartItems">
                            <p style="text-align:center;color:#999;padding:20px;">Votre panier est vide</p>
                        </div>
                        <div class="cart-footer">
                            <div class="cart-total"><span>Total TTC</span><span id="cartTotal">0 FCFA</span></div>
                            <div class="cart-buttons">
                                <button class="cart-btn cart-btn-danger" onclick="viderPanier()"><i class="fas fa-trash"></i> Vider</button>
                                <button class="cart-btn cart-btn-primary" onclick="openQuoteModal(event)"><i class="fas fa-paper-plane"></i> Demande de devis</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- ===== HERO ===== -->
<section class="actu-hero">
    <div class="actu-hero-bg"></div>
    <div class="actu-hero-accent"></div>
    <div class="actu-hero-accent2"></div>
    <div class="actu-hero-inner">
        <div class="hero-eyebrow"><i class="fas fa-bolt"></i> Mis à jour le <?php echo $today; ?></div>
        <h1>L'actualité <em>immobilière</em><br>ECOFI en direct</h1>
        <p>Programmes, terrains disponibles, projets en cours : retrouvez toutes les informations et mises à jour de nos opérations foncières et immobilières.</p>
        <div class="hero-cta-row">
            <a class="btn-hero-primary" href="#programme-immo"><i class="fas fa-building"></i> Voir le programme</a>
            <a class="btn-hero-outline" href="index.php#contact"><i class="fas fa-phone"></i> Contacter ECOFI</a>
        </div>
    </div>
    <div class="hero-stats-bar">
        <div class="hero-stats-inner">
            <div class="hero-stat"><div class="hero-stat-val"><?php echo count($programmes) ?: 3; ?></div><div class="hero-stat-lbl">Programmes actifs</div></div>
            <div class="hero-stat"><div class="hero-stat-val"><?php echo count($terrains); ?></div><div class="hero-stat-lbl">Terrains disponibles</div></div>
            <div class="hero-stat"><div class="hero-stat-val"><?php echo count($projets); ?></div><div class="hero-stat-lbl">Projets en cours</div></div>
            <div class="hero-stat"><div class="hero-stat-val">Thiès</div><div class="hero-stat-lbl">Zone principale</div></div>
            <div class="hero-stat"><div class="hero-stat-val">2026</div><div class="hero-stat-lbl">Millésime actif</div></div>
        </div>
    </div>
</section>

    <section class="actualites-list section-light">
        <div class="container">
            <div class="section-header">
                <h2>Actualités récentes</h2>
                <p>Retrouvez ici les dernières publications et mises à jour de nos opérations.</p>
            </div>

            <?php if (empty($actualites)): ?>
                <div class="empty-state">
                    <p>Aucune actualité publiée pour le moment. Utilisez le panneau d'administration pour ajouter du contenu.</p>
                </div>
            <?php else: ?>
                <div class="actualites-grid">
                    <?php foreach ($actualites as $item): ?>
                        <article class="actualite-card">
                            <?php if (!empty($item['image'])): ?>
                                <div class="actualite-media">
                                    <img src="<?= esc($item['image']) ?>" alt="<?= esc($item['title']) ?>">
                                </div>
                            <?php elseif (!empty($item['video'])): ?>
                                <div class="actualite-media">
                                    <video controls muted>
                                        <source src="<?= esc($item['video']) ?>" type="video/mp4">
                                        Votre navigateur ne supporte pas la vidéo.
                                    </video>
                                </div>
                            <?php endif; ?>
                            <div class="actualite-body">
                                <span class="actualite-category"><?= esc($item['category']) ?></span>
                                <h3><?= esc($item['title']) ?></h3>
                                <?php if (!empty($item['subtitle'])): ?>
                                    <p class="actualite-subtitle"><?= esc($item['subtitle']) ?></p>
                                <?php endif; ?>
                                <p><?= nl2br(esc($item['content'])) ?></p>
                                <div class="actualite-meta">
                                    <span><?= esc(date('d/m/Y', strtotime($item['published_at'] ?? 'now'))) ?></span>
                                    <span><?= esc(ucfirst($item['status'])) ?></span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

<!-- ===== MAIN CONTENT ===== -->
<main>
      <!-- === PROGRAMMES IMMOBILIERS === -->
    <section id="programmes">
        <div class="container">
            <h2>Programmes immobiliers</h2>
            <p>Appartements & Logements neufs. Des logements conçus pour durer, avec un accompagnement.</p>
        </div>
        <div class="actu-main">
        <section id="programme-immo" class="programme-immo programme-immo-actu">
            <div class="programme-banner">
                <div class="programme-banner-left">
                    <img src="app/IMG/logo-ecofi.png" alt="Logo ECOFI Construction">
                    <div class="programme-banner-info">
                        <span class="section-label">Programme immobilier ECOFI Services</span>
                        <h2><?= esc($program_title); ?></h2>
                        <p><?= esc($program_subtitle); ?> Une offre claire, accessible et sécurisée pour investir dès aujourd’hui.</p>
                    </div>
                </div>
                <a class="programme-cta" href="#adhesionForm">Adhérer au programme</a>
            </div>

            <div class="programme-info-grid">
                <div>
                    <article class="programme-card">
                        <img src="app/IMG/PROGRAMMEIMO.jpeg" alt="Terrain ECOFI Construction">
                        <div class="programme-card-body">
                            <h3>Présentation du terrain</h3>
                            <p>Terrains de <?= esc($program_surface); ?> situés à <?= esc($program_location); ?> avec accès sécurisé, environnement calme et proximité des infrastructures locales.</p>
                            <div class="programme-summary">
                                <div class="programme-summary-item">
                                    <strong>Offre</strong>
                                    <span><?= esc($program_title); ?></span>
                                </div>
                                <div class="programme-summary-item">
                                    <strong>Localisation</strong>
                                    <span><?= esc($program_location); ?></span>
                                </div>
                                <div class="programme-summary-item">
                                    <strong>Repère</strong>
                                    <span><?= esc($program_subtitle); ?></span>
                                </div>
                                <div class="programme-summary-item">
                                    <strong>Documents</strong>
                                    <span>Papier juridique et notification de bail</span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <aside class="payment-card">
                        <h3>Modalités de paiement</h3>
                        <ul>
                            <li><span>Acompte</span><strong><?= esc($program_deposit); ?></strong></li>
                            <li><span>Mensualité</span><strong><?= esc($program_monthly_payment); ?></strong></li>
                            <li><span>Durée</span><strong>24 mois</strong></li>
                            <li><span>Frais de dossier</span><strong>25 000 F CFA</strong></li>
                        </ul>
                        <p>Les documents obligatoires comprennent le dossier juridique complet et la notification de bail pour validation rapide.</p>
                    </aside>
                </div>

                <div class="programme-form-wrapper">
                    <div class="programme-form-card">
                        <h3>Formulaire d’adhésion</h3>
                        <p>Complétez vos informations pour rejoindre le programme immobilier ECOFI Construction.</p>
                        <form id="adhesionForm" action="adhesion.php" method="POST">
                            <div class="form-row-elegant">
                                <div class="form-group-elegant">
                                    <label for="nom">Nom</label>
                                    <input id="nom" name="nom" type="text" placeholder="Nom" required>
                                </div>
                                <div class="form-group-elegant">
                                    <label for="prenom">Prénom</label>
                                    <input id="prenom" name="prenom" type="text" placeholder="Prénom" required>
                                </div>
                            </div>
                            <div class="form-row-elegant">
                                <div class="form-group-elegant">
                                    <label for="date_naissance">Date de naissance</label>
                                    <input id="date_naissance" name="date_naissance" type="date" required>
                                </div>
                                <div class="form-group-elegant">
                                    <label for="lieu_naissance">Lieu de naissance</label>
                                    <input id="lieu_naissance" name="lieu_naissance" type="text" placeholder="Lieu de naissance" required>
                                </div>
                            </div>
                            <div class="form-group-elegant">
                                <label for="adresse">Adresse</label>
                                <input id="adresse" name="adresse" type="text" placeholder="Adresse complète" required>
                            </div>
                            <div class="form-row-elegant">
                                <div class="form-group-elegant">
                                    <label for="telephone">Téléphone</label>
                                    <input id="telephone" name="telephone" type="tel" placeholder="<?= esc($tel_fixe); ?>" required>
                                </div>
                                <div class="form-group-elegant">
                                    <label for="cni">N°CNI / Passeport</label>
                                    <input id="cni" name="cni" type="text" placeholder="N°CNI / Passeport" required>
                                </div>
                            </div>
                            <div class="form-group-elegant">
                                <label for="email">Email</label>
                                <input id="email" name="email" type="email" placeholder="<?= esc($email_contact); ?>" required>
                            </div>
                            <div class="form-group-elegant">
                                <label for="mode_paiement">Mode de paiement</label>
                                <select id="mode_paiement" name="mode_paiement" required>
                                    <option value="">Sélectionnez un mode</option>
                                    <option>Espèces</option>
                                    <option>Virement bancaire</option>
                                    <option>Mobile money</option>
                                    <option>Chèque</option>
                                </select>
                            </div>
                            <div class="form-group-elegant">
                                <label for="message">Message</label>
                                <textarea id="message" name="message" placeholder="Message (optionnel)" rows="4"></textarea>
                            </div>
                            <button type="submit" class="submit-btn-elegant">Adhérer au programme</button>
                            <div class="form-message" id="adhesionFormMessage"></div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="condition-block">
                <h3>Conditions importantes</h3>
                <ul>
                    <li>L’adhésion devient définitive après validation complète du dossier.</li>
                    <li>L’attribution du terrain intervient après paiement intégral, validation administrative et disponibilité du terrain.</li>
                    <li>Le membre doit respecter les échéances de paiement.</li>
                    <li>Les remboursements éventuels peuvent être soumis à des frais de gestion de 10%.</li>
                    <li>Toute cession du droit d’attribution est interdite sans autorisation écrite préalable.</li>
                </ul>
            </div>
        </section>

      
            <!-- <div class="programmes-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; width: 90%; max-width: 1200px; margin: 2rem auto;">
                <article class="prog-card" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                    <div style="position: relative; width: 100%; height: 250px; overflow: hidden;">
                        <img src="app/IMG/plan.jpeg" alt="Résidence Nguinth" style="width: 100%; height: 100%; object-fit: cover;">
                        <div style="position: absolute; top: 10px; right: 10px; background: #FF8533; color: white; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 700;">En cours</div>
                    </div>
                    <div style="padding: 1.5rem;">
                        <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 0.5rem; color: #333;">Résidence Nguinth</h3>
                        <p style="color: #666; font-size: 0.9rem; margin-bottom: 1rem;"><i class="fas fa-location-dot" style="color: #FF8533;"></i> Nguinth, Thiès</p>
                        <p style="color: #777; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1rem;">Appartements T3 et T4, résidence sécurisée avec parking et espace vert commun.</p>
                        <div style="display: flex; gap: 0.5rem; font-size: 0.85rem; color: #666; margin-bottom: 1rem;">
                            <span><i class="fas fa-ruler-combined" style="color: #FF8533;"></i> 80 m²</span>
                            <span><i class="fas fa-key" style="color: #FF8533;"></i> Titre foncier</span>
                        </div>
                        <div style="color: #FF8533; font-weight: 700; font-size: 1.3rem; margin-bottom: 1rem;">15 000 000 FCFA</div>
                        <button type="button" class="actu-detail-btn" data-actualite-title="Résidence Nguinth" data-actualite-type="Programme immobilier" onclick="openActualiteContact(this)">
                            <i class="fas fa-arrow-up-right-from-square"></i> Plus de détails
                        </button>
                    </div>
                </article>
                <article class="prog-card" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                    <div style="position: relative; width: 100%; height: 250px; overflow: hidden;">
                        <img src="app/IMG/chantier.jpg" alt="Villa Keur Salam" style="width: 100%; height: 100%; object-fit: cover;">
                        <div style="position: absolute; top: 10px; right: 10px; background: #666; color: white; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 700;">Planification</div>
                    </div>
                    <div style="padding: 1.5rem;">
                        <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 0.5rem; color: #333;">Villa Keur Salam</h3>
                        <p style="color: #666; font-size: 0.9rem; margin-bottom: 1rem;"><i class="fas fa-location-dot" style="color: #FF8533;"></i> Keur Salam, Thiès</p>
                        <p style="color: #777; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1rem;">Villa individuelle 4 chambres. Architecte agréé, matériaux premium.</p>
                        <div style="display: flex; gap: 0.5rem; font-size: 0.85rem; color: #666; margin-bottom: 1rem;">
                            <span><i class="fas fa-ruler-combined" style="color: #FF8533;"></i> 150 m²</span>
                            <span><i class="fas fa-key" style="color: #FF8533;"></i> Titre foncier</span>
                        </div>
                        <div style="color: #FF8533; font-weight: 700; font-size: 1.3rem; margin-bottom: 1rem;">25 000 000 FCFA</div>
                        <button type="button" class="actu-detail-btn" data-actualite-title="Villa Keur Salam" data-actualite-type="Programme immobilier" onclick="openActualiteContact(this)">
                            <i class="fas fa-arrow-up-right-from-square"></i> Plus de détails
                        </button>
                    </div>
                </article>
                <article class="prog-card" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                    <div style="position: relative; width: 100%; height: 250px; overflow: hidden;">
                        <img src="app/IMG/drone.jpg" alt="Cité des Artisans" style="width: 100%; height: 100%; object-fit: cover;">
                        <div style="position: absolute; top: 10px; right: 10px; background: #FF8533; color: white; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 700;">En cours</div>
                    </div>
                    <div style="padding: 1.5rem;">
                        <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 0.5rem; color: #333;">Cité des Artisans</h3>
                        <p style="color: #666; font-size: 0.9rem; margin-bottom: 1rem;"><i class="fas fa-location-dot" style="color: #FF8533;"></i> Zone Est, Thiès</p>
                        <p style="color: #777; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1rem;">Logements abordables F3 pour primo-accédants. Financement FONHAB possible.</p>
                        <div style="display: flex; gap: 0.5rem; font-size: 0.85rem; color: #666; margin-bottom: 1rem;">
                            <span><i class="fas fa-ruler-combined" style="color: #FF8533;"></i> 65 m²</span>
                            <span><i class="fas fa-key" style="color: #FF8533;"></i> Titre foncier</span>
                        </div>
                        <div style="color: #FF8533; font-weight: 700; font-size: 1.3rem; margin-bottom: 1rem;">9 500 000 FCFA</div>
                        <button type="button" class="actu-detail-btn" data-actualite-title="Cité des Artisans" data-actualite-type="Programme immobilier" onclick="openActualiteContact(this)">
                            <i class="fas fa-arrow-up-right-from-square"></i> Plus de détails
                        </button>
                    </div>
                </article>
            </div> -->

    </section>
        <hr class="sec-separator">

        <!-- === TERRAINS DISPONIBLES === -->
        <section id="terrains">
            <div class="container">
                <h2>Terrains disponibles</h2>
                <p>Parcelles & Zones foncières. Tous nos terrains sont vérifiés, titrés et bornés par nos géomètres experts. Accompagnement foncier complet.</p>
            </div>
            <div class="terrains-list">
                <article class="terrain-card">
                    <img class="terrain-img" src="app/IMG/terrain1.jpeg" alt="Terrain Nguinth Extension">
                    <div class="terrain-body">
                        <h3>Terrain viabilisé – Thies</h3>
                        <p style="color: var(--accent); font-weight:600; margin-bottom:0.5rem;"><i class="fas fa-location-dot"></i> Thiès Zac Nguinth</p>
                        <p style="color: #777; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1rem;">Terrain titré et borné, accès voie bitumée, eau et électricité disponibles.</p>
                        <div class="terrain-meta">
                            <span><i class="fas fa-check-circle" style="color: var(--green);"></i> Borné GNSS</span>
                            <span>-----</span>
                            <span><i class="fas fa-ruler-combined" style="color: var(--accent);"></i> 225 m²</span>
                        </div>
                        <div class="terrain-price">prix sur demande</div>
                        <button type="button" class="actu-detail-btn compact" data-actualite-title="Terrain viabilisé – Nguinth Extension" data-actualite-type="Terrain disponible" onclick="openActualiteContact(this)">
                            <i class="fas fa-arrow-up-right-from-square"></i> Plus de détails
                        </button>
                    </div>
                </article>

                <article class="terrain-card">
                    <img class="terrain-img" src="app/IMG/terraain2.jpeg" alt="Parcelle Cité Lamy">
                    <div class="terrain-body">
                        <h3>Parcelle à bâtir – Sen Iran</h3>
                        <p style="color: var(--accent); font-weight:600; margin-bottom:0.5rem;"><i class="fas fa-location-dot"></i> Thiès Nord Est Sen Iran</p>
                        <p style="color: #777; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1rem;">Disponible.</p>
                        <div class="terrain-meta">
                            <span><i class="fas fa-check-circle" style="color: var(--green);"></i> Borné GNSS</span>
                            <span><i class="fas fa-times" style="color: #999"></i> Sans titre</span>
                            <span><i class="fas fa-ruler-combined" style="color: var(--accent);"></i> 300 m²</span>
                        </div>
                        <div class="terrain-price">6 500 000 FCFA</div>
                        <button type="button" class="actu-detail-btn compact" data-actualite-title="Parcelle à bâtir – Cité Lamy" data-actualite-type="Terrain disponible" onclick="openActualiteContact(this)">
                            <i class="fas fa-arrow-up-right-from-square"></i> Plus de détails
                        </button>
                    </div>
                </article>

                <article class="terrain-card">
                    <img class="terrain-img" src="app/IMG/terrain5.jpeg" alt="Zone RN1 Thiès">
                    <div class="terrain-body">
                        <h3>Zone commerciale – Tivaoune Thiès</h3>
                        <p style="color: var(--accent); font-weight:600; margin-bottom:0.5rem;"><i class="fas fa-location-dot"></i> Ndiobene route de Tivaoune</p>
                        <p style="color: #777; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1rem;">Parfait pour commerce.</p>
                        <div class="terrain-meta">
                            <span><i class="fas fa-check-circle" style="color: var(--green);"></i> Borné GNSS</span>
                            <span><i class="fas fa-times" style="color: #999"></i> Sans titre</span>
                            <span><i class="fas fa-ruler-combined" style="color: var(--accent);"></i> 300 m²</span>
                        </div>
                        <div class="terrain-price">3 000 000 FCFA</div>
                        <button type="button" class="actu-detail-btn compact" data-actualite-title="Zone commerciale – RN1 Thiès" data-actualite-type="Terrain disponible" onclick="openActualiteContact(this)">
                            <i class="fas fa-arrow-up-right-from-square"></i> Plus de détails
                        </button>
                    </div>
                </article>

                <article class="terrain-card">
                    <img class="terrain-img" src="app/IMG/terrein6.jpeg" alt="Zone RN1 Thiès">
                    <div class="terrain-body">
                        <h3>Route Nationale 1 – Lalane</h3>
                        <p style="color: var(--accent); font-weight:600; margin-bottom:0.5rem;"><i class="fas fa-location-dot"></i> Thiès, Lalane</p>
                        <p style="color: #777; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1rem;">Terrain en façade sur route principale, très fort passage.</p>
                        <div class="terrain-meta">
                            <span><i class="fas fa-check-circle" style="color: var(--green);"></i> Borné GNSS</span>
                            <span><i class="fas fa-times" style="color: #999"></i> Sans titre</span>
                            <span><i class="fas fa-ruler-combined" style="color: var(--accent);"></i> 300 m²</span>
                        </div>
                        <div class="terrain-price">3 000 000 FCFA</div>
                        <button type="button" class="actu-detail-btn compact" data-actualite-title="Zone commerciale – RN1 Thiès" data-actualite-type="Terrain disponible" onclick="openActualiteContact(this)">
                            <i class="fas fa-arrow-up-right-from-square"></i> Plus de détails
                        </button>
                    </div>
                </article>
            </div>

        </section>

        <hr class="sec-separator">

        <!-- === PROJETS EN COURS === -->
        <section id="projets">
            <div class="container">
                <h2>Projets en cours</h2>
                <p>Chantiers & Réalisations 2026. Suivi en temps réel de l'avancement de chaque chantier. Transparence totale pour nos investisseurs et clients.</p>
            </div>
            <div class="projets-grid">
                <article class="projet-card">
                    <div class="projet-media">
                        <video autoplay muted loop playsinline>
                            <source src="app/IMG/fondation.mp4" type="video/mp4">
                            Votre navigateur ne supporte pas la vidéo.
                        </video>
                        <div class="projet-type-badge">Projet en cours</div>
                        <div class="projet-avance">
                            <div class="avance-label">
                                <span>Avancement :</span>
                                <strong>45%</strong>
                            </div>
                            <div class="avance-bar">
                                <div class="avance-fill" style="width: 65%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="projet-body">
                        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem; color: #333;"> Maison d'habitation et commerciale</h3>
                        <p style="color: #666; font-size: 0.9rem; margin-bottom: 0.8rem;"><i class="fas fa-location-dot" style="color: #FF8533;"></i> Est Mont Rolland, Thiès</p>
                        <p style="background: #f5f5f5; padding: 0.8rem; border-radius: 6px; color: #666; font-size: 0.85rem; margin-bottom: 1rem; line-height: 1.5;">
                            Construction Immeuble R+1 . Usage commercial et habitation.
                        </p>
                        <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: #666; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #eee;">
                            <div><strong>Début:</strong> Janvier 2026</div>
                            <div><strong>Livraison:</strong> Décembre 2026</div>
                        </div>
                        <button type="button" class="actu-detail-btn compact" data-actualite-title="Immeuble d'habitation et commerciale" data-actualite-type="Projet en cours" onclick="openActualiteContact(this)">
                            <i class="fas fa-eye"></i> Plus de détails
                        </button>
                    </div>
                </article>

                <!-- <article style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <div style="position: relative; width: 100%; height: 220px; overflow: hidden;">
                        <img src="app/IMG/plan.jpeg" alt="Villa Duplex Keur Salam" style="width: 100%; height: 100%; object-fit: cover;">
                        <div style="position: absolute; bottom: 10px; left: 10px; right: 10px; background: rgba(0,0,0,0.7); color: white; padding: 0.8rem; border-radius: 6px;">
                            <div style="font-size: 0.85rem; margin-bottom: 0.3rem;">Avancement : <strong style="color: #FF8533;">40%</strong></div>
                            <div style="height: 4px; background: rgba(255,255,255,0.3); border-radius: 2px; overflow: hidden;">
                                <div style="height: 100%; width: 40%; background: #FF8533;"></div>
                            </div>
                        </div>
                    </div>
                    <div style="padding: 1.5rem;">
                        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem; color: #333;">Villa Duplex Keur Salam</h3>
                        <p style="color: #666; font-size: 0.9rem; margin-bottom: 0.8rem;"><i class="fas fa-location-dot" style="color: #FF8533;"></i> Keur Salam, Thiès</p>
                        <p style="background: #f5f5f5; padding: 0.8rem; border-radius: 6px; color: #666; font-size: 0.85rem; margin-bottom: 1rem; line-height: 1.5;">
                            Villa moderne R+1 avec 4 chambres, 2 salons. Jardin paysagé, piscine optionnelle.
                        </p>
                        <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: #666; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #eee;">
                            <div><strong>Début:</strong> Mars 2026</div>
                            <div><strong>Livraison:</strong> Février 2027</div>
                        </div>
                        <button type="button" class="actu-detail-btn compact" data-actualite-title="Villa Duplex Keur Salam" data-actualite-type="Projet en cours" onclick="openActualiteContact(this)">
                            <i class="fas fa-eye"></i> Plus de détails
                        </button>
                    </div>
                </article> -->


                <article class="projet-card">
                    <div class="projet-media">
                         <video autoplay muted loop playsinline>
                            <source src="app/IMG/rplus.mp4" type="video/mp4">
                            Votre navigateur ne supporte pas la vidéo.
                        </video>
                        <div class="projet-type-badge">Projet en cours</div>
                        <div class="projet-avance">
                            <div class="avance-label">
                                <span>Avancement :</span>
                                <strong>15%</strong>
                            </div>
                            <div class="avance-bar">
                                <div class="avance-fill" style="width: 15%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="projet-body">
                        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem; color: #333;">Maison d'habitation</h3>
                        <p style="color: #666; font-size: 0.9rem; margin-bottom: 0.8rem;"><i class="fas fa-location-dot" style="color: #FF8533;"></i> Zac, Nguinth - Thiès</p>
                        <p style="background: #f5f5f5; padding: 0.8rem; border-radius: 6px; color: #666; font-size: 0.85rem; margin-bottom: 1rem; line-height: 1.5;">
                            Immeuble R+1 a usage d'habitation.
                        </p>
                        <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: #666; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #eee;">
                            <div><strong>Début:</strong> Avril 2026</div>
                            <div><strong>Livraison:</strong> janvier 2027</div>
                        </div>
                        <button type="button" class="actu-detail-btn compact" data-actualite-title="Immeuble d'habitation " data-actualite-type="Projet en cours" onclick="openActualiteContact(this)">
                            <i class="fas fa-eye"></i> Plus de détails
                        </button>
                    </div>
                </article>

                <article class="projet-card">
                    <div class="projet-media">
                        <video autoplay muted loop playsinline>
                            <source src="app/IMG/logement.mp4" type="video/mp4">
                            Votre navigateur ne supporte pas la vidéo.
                        </video>
                        <div class="projet-type-badge" style="background: rgba(34,197,94,0.95);">Disponible maintenant</div>
                    </div>
                    <div class="projet-body">
                        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem; color: #333;">Maison de logement disponible</h3>
                        <p style="color: #666; font-size: 0.9rem; margin-bottom: 0.8rem;">
                            <i class="fas fa-location-dot" style="color: #FF8533;"></i> Zac, Nguinth - Thiès
                        </p>
                        <p style="font-size: 1.15rem; font-weight: 800; color: #1a2340; margin-bottom: 1rem;">
                            Prix sur demande
                        </p>
                        <p style="background: #f5f5f5; padding: 0.8rem; border-radius: 6px; color: #666; font-size: 0.85rem; line-height: 1.5; margin-bottom: 1rem;">
                            Maison 4 chambres, 2 salons, jardin paysagé, proche accès route nationale et services.
                        </p>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1rem;">
                            <span style="background: #ecfdf5; color: #166534; padding: 0.55rem 0.9rem; border-radius: 999px; font-size: 0.85rem; font-weight: 700;">
                                4 chambres
                            </span>
                            <span style="background: #ecfdf5; color: #166534; padding: 0.55rem 0.9rem; border-radius: 999px; font-size: 0.85rem; font-weight: 700;">
                                180 m²
                            </span>
                        </div>
                        <button type="button" class="actu-detail-btn compact" style="background: #22c55e;" data-actualite-title="maison de logement" data-actualite-type="Projet en cours" onclick="openActualiteContact(this)">
                            <i class="fas fa-calendar-check"></i> Réserver
                        </button>
                       
                      </div>
                </article>
            </div>
           
        </section>

    </div><!-- .actu-main -->

    <!-- ===== GALERIE CHANTIER ===== -->
    <?php
        $galleryFiles = [
            'gallerie1.mp4',
            'gallerie2.jpeg',
            'gallerie3.jpeg',
            'gallerie4.jpeg',
            'gallerie5.jpeg',
            'gallerie6.jpeg',
            'gallerie7.jpeg',
            'gallerie8.mp4',
            'gallerie9.jpeg',
            'gallerie10.jpeg',
            'gallerie11.jpeg',
            'gallerie12.jpeg',
            'gallerie13.jpeg',
            'vigallerie1.mp4',
            'vigallerie2.mp4',
            'vigallerie3.mp4',
            'vigallerie4.mp4',
            'vigallerie5.mp4',         ];

        // $galleryItems = [];
        // $galleryLabels = [
        //     'gallerie1.mp4' => 'Vidéo chantier',
        //     'gallerie2.jpeg' => 'Terrassement',
        //     'gallerie3.jpeg' => 'Fondations',
        //     'gallerie4.jpeg' => 'Gros œuvre',
        //     'gallerie5.jpeg' => 'Pose du toit',
        //     'gallerie6.jpeg' => 'Intérieur',
        //     'gallerie7.jpeg' => 'Finitions',
        //     'gallerie8.mp4' => 'Vidéo drone',
        //     'gallerie9.jpeg' => 'Coulage',
        //     'gallerie10.jpeg' => 'Travail d’équipe',
        //     'gallerie11.jpeg' => 'Contrôle qualité',
        //     'gallerie12.jpeg' => 'Livraison',
        //     'gallerie13.jpeg' => 'Décoration',
        //     'vigallerie1.mp4' => 'Interview chantier',
        //     'vigallerie2.mp4' => 'Présentation site',
        //     'vigallerie3.mp4' => 'Voix off projet',
        //     'vigallerie4.mp4' => 'Visite interne',
        //     'vigallerie5.mp4' => 'Commentaire technique',
        //     'vigallerie6.mp4' => 'Processus',
        //     'vigallerie7.mp4' => 'Explication',
        //     'vigallerie8.mp4' => 'Bilan chantier',
        // ];

        foreach ($galleryFiles as $filename) {
            $path = __DIR__ . '/app/IMG/' . $filename;
            if (!file_exists($path)) {
                continue;
            }
            $src = 'app/IMG/' . $filename;
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $type = in_array($ext, ['mp4', 'webm', 'ogg']) ? 'video' : 'image';
            $title = $galleryLabels[$filename] ?? ($type === 'video' ? 'Vidéo chantier' : 'Photo chantier');
            $galleryItems[] = [
                'type' => $type,
                'src' => $src,
                'title' => $title,
            ];
        }
    ?>
    <section class="galerie-section" id="galerie">
        <div class="galerie-inner">
            <div class="container" style="color: white;">
                <h2>Galerie terrain</h2>
                <p>Photos & Vidéos de chantier. Documentation visuelle de nos opérations. Mises à jour régulières pour nos investisseurs.</p>
            </div>
            <div class="galerie-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; width: 90%; max-width: 1200px; margin: 2rem auto;">
                <?php foreach ($galleryItems as $item): ?>
                    <div class="galerie-item" data-type="<?= esc($item['type']); ?>" data-src="<?= esc($item['src']); ?>" data-title="<?= esc($item['title']); ?>" style="position:relative;border-radius:8px;overflow:hidden;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.1);transition:transform 0.3s ease;">
                        <?php if ($item['type'] === 'image'): ?>
                            <img src="<?= esc($item['src']); ?>" alt="<?= esc($item['title']); ?>" style="width:100%;height:200px;object-fit:cover;display:block;">
                        <?php else: ?>
                            <video muted autoplay loop playsinline preload="metadata" poster="app/IMG/chantier.jpg" style="width:100%;height:200px;object-fit:cover;display:block;">
                                <source src="<?= esc($item['src']); ?>" type="video/mp4">
                                Votre navigateur ne prend pas en charge la vidéo.
                            </video>
                        <?php endif; ?>
                        <div style="position:absolute;bottom:0;left:0;right:0;background:linear-gradient(transparent,rgba(0,0,0,0.8));padding:1rem;color:white;font-size:0.9rem;font-weight:600;">
                            <?= esc($item['title']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


    <!-- ===== INSCRIPTION ===== -->
    <section class="inscription-band" id="inscription">
        <div class="inscription-inner">
            <div class="inscription-text">
                <h2>Soyez informé en premier de nos nouveaux programmes</h2>
                <p>Inscrivez-vous pour recevoir en avant-première les lancements de terrains, les nouvelles résidences et les offres exclusives ECOFI.</p>
                <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-top:1.5rem;">
                    <div style="display:flex;align-items:center;gap:8px;color:rgba(255,255,255,0.8);font-size:.9rem;">
                        <i class="fas fa-shield-alt" style="color:rgba(255,255,255,0.7)"></i> Aucun spam
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;color:rgba(255,255,255,0.8);font-size:.9rem;">
                        <i class="fas fa-bell" style="color:rgba(255,255,255,0.7)"></i> Alertes personnalisées
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;color:rgba(255,255,255,0.8);font-size:.9rem;">
                        <i class="fas fa-handshake" style="color:rgba(255,255,255,0.7)"></i> Offres exclusives
                    </div>
                </div>
            </div>
            <form class="inscription-form" id="inscriptionForm" onsubmit="handleInscription(event)">
                <div class="inscription-row">
                    <input class="inscription-input" type="text" id="inscNom" name="name" placeholder="Votre nom" required>
                    <input class="inscription-input" type="tel" id="inscTel" name="phone" placeholder="Téléphone" required>
                </div>
                <input class="inscription-input" type="email" id="inscEmail" name="email" placeholder="Adresse email" required>
                <select class="inscription-input" id="inscInteret" name="interest" style="cursor:pointer;">
                    <option value="">Votre intérêt principal…</option>
                    <option value="programme">Programme immobilier</option>
                    <option value="terrain">Terrain / Parcelle</option>
                    <option value="chantier">Suivi chantier</option>
                    <option value="investissement">Investissement</option>
                </select>
                <button class="inscription-submit" type="submit">
                    <i class="fas fa-paper-plane"></i> M'inscrire aux alertes
                </button>
            </form>
        </div>
    </section>

</main>

<!-- ===== FOOTER ===== -->
<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-column">
                <div class="footer-logo-container">
                    <div class="footer-logo">
                        <img src="app/IMG/logo-ecofi.png" alt="ECOFI Logo" class="footer-logo-img">
                    </div>
                    <p class="footer-description">ECOFI transforme vos projets immobiliers en réalités durables. De la conception à la réalisation, nous vous accompagnons à chaque étape.</p>
                    <div class="ecofi-badge"><i class="fas fa-certificate"></i><span>Entreprise certifiée au Sénégal</span></div>
                </div>
            </div>
            <div class="footer-column">
                <h3>Navigation</h3>
                <ul class="footer-links">
                    <li><a href="index.php#accueil"><i class="fas fa-home"></i> Accueil</a></li>
                    <li><a href="index.php#apropos"><i class="fas fa-info-circle"></i> À propos</a></li>
                    <li><a href="actualites.php"><i class="fas fa-newspaper"></i> Actualités</a></li>
                    <li><a href="index.php#services"><i class="fas fa-cogs"></i> Services</a></li>
                    <li><a href="index.php#contact"><i class="fas fa-envelope"></i> Contact</a></li>
                    <li><a href="app/admin/Views/login.php"><i class="fas fa-user"></i> Espace personnel</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Actualités</h3>
                <ul class="footer-links">
                    <li><a href="#programme-immo"><i class="fas fa-building"></i> Programme immo</a></li>
                    <li><a href="#terrains"><i class="fas fa-map"></i> Terrains</a></li>
                    <li><a href="#projets"><i class="fas fa-helmet-safety"></i> Projets en cours</a></li>
                    <li><a href="#galerie"><i class="fas fa-camera"></i> Galerie chantier</a></li>
                    <li><a href="#inscription"><i class="fas fa-bell"></i> Alertes</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Contactez-nous</h3>
                <ul class="footer-links">
                    <li><a href="https://maps.google.com/?q=Zac+Nguinth+2ème+tranche,+Thiès,+Sénégal" target="_blank"><i class="fas fa-location-dot"></i><span>Zac Nguinth Thiès, Sénégal</span></a></li>
                    <li><a href="<?= esc($tel_fixe_href); ?>"><i class="fas fa-phone"></i> <?= esc($tel_fixe); ?></a></li>
                    <li><a href="<?= esc($tel_mobile_href); ?>"><i class="fas fa-mobile-alt"></i> <?= esc($tel_mobile); ?></a></li>
                    <li><a href="<?= esc($mail_contact_url); ?>" target="_blank"><i class="fas fa-envelope"></i> <?= esc($email_contact); ?></a></li>
                </ul>
                <div class="footer-social-icons">
                    <a href="https://www.facebook.com/profile.php?id=61584334332565&mibextid=ZbWKwL" class="footer-social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/ecofiservice?igsh=MTVnY2xwcGFicm00Zw==" class="footer-social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="footer-social-icon"><i class="fab fa-linkedin-in"></i></a>
                    <a href="https://www.tiktok.com/@ecofi.service.01?_r=1&_t=ZS-93Hkr11ak5K" class="footer-social-icon"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2026 <strong>ECOFI</strong> — Etablissement de conseils sur le foncier et l'immobilier. Tous droits réservés.</p>
            <p style="margin-top:8px;font-size:.8rem;opacity:.5">Conçu avec <i class="fas fa-heart" style="color:#FF8533"></i> pour l'excellence immobilière</p>
        </div>
    </div>
</footer>

<!-- Contact actualité -->
<div class="actualite-contact-modal" id="actualiteContactModal" aria-hidden="true">
    <div class="actualite-contact-panel" role="dialog" aria-modal="true" aria-labelledby="actualiteContactTitle">
        <button type="button" class="actualite-contact-close" onclick="closeActualiteContact()" aria-label="Fermer">&times;</button>
        <div class="actualite-contact-head">
            <span>Demande d'information</span>
            <h3 id="actualiteContactTitle">Plus de détails</h3>
            <p id="actualiteContactSubtitle">Laissez vos coordonnées, l’équipe ECOFI vous recontacte rapidement.</p>
        </div>

        <form id="actualiteContactForm" class="actualite-contact-form">
            <input type="hidden" id="actualiteTypeInput" name="actualite_type">
            <input type="hidden" id="actualiteTitleInput" name="actualite_title">

            <div class="actualite-contact-selected">
                <i class="fas fa-newspaper"></i>
                <div>
                    <strong id="selectedActualiteTitle">Actualité</strong>
                    <small id="selectedActualiteType">Type</small>
                </div>
            </div>

            <div class="actualite-form-grid">
                <label>
                    Nom complet
                    <input type="text" name="nom" id="actualiteNom" required>
                </label>
                <label>
                    Téléphone
                    <input type="tel" name="telephone" id="actualiteTelephone" required>
                </label>
            </div>

            <label>
                Email
                <input type="email" name="email" id="actualiteEmail" required>
            </label>

            <label>
                Message
                <textarea name="message" id="actualiteMessage" rows="4" required></textarea>
            </label>

            <button type="submit" class="actualite-contact-submit">
                <i class="fas fa-paper-plane"></i>
                Envoyer la demande
            </button>
            <div class="form-messages" id="actualiteContactMessage"></div>
        </form>
    </div>
</div>

<!-- Notification -->
<div class="notification" id="notification">
    <i class="fas fa-check-circle"></i>
    <span id="notificationMessage"></span>
</div>

<!-- Quote loader -->
<div id="quoteLoader" class="quote-loader-overlay" style="display:none;">
    <div class="quote-loader-box">
        <div class="quote-spinner"></div>
        <p id="quoteLoaderText">Envoi en cours…</p>
    </div>
</div>
<div id="quoteStatusMessage" class="quote-status-message" style="display:none;"></div>

<!-- Zoom modal (gallery) -->
<div class="zoom-modal-overlay" id="zoomModalOverlay" style="display:none;">
    <div class="zoom-modal">
        <button class="zoom-modal-close" id="zoomModalClose">&times;</button>
        <div class="zoom-modal-content">
            <div id="zoomImageContainer" style="display:none;">
                <img id="zoomImage" src="" alt="Image agrandie" style="max-width:100%;max-height:80vh;display:block;margin:0 auto;">
            </div>
            <div id="zoomVideoContainer" style="display:none;">
                <video id="zoomVideo" controls style="max-width:100%;max-height:80vh;display:block;margin:0 auto;">
                    <source src="" type="video/mp4">
                    Votre navigateur ne supporte pas la vidéo.
                </video>
            </div>
        </div>
        <div class="zoom-modal-info">
            <h4 id="zoomTitle"></h4>
            <p id="zoomDescription"></p>
            <div class="zoom-modal-nav">
                <button id="zoomPrev"><i class="fas fa-chevron-left"></i> Précédent</button>
                <span id="zoomCounter">1 / 1</span>
                <button id="zoomNext">Suivant <i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>
</div>

<script src="actualites.js"></script>

<script src="app.js"></script>
</body>
</html>
