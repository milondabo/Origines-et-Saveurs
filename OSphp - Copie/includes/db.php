<?php
// Configuration de la base de données
$host = 'xxx.infinityfree.com';
$dbname = 'if0_xxxx_os_db';
$username = 'if0_xxxxxx';
$password = 'Origineetsaveur'; // Par défaut sur XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Configuration pour afficher les erreurs SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Mode de récupération par défaut : Tableau associatif
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Fonction utilitaire pour sécuriser les données de formulaire
function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
?>
