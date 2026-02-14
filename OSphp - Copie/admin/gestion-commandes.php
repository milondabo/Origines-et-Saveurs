<?php
require_once '../includes/initialisation.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: connexion.php");
    exit();
}

// Mise à jour du statut de la commande
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $order_id]);
    
    // Redirection pour éviter de renvoyer le formulaire au rafraîchissement
    header("Location: gestion-commandes.php" . (isset($_GET['status']) ? "?status=".$_GET['status'] : ""));
    exit();
}

$status_filter = isset($_GET['status']) ? clean_input($_GET['status']) : 'all';
$query = "SELECT * FROM orders";
$params = [];

if ($status_filter !== 'all') {
    $query .= " WHERE status = ?";
    $params[] = $status_filter;
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$status_map = [
    'pending' => ['label' => 'En attente', 'class' => 'status-pending'],
    'confirmed' => ['label' => 'Confirmée', 'class' => 'status-confirmed'],
    'delivered' => ['label' => 'Livrée', 'class' => 'status-delivered'],
    'cancelled' => ['label' => 'Annulée', 'class' => 'status-cancelled'],
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Commandes - OS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-orange: #ff6b00;
            --sidebar-bg: #111111;
            --main-bg: #fdfdfd;
        }

        body { font-family: 'Inter', sans-serif; margin: 0; display: flex; background: var(--main-bg); }
        
        /* Sidebar */
        .sidebar { width: 260px; background: var(--sidebar-bg); color: white; height: 100vh; position: fixed; display: flex; flex-direction: column; z-index: 100; }
        .sidebar-header { padding: 30px 20px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid #222; }
        .sidebar-logo { height: 45px; width: 45px; object-fit: cover; border-radius: 12px; }
        .sidebar-header h2 { font-family: 'Outfit', sans-serif; font-size: 1.1rem; letter-spacing: 2px; margin: 0; color: #fff; font-weight: 700; }
        .sidebar-header h2 span { color: var(--primary-orange); font-size: 0.7rem; display: block; letter-spacing: 4px; font-weight: 400; margin-top: -4px; }
        .sidebar-menu { flex-grow: 1; padding: 20px 0; }
        .sidebar a { color: #999; text-decoration: none; display: flex; align-items: center; padding: 14px 25px; font-size: 0.88rem; transition: 0.3s; border-left: 4px solid transparent; }
        .sidebar a:hover { color: #fff; background: rgba(255, 107, 0, 0.05); }
        .sidebar a.active { background: rgba(255, 107, 0, 0.08); color: var(--primary-orange); border-left-color: var(--primary-orange); font-weight: 600; }
        .logout-btn { margin-top: auto; margin-bottom: 20px; color: #ff4d4d !important; border-top: 1px solid #222; padding-top: 20px !important; }

        /* Main */
        .main-content { margin-left: 260px; padding: 50px; width: calc(100% - 260px); }
        h1 { font-family: 'Outfit', sans-serif; font-size: 2rem; margin-bottom: 30px; }

        .filters { margin-bottom: 30px; display: flex; gap: 10px; }
        .filter-btn { padding: 8px 20px; border-radius: 30px; text-decoration: none; color: #666; background: #eee; font-size: 0.85rem; font-weight: 600; }
        .filter-btn.active { background: var(--primary-orange); color: white; }

        .order-card { background: white; border-radius: 20px; padding: 25px; margin-bottom: 20px; border: 1px solid #f0f0f0; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; align-items: start; }
        .order-info h3 { margin: 0 0 10px; font-size: 1.1rem; font-family: 'Outfit', sans-serif; }
        .order-info p { margin: 5px 0; color: #666; font-size: 0.9rem; }
        
        .status-badge { display: inline-block; padding: 6px 15px; border-radius: 30px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 15px; }
        .status-pending { background: #fff8e1; color: #f57c00; }
        .status-confirmed { background: #e3f2fd; color: #1976d2; }
        .status-delivered { background: #e8f5e9; color: #2e7d32; }
        .status-cancelled { background: #ffebee; color: #c62828; }

        .timeline { display: flex; gap: 5px; margin-bottom: 20px; }
        .step { flex: 1; height: 4px; background: #eee; border-radius: 2px; }
        .step.filled { background: var(--primary-orange); }

        .actions select { padding: 8px; border-radius: 8px; border: 1px solid #eee; font-family: inherit; margin-right: 10px; outline: none; }
        .actions button { padding: 8px 15px; background: #1a1a1a; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }
        .btn-facture { display: inline-block; margin-top: 15px; color: var(--primary-orange); text-decoration: none; font-weight: 600; font-size: 0.9rem; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="../logoos.png" class="sidebar-logo">
        <h2>ADMIN <span>ORIGINES & SAVEURS</span></h2>
    </div>
    <div class="sidebar-menu">
        <a href="tableau-de-bord.php">Tableau de bord</a>
        <a href="gestion-plats.php">Gestion des Plats</a>
        <a href="gestion-commandes.php" class="active">Commandes</a>
        <a href="gestion-devis.php">Devis</a>
        <a href="../index.php" target="_blank">Voir le site</a>
    </div>
    <a href="deconnexion.php" class="logout-btn">Déconnexion</a>
</div>

<div class="main-content">
    <h1>Gestion des Commandes</h1>

    <div class="filters">
        <a href="?status=all" class="filter-btn <?php echo $status_filter == 'all' ? 'active' : ''; ?>">Toutes</a>
        <a href="?status=pending" class="filter-btn <?php echo $status_filter == 'pending' ? 'active' : ''; ?>">En attente</a>
        <a href="?status=confirmed" class="filter-btn <?php echo $status_filter == 'confirmed' ? 'active' : ''; ?>">Confirmées</a>
        <a href="?status=delivered" class="filter-btn <?php echo $status_filter == 'delivered' ? 'active' : ''; ?>">Livrées</a>
        <a href="?status=cancelled" class="filter-btn <?php echo $status_filter == 'cancelled' ? 'active' : ''; ?>">Annulées</a>
    </div>

    <?php foreach ($orders as $o): ?>
        <?php 
        $s_info = $status_map[$o['status']]; 
        $steps = ['pending', 'confirmed', 'delivered'];
        $current_index = array_search($o['status'], $steps);
        $is_cancelled = ($o['status'] === 'cancelled');
        ?>
        <div class="order-card">
            <div class="order-info">
                <h3>Commande #<?php echo str_pad($o['id'], 5, '0', STR_PAD_LEFT); ?></h3>
                <p><strong>Client:</strong> <?php echo $o['customer_name']; ?></p>
                <p><strong>Tel:</strong> <?php echo $o['customer_phone']; ?></p>
                <p><strong>Date:</strong> <?php echo date('d/m/Y H:i', strtotime($o['created_at'])); ?></p>
                <p><strong>Total:</strong> <span style="color: var(--primary-orange); font-weight: 800;"><?php echo number_format($o['total_amount'], 0, ',', ' '); ?> FCFA</span></p>
                <a href="../facture.php?id=<?php echo $o['id']; ?>" target="_blank" class="btn-facture">Voir la facture →</a>
            </div>

            <div class="order-status">
                <span class="status-badge <?php echo $s_info['class']; ?>"><?php echo $s_info['label']; ?></span>
                
                <?php if (!$is_cancelled): ?>
                    <div class="timeline">
                        <div class="step filled"></div>
                        <div class="step <?php echo $current_index >= 1 ? 'filled' : ''; ?>"></div>
                        <div class="step <?php echo $current_index >= 2 ? 'filled' : ''; ?>"></div>
                    </div>
                <?php endif; ?>
                
                <p style="font-size: 0.8rem; color: #999;"><strong>Adresse:</strong> <?php echo $o['customer_address']; ?></p>
                <p style="font-size: 0.8rem; color: #999;"><strong>Paiement:</strong> <?php echo $o['payment_method']; ?></p>
            </div>

            <div class="actions">
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <?php if (!$is_cancelled && $current_index < 2): ?>
                        <?php $next_status = $steps[$current_index + 1]; ?>
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                            <input type="hidden" name="new_status" value="<?php echo $next_status; ?>">
                            <button type="submit" name="update_status" style="background: var(--primary-orange); width: 100%; margin-bottom: 10px; padding: 8px 15px; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                                Passer à "<?php echo $status_map[$next_status]['label']; ?>"
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if (!$is_cancelled && $o['status'] !== 'delivered'): ?>
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                            <input type="hidden" name="new_status" value="cancelled">
                            <button type="submit" name="update_status" style="background: #eee; color: #666; width: 100%; padding: 8px 15px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                                Annuler
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
