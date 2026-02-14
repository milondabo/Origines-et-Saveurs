<?php
require_once 'includes/initialisation.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean_input($_POST['name']);
    $phone = clean_input($_POST['phone']);
    $email = clean_input($_POST['email']);
    $date = clean_input($_POST['event-date']);
    $guests = intval($_POST['guests']);
    $type = clean_input($_POST['event-type']);
    $requests = clean_input($_POST['special-requests']);

    try {
        $stmt = $pdo->prepare("INSERT INTO quotes (name, phone, email, event_date, guests, event_type, requirements) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $phone, $email, $date, $guests, $type, $requests]);

        header("Location: succes-devis.php");
        exit();
    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
}
?>
