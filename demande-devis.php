<?php 
    require_once 'includes/initialisation.php';
    $title = "Demande de Devis";
    $page = "devis";
    include 'head.php'; 
    include 'header.php'; 
?>

<main class="devis-page">
    <section class="devis-hero">
        <!-- COLONNE GAUCHE : Visuels (Carrousel) -->
        <div class="devis-hero-left">
            <div class="hero-carousel">
                <div class="carousel-slide active" style="background-image: url('caroussel1.jpg')"></div>
                <div class="carousel-slide" style="background-image: url('caroussel2.jpg')"></div>
                <div class="carousel-slide" style="background-image: url('caroussel3.jpg')"></div>
                <div class="carousel-slide" style="background-image: url('caroussel4.jpg')"></div>
                <div class="carousel-slide" style="background-image: url('caroussel5.jpg')"></div>
            </div>
            <h1>VOTRE ÉVÉNEMENT,<br>NOS SAVEURS.</h1>
        </div>

        <!-- COLONNE DROITE : Formulaire -->
        <div class="devis-hero-right">
            <div class="devis-form-container">
                <h2>DEMANDE DE DEVIS</h2>
                <p class="form-subtitle">Faites de votre événement un moment inoubliable avec nos saveurs authentiques.</p>
                
                <form action="traitement-devis.php" method="POST" class="devis-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Nom complet</label>
                            <input type="text" id="name" name="name" class="form-input" placeholder="Votre nom" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Téléphone</label>
                            <input type="tel" id="phone" name="phone" class="form-input" placeholder="Votre numéro" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email professionnel</label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="votre@email.com" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="event-date">Date de l'événement</label>
                            <input type="date" id="event-date" name="event-date" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="guests">Nombre d'invités</label>
                            <input type="number" id="guests" name="guests" class="form-input" placeholder="Ex: 50" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="event-type">Type d'événement</label>
                        <select id="event-type" name="event-type" class="form-select" required>
                            <option value="">Sélectionnez le type...</option>
                            <option value="Mariage">Mariage</option>
                            <option value="Anniversaire">Anniversaire</option>
                            <option value="Entreprise">Événement d'entreprise</option>
                            <option value="Cocktail">Cocktail Dinatoire</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="special-requests">Besoins spécifiques</label>
                        <textarea id="special-requests" name="special-requests" class="form-textarea" rows="4" placeholder="Description de l'événement, allergies, menu spécifique..."></textarea>
                    </div>

                    <button type="submit" class="submit-button">Envoyer ma demande</button>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
