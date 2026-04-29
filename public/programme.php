<?php
session_start();

$site_title = "Programme Immobilier | ECOFI";
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
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="../app/IMG/logo-ecofi.png" type="image/png">
    <style>
        .programme-hero {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
        }

        .programme-hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .programme-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 10px 25px;
            border-radius: 25px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
        }

        .programme-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .programme-section {
            margin-bottom: 60px;
        }

        .programme-section h2 {
            font-size: 36px;
            color: #1a1a1a;
            margin-bottom: 25px;
            border-bottom: 3px solid #d97706;
            padding-bottom: 15px;
        }

        .programme-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
            margin-bottom: 40px;
        }

        .programme-image {
            width: 100%;
            height: 400px;
            background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .programme-image i {
            font-size: 80px;
            color: #d97706;
            opacity: 0.3;
        }

        .programme-text h3 {
            font-size: 28px;
            color: #1a1a1a;
            margin-bottom: 15px;
        }

        .programme-text p {
            font-size: 16px;
            color: #666;
            line-height: 1.8;
            margin-bottom: 15px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin: 40px 0;
        }

        .feature-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            border-color: #d97706;
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.1);
        }

        .feature-number {
            font-size: 32px;
            font-weight: bold;
            color: #d97706;
            margin-bottom: 10px;
        }

        .feature-label {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
            font-weight: 600;
        }

        .advantages-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
        }

        .advantage-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
        }

        .advantage-item i {
            font-size: 24px;
            color: #d97706;
            margin-top: 2px;
        }

        .advantage-item h4 {
            font-size: 16px;
            color: #1a1a1a;
            margin-bottom: 5px;
        }

        .advantage-item p {
            font-size: 14px;
            color: #666;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 30px 0;
        }

        .gallery-item {
            height: 250px;
            background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .gallery-item:hover {
            transform: scale(1.05);
        }

        .gallery-item i {
            font-size: 60px;
            color: #d97706;
            opacity: 0.3;
        }

        .cta-section {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: white;
            padding: 50px;
            border-radius: 12px;
            text-align: center;
            margin-top: 60px;
        }

        .cta-section h2 {
            color: white;
            border: none;
            margin-bottom: 20px;
            padding-bottom: 0;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .btn-primary, .btn-secondary {
            padding: 14px 40px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: white;
            color: #d97706;
        }

        .btn-primary:hover {
            background: #f3f4f6;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .programme-hero h1 {
                font-size: 32px;
            }

            .programme-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .gallery-grid {
                grid-template-columns: 1fr;
            }

            .advantages-list {
                grid-template-columns: 1fr;
            }

            .cta-buttons {
                flex-direction: column;
            }

            .btn-primary, .btn-secondary {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="top-navbar">
            <div class="container">
                <div class="contact-info">
                    <a href="mailto:service.ecofi01@gmail.com">
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
                    <a href="https://www.facebook.com/profile.php?id=61584334332565" class="footer-social-icon">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.instagram.com/ecofiservice" class="footer-social-icon">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
        </div>

        <nav class="navbar">
            <div class="container">
                <div class="nav-logo">
                    <img src="../app/IMG/logo-ecofi.png" alt="ECOFI Logo">
                    <span>ECOFI</span>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="#programmes">Programmes</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="programme-hero">
        <span class="programme-badge">🏗 Nouveau Programme</span>
        <h1>Résidence Les Palmiers</h1>
        <p style="font-size: 20px; margin-top: 10px;">Dakar, Almadies</p>
    </section>

    <!-- Main Content -->
    <main class="programme-container">
        <!-- Description Section -->
        <section class="programme-section">
            <div class="programme-grid">
                <div class="programme-image">
                    <i class="fas fa-building"></i>
                </div>
                <div class="programme-text">
                    <h3>Un projet d'exception au cœur des Almadies</h3>
                    <p>
                        La Résidence Les Palmiers est un programme immobilier de prestige situé dans l'une des 
                        zones les plus recherchées de Dakar. Ce projet offre une combinaison unique d'élégance 
                        moderne et de confort, parfait pour les familles et les investisseurs.
                    </p>
                    <p>
                        Caractérisé par des appartements spacieux, des finitions haut de gamme, une vue imprenable 
                        sur la mer et une sécurité 24h/24, ce programme est conçu pour les personnes exigeantes qui 
                        recherchent qualité et prestige.
                    </p>
                </div>
            </div>
        </section>

        <!-- Key Features -->
        <section class="programme-section">
            <h2>Caractéristiques principales</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-number">48</div>
                    <div class="feature-label">Unités disponibles</div>
                </div>
                <div class="feature-card">
                    <div class="feature-number">F3-F5</div>
                    <div class="feature-label">Types d'appartements</div>
                </div>
                <div class="feature-card">
                    <div class="feature-number">32%</div>
                    <div class="feature-label">Déjà vendus</div>
                </div>
                <div class="feature-card">
                    <div class="feature-number">2026</div>
                    <div class="feature-label">Date de livraison</div>
                </div>
            </div>
        </section>

        <!-- Advantages -->
        <section class="programme-section">
            <h2>Avantages & Points forts</h2>
            <div class="advantages-list">
                <div class="advantage-item">
                    <i class="fas fa-water"></i>
                    <div>
                        <h4>Vue imprenable sur la mer</h4>
                        <p>Profitez d'une vue spectaculaire sur l'océan Atlantique depuis votre balcon</p>
                    </div>
                </div>
                <div class="advantage-item">
                    <i class="fas fa-shield-alt"></i>
                    <div>
                        <h4>Sécurité 24h/24</h4>
                        <p>Surveillance électronique et personnel de sécurité disponible en permanence</p>
                    </div>
                </div>
                <div class="advantage-item">
                    <i class="fas fa-tree"></i>
                    <div>
                        <h4>Espaces verts</h4>
                        <p>Jardins paysagés et espaces de détente pour une vie agréable</p>
                    </div>
                </div>
                <div class="advantage-item">
                    <i class="fas fa-swimming-pool"></i>
                    <div>
                        <h4>Piscine commune</h4>
                        <p>Équipement de loisir moderne pour votre bien-être</p>
                    </div>
                </div>
                <div class="advantage-item">
                    <i class="fas fa-parking"></i>
                    <div>
                        <h4>Parking privatif</h4>
                        <p>Places de parking couvertes et sécurisées pour chaque unité</p>
                    </div>
                </div>
                <div class="advantage-item">
                    <i class="fas fa-hammer"></i>
                    <div>
                        <h4>Finitions premium</h4>
                        <p>Matériaux de haute qualité et finitions soignées</p>
                    </div>
                </div>
                <div class="advantage-item">
                    <i class="fas fa-heart"></i>
                    <div>
                        <h4>Bien-être</h4>
                        <p>Salle de gym, sauna et espace wellness</p>
                    </div>
                </div>
                <div class="advantage-item">
                    <i class="fas fa-location-dot"></i>
                    <div>
                        <h4>Localisation premium</h4>
                        <p>Proche des commerces, restaurants et écoles réputées</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Gallery -->
        <section class="programme-section">
            <h2>Galerie d'images</h2>
            <div class="gallery-grid">
                <div class="gallery-item">
                    <i class="fas fa-image"></i>
                </div>
                <div class="gallery-item">
                    <i class="fas fa-image"></i>
                </div>
                <div class="gallery-item">
                    <i class="fas fa-image"></i>
                </div>
                <div class="gallery-item">
                    <i class="fas fa-image"></i>
                </div>
                <div class="gallery-item">
                    <i class="fas fa-image"></i>
                </div>
                <div class="gallery-item">
                    <i class="fas fa-image"></i>
                </div>
            </div>
        </section>

        <!-- Location Section -->
        <section class="programme-section">
            <h2>Localisation</h2>
            <div class="programme-grid">
                <div style="background: #f9fafb; padding: 30px; border-radius: 10px;">
                    <h3 style="margin-bottom: 15px;">📍 Dakar, Almadies</h3>
                    <p style="margin-bottom: 10px;"><strong>Adresse:</strong> Route de l'Aéroport, Almadies, Dakar</p>
                    <p style="margin-bottom: 10px;"><strong>Distance du centre:</strong> 15 km</p>
                    <p style="margin-bottom: 10px;"><strong>Accessibilité:</strong> Excellente (autoroute, transports en commun)</p>
                    <p><strong>Environnement:</strong> Zone calme et résidentielle avec commerces à proximité</p>
                </div>
                <div style="background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%); border-radius: 10px; height: 300px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-map" style="font-size: 60px; color: #d97706; opacity: 0.3;"></i>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section class="programme-section">
            <h2>Tarification</h2>
            <div class="advantages-list">
                <div class="feature-card" style="border: 2px solid #d97706; padding: 30px; text-align: left;">
                    <h4 style="color: #d97706; margin-bottom: 15px; font-size: 18px;">F3 (2 chambres)</h4>
                    <p><strong>Surface:</strong> 85 m²</p>
                    <p><strong>À partir de:</strong> <span style="font-size: 24px; color: #d97706; font-weight: bold;">35 000 000 FCFA</span></p>
                </div>
                <div class="feature-card" style="border: 2px solid #d97706; padding: 30px; text-align: left;">
                    <h4 style="color: #d97706; margin-bottom: 15px; font-size: 18px;">F4 (3 chambres)</h4>
                    <p><strong>Surface:</strong> 120 m²</p>
                    <p><strong>À partir de:</strong> <span style="font-size: 24px; color: #d97706; font-weight: bold;">48 000 000 FCFA</span></p>
                </div>
                <div class="feature-card" style="border: 2px solid #d97706; padding: 30px; text-align: left;">
                    <h4 style="color: #d97706; margin-bottom: 15px; font-size: 18px;">F5 (4 chambres)</h4>
                    <p><strong>Surface:</strong> 160 m²</p>
                    <p><strong>À partir de:</strong> <span style="font-size: 24px; color: #d97706; font-weight: bold;">65 000 000 FCFA</span></p>
                </div>
                <div class="feature-card" style="border: 2px solid #d97706; padding: 30px; text-align: left;">
                    <h4 style="color: #d97706; margin-bottom: 15px; font-size: 18px;">Penthouse</h4>
                    <p><strong>Surface:</strong> 200+ m²</p>
                    <p><strong>À partir de:</strong> <span style="font-size: 24px; color: #d97706; font-weight: bold;">90 000 000 FCFA</span></p>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <h2>Prêt à investir ?</h2>
            <p style="font-size: 18px; margin: 15px 0;">Inscrivez-vous maintenant pour recevoir plus d'informations et réserver votre unité</p>
            <div class="cta-buttons">
                <a href="inscription.php?type=programme" class="btn-primary">
                    <i class="fas fa-edit"></i> S'inscrire
                </a>
                <a href="inscription.php?type=contact" class="btn-secondary">
                    <i class="fas fa-phone"></i> Être contacté
                </a>
            </div>
        </section>
    </main>

    <footer style="background: #1a1a1a; color: white; padding: 40px 20px; margin-top: 60px;">
        <div class="container">
            <div style="text-align: center;">
                <p>&copy; 2024-2026 ECOFI - Établissement de Conseils sur le Foncier et l'Immobilier</p>
                <p style="margin-top: 10px; font-size: 14px;">
                    <a href="mailto:service.ecofi01@gmail.com" style="color: #d97706;">service.ecofi01@gmail.com</a> | 
                    <a href="tel:+221339985072" style="color: #d97706;">33 998 50 72</a>
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
