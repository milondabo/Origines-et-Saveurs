<?php
require_once 'includes/initialisation.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$order_id = intval($_GET['id']);

// Récupération de la commande
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    die("Commande introuvable.");
}

// Récupération des articles de la commande
$stmt_items = $pdo->prepare("SELECT oi.*, p.name 
                             FROM order_items oi 
                             JOIN products p ON oi.product_id = p.id 
                             WHERE oi.order_id = ?");
$stmt_items->execute([$order_id]);
$items = $stmt_items->fetchAll();

$status_map = [
    'pending' => ['label' => 'En attente', 'class' => 'status-pending'],
    'confirmed' => ['label' => 'Confirmée', 'class' => 'status-confirmed'],
    'delivered' => ['label' => 'Livrée', 'class' => 'status-delivered'],
    'cancelled' => ['label' => 'Annulée', 'class' => 'status-cancelled'],
];

$title = "Facture #" . $order_id;
include 'head.php';
// On inclut le header mais on le cachera à l'impression via CSS
include 'header.php';
?>

<style>
    /* Styles spécifiques pour l'impression */
    @media print {
        header, footer, .nav, .boutons-action-facture {
            display: none !important;
        }
        body {
            background: white !important;
        }
        .invoice-page {
            padding: 0 !important;
            margin: 0 !important;
        }
        .invoice-container {
            box-shadow: none !important;
            border: none !important;
            width: 100% !important;
            max-width: 100% !important;
            padding: 20px !important;
        }
    }
</style>

<main class="invoice-page" style="padding: 120px 20px; background: #fdfdfd; min-height: 100vh;">
    <div class="invoice-container" style="max-width: 850px; margin: auto; background: white; padding: 50px; border-radius: 20px; box-shadow: 0 15px 50px rgba(0,0,0,0.05); border: 1px solid #f0f0f0;">
        
        <div class="invoice-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f9f9f9; padding-bottom: 30px; margin-bottom: 30px;">
            <div style="display: flex; align-items: center; gap: 20px;">
                <img src="logoos.png" alt="Logo" style="height: 60px;">
                <h2 style="font-family: 'Outfit', sans-serif; color: #1a1a1a; margin: 0; font-size: 1.2rem; letter-spacing: 2px; text-transform: uppercase;">ORIGINES & SAVEURS</h2>
            </div>
            <div style="text-align: right;">
                <h1 style="font-family: 'Outfit', sans-serif; color: #ff6b00; margin: 0; font-size: 1.8rem;">FACTURE</h1>
                <p style="color: #999; margin: 3px 0; letter-spacing: 1px; font-weight: 600; font-size: 0.8rem;">COMMANDE #<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></p>
                <p style="margin: 0; color: #1a1a1a; font-weight: 600; font-size: 0.9rem;"><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></p>
            </div>
        </div>

        <div class="invoice-details" style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px;">
            <div class="client-info">
                <h3 style="font-size: 0.75rem; color: #999; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 12px; border-left: 3px solid #ff6b00; padding-left: 10px;">Détails Client</h3>
                <p style="margin: 0; font-weight: 700; font-size: 1rem; color: #1a1a1a;"><?php echo $order['customer_name']; ?></p>
                <p style="margin: 4px 0; color: #666; font-size: 0.9rem;"><?php echo $order['customer_phone']; ?></p>
                <p style="margin: 4px 0; color: #666; font-size: 0.9rem; line-height: 1.4;"><?php echo $order['customer_address']; ?></p>
            </div>
            <div class="payment-info" style="text-align: right;">
                <h3 style="font-size: 0.75rem; color: #999; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 12px; border-right: 3px solid #ff6b00; padding-right: 10px;">Paiement & Statut</h3>
                <p style="margin: 0; font-weight: 700; color: #1a1a1a; font-size: 0.95rem;">
                    <?php echo ($order['payment_method'] == 'cash') ? 'Espèces à la livraison' : 'Mobile Money'; ?>
                </p>
                <div style="margin-top: 8px;">
                    <span style="display: inline-block; padding: 5px 12px; background: #fff8f4; color: #ff6b00; border-radius: 30px; font-weight: 700; font-size: 0.8rem; border: 1px solid #ffe8d9;">
                        <?php echo $status_map[$order['status']]['label']; ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="invoice-table" style="margin-bottom: 30px;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #f0f0f0;">
                        <th style="text-align: left; padding: 12px 0; color: #999; font-size: 0.75rem; text-transform: uppercase;">Désignation</th>
                        <th style="text-align: center; padding: 12px 0; color: #999; font-size: 0.75rem; text-transform: uppercase;">Prix Unit.</th>
                        <th style="text-align: center; padding: 12px 0; color: #999; font-size: 0.75rem; text-transform: uppercase;">Qté</th>
                        <th style="text-align: right; padding: 12px 0; color: #999; font-size: 0.75rem; text-transform: uppercase;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr style="border-bottom: 1px solid #f9f9f9;">
                        <td style="padding: 15px 0; font-weight: 600; color: #1a1a1a; font-size: 0.95rem;"><?php echo $item['name']; ?></td>
                        <td style="padding: 15px 0; text-align: center; color: #666; font-size: 0.9rem;"><?php echo number_format($item['price_at_purchase'], 0, ',', ' '); ?> FCFA</td>
                        <td style="padding: 15px 0; text-align: center; color: #1a1a1a; font-weight: 600; font-size: 0.9rem;"><?php echo $item['quantity']; ?></td>
                        <td style="padding: 15px 0; text-align: right; font-weight: 700; color: #1a1a1a; font-size: 0.95rem;"><?php echo number_format($item['price_at_purchase'] * $item['quantity'], 0, ',', ' '); ?> FCFA</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="invoice-footer-total" style="display: flex; justify-content: flex-end; padding-top: 15px; border-top: 2px solid #1a1a1a;">
            <div style="text-align: right;">
                <p style="margin: 0; color: #666; font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Somme Totale</p>
                <p style="margin: 3px 0 0; font-family: 'Outfit', sans-serif; font-size: 2.2rem; font-weight: 800; color: #ff6b00;">
                    <?php echo number_format($order['total_amount'], 0, ',', ' '); ?> <span style="font-size: 1rem;">FCFA</span>
                </p>
            </div>
        </div>

        <div style="margin-top: 50px; padding-top: 30px; border-top: 1px dashed #ddd; display: flex; justify-content: space-between; align-items: flex-end;">
            <div style="color: #666; font-size: 0.8rem; line-height: 1.6;">
                <p style="margin: 0; font-weight: 700; color: #1a1a1a;">Origines & Saveurs</p>
                <p style="margin: 0;">Contact : +225 07 47 03 62 10 / 01 01 10 11 11</p>
                <p style="margin: 0;">Adresse : Cocody, Angré 7e Tranche, Abidjan</p>
                <p style="margin: 2px 0; color: #ff6b00; font-weight: 600;">www.origines&saveurs.ci</p>
            </div>
            <div style="text-align: right;">
                <div class="boutons-action-facture" style="display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 10px;">
                    <button onclick="window.print()" style="padding: 8px 20px; background: #1a1a1a; color: white; border: none; border-radius: 50px; font-weight: 700; cursor: pointer; font-size: 0.8rem;">Imprimer</button>
                    <a href="menu.php" style="padding: 8px 20px; background: #f5f5f5; color: #666; border-radius: 50px; text-decoration: none; font-weight: 700; font-size: 0.8rem;">Menu</a>
                </div>
                <p style="margin: 0; color: #999; font-size: 0.7rem; font-style: italic;">Merci de votre confiance !</p>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>

