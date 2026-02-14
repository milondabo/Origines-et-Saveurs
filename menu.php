<?php 
    require_once 'includes/initialisation.php';
    $title = "Nos Plats";
    $page = "menu";
    include 'head.php'; 
    include 'header.php'; 
?>

<main class="page-menu-layout">
    <!-- BARRE LATÉRALE : Gestion des catégories de plats -->
    <aside class="barre-laterale">
        <h3>Catégories</h3>
        <ul class="categories">
            <li data-category="Tout" class="actif">
                <div><strong>TOUT VOIR</strong>
                    <div class="sub">Toutes nos saveurs</div>
                </div>
            </li>
            <?php
            // Récupération des catégories depuis la base de données
            $stmt = $pdo->query("SELECT * FROM categories");
            $categories = $stmt->fetchAll();
            
            // On définit des descriptions et images par défaut pour les catégories
            $cat_info = [
                'Africain' => ['sub' => "L'âme de notre terroir", 'img' => 'images_complementaires/cuisine_afri.jpg'],
                'Européen' => ['sub' => "L'élégance du vieux continent", 'img' => 'images_complementaires/cuisine_euro.jpg'],
                'Boisson' => ['sub' => "Vins et nectars d'exception", 'img' => 'images_complementaires/Boisson.jpg'],
                'Déssert' => ['sub' => "La douceur finale", 'img' => 'images_complementaires/Dessert.jpg'],
            ];

            foreach ($categories as $cat) {
                $name = $cat['name'];
                $sub = $cat_info[$name]['sub'] ?? "Découvrez nos choix";
                $img = $cat_info[$name]['img'] ?? "images_complementaires/default_cat.jpg";
                echo "
                <li data-category=\"$name\">
                    <img src=\"$img\" alt=\"$name\" class=\"thumb\">
                    <div><strong>".strtoupper($name)."</strong>
                        <div class=\"sub\">$sub</div>
                    </div>
                </li>";
            }
            ?>
        </ul>
    </aside>

    <!-- CONTENU PRINCIPAL : La grille des produits -->
    <section class="contenu">
        <div class="titre-recherche">
            <h2>Découvrez nos saveurs</h2>
        </div>

        <div class="grille-produits">
            <?php
            // Récupération des produits avec le nom de leur catégorie
            $query = "SELECT p.*, c.name as category_name 
                      FROM products p 
                      LEFT JOIN categories c ON p.category_id = c.id";
            $stmt = $pdo->query($query);
            $products = $stmt->fetchAll();

            foreach ($products as $product) {
                ?>
                <article class="carte" data-category="<?php echo $product['category_name']; ?>">
                    <div class="enveloppe-image-carte">
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="image">
                        <span class="etiquette-carte"><?php echo $product['category_name']; ?></span>
                        <div class="note-carte">
                            <span class="etoiles">★</span>
                            <span class="note">5.0</span>
                        </div>
                    </div>
                    <div class="corps-carte">
                        <div class="haut-corps">
                            <h3 class="titre-produit"><?php echo $product['name']; ?></h3>
                            <div class="prix"><?php echo number_format($product['price'], 0, ',', ' '); ?> FCFA</div>
                            <p class="description"><?php echo $product['description']; ?></p>
                        </div>
                        <div class="avis-caches" style="display:none">
                            <div class="element-avis">
                                <div class="en-tete-avis"><span>Client Satisfait</span><span>★★★★★</span></div>
                                <div class="texte-avis">"Un délice authentique, je recommande vivement !"</div>
                            </div>
                        </div>
                        <div class="groupe-boutons">
                            <button class="bouton-carte">
                                <img src="Icones/panier.svg" alt="Icone Panier" class="icone-bouton">
                                Ajouter au panier
                            </button>
                            <div class="selecteur-quantite" style="display: none;">
                                <button class="bouton-moins">-</button>
                                <span class="quantite-valeur">1</span>
                                <button class="bouton-plus">+</button>
                            </div>
                            <button class="bouton-favoris" title="Ajouter aux favoris">
                                <img src="Icones/heart.svg" alt="Icone Coeur" class="icone-bouton">
                            </button>
                        </div>
                    </div>
                </article>
                <?php
            }
            ?>

        </div>
    </section>
</main>

<!-- MODAL : La fenêtre qui s'affiche au clic sur un plat -->
<div id="productModal" class="modal">
    <div class="contenu-modal">
        <button class="fermer-modal">&times;</button>
        <div class="grid-modal">
            <div class="col-gauche-modal">
                <img src="" alt="" class="image-modal">
            </div>
            <div class="col-droite-modal">
                <h2 class="titre-modal"></h2>
                <div class="prix-modal"></div>
                <div class="evaluation-modal">
                    <span class="etoiles-modal">★</span>
                    <span class="note-modal"></span>
                </div>
                <p class="description-modal"></p>

                <div class="section-avis">
                    <h3>Avis clients</h3>
                    <div id="liste-avis-modal">
                        <!-- Rempli par le script -->
                    </div>
                </div>

                <div class="actions-modal" style="margin-top: 20px;">
                    <button class="bouton-ajout-modal">
                        <img src="Icones/panier.svg" alt="" class="icone-bouton">
                        Ajouter au panier
                    </button>
                </div>

                <p style="margin-top: 20px; font-size: 14px; color: #666;">
                    * Tous nos plats sont préparés avec des produits frais du jour.
                </p>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
