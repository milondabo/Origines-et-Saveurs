// 1. UTILITAIRES (FONCTIONS D'AIDE)

// Transforme un nombre en prix lisible (ex: 8500 -> 8 500 FCFA)
function formaterPrix(montant) {
    return montant.toLocaleString('fr-FR') + ' FCFA';
}

// Transforme un texte de prix en nombre (ex: "8 500 FCFA" -> 8500)
function analyserPrix(chainePrix) {
    // On enlève tout ce qui n'est pas un chiffre
    return parseInt(chainePrix.replace(/[^\d]/g, ''), 10);
}

// 2. GESTION DES FAVORIS

// Récupère la liste des produits favoris
function obtenirFavoris() {
    return JSON.parse(localStorage.getItem('favoris')) || [];
}

// Enregistre la liste des favoris
function sauvegarderFavoris(favoris) {
    localStorage.setItem('favoris', JSON.stringify(favoris));

    // Si on est sur la page du panier, on rafraîchit l'affichage des favoris
    if (window.location.pathname.includes('panier.php')) {
        mettreAJourInterfaceFavoris();
    }
}

// Ajoute ou retire un produit des favoris
function basculerFavori(produit) {
    let favoris = obtenirFavoris();
    const index = favoris.findIndex(f => f.name === produit.name);

    if (index > -1) {
        favoris.splice(index, 1); // Retire si déjà présent
        afficherNotification(`♡ ${produit.name} retiré des favoris`);
    } else {
        favoris.push(produit); // Ajoute si absent
        afficherNotification(`♥ ${produit.name} ajouté aux favoris`);
    }

    sauvegarderFavoris(favoris);
    mettreAJourEtatBoutonsFavoris();
}

// Met à jour l'apparence des boutons favoris (coeur rempli ou non)
function mettreAJourEtatBoutonsFavoris() {
    const favoris = obtenirFavoris();
    document.querySelectorAll('.carte').forEach(carte => {
        const titreEl = carte.querySelector('.titre-produit');
        if (!titreEl) return;
        const nom = titreEl.textContent.trim();
        const bouton = carte.querySelector('.bouton-favoris');
        const icone = bouton ? bouton.querySelector('.icone-bouton') : null;

        if (bouton && icone) {
            if (favoris.some(f => f.name === nom)) {
                bouton.classList.add('actif');
                icone.src = 'Icones/heart-filled.svg'; // Passage au coeur rempli
            } else {
                bouton.classList.remove('actif');
                icone.src = 'Icones/heart.svg'; // Retour au coeur vide
            }
        }
    });
}

// 3. GESTION DU PANIER

// Récupère la liste des produits enregistrés dans le navigateur
function obtenirPanier() {
    // On essaye de lire le panier, sinon on renvoie un tableau vide []
    return JSON.parse(localStorage.getItem('panier')) || [];
}

// Enregistre la liste des produits dans le navigateur
function sauvegarderPanier(panier) {
    localStorage.setItem('panier', JSON.stringify(panier));
    mettreAJourBadgePanier(); // Met à jour le petit chiffre sur l'icône panier
    mettreAJourSelecteursQuantite(); // Met à jour l'affichage sur les cartes

    // Si on est sur la page du panier, on rafraîchit l'affichage de la liste
    if (window.location.pathname.includes('panier.php')) {
        mettreAJourInterfacePanier();
    }
}

// Met à jour l'affichage des sélecteurs +/- sur chaque carte
function mettreAJourSelecteursQuantite() {
    const panier = obtenirPanier();
    document.querySelectorAll('.carte, .dish-card').forEach(carte => {
        const h3 = carte.querySelector('.titre-produit') || carte.querySelector('h3');
        if (!h3) return;

        const nom = h3.textContent.trim();
        const article = panier.find(a => a.name === nom);
        const boutonAchat = carte.querySelector('.bouton-carte, .add-to-cart');
        const selecteur = carte.querySelector('.selecteur-quantite');
        const valeurQte = selecteur ? selecteur.querySelector('.quantite-valeur') : null;

        if (article) {
            if (boutonAchat) boutonAchat.style.display = 'none';
            if (selecteur) {
                selecteur.style.display = 'flex';
                if (valeurQte) valeurQte.textContent = article.quantity;
            }
        } else {
            if (boutonAchat) boutonAchat.style.display = 'flex';
            if (selecteur) selecteur.style.display = 'none';
        }
    });
}

// Met à jour le nombre d'articles affiché dans le menu de navigation
function mettreAJourBadgePanier() {
    const panier = obtenirPanier();
    // On calcule la somme des quantités de chaque article
    const totalArticles = panier.reduce((somme, article) => somme + article.quantity, 0);

    // On cherche tous les éléments qui ont la classe 'badge-panier'
    document.querySelectorAll('.badge-panier').forEach(badge => {
        badge.textContent = totalArticles;
    });
}

// Affiche un petit message en bas de l'écran quand on ajoute un produit
function afficherNotification(message) {
    const toast = document.createElement('div');
    toast.className = 'notification';
    toast.textContent = message;
    document.body.appendChild(toast);

    // Apparition (ajout de la classe CSS 'visible')
    setTimeout(() => toast.classList.add('visible'), 100);

    // Disparition après 3 secondes
    setTimeout(() => {
        toast.classList.remove('visible');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// 4. LOGIQUE D'AJOUT ET MODIFICATION

// Ajoute un produit au panier ou augmente sa quantité s'il y est déjà
function ajouterAuPanier(produit) {
    const panier = obtenirPanier();
    const nomProduit = produit.name.trim();

    // On vérifie si le produit est déjà dans le panier
    const articleExistant = panier.find(article => article.name === nomProduit);

    if (articleExistant) {
        articleExistant.quantity += 1;
    } else {
        // Sinon on l'ajoute avec une quantité de 1
        panier.push({
            name: nomProduit,
            price: produit.price,
            image: produit.image,
            quantity: 1
        });
    }

    sauvegarderPanier(panier);
    afficherNotification(`✓ ${nomProduit} ajouté au panier`);
}

// Change la quantité d'un produit (utilisé sur la page Panier)
function modifierQuantite(index, nouvelleQte) {
    const panier = obtenirPanier();

    if (nouvelleQte > 0) {
        panier[index].quantity = nouvelleQte;
    } else {
        // Si la quantité tombe à 0, on supprime l'article
        panier.splice(index, 1);
    }

    sauvegarderPanier(panier);
}

// Supprime complètement un article du panier
function supprimerDuPanier(index) {
    const panier = obtenirPanier();
    panier.splice(index, 1);
    sauvegarderPanier(panier);
}

// 5. INTERFACE ET ÉVÉNEMENTS

// Initialise les boutons "Ajouter au panier" sur la page Menu
function initialiserBoutonsAchat() {
    // On cherche tous les boutons de commande sur la page
    const boutons = document.querySelectorAll('.bouton-carte, .add-to-cart');

    boutons.forEach(bouton => {
        bouton.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation(); // Évite d'ouvrir le modal si on clique sur le bouton

            // On récupère les infos du produit dans la carte parente
            const carte = bouton.closest('.carte') || bouton.closest('.dish-card');
            const nom = (carte.querySelector('.titre-produit') || carte.querySelector('h3')).textContent.trim();
            const prixTexte = (carte.querySelector('.prix') || carte.querySelector('.price')).textContent;
            const image = (carte.querySelector('.image') || carte.querySelector('img')).getAttribute('src');

            const produit = {
                name: nom,
                price: analyserPrix(prixTexte),
                image: image
            };

            ajouterAuPanier(produit);
        });
    });

    // Écouteur pour les boutons de favoris
    const boutonsFavoris = document.querySelectorAll('.bouton-favoris');
    boutonsFavoris.forEach(bouton => {
        bouton.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            const carte = bouton.closest('.carte');
            const nom = carte.querySelector('.titre-produit').textContent.trim();
            const prixTexte = carte.querySelector('.prix').textContent;
            const image = carte.querySelector('.image').getAttribute('src');

            const produit = {
                name: nom,
                price: analyserPrix(prixTexte),
                image: image
            };

            basculerFavori(produit);
        });
    });

    mettreAJourEtatBoutonsFavoris(); // Initialiser l'état au chargement

    // Initialisation globale des sélecteurs de quantité (+/-) par délégation d'événements
    document.addEventListener('click', (e) => {
        const boutonMoins = e.target.closest('.bouton-moins');
        const boutonPlus = e.target.closest('.bouton-plus');

        if (boutonMoins || boutonPlus) {
            e.stopPropagation();
            const carte = (boutonMoins || boutonPlus).closest('.carte') || (boutonMoins || boutonPlus).closest('.dish-card');
            const titreEl = carte.querySelector('.titre-produit') || carte.querySelector('h3');
            if (!titreEl) return;
            const nom = titreEl.textContent.trim();
            const panier = obtenirPanier();
            const index = panier.findIndex(a => a.name === nom);

            if (boutonMoins && index > -1) {
                if (panier[index].quantity > 1) {
                    panier[index].quantity -= 1;
                } else {
                    panier.splice(index, 1);
                }
                sauvegarderPanier(panier);
            } else if (boutonPlus && index > -1) {
                panier[index].quantity += 1;
                sauvegarderPanier(panier);
            }
        }
    });

    mettreAJourSelecteursQuantite(); // Initialiser au chargement

    // Écouteur pour le bouton "Ajouter au panier" DANS le modal
    const boutonModal = document.querySelector('.bouton-ajout-modal');
    if (boutonModal) {
        boutonModal.addEventListener('click', () => {
            const modal = document.getElementById('productModal');
            const nom = modal.querySelector('.titre-modal').textContent;
            const prixTexte = modal.querySelector('.prix-modal').textContent;
            const image = modal.querySelector('.image-modal').getAttribute('src');

            const produit = {
                name: nom,
                price: analyserPrix(prixTexte),
                image: image
            };

            ajouterAuPanier(produit);
        });
    }

    // Écouteur pour ouvrir le modal quand on clique sur une carte (hors boutons)
    document.querySelectorAll('.carte').forEach(carte => {
        carte.addEventListener('click', (e) => {
            // Si on clique sur le bouton panier ou coeur, on n'ouvre pas le modal
            if (e.target.closest('.groupe-boutons')) return;

            ouvrirModal(carte);
        });
    });
}

// Affiche les détails d'un produit dans une fenêtre surgissante (Modal)
function ouvrirModal(carte) {
    const modal = document.getElementById('productModal');
    if (!modal) return;

    // On récupère les infos de la carte
    const nom = carte.querySelector('.titre-produit').textContent;
    const prix = carte.querySelector('.prix').textContent;
    const image = carte.querySelector('.image').getAttribute('src');
    const description = carte.querySelector('.description').textContent;
    const note = carte.querySelector('.note').textContent;

    // Récupération des avis masqués
    const avisHTML = carte.querySelector('.avis-caches').innerHTML;

    // On remplit le modal avec ces infos
    modal.querySelector('.titre-modal').textContent = nom;
    modal.querySelector('.prix-modal').textContent = prix;
    modal.querySelector('.description-modal').textContent = description;
    modal.querySelector('.image-modal').src = image;
    modal.querySelector('.note-modal').textContent = note;

    // Remplissage des avis
    const conteneurAvis = document.getElementById('liste-avis-modal');
    if (conteneurAvis) {
        conteneurAvis.innerHTML = avisHTML;
    }

    // On affiche le modal (ajout de la classe 'actif')
    modal.classList.add('actif');
}

// Ferme la fenêtre surgissante
function fermerModal() {
    const modal = document.getElementById('productModal');
    if (modal) modal.classList.remove('actif');
}

// Gère le filtrage par catégories (Africain, Européen, etc.)
function configurerFiltresCategories() {
    const categories = document.querySelectorAll('.categories li');
    const produits = document.querySelectorAll('.carte');

    categories.forEach(li => {
        li.addEventListener('click', function () {
            const choix = this.getAttribute('data-category');

            // Changer le style du bouton actif
            categories.forEach(c => c.classList.remove('actif'));
            this.classList.add('actif');

            // Cacher ou montrer les produits
            produits.forEach(produit => {
                const catProduit = produit.getAttribute('data-category');
                if (choix === 'Tout' || catProduit === choix) {
                    produit.style.display = 'block';
                } else {
                    produit.style.display = 'none';
                }
            });
        });
    });
}

// 6. AFFICHAGE DES PAGES SPÉCIFIQUES

// Affiche la liste des articles sur la page panier.php
function mettreAJourInterfacePanier() {
    const conteneur = document.getElementById('cartItems');
    if (!conteneur) return; // Si on n'est pas sur la page panier, on arrête là

    const panier = obtenirPanier();
    let HTML = '';
    let total = 0;

    if (panier.length === 0) {
        conteneur.innerHTML = '<p class="panier-vide">Votre panier est vide.</p>';
        document.getElementById('total').textContent = '0 FCFA';
        if (document.getElementById('subtotal')) document.getElementById('subtotal').textContent = '0 FCFA';
        return;
    }

    panier.forEach((article, index) => {
        total += article.price * article.quantity;
        HTML += `
            <div class="article-panier">
                <img src="${article.image}" alt="${article.name}" class="image-article-panier">
                <div class="details-article">
                    <div class="nom-article">${article.name}</div>
                    <div class="prix-article">${formaterPrix(article.price)}</div>
                </div>
                <div class="actions-article">
                    <div class="selecteur-quantite" style="max-width: 120px; height: 40px; padding: 0 10px;">
                        <button onclick="modifierQuantite(${index}, ${article.quantity - 1})">-</button>
                        <span class="quantite-valeur">${article.quantity}</span>
                        <button onclick="modifierQuantite(${index}, ${article.quantity + 1})">+</button>
                    </div>
                    <button onclick="supprimerDuPanier(${index})" class="bouton-supprimer">
                        Supprimer
                    </button>
                </div>
            </div>
        `;
    });

    conteneur.innerHTML = HTML;
    document.getElementById('total').textContent = formaterPrix(total);
    if (document.getElementById('subtotal')) document.getElementById('subtotal').textContent = formaterPrix(total);
}

// Affiche la liste des favoris sur la page panier.php
function mettreAJourInterfaceFavoris() {
    const conteneur = document.getElementById('favoritesItems');
    if (!conteneur) return;

    const favoris = obtenirFavoris();
    if (favoris.length === 0) {
        conteneur.innerHTML = '<p class="panier-vide">Vous n\'avez pas encore de favoris.</p>';
        return;
    }

    conteneur.innerHTML = favoris.map(produit => `
        <article class="carte">
            <div class="enveloppe-image-carte">
                <img src="${produit.image}" alt="${produit.name}" class="image">
            </div>
            <div class="corps-carte">
                <h3 class="titre-produit">${produit.name}</h3>
                <div class="prix">${formaterPrix(produit.price)}</div>
                <div class="groupe-boutons">
                    <button class="bouton-carte" onclick="ajouterAuPanier({name: '${produit.name.replace(/'/g, "\\'")}', price: ${produit.price}, image: '${produit.image}'})">
                        <img src="Icones/panier.svg" alt="" class="icone-bouton">
                        Ajouter au panier
                    </button>
                    <!-- Sélecteur de quantité pour les favoris -->
                    <div class="selecteur-quantite" style="display: none;">
                        <button class="bouton-moins">-</button>
                        <span class="quantite-valeur">1</span>
                        <button class="bouton-plus">+</button>
                    </div>
                    <button class="bouton-favoris actif" onclick="basculerFavori({name:'${produit.name.replace(/'/g, "\\'")}', price:${produit.price}, image:'${produit.image}'})">
                        <img src="Icones/heart-filled.svg" alt="" class="icone-bouton">
                    </button>
                </div>
            </div>
        </article>
    `).join('');

    // On s'assure que si un favori est déjà dans le panier, on affiche le sélecteur
    mettreAJourSelecteursQuantite();
}

// Gère l'envoi du formulaire de commande
function initialiserFormulaireCommande() {
    const form = document.getElementById('checkoutForm');
    const cartInput = document.getElementById('cartContentInput');
    if (!form || !cartInput) return;

    form.addEventListener('submit', (e) => {
        const panier = obtenirPanier();

        // On vérifie si le panier n'est pas vide
        if (panier.length === 0) {
            e.preventDefault();
            alert("Votre panier est vide !");
            return;
        }

        // On remplit le champ caché avec le JSON du panier pour le PHP
        cartInput.value = JSON.stringify(panier);
    });
}

// Affiche le résumé de la commande sur la page de confirmation
function afficherResumeCommande() {
    const conteneur = document.getElementById('orderItems');
    if (!conteneur) return;

    const panier = obtenirPanier();
    let total = 0;

    conteneur.innerHTML = panier.map(article => {
        total += article.price * article.quantity;
        return `
            <div class="ligne-resume">
                <span>${article.quantity}x ${article.name}</span>
                <span>${formaterPrix(article.price * article.quantity)}</span>
            </div>
        `;
    }).join('');

    if (document.getElementById('checkoutTotal')) {
        document.getElementById('checkoutTotal').textContent = formaterPrix(total);
    }
}

// Gère le défilement automatique des images
function initialiserCarrousel() {
    const images = document.querySelectorAll('.carousel-slide');
    if (images.length === 0) return; // Si pas de carrousel, on s'arrête

    let index = 0;
    setInterval(() => {
        // On enlève la classe 'active' de l'image actuelle
        images[index].classList.remove('active');
        // On passe à l'image suivante (on revient à 0 après la dernière)
        index = (index + 1) % images.length;
        // On ajoute la classe 'active' à la nouvelle image
        images[index].classList.add('active');
    }, 5000); // Change toutes les 5 secondes
}

// Gère l'ouverture/fermeture du menu mobile
function initialiserMenuMobile() {
    const toggle = document.querySelector('.mobile-toggle');
    const nav = document.querySelector('.nav');

    if (toggle && nav) {
        toggle.addEventListener('click', () => {
            nav.classList.toggle('mobile-active');
        });

        // Fermer le menu si on clique sur un lien (utile pour les ancres comme #contact)
        const links = nav.querySelectorAll('ul li a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                nav.classList.remove('mobile-active');
            });
        });
    }
}

// 7. DÉMARRAGE (AU CHARGEMENT DE LA PAGE)

document.addEventListener('DOMContentLoaded', () => {
    // Ces fonctions se lancent sur toutes les pages
    mettreAJourBadgePanier();
    initialiserMenuMobile();

    // Fonctions spécifiques selon la page
    initialiserBoutonsAchat();
    configurerFiltresCategories();
    mettreAJourInterfacePanier();
    mettreAJourInterfaceFavoris();
    initialiserFormulaireCommande();
    afficherResumeCommande();
    initialiserCarrousel();

    // Fermer le modal si on clique sur la petite croix ou à côté
    const btnFermer = document.querySelector('.fermer-modal');
    if (btnFermer) btnFermer.addEventListener('click', fermerModal);

    window.addEventListener('click', (e) => {
        const modal = document.getElementById('productModal');
        if (e.target === modal) fermerModal();
    });
});
