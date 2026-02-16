-- =========================================
-- SCRIPT COMPLET POSTGRESQL
-- Base de données ECOFI
-- =========================================

-- =========================
-- 1. Table administrateurs
-- =========================
CREATE TABLE IF NOT EXISTS administrateurs (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nom_complet VARCHAR(100),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- 2. Types ENUM (PostgreSQL)
-- =========================
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'statut_commande') THEN
        CREATE TYPE statut_commande AS ENUM ('en_attente', 'traite', 'annule');
    END IF;

    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'type_demande') THEN
        CREATE TYPE type_demande AS ENUM ('devis', 'contact', 'info');
    END IF;

    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'statut_demande') THEN
        CREATE TYPE statut_demande AS ENUM ('nouveau', 'en_cours', 'traite');
    END IF;
END
$$;

-- =========================
-- 3. Table commandes
-- =========================
CREATE TABLE IF NOT EXISTS commandes (
    id SERIAL PRIMARY KEY,
    client_nom VARCHAR(100) NOT NULL,
    client_email VARCHAR(100),
    client_telephone VARCHAR(20) NOT NULL,
    produit VARCHAR(255) NOT NULL,
    quantite INT DEFAULT 1,
    prix VARCHAR(50),
    total VARCHAR(50),
    status statut_commande DEFAULT 'en_attente',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    traite_at TIMESTAMP
);

-- =========================
-- 4. Table demandes_contact
-- =========================
CREATE TABLE IF NOT EXISTS demandes_contact (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telephone VARCHAR(20),
    service VARCHAR(100),
    message TEXT,
    type type_demande NOT NULL,
    status statut_demande DEFAULT 'nouveau',
    assigned_to INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_admin
        FOREIGN KEY (assigned_to)
        REFERENCES administrateurs(id)
        ON DELETE SET NULL
);

-- =========================
-- 5. Table statistiques
-- =========================
CREATE TABLE IF NOT EXISTS statistiques (
    id SERIAL PRIMARY KEY,
    date DATE UNIQUE NOT NULL,
    visites INT DEFAULT 0,
    commandes INT DEFAULT 0,
    demandes INT DEFAULT 0,
    chiffre_affaire DECIMAL(10,2) DEFAULT 0
);

-- =========================
-- 6. Insertion admin par défaut
-- Mot de passe : admin123
-- =========================
INSERT INTO administrateurs (username, password_hash, nom_complet, email)
VALUES (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Admin ECOFI',
    'service.ecofi01@gmail.com'
)
ON CONFLICT (username) DO NOTHING;

-- 7. Insérer des données de test
INSERT INTO commandes (client_nom, client_telephone, produit, prix, status) VALUES
('Client Test 1', '771234567', 'Brique rouge', '850 FCFA', 'en_attente'),
('Client Test 2', '772345678', 'Pavé autobloquant', '2500 FCFA/m²', 'traite');

-- Insérer un exemple de demande de contact
INSERT INTO demandes_contact
    (nom, email, telephone, service, message, type, status, assigned_to, created_at)
VALUES
    (
        'Jean Dupont',                  -- nom
        'jean@test.com',                -- email
        '773456789',                    -- téléphone
        'Conseil immobilier',           -- service
        'Bonjour je cherche un terrain',-- message
        'contact',                      -- type : 'devis', 'contact' ou 'info'
        'nouveau',                      -- status : 'nouveau', 'en_cours', 'traite'
        NULL,                           -- assigned_to : pas encore assigné
        CURRENT_TIMESTAMP               -- created_at : date actuelle
    );

select * from demandes_contact;

SELECT 'Table créée avec succès !' as Message;
SELECT COUNT(*) as Nombre_admins FROM administrateurs;
-- =========================================
-- FIN DU SCRIPT
-- =========================================
-- 5. VÉRIFICATION
SELECT 'CRÉATION TERMINÉE !' as RESULTAT;
SELECT 'administrateurs' as TABLE, COUNT(*) as LIGNES FROM administrateurs
UNION ALL
SELECT 'commandes', COUNT(*) FROM commandes
UNION ALL
SELECT 'demandes_contact', COUNT(*) FROM demandes_contact;