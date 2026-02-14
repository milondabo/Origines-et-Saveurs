<?php 
    require_once 'includes/initialisation.php';
    $title = "Commander";
    $page = "order";
    include 'head.php'; 
    include 'header.php'; 
?>

<main class="order-page">
    <section class="order-hero">
        <div class="order-container">
            <h1>FINALISER LA COMMANDE</h1>

            <div class="order-layout">
                <!-- COLONNE GAUCHE : Formulaire de livraison -->
                <div class="order-form-column">
                    <div class="form-container-card">
                        <!-- L'ID 'checkoutForm' est utilisé par le scripts.js pour gérer l'envoi -->
                        <form id="checkoutForm" class="checkout-form" method="POST" action="traitement-commande.php">
                            <!-- Champ caché pour envoyer le contenu du panier localStorage au PHP -->
                            <input type="hidden" name="cart_json" id="cartContentInput">
                            <h3 class="form-section-title">Informations Personnelles</h3>
                            <div class="form-group">
                                <label for="name">Nom complet</label>
                                <input type="text" id="name" name="name" class="form-input" placeholder="Votre nom"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Téléphone</label>
                                <input type="tel" id="phone" name="phone" class="form-input"
                                    placeholder="+225 07..." required>
                            </div>

                            <h3 class="form-section-title">Adresse de livraison</h3>
                            <div class="form-group">
                                <label for="address">Adresse complète</label>
                                <textarea id="address" name="address" class="form-textarea" rows="3"
                                    placeholder="Commune, quartier, repères..." required></textarea>
                            </div>

                            <h3 class="form-section-title">Moyen de paiement</h3>
                            <div class="payment-methods">
                                <label class="payment-option selected">
                                    <input type="radio" name="payment" value="cash" checked>
                                    <div class="payment-content">
                                        <span>Espèces à la livraison</span>
                                    </div>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="payment" value="mobile">
                                    <div class="payment-content">
                                        <span>Mobile Money</span>
                                    </div>
                                </label>
                            </div>

                            <button type="submit" class="submit-order-btn">Confirmer la commande</button>
                        </form>
                    </div>
                </div>

                <!-- COLONNE DROITE : Résumé des articles -->
                <div class="order-summary-column">
                    <div class="summary-card">
                        <h2>Résumé de la commande</h2>

                        <!-- L'ID 'orderItems' est rempli par scripts.js -->
                        <div id="orderItems" class="order-items-list">
                            <p>Chargement du panier...</p>
                        </div>

                        <div class="summary-divider"></div>

                        <div class="summary-row total">
                            <span>Total à payer</span>
                            <!-- L'ID 'checkoutTotal' est mis à jour par scripts.js -->
                            <span id="checkoutTotal">0 FCFA</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
