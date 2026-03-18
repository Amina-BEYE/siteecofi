// =========================
// DONNÉES DES PRODUITS
// =========================

const   serviceData = {
            briques: {
                title: "Production de briques et pavés",
                description: "Découvrez notre gamme complète de briques et pavés fabriqués avec les meilleurs matériaux.",
                products: [
                    { name: "Brique hourdis", desc: "Brique spéciale pour planchers", price: "490 FCFA/unité", img: "../app/IMG/brique-hourdis.jpg", oldPrice: "630 FCFA", rating: 4.5, reviews: 45 },
                    { name: "Pavé", desc: "Pavé pour aménagement d'allées et terrasses", price: "8.500 FCFA/m²", img: "../app/IMG/pave3.jpeg", oldPrice: "9.500 FCFA", rating: 4.5, reviews: 28 },
                    { name: "Pavé", desc: "Pavé pour aménagement d'allées et terrasses", price: "8.500 FCFA/m²", img: "../app/IMG/pave6.jpeg", rating: 4, reviews: 19 },
                    { name: "Pavé", desc: "Pavé pour aménagement d'allées et terrasses", price: "7.000 FCFA/m²", img: "../app/IMG/pave5.jpeg", oldPrice: "8.000 FCFA", rating: 4.5, reviews: 32 },
                    { name: "Pavé drainant", desc: "Pavé perméable pour gestion des eaux de pluie", price: "7.000 FCFA/m²", img: "../app/IMG/pave4.jpeg", rating: 5, reviews: 41 },
                    { name: "Pavé", desc: "Pavé pour aménagement d'allées et terrasses", price: "7.500 FCFA/m²", img: "../app/IMG/pave1.jpeg", oldPrice: "8.500 FCFA", rating: 4.5, reviews: 37 },
                    { name: "Pavé", desc: "Pavé pour aménagement d'allées et terrasses", price: "7.500 FCFA/m²", img: "../app/IMG/pave7.jpeg", rating: 4, reviews: 23 },
                    { name: "Brique creuse", desc: "Brique à alvéoles pour isolation thermique", price: "300-1.020 FCFA/unité", img: "../app/IMG/brique-creuse.jpg", oldPrice: "1.200 FCFA", rating: 4.5, reviews: 52 },
                    { name: "Pavé", desc: "Pavé pour aménagement d'allées et terrasses", price: "7.500 FCFA/m²", img: "../app/IMG/pave2.jpeg", rating: 4, reviews: 16 },
                    { name: "Pavé", desc: "Pavé pour aménagement d'allées et terrasses", price: "7.500 FCFA/m²", img: "../app/IMG/pave16.jpeg", oldPrice: "8.500 FCFA", rating: 4.5, reviews: 29 },
                    { name: "Pavé", desc: "Pavé pour aménagement d'allées et terrasses", price: "7.500 FCFA/m²", img: "../app/IMG/pave8.jpeg", rating: 4, reviews: 21 },
                    { name: "Brique pleine", desc: "Brique décorative pour finitions", price: "670-1020 FCFA/m²", img: "../app/IMG/brique-pleine.jpg", oldPrice: "1.200 FCFA", rating: 4.5, reviews: 33 },
                    { name: "Pavé hexagone", desc: "Pavé pour aménagement d'allées et terrasses", price: "8.500 FCFA/m²", img: "../app/IMG/pave-hexagone.jpg", rating: 5, reviews: 27 },
                    { name: "Pavé", desc: "Pavé pour aménagement d'allées et terrasses", price: "7.000 FCFA/m²", img: "../app/IMG/pave9.jpeg", oldPrice: "8.000 FCFA", rating: 4, reviews: 18 },
                    { name: "Pavé", desc: "Pavé pour aménagement d'allées et terrasses", price: "7.500 FCFA/m²", img: "../app/IMG/pave10.jpeg", rating: 4.5, reviews: 24 },
                    { name: "Pavé", desc: "Pavé pour aménagement d'allées et terrasses", price: "7.500 FCFA/m²", img: "../app/IMG/pave11.jpeg", rating: 4, reviews: 15 },
                    { name: "Bordure", desc: "Bordure pour allées et jardins", price: "5.700 FCFA/unité", img: "../app/IMG/bordure.jpg", oldPrice: "6.500 FCFA", rating: 4.5, reviews: 31 },
                    { name: "Pavé", desc: "Pavé pour aménagement d'allées et terrasses", price: "7.500 FCFA/m²", img: "../app/IMG/pave12.jpeg", rating: 4, reviews: 22 },
                    { name: "Pavé", desc: "Pavé pour aménagement d'allées et terrasses", price: "8.500 FCFA/m²", img: "../app/IMG/pave13.jpeg", oldPrice: "9.500 FCFA", rating: 4.5, reviews: 26 },
                    { name: "Pavé savon", desc: "Pavé au fini lisse, surface antidérapante", price: "8.500 FCFA/m²", img: "../app/IMG/pave-savon.jpg", rating: 5, reviews: 34 },
                    { name: "Pavé", desc: "Pavé pour aménagement d'allées et terrasses", price: "7.500 FCFA/m²", img: "../app/IMG/pave14.jpeg", oldPrice: "8.500 FCFA", rating: 4, reviews: 19 },
                    { name: "Pavé", desc: "Pavé pour aménagement d'allées et terrasses", price: "7.500 FCFA/m²", img: "../app/IMG/pave15.jpeg", rating: 4.5, reviews: 23 },
                    { name: "Pavé autobloquant", desc: "Pavé autobloquant pour allées", price: "6.550-7.600 FCFA/m²", img: "../app/IMG/paves-autobloquant.jpg", oldPrice: "8.500 FCFA", rating: 4.5, reviews: 42 },
                    { name: "Aglos", desc: "Agglos pour construction", price: "360-630 FCFA", img: "../app/IMG/aglos.jpg", rating: 4, reviews: 37 }
                ]
            },
            materiaux: {
                title: "Matériaux de construction",
                description: "Tous les matériaux essentiels pour vos chantiers",
                products: [
                    { name: "Ciment 42.5", desc: "Ciment haute résistance", price: "68.000 FCFA/sac", img: "../app/IMG/ciment.jpg", oldPrice: "70.000 FCFA", rating: 5, reviews: 67 },
                    { name: "Sable de rivière", desc: "Sable lavé pour béton", price: "-----", img: "../app/IMG/sable.jpg", rating: 4, reviews: 23 },
                    { name: "Gravier", desc: "Gravier pour béton", price: " Variable ", img: "../app/IMG/gravier.jpg", rating: 4, reviews: 31 },
                    { name: "Gravier", desc: "Gravier pour béton", price: " Variable ", img: "../app/IMG/grapb.jpg", rating: 4.5, reviews: 28 },
                    { name: "Gravier", desc: "Gravier pour béton", price: " Variable ", img: "../app/IMG/grapn.jpeg", rating: 4, reviews: 19 },
                    { name: "Gravier", desc: "Gravier pour béton", price: " Variable ", img: "../app/IMG/grapn.jpeg", rating: 4, reviews: 22 },
                    { name: "Gravier", desc: "Gravier pour béton", price: " Variable ", img: "../app/IMG/grapn.jpeg", rating: 4.5, reviews: 25 },
                    { name: "Fers à béton", desc: "Armature pour béton armé", price: " Variable ", img: "../app/IMG/fer.jpg", rating: 4.5, reviews: 41 },
                    { name: "Bois de charpente", desc: "Bois traité pour coffrages", price: "Prix sur demande", img: "../app/IMG/baton.jpg", rating: 4, reviews: 16 },
                    { name: "Tôle galvanisée", desc: "Pour clôtures", price: "Variable", img: "../app/IMG/toit.jpg", rating: 4, reviews: 13 },
                    { name: "Kit sécurité complet", desc: "Tenue complète chantier", price: "Variable", img: "../app/IMG/ma.jpg", rating: 4.5, reviews: 21 },
                    { name: "Casque de protection", desc: "Normes internationales", price: " Variable ", img: "../app/IMG/casque.jpg", rating: 5, reviews: 34 },
                    { name: "Chaussures de sécurité", desc: "Semelle anti-perforation", price: " Variable ", img: "../app/IMG/chaussures.jpg", rating: 4.5, reviews: 27 },
                    { name: "Gants professionnels", desc: "Protection mains", price: "----", img: "../app/IMG/gants.jpg", rating: 4, reviews: 42 },
                    { name: "Gilet haute visibilité", desc: "Fluorescent", price: "----", img: "../app/IMG/gilet.jpg", rating: 4.5, reviews: 19 },
                    { name: "Kit gilet complet", desc: "Kit complet sécurité", price: "----", img: "../app/IMG/kit.jpg", rating: 5, reviews: 23 }
                ]
            },
            decoration: {
                title: "Décoration & Aménagement",
                description: "Nos transformations en vidéo + services sur mesure",
                products: [
                    {
                        name: "Conception 3D",
                        desc: "Visualisez votre projet avant réalisation",
                        price: "----",
                        media: {
                            type: "video",
                            src: "../app/IMG/video2.mp4",
                            thumbnail: "../app/IMG/plan-3d-batiment-09.png"
                        }
                    },
                    {
                        name: "Decoration Chambre",
                        desc: "Visualisez votre projet avant réalisation",
                        price: "----",
                        media: {
                            type: "video",
                            src: "../app/IMG/decochambre.mp4",
                            thumbnail: "../app/IMG/chambre.jpg"
                        }
                    },
                    {
                        name: "Salon Moderne",
                        desc: "Transformation complète d'un salon",
                        price: "----",
                        media: {
                            type: "video",
                            src: "../app/IMG/decosalon.mp4",
                            thumbnail: "../app/IMG/salon.jpeg"
                        }
                    },
                    {
                        name: "Cuisine Design",
                        desc: "Aménagement de cuisine sur mesure",
                        price: "----",
                        media: {
                            type: "video",
                            src: "../app/IMG/decocuisine.mp4",
                            thumbnail: "../app/IMG/cuisine.jpg"
                        }
                    },
                    {
                        name: "Aménagement Extérieur",
                        desc: "Espace extérieur complet",
                        price: "----",
                        media: {
                            type: "video",
                            src: "../app/IMG/decoxterieur.mp4",
                            thumbnail: "../app/IMG/decoex.jfif"
                        }
                    },
                    {
                        name: "Aménagement Intérieur",
                        desc: "Espace interieur complet",
                        price: "----",
                        media: {
                            type: "video",
                            src: "../app/IMG/decointer.mp4",
                            thumbnail: "../app/IMG/decointerieur.jpg"
                        }
                    }
                ]
            },
            conseil: {
                title: "Conseil immobilier",
                description: "Expertise pour vos transactions immobilières",
                products: [
                    { name: "Évaluation de propriété", desc: "Estimation précise de votre bien", img: "https://images.unsplash.com/photo-1560518883-ce09059eeffa", rating: 4.5, reviews: 22 },
                    { name: "Accompagnement à l'achat", desc: "Assistance complète pour achat", img: "https://images.unsplash.com/photo-1581094794329-c8112a89af12", rating: 5, reviews: 18 },
                    { name: "Accompagnement à la vente", desc: "Gestion complète de la vente", img: "https://images.unsplash.com/photo-1541140532154-b024d705b90a", rating: 4.5, reviews: 15 },
                    { name: "Étude de faisabilité", desc: "Analyse technique et financière", img: "https://images.unsplash.com/photo-1504307651254-35680f356dfd", rating: 5, reviews: 13 }
                ]
            },
            plans: {
                title: "Plans de Construction",
                description: "Plans architecturaux prêts à l'emploi",
                products: [
                    { name: "Plan R+1", desc: "RDC + étage, 3 chambres", price: "----", img: "../app/IMG/R+1.jpg.jpeg", rating: 4.5, reviews: 31 },
                    { name: "Plan R+2", desc: "RDC + 2 étages, garage", price: "----", img: "../app/IMG/R+2.jpg.jpeg", rating: 5, reviews: 27 },
                    { name: "Plan RDC", desc: "Maison plain-pied", price: "----", img: "../app/IMG/RDC.jpg.jpeg", rating: 4, reviews: 43 },
                    { name: "Plan 3D Rendu", desc: "Visualisation 3D réaliste", price: "----", img: "https://images.unsplash.com/photo-1613490493576-7fde63acd811", rating: 4.5, reviews: 19 },
                    { name: "Plan 3D Aménagé", desc: "3D avec décoration intérieure", price: "----", img: "https://images.unsplash.com/photo-1598928506311-c55ded91a20c", rating: 5, reviews: 16 },
                    { name: "Pack Complet R+2+3D", desc: "Plans + visualisation 3D", price: "----", img: "https://images.unsplash.com/photo-1560448204-e02f11c3d0e2", rating: 4.5, reviews: 22 }
                ]
            },
            location: {
                title: "Location matériel topographique",
                description: "Équipements de précision pour vos travaux",
                products: [
                    { name: "Récepteur GNSS", desc: "Haute précision pour mesures", price: "----", img: "../app/IMG/gnss.jpg", rating: 5, reviews: 14 },
                    { name: "Niveau", desc: "Pour alignements précis", price: "----", img: "../app/IMG/niveau.jpg", rating: 4, reviews: 21 },
                    { name: "Station totale", desc: "Mesure angulaire de précision", price: "----", img: "../app/IMG/station.jpg", rating: 4.5, reviews: 9 },
                    { name: "GPS de poche", desc: "Pour repérage sur terrain", price: "----", img: "../app/IMG/gps.jpg", rating: 4, reviews: 17 },
                    { name: "Drone topographique", desc: "Levés aériens et modélisation", price: "----", img: "../app/IMG/drone.jpg", rating: 5, reviews: 8 },
                    { name: "Décamètre mécanique", desc: "Roulette pour longues distances", price: "----", img: "../app/IMG/deac.jpg", rating: 4, reviews: 25 }
                ]
            }
        };

const promoCodes = [
    { code: "ECOFI10", reduction: 10, desc: "-10% sur tout le site" },
    { code: "BIENVENUE", reduction: 15, desc: "-15% pour les nouveaux clients" }
];

const regionsSenegal = {
    "Dakar": {
        "Dakar": ["Plateau", "Fass", "Médina"]
    },
    "Thiès": {
        "Thiès": ["Nguinth", "Sicap", "Médina Fall"]
    }
};

// =========================
// VARIABLES GLOBALES
// =========================
let panier = [];
let currentQuoteItems = [];
let selectedPaymentMethod = null;
let currentPromoCode = null;
let livraisonInfo = null;
let factures = [];
let currentFacture = null;

// =========================
// INITIALISATION
// =========================
document.addEventListener('DOMContentLoaded', function () {
    initialiserApplication();
});

function initialiserApplication() {
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
            if (searchBox.classList.contains('active')) {
                searchInput.focus();
            } else {
                searchResults.classList.remove('active');
            }
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
                    if (
                        product.name.toLowerCase().includes(query) ||
                        (product.desc && product.desc.toLowerCase().includes(query))
                    ) {
                        results.push({
                            ...product,
                            category: serviceData[category].title,
                            categoryKey: category
                        });
                    }
                });
            }

            if (results.length > 0) {
                searchResults.innerHTML = results.slice(0, 5).map(item => `
                    <div class="search-result-item" onclick="openProductFromSearch('${item.categoryKey}', '${item.name}')">
                        <img src="${item.img || 'https://via.placeholder.com/40'}" alt="${item.name}">
                        <div class="search-result-info">
                            <h4>${item.name}</h4>
                            <p>${item.price || 'Prix sur demande'} - ${item.category}</p>
                        </div>
                    </div>
                `).join('');
                searchResults.classList.add('active');
            } else {
                searchResults.innerHTML = '<div style="padding:10px; text-align:center;">Aucun résultat trouvé</div>';
                searchResults.classList.add('active');
            }
        });
    }
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
// PROMOS
// =========================
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
// PANIER
// =========================
function toggleCart(event) {
    //event.stopPropagation();
    //const cartDropdown = document.getElementById('cartDropdown');
    //cartDropdown.style.display = cartDropdown.style.display === 'block' ? 'none' : 'block';

    event.stopPropagation();
    const cart = document.getElementById("cartDropdown");

    if (cart.style.display === "flex") {
        cart.style.display = "none";
    } else {
        cart.style.display = "flex";
    }
}

function ajouterAuPanier(produit) {
    const existant = panier.find(item => item.nom === produit.name);

    if (existant) {
        existant.quantite++;
    } else {
        panier.push({
            id: Date.now() + Math.random(),
            nom: produit.name,
            prix: extrairePrix(produit.price),
            ancienPrix: produit.oldPrice ? extrairePrix(produit.oldPrice) : null,
            image: produit.img || '🏗️',
            quantite: 1
        });
    }

    mettreAJourPanier();
    afficherNotification(`${produit.name} ajouté au panier !`);
}

function extrairePrix(price) {
    if (!price) return 0;
    const chiffres = price.replace(/[^0-9]/g, '');
    return chiffres ? parseFloat(chiffres) : 0;
}

function mettreAJourPanier() {
    const cartItems = document.getElementById('cartItems');
    const cartCount = document.getElementById('cartCount');
    const cartTotal = document.getElementById('cartTotal');
    const cartHeaderTotal = document.getElementById('cartHeaderTotal');
    const cartItemCount = document.getElementById('cartItemCount');

    let total = panier.reduce((sum, item) => sum + (item.prix * item.quantite), 0);

    if (currentPromoCode) {
        total = total * (1 - currentPromoCode.reduction / 100);
    }

    const nombreArticles = panier.reduce((sum, item) => sum + item.quantite, 0);

    if (cartCount) cartCount.textContent = nombreArticles;
    if (cartTotal) cartTotal.textContent = total.toFixed(0) + ' FCFA';
    if (cartHeaderTotal) cartHeaderTotal.textContent = total.toFixed(0) + ' FCFA';
    if (cartItemCount) cartItemCount.textContent = nombreArticles + ' article(s)';

    if (!cartItems) return;

    if (panier.length === 0) {
        cartItems.innerHTML = '<p style="text-align:center; color:#999; padding:20px;">Votre panier est vide</p>';
        currentPromoCode = null;
        return;
    }

    cartItems.innerHTML = panier.map(item => `
        <div class="cart-item">
            <div class="cart-item-image" style="overflow:hidden;">
                <img src="${item.image}" alt="${item.nom}" style="width:100%; height:100%; object-fit:cover; border-radius:8px;">
            </div>
            <div class="cart-item-details">
                <div class="cart-item-name">${item.nom}</div>
                <div class="cart-item-price">${(item.prix * item.quantite).toFixed(0)} FCFA</div>
                <div class="cart-item-actions">
                    <div class="cart-item-quantity">
                        <button class="qty-btn" onclick="changerQuantite('${item.id}', -1)">-</button>
                        <span>${item.quantite}</span>
                        <button class="qty-btn" onclick="changerQuantite('${item.id}', 1)">+</button>
                    </div>
                    <span class="remove-item" onclick="retirerDuPanier('${item.id}')">
                        <i class="fas fa-trash"></i>
                    </span>
                </div>
            </div>
        </div>
    `).join('');
}

function changerQuantite(id, changement) {
    const item = panier.find(i => i.id == id);
    if (!item) return;

    item.quantite += changement;

    if (item.quantite <= 0) {
        retirerDuPanier(id);
    } else {
        mettreAJourPanier();
    }
}

function retirerDuPanier(id) {
    panier = panier.filter(item => item.id != id);
    if (panier.length === 0) currentPromoCode = null;
    mettreAJourPanier();
    afficherNotification('Produit retiré du panier');
}

function viderPanier() {
    panier = [];
    currentPromoCode = null;
    mettreAJourPanier();
    afficherNotification('Panier vidé');
}

// =========================
// MODAL PRODUITS
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
            ${service.products.map(product => `
                <div class="product-card-modern">
                    <div class="product-img-modern">
                        <img src="${product.img || 'https://via.placeholder.com/400x200?text=ECOFI'}" alt="${product.name}">
                    </div>
                    <div class="product-info-modern">
                        <h4>${product.name}</h4>
                        <p>${product.desc || ''}</p>
                        <div class="product-price-modern">${product.price || 'Prix sur demande'}</div>
                        <button class="buy-btn-modern" onclick='ajouterAuPanier(${JSON.stringify({
                            name: product.name,
                            price: product.price || '0 FCFA',
                            oldPrice: product.oldPrice || null,
                            img: product.img || ''
                        })})'>
                            <i class="fas fa-cart-plus"></i> Ajouter au panier
                        </button>
                    </div>
                </div>
            `).join('')}
        </div>
    `;

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// =========================
// RECHERCHE -> OUVRIR PRODUIT
// =========================
function openProductFromSearch(categoryKey, productName) {
    openProductsModal(categoryKey);

    const searchResults = document.getElementById('searchResults');
    if (searchResults) {
        searchResults.classList.remove('active');
    }
}

// =========================
// ZOOM
// =========================
function initZoomFeature() {
    document.getElementById('zoomModalClose')?.addEventListener('click', closeZoomModal);
    document.getElementById('zoomModalOverlay')?.addEventListener('click', function (e) {
        if (e.target === this) closeZoomModal();
    });
}

function closeZoomModal() {
    const modal = document.getElementById('zoomModalOverlay');
    const zoomVideo = document.getElementById('zoomVideo');

    if (zoomVideo) {
        zoomVideo.pause();
        zoomVideo.currentTime = 0;
    }

    if (modal) modal.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// =========================
// NOTIFICATIONS
// =========================
function afficherNotification(message, type = 'success') {
    const notification = document.getElementById('notification');
    const messageSpan = document.getElementById('notificationMessage');
    const icon = notification?.querySelector('i');

    if (!notification || !messageSpan) return;

    notification.className = `notification ${type}`;
    messageSpan.textContent = message;

    if (icon) {
        if (type === 'success') icon.className = 'fas fa-check-circle';
        else if (type === 'error') icon.className = 'fas fa-exclamation-circle';
        else if (type === 'warning') icon.className = 'fas fa-exclamation-triangle';
        else if (type === 'info') icon.className = 'fas fa-info-circle';
    }

    notification.style.display = 'flex';

    setTimeout(() => {
        notification.style.display = 'none';
    }, 3000);
}

// =========================
// UI / EVENTS
// =========================
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
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 100,
                    behavior: 'smooth'
                });
            }
        });
    });
}

function initHeaderScroll() {
    const header = document.querySelector('header');
    window.addEventListener('scroll', function () {
        if (header) {
            header.style.boxShadow = window.scrollY > 100
                ? '0 5px 20px rgba(0, 0, 0, 0.15)'
                : '0 2px 10px rgba(0, 0, 0, 0.1)';
        }
    });
}

function initModals() {
    document.getElementById('productsModalClose')?.addEventListener('click', function () {
        document.getElementById('productsModal')?.classList.remove('active');
        document.body.style.overflow = 'auto';
    });

    document.getElementById('productsModal')?.addEventListener('click', function (e) {
        if (e.target === this) {
            this.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    });

    document.getElementById('quoteModal')?.addEventListener('click', function (e) {
        if (e.target === this) {
            closeQuoteModal();
        }
    });
}

// =========================
// DEVIS
// =========================

function formatPrice(value) {
    return new Intl.NumberFormat('fr-FR').format(Number(value || 0)) + ' FCFA';
}

function openQuoteModal() {
    if (!panier || panier.length === 0) {
        afficherNotification('Votre panier est vide.');
        return;
    }

    const quoteItems = document.getElementById('quoteItems');
    const quoteTotal = document.getElementById('quoteTotal');
    const modal = document.getElementById('quoteModal');

    let html = '';
    let total = 0;

    panier.forEach(item => {
        const prix = Number(item.prix || 0);
        const quantite = Number(item.quantite || 1);
        const totalLigne = prix * quantite;
        total += totalLigne;

        html += `
            <div class="quote-item-row" style="padding:12px 0; border-bottom:1px solid #eee;">
                <div><strong>${item.nom}</strong></div>
                <div>Quantité : ${quantite}</div>
                <div>Prix unitaire : ${formatPrice(prix)}</div>
                <div>Total : ${formatPrice(totalLigne)}</div>
            </div>
        `;
    });

    quoteItems.innerHTML = html;
    quoteTotal.innerHTML = `Total estimé : ${formatPrice(total)}`;
    modal.style.display = 'flex';
}

function closeQuoteModal() {
    document.getElementById('quoteModal').style.display = 'none';
}

async function submitQuote(event) {
    event.preventDefault();

    if (!panier || panier.length === 0) {
        afficherNotification('Votre panier est vide.');
        return;
    }

   

    const nom = document.getElementById('quoteName').value.trim();
    const email = document.getElementById('quoteEmail').value.trim();
    const telephone = document.getElementById('quotePhone').value.trim();
    const message = document.getElementById('quoteMessage').value.trim();

    if (!nom || !email || !telephone) {
        afficherNotification('Veuillez remplir les champs obligatoires.');
        return;
    }

    const payload = {
        nom,
        email,
        telephone,
        message,
        items: panier.map(item => ({
            id: item.id ?? null,
            nom: item.nom,
            prix: Number(item.prix || 0),
            quantite: Number(item.quantite || 1)
        }))
    };

    try {
        const response = await fetch('/SITEECOFI/app/api/submit_quote.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Erreur lors de l’envoi du devis.');
        }

        afficherNotification('Votre demande de devis a bien été envoyée.');

        panier.length = 0;
        mettreAJourPanier();

        document.getElementById('quoteForm').reset();
        closeQuoteModal();

        if (result.pdf_url) {
            window.open(result.pdf_url, '_blank');
        }

    } catch (error) {
        afficherNotification(error.message);
    }
}

document.getElementById('quoteForm').addEventListener('submit', submitQuote);

document.getElementById('quoteModal').addEventListener('click', function (e) {
    if (e.target === this) {
        closeQuoteModal();
    }
});