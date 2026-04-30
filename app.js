// =========================
// DONNÉES DES PRODUITS
// =========================

const serviceData = {
    briques: {
        title: "Production de briques et pavés",
        description: "Matériaux de construction de haute qualité pour vos projets",
        products: [
            { id: 1, name: "Brique hourdis", desc: "Brique hourdis pour planchers", price: 490, price_text: "490 FCFA/unité", img: "../app/IMG/brique-hourdis.jpg", oldPrice: 630, oldPrice_text: "630 FCFA", rating: 4.5, reviews: 45, is_location: false, unit: "unité" },
            { id: 2, name: "Pavé autobloquant", desc: "Pavé pour voiries", price: 8500, price_text: "8.500 FCFA/m²", img: "../app/IMG/pave3.jpeg", oldPrice: 9500, oldPrice_text: "9.500 FCFA", rating: 4.5, reviews: 28, is_location: false, unit: "m²" },
            { id: 3, name: "Pavé hexagonal", desc: "Pavé design", price: 8500, price_text: "8.500 FCFA/m²", img: "../app/IMG/pave6.jpeg", rating: 4, reviews: 19, is_location: false, unit: "m²" },
            { id: 4, name: "Pavé granito", desc: "Pavé décoratif", price: 7000, price_text: "7.000 FCFA/m²", img: "../app/IMG/pave5.jpeg", oldPrice: 8000, oldPrice_text: "8.000 FCFA", rating: 4.5, reviews: 32, is_location: false, unit: "m²" },
            { id: 5, name: "Pavé drainant", desc: "Pavé perméable", price: 7000, price_text: "7.000 FCFA/m²", img: "../app/IMG/pave4.jpeg", rating: 5, reviews: 41, is_location: false, unit: "m²" },
            { id: 6, name: "Pavé classique", desc: "Pavé standard", price: 7500, price_text: "7.500 FCFA/m²", img: "../app/IMG/pave1.jpeg", oldPrice: 8500, oldPrice_text: "8.500 FCFA", rating: 4.5, reviews: 37, is_location: false, unit: "m²" },
            { id: 7, name: "Pavé rue", desc: "Pavé robuste", price: 7500, price_text: "7.500 FCFA/m²", img: "../app/IMG/pave7.jpeg", rating: 4, reviews: 23, is_location: false, unit: "m²" },
            { id: 8, name: "Brique creuse", desc: "Brique légère", price: 300, price_text: "300-1.020 FCFA/unité", img: "../app/IMG/brique-creuse.jpg", oldPrice: 1200, oldPrice_text: "1.200 FCFA", rating: 4.5, reviews: 52, is_location: false, unit: "unité" },
            { id: 9, name: "Pavé mosaïque", desc: "Pavé décoratif", price: 7500, price_text: "7.500 FCFA/m²", img: "../app/IMG/pave2.jpeg", rating: 4, reviews: 16, is_location: false, unit: "m²" },
            { id: 10, name: "Pavé premium", desc: "Pavé haute qualité", price: 7500, price_text: "7.500 FCFA/m²", img: "../app/IMG/pave16.jpeg", oldPrice: 8500, oldPrice_text: "8.500 FCFA", rating: 4.5, reviews: 29, is_location: false, unit: "m²" }
        ]
    },
    materiaux: {
        title: "Matériaux de construction",
        description: "Tous les matériaux essentiels pour vos chantiers",
        products: [
            { id: 101, name: "Ciment 42.5", desc: "Ciment haute résistance", price: 68000, price_text: "68.000 FCFA/sac", img: "../app/IMG/ciment.jpg", oldPrice: 70000, oldPrice_text: "70.000 FCFA", rating: 5, reviews: 67, is_location: false, unit: "sac" },
            { id: 102, name: "Sable de rivière", desc: "Sable lavé", price: 0, price_text: "Prix sur demande", img: "../app/IMG/sable.jpg", rating: 4, reviews: 23, is_location: false, unit: "m³" },
            { id: 103, name: "Gravier concassé", desc: "Gravier pour béton", price: 0, price_text: "Variable", img: "../app/IMG/gravier.jpg", rating: 4, reviews: 31, is_location: false, unit: "m³" },
            { id: 104, name: "Gravier décoratif", desc: "Gravier pour aménagement", price: 0, price_text: "Variable", img: "../app/IMG/grapb.jpg", rating: 4.5, reviews: 28, is_location: false, unit: "m³" },
            { id: 105, name: "Fers à béton", desc: "Armature", price: 0, price_text: "Variable", img: "../app/IMG/fer.jpg", rating: 4.5, reviews: 41, is_location: false, unit: "kg" },
            { id: 106, name: "Bois de charpente", desc: "Bois traité", price: 0, price_text: "Prix sur demande", img: "../app/IMG/baton.jpg", rating: 4, reviews: 16, is_location: false, unit: "m³" },
            { id: 107, name: "Tôle galvanisée", desc: "Pour toitures", price: 0, price_text: "Variable", img: "../app/IMG/toit.jpg", rating: 4, reviews: 13, is_location: false, unit: "m²" },
            { id: 108, name: "Kit sécurité", desc: "Tenue complète", price: 0, price_text: "Variable", img: "../app/IMG/ma.jpg", rating: 4.5, reviews: 21, is_location: false, unit: "kit" },
            { id: 109, name: "Casque protection", desc: "Normes internationales", price: 0, price_text: "Variable", img: "../app/IMG/casque.jpg", rating: 5, reviews: 34, is_location: false, unit: "pièce" },
            { id: 110, name: "Chaussures sécurité", desc: "Anti-perforation", price: 0, price_text: "Variable", img: "../app/IMG/chaussures.jpg", rating: 4.5, reviews: 27, is_location: false, unit: "paire" },
            { id: 111, name: "Gants pro", desc: "Protection mains", price: 0, price_text: "Prix sur demande", img: "../app/IMG/gants.jpg", rating: 4, reviews: 42, is_location: false, unit: "paire" },
            { id: 112, name: "Gilet haute visibilité", desc: "Fluorescent", price: 0, price_text: "Prix sur demande", img: "../app/IMG/gilet.jpg", rating: 4.5, reviews: 19, is_location: false, unit: "pièce" }
        ]
    },
    decoration: {
        title: "Décoration & Aménagement",
        description: "Nos transformations en vidéo + services sur mesure",
        products: [
            { id: 201, name: "Conception 3D", desc: "Visualisation 3D réaliste", price: 0, price_text: "Sur devis", is_location: false },
            { id: 202, name: "Decoration Chambre", desc: "Aménagement complet", price: 0, price_text: "Sur devis", is_location: false },
            { id: 203, name: "Salon Moderne", desc: "Design contemporain", price: 0, price_text: "Sur devis", is_location: false },
            { id: 204, name: "Cuisine Design", desc: "Sur mesure", price: 0, price_text: "Sur devis", is_location: false },
            { id: 205, name: "Aménagement Extérieur", desc: "Jardin, terrasse", price: 0, price_text: "Sur devis", is_location: false },
            { id: 206, name: "Aménagement Intérieur", desc: "Rénovation totale", price: 0, price_text: "Sur devis", is_location: false }
        ]
    },
    conseil: {
        title: "Conseil immobilier",
        description: "Expertise pour vos transactions immobilières",
        products: [
            { id: 301, name: "Évaluation de propriété", desc: "Estimation précise", price: 0, price_text: "Sur devis", is_location: false },
            { id: 302, name: "Accompagnement à l'achat", desc: "Assistance complète", price: 0, price_text: "Sur devis", is_location: false },
            { id: 303, name: "Accompagnement à la vente", desc: "Gestion complète", price: 0, price_text: "Sur devis", is_location: false },
            { id: 304, name: "Étude de faisabilité", desc: "Analyse technique", price: 0, price_text: "Sur devis", is_location: false }
        ]
    },
    plans: {
        title: "Plans de Construction",
        description: "Plans architecturaux prêts à l'emploi",
        products: [
            { id: 401, name: "Plan R+1", desc: "RDC + étage", price: 0, price_text: "Sur devis", img: "../app/IMG/R+1.jpg.jpeg", is_location: false },
            { id: 402, name: "Plan R+2", desc: "RDC + 2 étages", price: 0, price_text: "Sur devis", img: "../app/IMG/R+2.jpg.jpeg", is_location: false },
            { id: 403, name: "Plan RDC", desc: "Maison plain-pied", price: 0, price_text: "Sur devis", img: "../app/IMG/RDC.jpg.jpeg", is_location: false },
            { id: 404, name: "Plan 3D Rendu", desc: "Visualisation 3D", price: 0, price_text: "Sur devis", is_location: false },
            { id: 405, name: "Plan 3D Aménagé", desc: "Avec décoration", price: 0, price_text: "Sur devis", is_location: false },
            { id: 406, name: "Pack Complet", desc: "Plans + 3D", price: 0, price_text: "Sur devis", is_location: false }
        ]
    },
    location: {
        title: "Location matériel topographique",
        description: "Équipements de précision - Location à l'heure, journée, semaine ou mois",
        products: [
            {
                id: 501, name: "Récepteur GNSS", desc: "Haute précision",
                location_options: {
                    hourly: { price: 5000, enabled: true, price_text: "5.000 FCFA/heure" },
                    daily: { price: 25000, enabled: true, price_text: "25.000 FCFA/jour" },
                    weekly: { price: 125000, enabled: true, price_text: "125.000 FCFA/semaine" },
                    monthly: { price: 400000, enabled: true, price_text: "400.000 FCFA/mois" }
                }, img: "../app/IMG/gnss.jpg", rating: 5, reviews: 14, is_location: true, caution: 500000, caution_text: "500.000 FCFA de caution"
            },
            {
                id: 502, name: "Niveau électronique", desc: "Pour nivellements",
                location_options: {
                    hourly: { price: 3000, enabled: true, price_text: "3.000 FCFA/heure" },
                    daily: { price: 15000, enabled: true, price_text: "15.000 FCFA/jour" },
                    weekly: { price: 75000, enabled: true, price_text: "75.000 FCFA/semaine" },
                    monthly: { price: 250000, enabled: true, price_text: "250.000 FCFA/mois" }
                }, img: "../app/IMG/niveau.jpg", rating: 4, reviews: 21, is_location: true, caution: 250000, caution_text: "250.000 FCFA de caution"
            },
            {
                id: 503, name: "Station totale", desc: "Mesure de précision",
                location_options: {
                    hourly: { price: 8000, enabled: true, price_text: "8.000 FCFA/heure" },
                    daily: { price: 40000, enabled: true, price_text: "40.000 FCFA/jour" },
                    weekly: { price: 200000, enabled: true, price_text: "200.000 FCFA/semaine" },
                    monthly: { price: 650000, enabled: true, price_text: "650.000 FCFA/mois" }
                }, img: "../app/IMG/station.jpg", rating: 4.5, reviews: 9, is_location: true, caution: 1000000, caution_text: "1.000.000 FCFA de caution"
            },
            {
                id: 504, name: "GPS de poche", desc: "Repérage terrain",
                location_options: {
                    hourly: { price: 2000, enabled: true, price_text: "2.000 FCFA/heure" },
                    daily: { price: 10000, enabled: true, price_text: "10.000 FCFA/jour" },
                    weekly: { price: 50000, enabled: true, price_text: "50.000 FCFA/semaine" },
                    monthly: { price: 150000, enabled: true, price_text: "150.000 FCFA/mois" }
                }, img: "../app/IMG/gps.jpg", rating: 4, reviews: 17, is_location: true, caution: 150000, caution_text: "150.000 FCFA de caution"
            },
            {
                id: 505, name: "Drone topographique", desc: "Levés aériens",
                location_options: {
                    hourly: { price: 15000, enabled: true, price_text: "15.000 FCFA/heure" },
                    daily: { price: 75000, enabled: true, price_text: "75.000 FCFA/jour" },
                    weekly: { price: 350000, enabled: true, price_text: "350.000 FCFA/semaine" },
                    monthly: { price: 1200000, enabled: true, price_text: "1.200.000 FCFA/mois" }
                }, img: "../app/IMG/drone.jpg", rating: 5, reviews: 8, is_location: true, caution: 2000000, caution_text: "2.000.000 FCFA de caution"
            },
            {
                id: 506, name: "Décamètre mécanique", desc: "Roulette 50m",
                location_options: {
                    hourly: { price: 1000, enabled: true, price_text: "1.000 FCFA/heure" },
                    daily: { price: 5000, enabled: true, price_text: "5.000 FCFA/jour" },
                    weekly: { price: 25000, enabled: true, price_text: "25.000 FCFA/semaine" },
                    monthly: { price: 75000, enabled: true, price_text: "75.000 FCFA/mois" }
                }, img: "../app/IMG/deac.jpg", rating: 4, reviews: 25, is_location: true, caution: 50000, caution_text: "50.000 FCFA de caution"
            }
        ]
    }
};

const promoCodes = [
    { code: "ECOFI10", reduction: 10, desc: "-10% sur tout le site" },
    { code: "BIENVENUE", reduction: 15, desc: "-15% pour les nouveaux clients" }
];

// =========================
// LOCAL STORAGE
// =========================

const CART_STORAGE_KEY = 'siteecofi_panier';
const PROMO_STORAGE_KEY = 'siteecofi_promo';

function sauvegarderPanier() {
    localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(panier));
    localStorage.setItem(PROMO_STORAGE_KEY, JSON.stringify(currentPromoCode));
}

function chargerPanier() {
    try {
        const savedCart = localStorage.getItem(CART_STORAGE_KEY);
        const savedPromo = localStorage.getItem(PROMO_STORAGE_KEY);

        panier = savedCart ? JSON.parse(savedCart) : [];
        currentPromoCode = savedPromo ? JSON.parse(savedPromo) : null;

        if (!Array.isArray(panier)) {
            panier = [];
        }
    } catch (e) {
        panier = [];
        currentPromoCode = null;
    }
}

function supprimerPanierLocalStorage() {
    localStorage.removeItem(CART_STORAGE_KEY);
    localStorage.removeItem(PROMO_STORAGE_KEY);
}


// =========================
// VARIABLES GLOBALES
// =========================
let panier = [];
let currentPromoCode = null;
let currentQuoteType = null;

// =========================
// INITIALISATION
// =========================
document.addEventListener('DOMContentLoaded', function () {
    initialiserApplication();
});

function initialiserApplication() {
    chargerPanier();
    afficherCodesPromo();
    mettreAJourPanier();
    initZoomFeature();
    initRecherche();
    initBoutonsDetails();
    initFermeturePanierExterieur();
    initSmoothScroll();
    initHeaderScroll();
    initModals();
}

// =========================
// BOUTONS DETAILS
// =========================
function initBoutonsDetails() {
    document.querySelectorAll('.btn-details-compact').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const expertise = this.getAttribute('data-target');
            openProductsModal(expertise);
        });
    });
}

// =========================
// MODAL PRODUITS
// =========================
// =========================
// MODAL PRODUITS - VERSION SIMPLIFIÉE
// =========================
// =========================
// MODAL PRODUITS - VERSION CORRECTE
// =========================
function openProductsModal(expertise) {
    if (!serviceData[expertise]) return;

    const service = serviceData[expertise];
    const title = document.getElementById('productsModalTitle');
    const body = document.getElementById('productsModalBody');
    const modal = document.getElementById('productsModal');

    if (!title || !body || !modal) return;

    title.textContent = service.title;

    body.innerHTML = `
        <div class="modal-description">
            <p>${service.description}</p>
        </div>
        <div class="product-grid-modern">
            ${service.products.map(product => {
        if (product.is_location) {
            // PRODUITS DE LOCATION - Bouton simple qui ajoute au panier sans options
            return `
                        <div class="product-card-modern location-card">
                            <div class="product-img-modern">
                                <img src="${product.img || 'https://via.placeholder.com/400x200'}" alt="${product.name}">
                                <span class="location-badge-product"><i class="fas fa-calendar-alt"></i> Location</span>
                            </div>
                            <div class="product-info-modern">
                                <h4>${product.name}</h4>
                                <p>${product.desc || ''}</p>
                                <div class="product-price-modern">Location - Prix sur demande</div>
                                ${product.caution ? `<div class="caution-info"><i class="fas fa-shield-alt"></i> ${product.caution_text}</div>` : ''}
                                <button class="buy-btn-modern location-btn" onclick='ajouterLocationSimpleAuPanier(${JSON.stringify(product).replace(/"/g, '&quot;')})'>
                                    <i class="fas fa-cart-plus"></i> Ajouter au panier
                                </button>
                            </div>
                        </div>
                    `;
        } else {
            // PRODUITS D'ACHAT
            return `
                        <div class="product-card-modern">
                            <div class="product-img-modern">
                                <img src="${product.img || 'https://via.placeholder.com/400x200'}" alt="${product.name}">
                                <span class="purchase-badge-product"><i class="fas fa-shopping-cart"></i> Achat</span>
                            </div>
                            <div class="product-info-modern">
                                <h4>${product.name}</h4>
                                <p>${product.desc || ''}</p>
                                <div class="product-price-modern">${product.price_text || 'Prix sur demande'}</div>
                                <button class="buy-btn-modern" onclick='ajouterAchatDepuisModal(this)'>
                                    <i class="fas fa-cart-plus"></i> Ajouter au panier
                                </button>
                            </div>
                        </div>
                    `;
        }
    }).join('')}
        </div>
    `;

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Ajout simple d'un produit de location au panier (sans options)
// Ajout simple d'un produit de location au panier (sans options)
function ajouterLocationSimpleAuPanier(produit) {
    console.log('Ajout location simple au panier:', produit.name);

    const existant = panier.find(item => item.produitId === produit.id && item.is_location === true);

    if (existant) {
        existant.quantity++;
    } else {
        panier.push({
            cartId: Date.now().toString() + Math.random().toString(16).slice(2),
            produitId: produit.id,
            nom: produit.name,
            is_location: true,
            period: null,
            period_text: "À définir dans le devis",
            price_per_period: 0,
            price_text: "Prix à définir",
            quantity: 1,
            caution: produit.caution || 0,
            caution_text: produit.caution_text || "Caution",
            total_location: 0,
            startDate: null,
            endDate: null,
            image: produit.img || '🏗️'
        });
    }

    mettreAJourPanier();
    afficherNotification(`${produit.name} ajouté au panier (location)`);
}

function getPeriodText(period) {
    const periods = { hourly: "heure", daily: "jour", weekly: "semaine", monthly: "mois" };
    return periods[period] || period;
}

// =========================
// FONCTIONS PANIER
// =========================
function ajouterAchatAuPanier(produit) {
    const existant = panier.find(item => item.produitId === produit.id && !item.is_location);
    if (existant) {
        existant.quantite++;
    } else {
        panier.push({
            cartId: Date.now().toString() + Math.random().toString(16).slice(2),
            produitId: produit.id,
            nom: produit.name,
            is_location: false,
            prix: produit.price || 0,
            price_text: produit.price_text,
            quantite: 1,
            image: produit.img || '🏗️'
        });
    }
    mettreAJourPanier();
    afficherNotification(`${produit.name} ajouté au panier !`);
}

function ajouterLocationAuPanier(produit, period, quantity, startDate, endDate) {
    const locationOption = produit.location_options[period];
    const totalLocation = locationOption.price * quantity;

    panier.push({
        cartId: Date.now().toString() + Math.random().toString(16).slice(2),
        produitId: produit.id,
        nom: produit.name,
        is_location: true,
        period: period,
        period_text: getPeriodText(period),
        price_per_period: locationOption.price,
        price_text: locationOption.price_text,
        quantity: quantity,
        caution: produit.caution || 0,
        caution_text: produit.caution_text || "Caution",
        total_location: totalLocation,
        startDate: startDate,
        endDate: endDate,
        image: produit.img || '🏗️'
    });
    mettreAJourPanier();
    afficherNotification(`${produit.name} loué du ${startDate} au ${endDate} !`);
}

function mettreAJourPanier() {
    // Sauvegarde en Local
    sauvegarderPanier();
    const cartItems = document.getElementById('cartItems');
    const cartCount = document.getElementById('cartCount');
    const cartTotal = document.getElementById('cartTotal');
    const cartHeaderTotal = document.getElementById('cartHeaderTotal');
    const cartItemCount = document.getElementById('cartItemCount');

    let totalAchats = 0;
    let totalLocations = 0;
    let nombreArticles = 0;

    panier.forEach(item => {
        if (item.is_location) {
            totalLocations += item.total_location;
            nombreArticles += item.quantity;
        } else {
            totalAchats += (item.prix * item.quantite);
            nombreArticles += item.quantite;
        }
    });

    let total = totalAchats + totalLocations;
    if (currentPromoCode && totalAchats > 0) {
        total = total * (1 - currentPromoCode.reduction / 100);
    }

    if (cartCount) cartCount.textContent = nombreArticles;
    if (cartTotal) cartTotal.textContent = total.toLocaleString('fr-FR') + ' FCFA';
    if (cartHeaderTotal) cartHeaderTotal.textContent = total.toLocaleString('fr-FR') + ' FCFA';
    if (cartItemCount) cartItemCount.textContent = nombreArticles + ' article(s)';

    if (!cartItems) return;
    if (panier.length === 0) {
        cartItems.innerHTML = '<p style="text-align:center; color:#999; padding:20px;">Votre panier est vide</p>';
        return;
    }

    let html = '';
    panier.forEach(item => {
        if (item.is_location) {
            html += `
                <div class="cart-item cart-item-location">
                    <div class="cart-item-image"><img src="${item.image}" width="50"></div>
                    <div class="cart-item-details">
                        <div class="cart-item-name">${item.nom}</div>
                        <div class="cart-item-location-info">
                            <span class="location-badge">${item.period_text}</span>
                            <span>Du ${item.startDate} au ${item.endDate}</span>
                        </div>
                        <div class="cart-item-price">${item.total_location.toLocaleString('fr-FR')} FCFA</div>
                        <div class="cart-item-actions">
                            <span class="remove-item" onclick="retirerDuPanier('${item.cartId}')"><i class="fas fa-trash"></i></span>
                        </div>
                    </div>
                </div>
            `;
        } else {
            html += `
                <div class="cart-item">
                    <div class="cart-item-image"><img src="${item.image}" width="50"></div>
                    <div class="cart-item-details">
                        <div class="cart-item-name">${item.nom}</div>
                        <div class="cart-item-price">${(item.prix * item.quantite).toLocaleString('fr-FR')} FCFA</div>
                        <div class="cart-item-actions">
                            <div class="cart-item-quantity">
                                <button class="qty-btn" onclick="changerQuantite('${item.cartId}', -1)">-</button>
                                <span>${item.quantite}</span>
                                <button class="qty-btn" onclick="changerQuantite('${item.cartId}', 1)">+</button>
                            </div>
                            <span class="remove-item" onclick="retirerDuPanier('${item.cartId}')"><i class="fas fa-trash"></i></span>
                        </div>
                    </div>
                </div>
            `;
        }
    });
    cartItems.innerHTML = html;
}

function changerQuantite(cartId, changement) {
    const item = panier.find(i => i.cartId === cartId);
    if (!item || item.is_location) return;
    item.quantite += changement;
    if (item.quantite <= 0) {
        retirerDuPanier(cartId);
    } else {
        mettreAJourPanier();
    }
}

function retirerDuPanier(cartId) {
    panier = panier.filter(item => item.cartId !== cartId);
    mettreAJourPanier();
}

function viderPanier() {
    panier = [];
    currentPromoCode = null;
    supprimerPanierLocalStorage();
    mettreAJourPanier();
    afficherNotification('Panier vidé');
}

function toggleCart(event) {
    event.stopPropagation();
    const cart = document.getElementById("cartDropdown");
    if (cart.style.display === "flex") {
        cart.style.display = "none";
    } else {
        cart.style.display = "flex";
    }
}

// =========================
// FONCTIONS GLOBALES POUR LES MODALS
// =========================
window.ajouterAchatDepuisModal = function (button) {
    const productCard = button.closest('.product-card-modern');
    const productName = productCard.querySelector('h4').textContent;
    let product = null;
    for (let cat in serviceData) {
        product = serviceData[cat].products.find(p => p.name === productName);
        if (product) break;
    }
    if (product) ajouterAchatAuPanier(product);
};

// =========================
// FONCTIONS CORRIGÉES POUR LA LOCATION
// =========================

// Fonction pour ajouter un produit de location au panier (appelée depuis le modal)
window.ajouterLocationDepuisModal = function (button) {
    console.log('=== AJOUT LOCATION DEPUIS MODAL ===');

    const productCard = button.closest('.product-card-modern');
    if (!productCard) {
        afficherNotification('Erreur: produit non trouvé', 'error');
        return;
    }

    const periodSelect = productCard.querySelector('.location-period-select');
    const quantityInput = productCard.querySelector('.location-quantity');

    if (!periodSelect || !quantityInput) {
        afficherNotification('Erreur: options de location non disponibles', 'error');
        return;
    }

    let productData;
    try {
        productData = JSON.parse(periodSelect.dataset.product);
        console.log('Produit à louer:', productData.name);
    } catch (e) {
        console.error('Erreur parsing:', e);
        afficherNotification('Erreur: données produit invalides', 'error');
        return;
    }

    const period = periodSelect.value;
    const quantity = parseInt(quantityInput.value) || 1;

    // Ouvrir le modal de sélection des dates et options
    openLocationOptionsModal(productData, period, quantity);
};

// Modal complet pour la location avec toutes les options
function openLocationOptionsModal(productData, selectedPeriod, quantity) {
    console.log('Ouverture modal options location pour:', productData.name);

    // Fermer le modal existant
    const existingModal = document.querySelector('.location-options-modal-overlay');
    if (existingModal) existingModal.remove();

    const modalDiv = document.createElement('div');
    modalDiv.className = 'location-options-modal-overlay';
    modalDiv.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        z-index: 10000;
        display: flex;
        justify-content: center;
        align-items: center;
    `;

    modalDiv.innerHTML = `
        <div class="location-options-modal" style="background: white; border-radius: 15px; width: 90%; max-width: 550px; max-height: 90vh; overflow-y: auto; padding: 20px;">
            <div class="location-options-modal-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">
                <h3 style="margin:0;"><i class="fas fa-calendar-alt" style="color: #ff8533;"></i> Location : ${productData.name}</h3>
                <button class="location-options-modal-close" style="background: none; border: none; font-size: 24px; cursor: pointer;" onclick="this.closest('.location-options-modal-overlay').remove()">&times;</button>
            </div>
            <div class="location-options-modal-body">
                <div class="location-product-info" style="text-align:center; margin-bottom:15px;">
                    <img src="${productData.img || 'https://via.placeholder.com/100'}" alt="${productData.name}" style="width:80px; height:80px; object-fit:cover; border-radius:8px;">
                    <p>${productData.desc || ''}</p>
                    ${productData.caution ? `<div style="background:#fff3cd; color:#856404; padding:8px; border-radius:5px; font-size:12px;"><i class="fas fa-shield-alt"></i> ${productData.caution_text}</div>` : ''}
                </div>
                
                <div class="location-period-selector" style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Durée de location :</label>
                    <select id="locPeriodSelect" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                        ${Object.entries(productData.location_options || {})
            .filter(([_, opt]) => opt.enabled)
            .map(([p, opt]) => `
                                <option value="${p}" ${p === selectedPeriod ? 'selected' : ''} data-price="${opt.price}" data-price-text="${opt.price_text}">
                                    ${getPeriodText(p)} - ${opt.price_text}
                                </option>
                            `).join('')}
                    </select>
                </div>
                
                <div class="location-dates" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom:15px;">
                    <div class="date-group">
                        <label style="display:block; margin-bottom:5px; font-weight:bold;">Date de début :</label>
                        <input type="date" id="locStartDate" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                    </div>
                    <div class="date-group">
                        <label style="display:block; margin-bottom:5px; font-weight:bold;">Date de fin :</label>
                        <input type="date" id="locEndDate" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                    </div>
                </div>
                
                <div class="location-quantity-group" style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Nombre de périodes :</label>
                    <input type="number" id="locQuantity" value="${quantity}" min="1" step="1" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>
                
                <div class="location-price-preview" style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center; margin: 15px 0;">
                    <strong>Détail du prix :</strong>
                    <div id="priceDetail" style="font-size:14px; margin-top:5px;"></div>
                    <div id="totalPrice" style="font-size:20px; font-weight:bold; color:#ff8533; margin-top:10px;">0 FCFA</div>
                </div>
                
                <button id="confirmLocationBtn" style="width: 100%; padding: 14px; background: #ff8533; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 16px;">
                    <i class="fas fa-check-circle"></i> Ajouter au panier
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(modalDiv);

    // Récupérer les éléments
    const periodSelectEl = modalDiv.querySelector('#locPeriodSelect');
    const startDateInput = modalDiv.querySelector('#locStartDate');
    const endDateInput = modalDiv.querySelector('#locEndDate');
    const quantityInputEl = modalDiv.querySelector('#locQuantity');
    const priceDetailDiv = modalDiv.querySelector('#priceDetail');
    const totalPriceSpan = modalDiv.querySelector('#totalPrice');
    const confirmBtn = modalDiv.querySelector('#confirmLocationBtn');

    // Date minimale = aujourd'hui
    const today = new Date().toISOString().split('T')[0];
    startDateInput.min = today;
    endDateInput.min = today;

    // Fonction de mise à jour du prix
    function updatePrice() {
        const currentPeriod = periodSelectEl.value;
        const qty = parseInt(quantityInputEl.value) || 1;
        const option = productData.location_options[currentPeriod];

        if (option) {
            const total = option.price * qty;
            const periodText = getPeriodText(currentPeriod);

            // Afficher le détail
            priceDetailDiv.innerHTML = `
                ${option.price.toLocaleString('fr-FR')} FCFA x ${qty} ${periodText}(s) = ${total.toLocaleString('fr-FR')} FCFA
            `;

            let totalText = total.toLocaleString('fr-FR') + ' FCFA';
            if (productData.caution) {
                totalText += `<br><small style="font-size:12px;">+ ${productData.caution.toLocaleString('fr-FR')} FCFA de caution</small>`;
            }
            totalPriceSpan.innerHTML = totalText;
        }
    }

    periodSelectEl.addEventListener('change', updatePrice);
    quantityInputEl.addEventListener('input', updatePrice);
    startDateInput.addEventListener('change', updatePrice);
    endDateInput.addEventListener('change', updatePrice);
    updatePrice();

    // Confirmation
    confirmBtn.onclick = function () {
        const selectedPeriod = periodSelectEl.value;
        const qty = parseInt(quantityInputEl.value) || 1;
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        if (!startDate || !endDate) {
            afficherNotification('Veuillez sélectionner les dates de location', 'warning');
            return;
        }

        if (new Date(startDate) > new Date(endDate)) {
            afficherNotification('La date de fin doit être postérieure à la date de début', 'warning');
            return;
        }

        // Ajouter au panier
        ajouterLocationAuPanier(productData, selectedPeriod, qty, startDate, endDate);
        modalDiv.remove();
    };
}

// Fonction pour ouvrir le modal de devis de location (version complète)
// =========================
// MODAL DE DEVIS POUR LOCATION - AVEC TOUTES LES OPTIONS
// =========================
function openLocationQuoteModal() {
    console.log('=== OUVERTURE MODAL DEVIS LOCATION ===');

    const locationItems = panier.filter(item => item.is_location === true);
    console.log('Articles de location dans le panier:', locationItems);

    if (locationItems.length === 0) {
        afficherNotification('Aucun produit de location dans le panier', 'warning');
        return;
    }

    const existingModal = document.querySelector('.quote-modal-overlay');
    if (existingModal) existingModal.remove();

    const modal = document.createElement('div');
    modal.className = 'quote-modal-overlay';
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        z-index: 10002;
        display: flex;
        justify-content: center;
        align-items: center;
    `;

    let produitsHtml = '';
    locationItems.forEach((item, idx) => {
        let produitOriginal = null;
        for (let cat in serviceData) {
            produitOriginal = serviceData[cat].products.find(p => p.id === item.produitId);
            if (produitOriginal) break;
        }

        if (produitOriginal && produitOriginal.location_options) {
            produitsHtml += `
                <div class="quote-item-row location-item" style="border:1px solid #eee; border-radius:10px; padding:15px; margin-bottom:20px; background:#f9f9f9;">
                    <div><strong style="font-size:16px; color:#333;">${item.nom}</strong></div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-top:15px;">
                        <div class="form-group">
                            <label style="display:block; margin-bottom:5px; font-weight:500;">Durée de location <span style="color:red;">*</span></label>
                            <select id="period_${idx}" class="period-select" data-id="${item.produitId}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;">
                                <option value="">Sélectionnez une durée</option>
                                ${Object.entries(produitOriginal.location_options)
                    .filter(([_, opt]) => opt.enabled)
                    .map(([p, opt]) => `<option value="${p}" data-price="${opt.price}" data-price-text="${opt.price_text}">${getPeriodText(p)} - ${opt.price_text}</option>`).join('')}
                            </select>
                        </div>
                        <div class="form-group">
                            <label style="display:block; margin-bottom:5px; font-weight:500;">Nombre de périodes <span style="color:red;">*</span></label>
                            <input type="number" id="quantity_${idx}" class="quantity-input" data-id="${item.produitId}" value="1" min="1" step="1" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;">
                        </div>
                        <div class="form-group">
                            <label style="display:block; margin-bottom:5px; font-weight:500;">Date de début <span style="color:red;">*</span></label>
                            <input type="date" id="startDate_${idx}" class="start-date" data-id="${item.produitId}" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;">
                        </div>
                        <div class="form-group">
                            <label style="display:block; margin-bottom:5px; font-weight:500;">Date de fin <span style="color:red;">*</span></label>
                            <input type="date" id="endDate_${idx}" class="end-date" data-id="${item.produitId}" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;">
                        </div>
                    </div>
                    <div class="price-preview" id="preview_${idx}" style="margin-top:15px; padding:10px; background:#e8f4fd; border-radius:8px; text-align:center;">
                        Prix: 0 FCFA
                    </div>
                    ${produitOriginal.caution ? `<div style="margin-top:10px; color:#856404;"><i class="fas fa-shield-alt"></i> Caution : ${produitOriginal.caution.toLocaleString('fr-FR')} FCFA (remboursable)</div>` : ''}
                    <input type="hidden" id="productId_${idx}" value="${item.produitId}">
                    <input type="hidden" id="productName_${idx}" value="${item.nom}">
                    <input type="hidden" id="caution_${idx}" value="${item.caution || 0}">
                </div>
            `;
        }
    });

    modal.innerHTML = `
        <div class="quote-modal location-quote-modal" style="background: white; border-radius: 15px; width: 90%; max-width: 800px; max-height: 90vh; overflow-y: auto; padding: 20px;">
            <div class="quote-modal-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ff8533; padding-bottom: 15px; margin-bottom: 20px;">
                <h3 style="margin:0; color:#333;"><i class="fas fa-calendar-alt" style="color: #ff8533;"></i> Demande de Devis - Location</h3>
                <button class="quote-modal-close" style="background: none; border: none; font-size: 28px; cursor: pointer; color:#999;" onclick="this.closest('.quote-modal-overlay').remove()">&times;</button>
            </div>
            <div class="quote-modal-body">
                <h4 style="margin-bottom:15px;">Configurez votre location :</h4>
                ${produitsHtml}
                
                <div class="quote-total" style="background:#f8f9fa; padding:15px; border-radius:10px; margin:20px 0;">
                    <div id="totalLocationPreview" style="display:flex; justify-content:space-between; font-size:1.2rem; font-weight:bold;">
                        <span>Total location :</span>
                        <span id="totalLocationSpan" style="color:#ff8533;">0 FCFA</span>
                    </div>
                    <div id="totalCautionPreview" style="display:flex; justify-content:space-between; font-size:1rem; margin-top:10px;">
                        <span>Total caution :</span>
                        <span id="totalCautionSpan">0 FCFA</span>
                    </div>
                    <div id="totalGlobalPreview" style="display:flex; justify-content:space-between; font-size:1.2rem; font-weight:bold; margin-top:15px; padding-top:10px; border-top:2px solid #ff8533;">
                        <span>Total à prévoir :</span>
                        <span id="totalGlobalSpan" style="color:#ff8533;">0 FCFA</span>
                    </div>
                </div>
                
                <form id="locationQuoteForm" onsubmit="submitLocationQuoteWithDetails(event)">
                    <h4 style="margin:20px 0 15px 0; color:#333;">Vos informations personnelles :</h4>
                    
                    <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="form-group">
                            <label for="locationName" style="display:block; margin-bottom:5px; font-weight:500;">Nom complet <span style="color:red;">*</span></label>
                            <input type="text" id="locationName" required placeholder="Votre nom" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px;">
                        </div>
                        <div class="form-group">
                            <label for="locationEmail" style="display:block; margin-bottom:5px; font-weight:500;">Email <span style="color:red;">*</span></label>
                            <input type="email" id="locationEmail" required placeholder="votre@email.com" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px;">
                        </div>
                    </div>
                    
                    <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="form-group">
                            <label for="locationPhone" style="display:block; margin-bottom:5px; font-weight:500;">Téléphone <span style="color:red;">*</span></label>
                            <input type="tel" id="locationPhone" required placeholder="77 123 45 67" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px;">
                        </div>
                        <div class="form-group">
                            <label for="locationIdCard" style="display:block; margin-bottom:5px; font-weight:500;">N° Pièce d'identité <span style="color:red;">*</span></label>
                            <input type="text" id="locationIdCard" required placeholder="CNI / Passeport" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px;">
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom:15px;">
                        <label for="locationAddress" style="display:block; margin-bottom:5px; font-weight:500;">Adresse de livraison/retrait <span style="color:red;">*</span></label>
                        <input type="text" id="locationAddress" required placeholder="Votre adresse complète" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px;">
                    </div>
                    
                    <div class="form-group" style="margin-bottom:15px;">
                        <label for="locationCompany" style="display:block; margin-bottom:5px; font-weight:500;">Société / Organisation</label>
                        <input type="text" id="locationCompany" placeholder="Nom de votre société (optionnel)" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px;">
                    </div>
                    
                    <div class="form-group" style="margin-bottom:15px;">
                        <label for="locationMessage" style="display:block; margin-bottom:5px; font-weight:500;">Message</label>
                        <textarea id="locationMessage" placeholder="Informations complémentaires..." style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; min-height:80px;"></textarea>
                    </div>
                    
                    <div class="terms-checkbox" style="display:flex; align-items:center; gap:12px; margin:20px 0; padding:12px; background:#f8f9fa; border-radius:10px;">
                        <input type="checkbox" id="acceptTerms" required style="width:18px; height:18px;">
                        <label for="acceptTerms" style="font-size:13px;">J'accepte les conditions générales de location et m'engage à restituer le matériel en bon état <span style="color:red;">*</span></label>
                    </div>
                    
                    <button type="submit" class="btn-primary" style="width:100%; padding:14px; background:#ff8533; color:white; border:none; border-radius:8px; font-weight:600; cursor:pointer; font-size:16px;">
                        <i class="fas fa-paper-plane"></i> Envoyer la demande de devis
                    </button>
                </form>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    // Mise à jour des prix
    const locationCount = locationItems.length;

    function updateAllPrices() {
        let totalLocation = 0;
        let totalCaution = 0;

        for (let i = 0; i < locationCount; i++) {
            const periodSelect = document.getElementById(`period_${i}`);
            const quantityInput = document.getElementById(`quantity_${i}`);
            const previewDiv = document.getElementById(`preview_${i}`);
            const cautionValue = parseInt(document.getElementById(`caution_${i}`)?.value) || 0;

            if (periodSelect && periodSelect.value) {
                const selectedOption = periodSelect.options[periodSelect.selectedIndex];
                const price = parseInt(selectedOption.dataset.price) || 0;
                const quantity = parseInt(quantityInput?.value) || 1;
                const total = price * quantity;
                totalLocation += total;
                totalCaution += cautionValue;

                if (previewDiv) {
                    previewDiv.innerHTML = `Prix: ${total.toLocaleString('fr-FR')} FCFA pour ${quantity} ${getPeriodText(periodSelect.value)}(s)`;
                }
            }
        }

        document.getElementById('totalLocationSpan').innerHTML = totalLocation.toLocaleString('fr-FR') + ' FCFA';
        document.getElementById('totalCautionSpan').innerHTML = totalCaution.toLocaleString('fr-FR') + ' FCFA';
        document.getElementById('totalGlobalSpan').innerHTML = (totalLocation + totalCaution).toLocaleString('fr-FR') + ' FCFA';
    }

    // Ajouter les événements
    for (let i = 0; i < locationCount; i++) {
        const periodSelect = document.getElementById(`period_${i}`);
        const quantityInput = document.getElementById(`quantity_${i}`);
        const startDateInput = document.getElementById(`startDate_${i}`);
        const endDateInput = document.getElementById(`endDate_${i}`);

        const today = new Date().toISOString().split('T')[0];
        if (startDateInput) startDateInput.min = today;
        if (endDateInput) endDateInput.min = today;

        if (periodSelect) periodSelect.addEventListener('change', updateAllPrices);
        if (quantityInput) quantityInput.addEventListener('input', updateAllPrices);
        if (startDateInput) startDateInput.addEventListener('change', updateAllPrices);
        if (endDateInput) endDateInput.addEventListener('change', updateAllPrices);
    }

    updateAllPrices();
}



// =========================
// PROMOS
// =========================

// =========================
// SOUMISSION DU DEVIS DE LOCATION AVEC DÉTAILS
// =========================

async function submitLocationQuoteWithDetails(event) {
    if (event) event.preventDefault();

    const locationItems = panier.filter(item => item.is_location === true);
    const locationCount = locationItems.length;

    const nom = document.getElementById('locationName')?.value.trim();
    const email = document.getElementById('locationEmail')?.value.trim();
    const telephone = document.getElementById('locationPhone')?.value.trim();
    const idCard = document.getElementById('locationIdCard')?.value.trim();
    const adresse = document.getElementById('locationAddress')?.value.trim();
    const societe = document.getElementById('locationCompany')?.value.trim();
    const message = document.getElementById('locationMessage')?.value.trim();
    const acceptTerms = document.getElementById('acceptTerms')?.checked;

    if (!nom || !email || !telephone || !idCard || !adresse) {
        afficherNotification('Veuillez remplir tous les champs obligatoires (*)', 'warning');
        return;
    }

    if (!acceptTerms) {
        afficherNotification('Veuillez accepter les conditions générales de location.', 'warning');
        return;
    }

    let itemsWithDetails = [];
    let totalLocations = 0;
    let totalCaution = 0;

    for (let i = 0; i < locationCount; i++) {
        const period = document.getElementById(`period_${i}`)?.value;
        const quantity = parseInt(document.getElementById(`quantity_${i}`)?.value) || 1;
        const startDate = document.getElementById(`startDate_${i}`)?.value;
        const endDate = document.getElementById(`endDate_${i}`)?.value;
        const caution = parseInt(document.getElementById(`caution_${i}`)?.value) || 0;
        const productId = parseInt(document.getElementById(`productId_${i}`)?.value);
        const productName = document.getElementById(`productName_${i}`)?.value;

        if (!period || !startDate || !endDate) {
            afficherNotification(`Veuillez configurer complètement la location pour ${productName}`, 'warning');
            return;
        }

        const periodSelect = document.getElementById(`period_${i}`);
        const selectedOption = periodSelect.options[periodSelect.selectedIndex];
        const pricePerPeriod = parseInt(selectedOption.dataset.price) || 0;
        const totalLocation = pricePerPeriod * quantity;

        totalLocations += totalLocation;
        totalCaution += caution;

        itemsWithDetails.push({
            id: productId,
            nom: productName,
            period: period,
            period_text: getPeriodText(period),
            price_per_period: pricePerPeriod,
            price_text: selectedOption.dataset.priceText || `${pricePerPeriod} FCFA`,
            quantity: quantity,
            startDate: startDate,
            endDate: endDate,
            total_location: totalLocation,
            caution: caution,
            caution_text: "Caution remboursable"
        });
    }

    const payload = {
        type: "location",
        nom: nom,
        email: email,
        telephone: telephone,
        idCard: idCard,
        adresse: adresse,
        societe: societe || null,
        message: message || '',
        items: itemsWithDetails,
        totals: {
            location: totalLocations,
            caution: totalCaution,
            total: totalLocations + totalCaution
        }
    };

    console.log('PAYLOAD LOCATION ENVOYÉ:', JSON.stringify(payload, null, 2));

    showQuoteLoader('Envoi du devis de location en cours...');

    try {
        const response = await fetch('/app/api/submit_quote.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Erreur lors de l\'envoi du devis de location.');
        }

        hideQuoteLoader();
        showQuoteStatusMessage('Votre devis de location a bien été envoyé !', 'success');
        afficherNotification('Votre devis de location a bien été envoyé par email.', 'success');

        panier = panier.filter(item => !item.is_location);

        if (panier.length === 0) {
            currentPromoCode = null;
        }

        mettreAJourPanier();

        const modal = document.querySelector('.quote-modal-overlay');
        if (modal) modal.remove();

        if (result.pdf_url) {
            window.open(result.pdf_url, '_blank');
        }

    } catch (error) {
        console.error('Erreur:', error);
        hideQuoteLoader();
        showQuoteStatusMessage(error.message || 'Une erreur est survenue lors de l\'envoi du devis.', 'error');
    }
}

function afficherCodesPromo() {
    const promoGrid = document.getElementById('promoGrid');
    if (!promoGrid) return;
    promoGrid.innerHTML = promoCodes.map(promo => `
        <div class="promo-card" onclick="appliquerCode('${promo.code}', ${promo.reduction})">
            <div class="promo-code">${promo.code}</div>
            <div class="promo-desc">${promo.desc}</div>
        </div>
    `).join('');
}

function appliquerCode(code, reduction) {
    currentPromoCode = { code, reduction };
    afficherNotification(`Code ${code} appliqué : ${reduction}% de réduction !`);
    mettreAJourPanier();
}

// =========================
// DEVIS AVEC ENVOI EMAIL
// =========================

function openQuoteModal() {
    if (!panier || panier.length === 0) {
        afficherNotification('Votre panier est vide.');
        return;
    }

    const hasLocations = panier.some(item => item.is_location === true);
    const hasPurchases = panier.some(item => item.is_location === false);

    if (hasLocations && hasPurchases) {
        showQuoteTypeSelectionModal();
    } else if (hasLocations) {
        currentQuoteType = 'location';
        openLocationQuoteModal();
    } else {
        currentQuoteType = 'achat';
        openPurchaseQuoteModal();
    }
}

function showQuoteTypeSelectionModal() {
    const modal = document.createElement('div');
    modal.className = 'quote-type-modal-overlay';
    modal.innerHTML = `
        <div class="quote-type-modal">
            <div class="quote-type-modal-header">
                <h3><i class="fas fa-file-invoice"></i> Type de devis</h3>
                <button class="quote-type-modal-close" onclick="this.closest('.quote-type-modal-overlay').remove()">&times;</button>
            </div>
            <div class="quote-type-modal-body">
                <p>Votre panier contient à la fois des achats et des locations.</p>
                <p>Veuillez choisir le type de devis que vous souhaitez générer :</p>
                <div class="quote-type-buttons">
                    <button class="quote-type-btn purchase-type" onclick="selectQuoteType('achat')">
                        <i class="fas fa-shopping-cart"></i>
                        <strong>Devis d'achat</strong>
                        <span>Pour les produits à acheter</span>
                    </button>
                    <button class="quote-type-btn location-type" onclick="selectQuoteType('location')">
                        <i class="fas fa-calendar-alt"></i>
                        <strong>Devis de location</strong>
                        <span>Pour les équipements en location</span>
                    </button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

function selectQuoteType(type) {
    const modal = document.querySelector('.quote-type-modal-overlay');
    if (modal) modal.remove();
    currentQuoteType = type;
    if (type === 'location') openLocationQuoteModal();
    else openPurchaseQuoteModal();
}

function openPurchaseQuoteModal() {
    const purchaseItems = panier.filter(item => !item.is_location);

    if (purchaseItems.length === 0) {
        afficherNotification('Aucun produit d\'achat dans le panier', 'warning');
        return;
    }

    const existingModal = document.querySelector('.quote-modal-overlay');
    if (existingModal) existingModal.remove();

    const getTotal = () => {
        const total = purchaseItems.reduce((sum, item) => {
            return sum + ((Number(item.prix) || 0) * (Number(item.quantite) || 1));
        }, 0);

        return currentPromoCode
            ? total * (1 - currentPromoCode.reduction / 100)
            : total;
    };

    const getItemsHtml = () => {
        return purchaseItems.map((item, index) => {
            const prix = Number(item.prix) || 0;
            const quantite = Number(item.quantite) || 1;
            const totalLigne = prix * quantite;

            return `
               <style>
               .quote-quantity-control {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    margin: 10px 0;
                }

                .quote-quantity-control span {
                    font-size: 13px;
                    color: #555;
                    margin-right: 5px;
                }

                /* Boutons + et - */
                .quote-qty-btn {
                    width: 32px;
                    height: 32px;
                    border: none;
                    border-radius: 50%;
                    background: #0B2A5A;
                    color: white;
                    font-size: 18px;
                    font-weight: bold;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: all 0.2s ease;
                }

                .quote-qty-btn:hover {
                    background: #E8A020;
                    transform: scale(1.1);
                }

                .quote-qty-btn:active {
                    transform: scale(0.95);
                }

                /* Input quantité */
                .quote-qty-input {
                    width: 60px;
                    height: 32px;
                    text-align: center;
                    border: 1px solid #ddd;
                    border-radius: 6px;
                    font-size: 14px;
                }

                /* Ligne produit */
                .quote-item-row {
                    border: 1px solid #eee;
                    border-radius: 10px;
                    padding: 15px;
                    margin-bottom: 12px;
                    background: #fafafa;
                    transition: 0.2s;
                }

                .quote-item-row:hover {
                    background: #f1f5ff;
                }
                 </style>
                <div class="quote-item-row" data-index="${index}">
                    <div><strong>${item.nom}</strong></div>

                    <div class="quote-quantity-control">
                        <span>Quantité :</span>

                        <button type="button" class="quote-qty-btn" data-action="minus" data-index="${index}">
                            −
                        </button>

                        <input
                            type="number"
                            min="1"
                            value="${quantite}"
                            class="quote-qty-input"
                            data-index="${index}"
                        >

                        <button type="button" class="quote-qty-btn" data-action="plus" data-index="${index}">
                            +
                        </button>
                    </div>

                    <div>Prix unitaire : ${prix.toLocaleString('fr-FR')} FCFA</div>
                    <div><strong>Total : ${totalLigne.toLocaleString('fr-FR')} FCFA</strong></div>
                </div>
            `;
        }).join('');
    };

    const modal = document.createElement('div');
    modal.className = 'quote-modal-overlay';
    modal.style.display = 'flex';

    modal.innerHTML = `
        <div class="quote-modal purchase-quote-modal">
            <div class="quote-modal-header">
                <h3><i class="fas fa-file-invoice"></i> Demande de Devis - Achat</h3>
                <button class="quote-modal-close" onclick="this.closest('.quote-modal-overlay').remove()">&times;</button>
            </div>

            <div class="quote-modal-body">
                <div class="quote-items">
                    <h4>Produits sélectionnés :</h4>

                    <div id="purchaseQuoteItems">
                        ${getItemsHtml()}
                    </div>

                    ${currentPromoCode ? `<div class="promo-applied">Code promo appliqué : ${currentPromoCode.code} (${currentPromoCode.reduction}% de réduction)</div>` : ''}
                </div>

                <div class="quote-total" id="purchaseQuoteTotal">
                    Total TTC : ${getTotal().toLocaleString('fr-FR')} FCFA
                </div>
                
                <form id="purchaseQuoteForm" onsubmit="submitPurchaseQuote(event)">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom complet *</label>
                            <input type="text" id="quoteName" required>
                        </div>

                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" id="quoteEmail" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Téléphone *</label>
                            <input type="tel" id="quotePhone" required>
                        </div>

                        <div class="form-group">
                            <label>Adresse</label>
                            <input type="text" id="quoteAddress">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Message</label>
                        <textarea id="quoteMessage"></textarea>
                    </div>

                    <button type="submit" class="btn-primary">
                        <i class="fas fa-paper-plane"></i> Envoyer le devis
                    </button>
                </form>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    function updateQuoteDisplay() {
        document.getElementById('purchaseQuoteItems').innerHTML = getItemsHtml();
        document.getElementById('purchaseQuoteTotal').textContent =
            `Total TTC : ${getTotal().toLocaleString('fr-FR')} FCFA`;

        mettreAJourPanier();
    }

    function updateQuantity(index, quantity) {
        if (!purchaseItems[index]) return;

        quantity = parseInt(quantity, 10);
        if (!quantity || quantity < 1) quantity = 1;

        purchaseItems[index].quantite = quantity;

        const panierIndex = panier.indexOf(purchaseItems[index]);
        if (panierIndex !== -1) {
            panier[panierIndex].quantite = quantity;
        }

        updateQuoteDisplay();
    }

    modal.addEventListener('click', function (event) {
        const button = event.target.closest('.quote-qty-btn');
        if (!button) return;

        const index = Number(button.dataset.index);
        const action = button.dataset.action;
        const currentQuantity = Number(purchaseItems[index]?.quantite) || 1;

        if (action === 'plus') updateQuantity(index, currentQuantity + 1);
        if (action === 'minus') updateQuantity(index, currentQuantity - 1);
    });

    modal.addEventListener('change', function (event) {
        const input = event.target.closest('.quote-qty-input');
        if (!input) return;

        updateQuantity(Number(input.dataset.index), input.value);
    });
}


// =========================
// SOUMISSION DES DEVIS AVEC ENVOI EMAIL
// =========================

async function submitPurchaseQuote(event) {
    if (event) event.preventDefault();

    const purchaseItems = panier.filter(item => !item.is_location);

    panier = panier.filter(item => item.is_location === true);

    if (panier.length === 0) {
        currentPromoCode = null;
    }

    mettreAJourPanier();

    const nom = document.getElementById('quoteName')?.value.trim();
    const email = document.getElementById('quoteEmail')?.value.trim();
    const telephone = document.getElementById('quotePhone')?.value.trim();
    const adresse = document.getElementById('quoteAddress')?.value.trim();
    const message = document.getElementById('quoteMessage')?.value.trim();

    if (!nom || !email || !telephone) {
        afficherNotification('Veuillez remplir tous les champs obligatoires (*)', 'warning');
        return false;
    }

    const total = purchaseItems.reduce((sum, item) => sum + (item.prix * item.quantite), 0);
    const totalWithPromo = currentPromoCode ? total * (1 - currentPromoCode.reduction / 100) : total;

    const payload = {
        type: "achat",
        nom: nom,
        email: email,
        telephone: telephone,
        adresse: adresse || '',
        message: message || '',
        promo_code: currentPromoCode,
        items: purchaseItems.map(item => ({
            id: item.produitId,
            nom: item.nom,
            prix: item.prix,
            quantite: item.quantite,
            total: item.prix * item.quantite
        })),
        total: totalWithPromo
    };

    showQuoteLoader('Envoi du devis d\'achat en cours...');

    try {
        const response = await fetch('/app/api/submit_quote.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Erreur lors de l\'envoi du devis.');
        }

        hideQuoteLoader();
        showQuoteStatusMessage('Votre devis d\'achat a bien été envoyé !', 'success');

        panier = panier.filter(item => item.is_location === true);
        mettreAJourPanier();

        const modal = document.querySelector('.quote-modal-overlay');
        if (modal) modal.remove();

    } catch (error) {
        console.error(error);
        hideQuoteLoader();
        showQuoteStatusMessage(error.message, 'error');
    }
}



function showQuoteLoader(text = 'Envoi du devis en cours...') {
    const loader = document.getElementById('quoteLoader');
    const loaderText = document.getElementById('quoteLoaderText');
    if (loaderText) loaderText.textContent = text;
    if (loader) loader.style.display = 'flex';
}

function hideQuoteLoader() {
    const loader = document.getElementById('quoteLoader');
    if (loader) loader.style.display = 'none';
}

function showQuoteStatusMessage(message, type = 'success') {
    const box = document.getElementById('quoteStatusMessage');
    if (!box) return;
    box.className = `quote-status-message ${type}`;
    box.textContent = message;
    box.style.display = 'block';
    setTimeout(() => box.style.display = 'none', 5000);
}

// =========================
// NOTIFICATIONS
// =========================
function afficherNotification(message, type = 'success') {
    const notification = document.getElementById('notification');
    const messageSpan = document.getElementById('notificationMessage');
    if (!notification || !messageSpan) return;
    notification.className = `notification ${type}`;
    messageSpan.textContent = message;
    notification.style.display = 'flex';
    setTimeout(() => notification.style.display = 'none', 3000);
}

// =========================
// RECHERCHE
// =========================
function initRecherche() {
    const searchToggle = document.getElementById('searchToggle');
    const searchBox = document.getElementById('searchBox');
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    if (searchToggle) {
        searchToggle.addEventListener('click', () => {
            searchBox.classList.toggle('active');
            if (searchBox.classList.contains('active')) searchInput.focus();
            else searchResults.classList.remove('active');
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            if (query.length < 2) {
                searchResults.classList.remove('active');
                return;
            }
            const results = [];
            for (let category in serviceData) {
                serviceData[category].products.forEach(product => {
                    if (product.name.toLowerCase().includes(query)) {
                        results.push({ ...product, categoryKey: category });
                    }
                });
            }
            if (results.length > 0) {
                searchResults.innerHTML = results.slice(0, 5).map(item => `
                    <div class="search-result-item" onclick="openProductFromSearch('${item.categoryKey}', '${item.name}')">
                        <img src="${item.img || 'https://via.placeholder.com/40'}" width="40">
                        <div><strong>${item.name}</strong><br>${item.price_text || 'Prix sur demande'}</div>
                    </div>
                `).join('');
                searchResults.classList.add('active');
            } else {
                searchResults.innerHTML = '<div style="padding:10px;text-align:center;">Aucun résultat</div>';
                searchResults.classList.add('active');
            }
        });
    }
}

function openProductFromSearch(categoryKey, productName) {
    openProductsModal(categoryKey);
    const searchResults = document.getElementById('searchResults');
    if (searchResults) searchResults.classList.remove('active');
}

// =========================
// AUTRES INITIALISATIONS
// =========================
function initZoomFeature() { }
function initFermeturePanierExterieur() {
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.cart-wrapper')) {
            const cartDropdown = document.getElementById('cartDropdown');
            if (cartDropdown) cartDropdown.style.display = 'none';
        }
    });
}
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) target.scrollIntoView({ behavior: 'smooth' });
        });
    });
}
function initHeaderScroll() {
    const header = document.querySelector('header');
    window.addEventListener('scroll', () => {
        if (header) header.style.boxShadow = window.scrollY > 100 ? '0 5px 20px rgba(0,0,0,0.15)' : '0 2px 10px rgba(0,0,0,0.1)';
    });
}
function initModals() {
    document.getElementById('productsModalClose')?.addEventListener('click', () => {
        document.getElementById('productsModal')?.classList.remove('active');
        document.body.style.overflow = 'auto';
    });
    document.getElementById('productsModal')?.addEventListener('click', (e) => {
        if (e.target === e.currentTarget) {
            e.target.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    });
}

// =========================
// FONCTIONS UTILITAIRES
// =========================
function formatPrice(value) {
    return new Intl.NumberFormat('fr-FR').format(value || 0) + ' FCFA';
}