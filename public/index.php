<?php
session_start();

// Variables PHP réutilisables
$site_title = "Panier - Etablissement de conseils sur le foncier et l'immobilier";
$email_contact = "service.ecofi01@gmail.com";
$tel_fixe = "33 998 50 72";
$tel_mobile = "71 039 75 75";
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="../app/IMG/logo-ecofi.png" type="image/png">
</head>
<body>
    <header>
        <div class="top-navbar">
            <div class="container">
                <div class="contact-info">
                    <a href="https://mail.google.com/mail/?view=cm&fs=1&to=service.ecofi01@gmail.com&su=Contact%20ECOFI&body=Bonjour%20ECOFI,%0D%0A%0D%0AJe%20vous%20contacte%20au%20sujet%20de..."
                        target="_blank">
                        <i class="fas fa-envelope"></i> service.ecofi01@gmail.com
                    </a>
                    <a href="tel:+221339985072">
                        <i class="fas fa-phone"></i> 33 998 50 72
                    </a>
                    <a href="tel:+221710397575">
                        <i class="fas fa-mobile-alt"></i> 71 039 75 75
                    </a>
                </div>
                <div class="social-links">
                    <a href="https://www.facebook.com/profile.php?id=61584334332565&mibextid=ZbWKwL"
                        class="footer-social-icon" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/ecofiservice?igsh=MTVnY2xwcGFicm00Zw=="
                        class="footer-social-icon" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="footer-social-icon" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="footer-social-icon" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="footer-social-icon" title="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="https://www.tiktok.com/@ecofi.service.01?_r=1&_t=ZS-93Hkr11ak5K" class="footer-social-icon"
                        title="TikTok"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>

        <div class="main-navbar">
            <div class="container">
                <div class="logo">
                    <img src="../app/IMG/logo-ecofi.png" alt="ECOFI" class="custom-logo">
                    <div class="logo-definition">
                        <div class="definition-main">Etablissement de Conseils</div>
                        <div class="definition-sub">sur le Foncier et l'Immobilier</div>
                    </div>
                </div>

                <div class="nav-search-container">
                    <nav id="mainNav">
                        <ul>
                            <li><a href="#accueil">Accueil</a></li>
                            <li><a href="#apropos">À propos</a></li>
                            <li><a href="#services">Services</a></li>
                            <li><a href="#contact">Contact</a></li>
                        </ul>
                    </nav>

                    <div class="search-container">
                        <button class="search-toggle" id="searchToggle">
                            <i class="fas fa-search"></i>
                        </button>
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
                                <span><i class="fas fa-shopping-cart" style="color: var(--accent-color);"></i> Mon
                                    Panier</span>
                                <span id="cartItemCount">0 article(s)</span>
                            </div>
                            <div class="cart-items" id="cartItems">
                                <p style="text-align: center; color: #999; padding: 20px;">Votre panier est vide</p>
                            </div>
                            <div class="cart-footer">
                                <div class="cart-total">
                                    <span>Total TTC</span>
                                    <span id="cartTotal">0 FCFA</span>
                                </div>
                                <div class="cart-buttons">
                                    <button class="cart-btn cart-btn-danger" onclick="viderPanier()">
                                        <i class="fas fa-trash"></i> Vider
                                    </button>
<!--                                     <button class="cart-btn cart-btn-primary" onclick="procederAuPaiement()">
                                        <i class="fas fa-credit-card"></i> Payer
                                    </button> -->
                                    <button class="cart-btn cart-btn-primary" onclick="openQuoteModal(event)">
                                        <i class="fas fa-paper-plane"></i> Demande de devis
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="hero-modern" id="accueil">
        <div class="container">
            <div class="hero-content-modern">
                <div>
                    <h1>Construisez votre maison, <span>avec ECOFI</span></h1>
                    <p>ECOFI transforme vos projets immobiliers en réalités durables. De la conception à la réalisation,
                        nous vous accompagnons à chaque étape avec expertise et professionnalisme.</p>
                </div>
                <div class="hero-buttons-modern">
                    <a href="#services" class="btn btn-accent">Explorer nos services</a>
                    <a href="#contact" class="btn btn-outline">Demander un devis gratuit</a>
                </div>
            </div>

            <div class="hero-image-modern">
                <img src="../app/IMG/HERO.jpeg" alt="Projet de construction moderne">
            </div>

            <div class="hero-features-container">
                <div class="hero-features">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-building"></i></div>
                        <div class="feature-text">
                            <h4>BTP & Construction</h4>
                            <p>Projets clés en main</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-cubes"></i></div>
                        <div class="feature-text">
                            <h4>Production de briques</h4>
                            <p>Matériaux durables</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-paint-roller"></i></div>
                        <div class="feature-text">
                            <h4>Décoration</h4>
                            <p>Intérieur & Extérieur</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-home"></i></div>
                        <div class="feature-text">
                            <h4>Conseil immobilier</h4>
                            <p>Expertise foncière</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="apropos">
        <div class="container">
            <h2>À propos d'ECOFI</h2>
            <div class="about-content">
                <div class="about-text">
                    <p><strong>ECOFI</strong> est une entreprise sénégalaise basée à Thiès, Nguinth 2ème tranche,
                        spécialisée dans le Bâtiment et les Travaux Publics (BTP), la production de briques et pavés, la
                        décoration intérieure et extérieure, la vente et distribution de matériaux de construction,
                        ainsi que le conseil immobilier.</p>
                    <p>Notre mission est de fournir des services de haute qualité dans le secteur de la construction et
                        de l'immobilier, en alliant expertise technique, innovation et respect des normes
                        environnementales.</p>
                    <p>Avec une équipe d'experts passionnés, nous nous engageons à transformer vos projets en réalités
                        durables, tout en garantissant un accompagnement personnalisé à chaque étape.</p>

                    <button class="btn-voir-plus" onclick="window.location.href='#services'">Voir tous nos
                        services</button>
                </div>

                <div class="carrousel-container-modern">
                    <div class="carrousl-track-modern auto-slide">
                        <div class="carrousel-slide-modern active">
                            <img src="../app/IMG/chantier.jpg" alt="Chantier de construction">
                            <div class="slide-overlay-modern">
                                <div class="slide-title-modern">Chantier professionnel</div>
                                <div class="slide-description-modern">Suivi rigoureux de chaque projet</div>
                            </div>
                        </div>
                        <div class="carrousel-slide-modern">
                            <img src="../app/IMG/briques.jpg" alt="Production de briques">
                            <div class="slide-overlay-modern">
                                <div class="slide-title-modern">Briques qualité supérieure</div>
                                <div class="slide-description-modern">Matériaux durables fabriqués localement</div>
                            </div>
                        </div>
                        <div class="carrousel-slide-modern">
                            <img src="../app/IMG/decointerieur.jpg" alt="Décoration intérieure">
                            <div class="slide-overlay-modern">
                                <div class="slide-title-modern">Décoration sur mesure</div>
                                <div class="slide-description-modern">Espaces harmonieux adaptés à vos besoins</div>
                            </div>
                        </div>
                        <div class="carrousel-slide-modern">
                            <img src="../app/IMG/expertise.jpeg" alt="Conseil immobilier">
                            <div class="slide-overlay-modern">
                                <div class="slide-title-modern">Expertise foncière</div>
                                <div class="slide-description-modern">Accompagnement personnalisé</div>
                            </div>
                        </div>
                        <div class="carrousel-slide-modern">
                            <img src="../app/IMG/conception.jpg" alt="Plans architecturaux">
                            <div class="slide-overlay-modern">
                                <div class="slide-title-modern">Conception technique</div>
                                <div class="slide-description-modern">Plans détaillés et conformes</div>
                            </div>
                        </div>
                        <div class="carrousel-slide-modern">
                            <img src="../app/IMG/materiel.jpeg" alt="Matériaux construction">
                            <div class="slide-overlay-modern">
                                <div class="slide-title-modern">Matériaux de qualité</div>
                                <div class="slide-description-modern">Large gamme de produits disponibles</div>
                            </div>
                        </div>
                        <div class="carrousel-slide-modern">
                            <img src="../app/IMG/matopo.jpg" alt="Matériel topographique">
                            <div class="slide-overlay-modern">
                                <div class="slide-title-modern">Équipement moderne</div>
                                <div class="slide-description-modern">Location de GPS et matériel de précision</div>
                            </div>
                        </div>
                        <div class="carrousel-slide-modern">
                            <img src="../app/IMG/com.jpeg" alt="Projet terminé">
                            <div class="slide-overlay-modern">
                                <div class="slide-title-modern">Réalisations complètes</div>
                                <div class="slide-description-modern">De la conception à la livraison</div>
                            </div>
                        </div>
                    </div>

                    <div class="carrousel-dots-modern">
                        <span class="dot-modern active" data-slide="0"></span>
                        <span class="dot-modern" data-slide="1"></span>
                        <span class="dot-modern" data-slide="2"></span>
                        <span class="dot-modern" data-slide="3"></span>
                        <span class="dot-modern" data-slide="4"></span>
                        <span class="dot-modern" data-slide="5"></span>
                        <span class="dot-modern" data-slide="6"></span>
                        <span class="dot-modern" data-slide="7"></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-light" id="services">
        <div class="container">
            <div class="service-header">
                <h2>Nos Services</h2>
                <p>Découvrez nos services avec options d'achat</p>
            </div>

            <div class="service-row">
                <div class="service-card-compact" data-expertise="briques">
                    <div class="service-img-compact">
                        <img src="../app/IMG/bripa.jpg" alt="Production de briques et pavés">
                        <div class="service-overlay">
                            <div class="service-icon"><i class="fas fa-cubes"></i></div>
                        </div>
                    </div>
                    <div class="service-content-compact">
                        <h3>Production de briques et paves</h3>
                        <p>Matériaux de construction pour vos projets</p>
                        <div class="service-buttons-compact">
                            <button class="btn-service-compact btn-details-compact" data-target="briques">Détails</button>
                        </div>
                    </div>
                </div>

                <div class="service-card-compact" data-expertise="materiaux">
                    <div class="service-img-compact">
                        <img src="../app/IMG/materiel.jpeg" alt="Matériaux de construction">
                        <div class="service-overlay">
                            <div class="service-icon"><i class="fas fa-truck-loading"></i></div>
                        </div>
                    </div>
                    <div class="service-content-compact">
                        <h3>Matériaux et Equipements</h3>
                        <p>Approvisionnement pour tous vos chantiers</p>
                        <div class="service-buttons-compact">
                            <button class="btn-service-compact btn-details-compact" data-target="materiaux">Détails</button>
                        </div>
                    </div>
                </div>

                <div class="service-card-compact" data-expertise="decoration">
                    <div class="service-img-compact">
                        <video class="service-video" playsinline muted loop autoplay preload="auto"
                            poster="../app/IMG/decointerieur.jpg">
                            <source src="../app/IMG/video2.mp4" type="video/mp4">
                            <source src="../app/IMG/video2.mp4" type="video/mp4">
                            <img src="../app/IMG/decointerieur.jpg" alt="Décoration">
                        </video>
                        <div class="service-overlay">
                            <div class="service-icon"><i class="fas fa-paint-roller"></i></div>
                        </div>
                    </div>
                    <div class="service-content-compact">
                        <h3>Décoration</h3>
                        <p>Intérieur & extérieur sur mesure</p>
                        <div class="service-buttons-compact">
                            <button class="btn-service-compact btn-details-compact" data-target="decoration">Détails</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="service-row">
                <div class="service-card-compact" data-expertise="conseil">
                    <div class="service-img-compact">
                        <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                            alt="Conseil immobilier">
                        <div class="service-overlay">
                            <div class="service-icon"><i class="fas fa-home"></i></div>
                        </div>
                    </div>
                    <div class="service-content-compact">
                        <h3>Conseil immobilier</h3>
                        <p>Expertise et accompagnement personnalisé</p>
                        <div class="service-buttons-compact">
                            <button class="btn-service-compact btn-details-compact" data-target="conseil">Détails</button>
                        </div>
                    </div>
                </div>

                <div class="service-card-compact" data-expertise="plans">
                    <div class="service-img-compact">
                        <img src="../app/IMG/plan.jpeg" alt="Conception de plans">
                        <div class="service-overlay">
                            <div class="service-icon"><i class="fas fa-drafting-compass"></i></div>
                        </div>
                    </div>
                    <div class="service-content-compact">
                        <h3>Plans architecturaux</h3>
                        <p>Conception technique détaillée</p>
                        <div class="service-buttons-compact">
                            <button class="btn-service-compact btn-details-compact" data-target="plans">Détails</button>
                        </div>
                    </div>
                </div>

                <div class="service-card-compact" data-expertise="location">
                    <div class="service-img-compact">
                        <img src="../app/IMG/location recepteur.jpg" alt="Location de GPS">
                        <div class="service-overlay">
                            <div class="service-icon"><i class="fas fa-satellite-dish"></i></div>
                        </div>
                    </div>
                    <div class="service-content-compact">
                        <h3>Location Recepteur</h3>
                        <p>Équipements topographiques de précision</p>
                        <div class="service-buttons-compact">
                            <button class="btn-service-compact btn-details-compact" data-target="location">Détails</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="promo-section">
        <div class="container">
            <h2>Codes Promo du Moment</h2>
            <div class="promo-grid" id="promoGrid"></div>
        </div>
    </section>

    <div class="modal-overlay" id="productsModal">
        <div class="modal products-modal">
            <div class="modal-header">
                <h3 id="productsModalTitle">Produits disponibles</h3>
                <button class="modal-close" id="productsModalClose">&times;</button>
            </div>
            <div class="modal-body" id="productsModalBody"></div>
        </div>
    </div>

    <div class="quote-modal-overlay" id="quoteModal">
        <div class="quote-modal">
            <div class="quote-modal-header">
                <h3><i class="fas fa-file-invoice" style="color: var(--accent-color);"></i> Demande de Devis</h3>
                <button class="quote-modal-close" onclick="closeQuoteModal()">&times;</button>
            </div>
            <div class="quote-modal-body">
                <div class="quote-items" id="quoteItems"></div>
                <div class="quote-total" id="quoteTotal">Total: 0 FCFA</div>
                <form id="quoteForm">
                    <div class="form-group">
                        <label for="quoteName">Nom complet</label>
                        <input type="text" id="quoteName" required placeholder="Votre nom">
                    </div>
                    <div class="form-group">
                        <label for="quoteEmail">Email</label>
                        <input type="email" id="quoteEmail" required placeholder="votre@email.com">
                    </div>
                    <div class="form-group">
                        <label for="quotePhone">Téléphone</label>
                        <input type="tel" id="quotePhone" required placeholder="77 123 45 67">
                    </div>
                    <div class="form-group">
                        <label for="quoteMessage">Message (optionnel)</label>
                        <textarea id="quoteMessage" placeholder="Votre message..."></textarea>
                    </div>
                    <button type="submit" class="btn-primary" onclick="submitQuote(event)">
                        <i class="fas fa-paper-plane"></i> Envoyer la demande
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="payment-modal-overlay" id="paymentModal">
        <div class="payment-modal">
            <div class="payment-modal-header">
                <h3><i class="fas fa-credit-card" style="color: var(--accent-color);"></i> Paiement sécurisé</h3>
                <button class="payment-modal-close" onclick="closePaymentModal()">&times;</button>
            </div>
            <div class="payment-modal-body">
                <div class="payment-summary" id="paymentSummary"></div>

                <h4 style="margin-bottom: 1rem;">Choisissez votre mode de paiement</h4>
                <div class="payment-methods" id="paymentMethods">
                    <div class="payment-method" onclick="selectPaymentMethod('wave')" id="method-wave">
                        <img src="../app/IMG/wave-logo.webp" alt="Wave" class="payment-logo">
                        <small>Paiement mobile</small>
                    </div>
                    <div class="payment-method" onclick="selectPaymentMethod('orange')" id="method-orange">
                        <img src="../app/IMG/orange-money-logo.jpg" alt="Orange Money" class="payment-logo">
                        <small>Paiement mobile</small>
                    </div>
                    <div class="payment-method" onclick="selectPaymentMethod('card')" id="method-card">
                        <img src="../app/IMG/carte.png" alt="Carte bancaire" class="payment-logo">
                        <small>Visa, Mastercard</small>
                    </div>
                    <div class="payment-method" onclick="selectPaymentMethod('cash')" id="method-cash">
                        <img src="../app/IMG/livraison.jfif" alt="Paiement à la livraison" class="payment-logo">
                        <small>Payer à la réception</small>
                    </div>
                </div>

                <div id="paymentFormContainer"></div>

                <div class="payment-secure-badge">
                    <i class="fas fa-lock"></i>
                    <span>Paiement 100% sécurisé - Vos données sont protégées</span>
                </div>
            </div>
        </div>
    </div>

    <div class="facture-modal-overlay" id="factureModal">
        <div class="facture-modal">
            <div class="facture-modal-header">
                <h3><i class="fas fa-file-invoice"></i> FACTURE</h3>
                <button class="facture-modal-close" onclick="closeFactureModal()">&times;</button>
            </div>
            <div class="facture-modal-body" id="factureContent"></div>
            <div class="facture-modal-footer">
                <button class="btn btn-accent" onclick="telechargerFacture()">
                    <i class="fas fa-download"></i> Télécharger la facture
                </button>
                <button class="btn btn-outline" onclick="closeFactureModal()">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    <section id="contact" class="contact-elegant">
        <div class="container">
            <div class="contact-header">
                <h2>Nous contacter</h2>
                <p class="contact-subtitle">Prêts à concrétiser votre projet ? Parlons-en !</p>
            </div>

            <div class="contact-grid">
                <div class="contact-info-elegant">
                    <div class="contact-card-elegant">
                        <div class="card-header">
                            <div class="header-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <h3>Où nous trouver</h3>
                        </div>

                        <div class="address-elegant">
                            <div class="address-line">
                                <i class="fas fa-building"></i>
                                <span>À côté de la Pharmacie Keur Baye Thier</span>
                            </div>
                            <div class="address-line">
                                <i class="fas fa-location-dot"></i>
                                <span>Zac de Nginth, Thiès</span>
                            </div>
                        </div>

                        <div class="contact-methods">
                            <div class="contact-method">
                                <div class="method-icon phone">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="method-info">
                                    <h4>Téléphone</h4>
                                    <div class="method-links">
                                        <a href="tel:+221339985072" class="phone-link">33 998 50 72</a>
                                        <a href="tel:+221710397575" class="phone-link">71 039 75 75</a>
                                    </div>
                                </div>
                            </div>

                            <div class="contact-method">
                                <div class="method-icon email">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="method-info">
                                    <h4>Email</h4>
                                    <a href="https://mail.google.com/mail/?view=cm&fs=1&to=service.ecofi01@gmail.com&su=Contact%20ECOFI&body=Bonjour%20ECOFI,%0D%0A%0D%0AJe%20vous%20contacte%20au%20sujet%20de..."
                                        target="_blank" class="email-link">
                                        service.ecofi01@gmail.com
                                    </a>
                                </div>
                            </div>

                            <div class="contact-method">
                                <div class="method-icon whatsapp">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <div class="method-info">
                                    <h4>WhatsApp</h4>
                                    <a href="https://wa.me/221710397575" target="_blank" class="whatsapp-link">
                                        Écrivez-nous directement
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="map-preview">
                            <div class="map-placeholder">
                                <i class="fas fa-map"></i>
                                <p>Localisation Thiès, Sénégal</p>
                                <a href="https://maps.google.com/?q=14.824917,-16.932083" target="_blank"
                                    class="view-map-btn">
                                    <i class="fas fa-external-link-alt"></i>
                                    Voir sur la carte
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="contact-form-elegant">
                    <div class="form-intro">
                        <h3>Demande de devis</h3>
                        <p>Recevez un devis personnalisé gratuit en 24h</p>
                    </div>

                    <form id="elegantContactForm">
                        <div class="form-group-elegant">
                            <input type="text" placeholder="Votre nom complet" required>
                        </div>

                        <div class="form-row-elegant">
                            <div class="form-group-elegant">
                                <input type="email" placeholder="Adresse email" required>
                            </div>
                            <div class="form-group-elegant">
                                <input type="tel" placeholder="Numéro de téléphone" required>
                            </div>
                        </div>

                        <div class="form-group-elegant">
                            <select required>
                                <option value="">Type de service</option>
                                <option>Production de briques</option>
                                <option>Matériaux de construction</option>
                                <option>Décoration intérieure/extérieure</option>
                                <option>Conseil immobilier</option>
                                <option>Conception de plans</option>
                                <option>Location GPS</option>
                                <option>Construction BTP</option>
                            </select>
                        </div>

                        <div class="form-group-elegant">
                            <textarea placeholder="Décrivez votre projet..." rows="4" required></textarea>
                        </div>

                        <button type="submit" class="submit-btn-elegant">
                            <i class="fas fa-paper-plane"></i>
                            Envoyer ma demande
                        </button>

                        <div id="formMessages"></div>
                    </form>

                    <div class="contact-hours">
                        <h4><i class="fas fa-clock"></i> Horaires d'ouverture</h4>
                        <div class="hours-grid">
                            <div class="hour-item">
                                <span class="days">Lun - Ven</span>
                                <span class="time">9h - 17h</span>
                            </div>
                            <div class="hour-item">
                                <span class="days">Samedi</span>
                                <span class="time">9h - 13h</span>
                            </div>
                            <div class="hour-item">
                                <span class="days">Dimanche</span>
                                <span class="time closed">Sur RDV</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <div class="footer-logo-container">
                        <div class="footer-logo">
                            <img src="../app/IMG/logo-ecofi.png" alt="ECOFI Logo" class="footer-logo-img">
                        </div>
                        <p class="footer-description">
                            ECOFI transforme vos projets immobiliers en réalités durables.
                            De la conception à la réalisation, nous vous accompagnons à chaque étape.
                        </p>
                        <div class="ecofi-badge">
                            <i class="fas fa-certificate"></i>
                            <span>Entreprise certifiée au Sénégal</span>
                        </div>
                    </div>
                </div>

                <div class="footer-column">
                    <h3>Navigation</h3>
                    <ul class="footer-links">
                        <li><a href="#accueil"><i class="fas fa-home"></i> Accueil</a></li>
                        <li><a href="#apropos"><i class="fas fa-info-circle"></i> À propos</a></li>
                        <li><a href="#services"><i class="fas fa-cogs"></i> Nos services</a></li>
                        <li><a href="#contact"><i class="fas fa-envelope"></i> Contact</a></li>
                        <li><a href="/SITEECOFI/app/admin/Views/login.php"><i class="fas fa-user"></i> Espace personnel</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3>Nos Services</h3>
                    <ul class="footer-links">
                        <li><a href="#services" onclick="openProductsModal('briques')"><i class="fas fa-cubes"></i> Production de briques</a></li>
                        <li><a href="#services" onclick="openProductsModal('materiaux')"><i class="fas fa-truck-loading"></i> Matériaux</a></li>
                        <li><a href="#services" onclick="openProductsModal('decoration')"><i class="fas fa-paint-roller"></i> Décoration</a></li>
                        <li><a href="#services" onclick="openProductsModal('conseil')"><i class="fas fa-building"></i> Conseil immobilier</a></li>
                        <li><a href="#services" onclick="openProductsModal('plans')"><i class="fas fa-drafting-compass"></i> Plans</a></li>
                        <li><a href="#services" onclick="openProductsModal('location')"><i class="fas fa-satellite-dish"></i> Location</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3>Contactez-nous</h3>
                    <ul class="footer-links">
                        <li>
                            <a href="https://maps.google.com/?q=Zac+Nguinth+2ème+tranche,+Thiès,+Sénégal" target="_blank">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Zac Nguinth Thiès, Sénégal</span>
                            </a>
                        </li>
                        <li><a href="tel:+221339985072"><i class="fas fa-phone"></i> 33 998 50 72</a></li>
                        <li><a href="tel:+221710397575"><i class="fas fa-mobile-alt"></i> 71 039 75 75</a></li>
                        <li><a href="https://mail.google.com/mail/?view=cm&fs=1&to=service.ecofi01@gmail.com" target="_blank">
                                <i class="fas fa-envelope"></i> service.ecofi01@gmail.com
                            </a></li>
                    </ul>

                    <div class="footer-social-icons">
                        <a href="https://www.facebook.com/profile.php?id=61584334332565&mibextid=ZbWKwL"
                            class="footer-social-icon" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/ecofiservice?igsh=MTVnY2xwcGFicm00Zw=="
                            class="footer-social-icon" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="footer-social-icon" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="footer-social-icon" title="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.tiktok.com/@ecofi.service.01?_r=1&_t=ZS-93Hkr11ak5K"
                            class="footer-social-icon" title="TikTok"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>

            <div class="copyright">
                <p>&copy; 2026 <strong>ECOFI</strong> - Etablissement de conseils sur le foncier et l'immobilier. Tous droits réservés.</p>
                <p style="margin-top: 10px; font-size: 0.8rem; opacity: 0.6;">
                    Conçu avec <i class="fas fa-heart" style="color: #FF8533;"></i> pour l'excellence immobilière
                </p>
            </div>
        </div>
    </footer>

    <div class="notification" id="notification">
        <i class="fas fa-check-circle"></i>
        <span id="notificationMessage"></span>
    </div>

    <div class="zoom-modal-overlay" id="zoomModalOverlay">
        <div class="zoom-modal">
            <button class="zoom-modal-close" id="zoomModalClose">&times;</button>
            <div class="zoom-modal-content">
                <div id="zoomImageContainer" style="display: none;">
                    <img id="zoomImage" src="" alt="Image agrandie">
                </div>
                <div id="zoomVideoContainer" style="display: none;">
                    <video id="zoomVideo" controls autoplay>
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

    <script src="../public/app.js"></script>
        <script>
        const PHP_CONFIG = {
            emailContact: "<?php echo addslashes($email_contact); ?>",
            telFixe: "<?php echo addslashes($tel_fixe); ?>",
            telMobile: "<?php echo addslashes($tel_mobile); ?>"
        };
    </script>

    <script>
        // Ici tu gardes tout ton JavaScript actuel
    </script>
</body>
</html>
