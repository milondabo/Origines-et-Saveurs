<?php 
    require_once 'includes/initialisation.php';
    $title = "Accueil";
    $page = "index";
    include 'head.php'; 
    include 'header.php'; 
?>

<main>
    <!-- SECTION HERO : La première chose que l'utilisateur voit -->
    <section class="hero">
        <div class="hero-content">
            <h1>Origines & Saveurs</h1>
            <p>Quand la gourmandise rencontre l'élégance.</p>
            <div class="hero-buttons">
                <a href="menu.php" class="cta-button primary">Explorer le menu</a>
                <a href="demande-devis.php" class="cta-button secondary">Demander un devis</a>
            </div>
        </div>
        <!-- IMAGES FLOTTANTES : Pour un effet visuel dynamique -->
        <div class="hero-images">
            <img src="poulet.png" alt="Poulet" class="floating-img floating-img-1">
            <img src="burger.png" alt="Burger" class="floating-img floating-img-3">
            <img src="frites.png" alt="Frites" class="floating-img floating-img-4">
            <img src="gateau.png" alt="Gâteau" class="floating-img floating-img-5">
            <img src="sambossa.png" alt="Sambossa" class="floating-img floating-img-6">
            <img src="eclatchocolat.png" alt="Chocolat" class="floating-img floating-img-7">
        </div>
    </section>

    <!-- SECTION PLATS VEDETTES : Les plats phares de l'accueil -->
    <section class="featured-dishes">
        <div class="container">
            <div class="section-header">
                <span class="subtitle">Sélection du chef</span>
                <h2>NOS INCONTOURNABLES</h2>
            </div>
            <div class="dishes-grid">
                <?php
                // Récupération des plats vedettes (is_featured = 1)
                $stmt = $pdo->prepare("SELECT * FROM products WHERE is_featured = 1 LIMIT 3");
                $stmt->execute();
                $featured_products = $stmt->fetchAll();

                // Si aucun plat n'est marqué comme vedette, on prend les 3 derniers ajoutés
                if (empty($featured_products)) {
                    $featured_products = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 3")->fetchAll();
                }

                foreach ($featured_products as $product): 
                ?>
                <div class="dish-card">
                    <div class="dish-image">
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    </div>
                    <div class="dish-info">
                        <h3><?php echo $product['name']; ?></h3>
                        <p><?php echo $product['description']; ?></p>
                        <span class="price"><?php echo number_format($product['price'], 0, ',', ' '); ?> FCFA</span>
                        <div class="groupe-boutons">
                            <button class="bouton-carte add-to-cart">
                                <img src="Icones/panier.svg" alt="Panier" class="icone-bouton">
                                Ajouter au panier
                            </button>
                            <div class="selecteur-quantite" style="display: none;">
                                <button class="bouton-moins">-</button>
                                <span class="quantite-valeur">1</span>
                                <button class="bouton-plus">+</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="view-all">
                <a href="menu.php" class="link-button">Voir tout le menu →</a>
            </div>
        </div>
    </section>

    <!-- SECTION ENGAGEMENTS : Nos valeurs -->
    <section class="features">
        <div class="container">
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">🌿</div>
                    <h3>Produits Frais</h3>
                    <p>Des ingrédients sourcés localement chaque matin.</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">👨‍🍳</div>
                    <h3>Savoir-faire</h3>
                    <p>Une cuisine authentique alliée à l'élégance moderne.</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🚀</div>
                    <h3>Service Rapide</h3>
                    <p>Livraison soignée et ponctuelle à votre porte.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
