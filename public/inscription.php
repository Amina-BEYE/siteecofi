<?php
session_start();

$site_title = "Inscription - Programme Immobilier | ECOFI";
$email_contact = "service.ecofi01@gmail.com";
$type = $_GET['type'] ?? 'programme';
$message = $_SESSION['message'] ?? null;
$message_type = $_SESSION['message_type'] ?? 'success';

// Nettoyer les messages de session
if (isset($_SESSION['message'])) {
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
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
        .inscription-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .inscription-header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #d97706;
            padding-bottom: 20px;
        }

        .inscription-header h1 {
            font-size: 36px;
            color: #1a1a1a;
            margin-bottom: 10px;
        }

        .inscription-header p {
            color: #666;
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1a1a1a;
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #d97706;
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .checkbox-group label {
            margin: 0;
            font-weight: 400;
            cursor: pointer;
        }

        .submit-btn {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: white;
            padding: 14px 50px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(217, 119, 6, 0.2);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert.success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert i {
            font-size: 20px;
        }

        .form-type-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #e5e7eb;
        }

        .form-type-tab {
            padding: 12px 20px;
            background: none;
            border: none;
            color: #666;
            font-weight: 600;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .form-type-tab.active {
            color: #d97706;
            border-bottom-color: #d97706;
        }

        .programme-badge {
            display: inline-block;
            background: rgba(217, 119, 6, 0.1);
            color: #d97706;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .inscription-container {
                margin: 20px;
                padding: 25px;
            }

            .inscription-header h1 {
                font-size: 24px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .form-type-tabs {
                flex-wrap: wrap;
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
                    <li><a href="programme.php">Programme</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <div class="inscription-container">
            <div class="inscription-header">
                <span class="programme-badge">Résidence Les Palmiers</span>
                <h1><?php echo $type === 'contact' ? 'Demander une Information' : 'S\'inscrire au Programme'; ?></h1>
                <p>Complétez le formulaire ci-dessous pour nous rejoindre</p>
            </div>

            <?php if ($message): ?>
                <div class="alert <?php echo $message_type === 'error' ? 'error' : 'success'; ?>">
                    <i class="fas <?php echo $message_type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'; ?>"></i>
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>

            <div class="form-type-tabs">
                <button class="form-type-tab <?php echo $type !== 'contact' ? 'active' : ''; ?>" onclick="window.location.href='inscription.php?type=programme'">
                    <i class="fas fa-edit"></i> S'inscrire
                </button>
                <button class="form-type-tab <?php echo $type === 'contact' ? 'active' : ''; ?>" onclick="window.location.href='inscription.php?type=contact'">
                    <i class="fas fa-phone"></i> Demander un contact
                </button>
            </div>

            <form method="POST" action="process_inscription.php">
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">

                <!-- Informations Personnelles -->
                <div style="margin-bottom: 30px; border-bottom: 2px solid #e5e7eb; padding-bottom: 30px;">
                    <h3 style="color: #1a1a1a; margin-bottom: 20px;">📋 Informations personnelles</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom">Nom complet *</label>
                            <input type="text" id="nom" name="nom" required placeholder="Votre nom complet">
                        </div>
                        <div class="form-group">
                            <label for="email">Adresse email *</label>
                            <input type="email" id="email" name="email" required placeholder="votre.email@exemple.com">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="telephone">Téléphone *</label>
                            <input type="tel" id="telephone" name="telephone" required placeholder="Votre numéro de téléphone">
                        </div>
                        <div class="form-group">
                            <label for="statut">Statut *</label>
                            <select id="statut" name="statut" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="particulier">Particulier</option>
                                <option value="investisseur">Investisseur</option>
                                <option value="professionnel">Professionnel</option>
                            </select>
                        </div>
                    </div>

                    <?php if ($type !== 'contact'): ?>
                        <div class="form-group">
                            <label for="adresse">Adresse actuelle</label>
                            <input type="text" id="adresse" name="adresse" placeholder="Votre adresse">
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Programme ou Intérêt -->
                <div style="margin-bottom: 30px; border-bottom: 2px solid #e5e7eb; padding-bottom: 30px;">
                    <h3 style="color: #1a1a1a; margin-bottom: 20px;">🏢 Programme</h3>

                    <div class="form-group">
                        <label for="programme">Sélectionnez le programme *</label>
                        <select id="programme" name="programme" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="residence-palmiers">Résidence Les Palmiers - Dakar</option>
                            <option value="autre">Autre programme</option>
                        </select>
                    </div>

                    <?php if ($type !== 'contact'): ?>
                        <div class="form-group">
                            <label for="type_bien">Type de bien intéressé *</label>
                            <select id="type_bien" name="type_bien" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="f3">F3 (2 chambres) - À partir de 35M FCFA</option>
                                <option value="f4">F4 (3 chambres) - À partir de 48M FCFA</option>
                                <option value="f5">F5 (4 chambres) - À partir de 65M FCFA</option>
                                <option value="penthouse">Penthouse - À partir de 90M FCFA</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="budget">Budget estimé *</label>
                            <select id="budget" name="budget" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="25-50">25M - 50M FCFA</option>
                                <option value="50-75">50M - 75M FCFA</option>
                                <option value="75-100">75M - 100M FCFA</option>
                                <option value="100">100M+ FCFA</option>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Message -->
                <div style="margin-bottom: 30px; border-bottom: 2px solid #e5e7eb; padding-bottom: 30px;">
                    <h3 style="color: #1a1a1a; margin-bottom: 20px;">💬 Message</h3>

                    <div class="form-group">
                        <label for="message">
                            Message <?php echo $type === 'contact' ? '*' : '(optionnel)'; ?>
                        </label>
                        <textarea id="message" name="message" <?php echo $type === 'contact' ? 'required' : ''; ?> 
                            placeholder="Parlez-nous de vos attentes, questions ou demandes spécifiques..."></textarea>
                    </div>
                </div>

                <!-- Préférences de Contact -->
                <div style="margin-bottom: 30px;">
                    <h3 style="color: #1a1a1a; margin-bottom: 20px;">📞 Préférences de contact</h3>

                    <div class="checkbox-group">
                        <input type="checkbox" id="contact_email" name="contact_email" value="oui" checked>
                        <label for="contact_email">Me contacter par email</label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="contact_tel" name="contact_tel" value="oui">
                        <label for="contact_tel">Me contacter par téléphone</label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="contact_whatsapp" name="contact_whatsapp" value="oui">
                        <label for="contact_whatsapp">Me contacter par WhatsApp</label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="newsletter" name="newsletter" value="oui">
                        <label for="newsletter">Recevoir nos actualités et offres exclusives</label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="conditions" name="conditions" value="oui" required>
                        <label for="conditions">
                            J'accepte les <strong>conditions d'utilisation</strong> et la <strong>politique de confidentialité</strong> *
                        </label>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i>
                    <?php echo $type === 'contact' ? 'Soumettre ma demande' : 'Confirmer mon inscription'; ?>
                </button>
            </form>

            <p style="text-align: center; color: #666; margin-top: 20px; font-size: 14px;">
                Nous vous répondrons dans les 24 heures.
            </p>
        </div>
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
