<?php require_once 'includes/initialisation.php'; ?>
<header>
    <nav class="nav">
        <!-- Logo du restaurant -->
        <div class="logo">
            <img src="logoos.png" alt="logo de Origines & Saveurs">
        </div>

        <!-- Bouton Menu Mobile -->
        <button class="mobile-toggle" aria-label="Menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>

        <!-- Liens de navigation -->
        <ul>
            <li><a href="index.php" class="<?php echo ($page == 'index') ? 'active' : ''; ?>">Accueil</a></li>
            <li><a href="menu.php" class="<?php echo ($page == 'menu') ? 'active' : ''; ?>">Nos plats</a></li>
            <li><a href="aboutus.php" class="<?php echo ($page == 'about') ? 'active' : ''; ?>">A propos de nous</a></li>
            <li><a href="#footer-contact">Contact</a></li>
            <!-- Badge panier pour voir le nombre d'articles ajoutés -->
            <li><a href="panier.php">Panier <span class="badge-panier">0</span></a></li>
            <?php if(isset($_SESSION['user_name'])): ?>
                <li><a href="mes-commandes.php">Mes commandes</a></li>
                <li style="margin-left: 20px; font-size: 0.9em;">Bienvenue, <strong><?php echo $_SESSION['user_name']; ?></strong> | <a href="deconnexion.php" style="display:inline; padding:0; background:none; color:#ff6b00;">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="connexion.php">Connexion</a></li>
            <?php endif; ?>
        </ul>
        <!-- Bouton d'action rapide vers la commande -->
        <a href="commande.php" class="nav-cta">Commandez ici</a>
    </nav>
</header>
