<?php
require_once 'includes/initialisation.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage des données
    $name = clean_input($_POST['name']);
    $phone = clean_input($_POST['phone']);
    $address = clean_input($_POST['address']);
    $payment = clean_input($_POST['payment']);
    $cart_data = $_POST['cart_json']; // JSON envoyé depuis le JS

    $cart = json_decode($cart_data, true);

    if (empty($cart)) {
        die("Erreur : Le panier est vide.");
    }

    try {
        $pdo->beginTransaction();

        // 1. Calcul du total côté serveur (pour la sécurité)
        $total = 0;
        foreach ($cart as $item) {
            // On récupère le prix réel en base pour éviter les fraudes JS
            $stmt = $pdo->prepare("SELECT price FROM products WHERE name = ?");
            $stmt->execute([$item['name']]);
            $product = $stmt->fetch();
            
            if ($product) {
                $total += $product['price'] * $item['quantity'];
            }
        }

        // 2. Insertion de la commande
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, customer_name, customer_phone, customer_address, payment_method, total_amount) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $name, $phone, $address, $payment, $total]);
        $order_id = $pdo->lastInsertId();

        // 3. Insertion des articles de la commande
        $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
        
        foreach ($cart as $item) {
            // On récupère l'ID du produit
            $stmt_p = $pdo->prepare("SELECT id, price FROM products WHERE name = ?");
            $stmt_p->execute([$item['name']]);
            $p = $stmt_p->fetch();
            
            if ($p) {
                $stmt_item->execute([$order_id, $p['id'], $item['quantity'], $p['price']]);
            }
        }

        $pdo->commit();

        // Redirection vers la facture avec l'ID de commande
        header("Location: facture.php?id=" . $order_id);
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erreur lors de l'enregistrement : " . $e->getMessage());
    }
}
?>
