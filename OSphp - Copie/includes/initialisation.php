<?php
// Démarrage de la session pour la connexion utilisateur et le panier
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclusion de la connexion base de données (Fichier exception : db.php)
require_once dirname(__FILE__) . '/db.php';

// Variables globales
$site_name = "Origines & Saveurs";
?>
