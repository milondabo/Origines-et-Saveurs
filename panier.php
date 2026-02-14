<?php 
    require_once 'includes/initialisation.php';
    $title = "Votre Panier";
    $page = "panier";
    include 'head.php'; 
    include 'header.php'; 
?>

<main class="cart-page">
    <!-- SECTION PANIER -->
    <section class="cart-hero">
        <div class="cart-container">
            <h1>VOTRE PANIER</h1>

            <div class="cart-content">
                <!-- LISTE DES ARTICLES -->
                <div class="cart-items" id="cartItems">
                </div>

                <!-- RÉSUMÉ DU PANIER -->
                <div class="cart-summary">
                    <h2>RÉSUMÉ</h2>
                    <div class="summary-row">
                        <span>Sous-total</span>
                        <span id="subtotal">0 FCFA</span>
                    </div>
                    <div class="summary-row">
                        <span>Livraison</span>
                        <span>Gratuit</span>
                    </div>
                    <div class="summary-divider"></div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span id="total">0 FCFA</span>
                    </div>
                    <a href="commande.php" class="checkout-button"
                        style="text-align: center; text-decoration: none; display: block;">
                        PASSER LA COMMANDE
                    </a>
                    <a href="menu.php" class="continue-shopping">Continuer mes achats</a>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION FAVORIS -->
    <section class="favorites-section" style="margin-top: 80px;">
        <div class="cart-container">
            <h1 style="margin-bottom: 40px;">FAVORIS</h1>
            <div class="grille-produits" id="favoritesItems">
                <!-- Rempli par script.js -->
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
