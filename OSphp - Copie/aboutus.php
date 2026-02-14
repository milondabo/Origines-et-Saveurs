<?php 
    require_once 'includes/initialisation.php';
    $title = "À Propos";
    $page = "about";
    include 'head.php'; 
    include 'header.php'; 
?>

<main class="about-page">
    <!-- SECTION HERO : Présentation du restaurant -->
    <section class="about-hero">
        <!-- Côté gauche : Titre et décorations -->
        <div class="about-hero-left">
            <h1>À PROPOS DE NOUS</h1>
            <!-- Décorations d'ingrédients qui flottent (voir CSS pour les animations) -->
            <div class="floating-ingredients">
                <img src="piment_pate.png" alt="Ingrédient" class="ingredient ingredient-1">
                <img src="attiekealloco.png" alt="Ingrédient" class="ingredient ingredient-2">
                <img src="placali.png" alt="Ingrédient" class="ingredient ingredient-3">
                <img src="saucefeuille.png" alt="Ingrédient" class="ingredient ingredient-4">
                <img src="carteafriqueplat.png" alt="Ingrédient" class="ingredient ingredient-5">
                <img src="taro.png" alt="Ingrédient" class="ingredient ingredient-6">
            </div>
        </div>

        <!-- Côté droit : Cartes d'informations textuelles -->
        <div class="about-hero-right">
            <div class="info-card-grid">
                <div class="info-card card-orange">
                    <h2>NOTRE HISTOIRE</h2>
                    <p>Origines & Saveurs est né d'une passion pour la gastronomie africaine et internationale.
                        Notre objectif est de faire découvrir des saveurs authentiques avec une touche de modernité.
                    </p>
                </div>
                <div class="info-card card-image-only">
                    <img src="Porc.jpg" alt="Notre cuisine" class="card-image-full">
                </div>
            </div>

            <div class="info-card card-black">
                <h2>NOS HORAIRES</h2>
                <div class="hours">
                    <p><strong>Lundi – Jeudi:</strong> 07:00 – 23:00</p>
                    <p><strong>Vendredi – Dimanche:</strong> 07:00 – Minuit</p>
                </div>
            </div>

            <div class="info-card-grid">
                <div class="info-card philosophy-card card-orange">
                    <h2>PHILOSOPHIE</h2>
                    <p>Nous utilisons uniquement des produits frais et locaux pour garantir une qualité
                        irréprochable à chaque bouchée.</p>
                </div>
                <div class="info-card card-image-only">
                    <img src="Poisson_braisé.jpg" alt="Poissons frais" class="card-image-full">
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
