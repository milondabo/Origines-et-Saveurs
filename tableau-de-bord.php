<?php
require_once '../includes/initialisation.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: connexion.php");
    exit();
}

// Statistiques rapides
$order_count = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$quote_count = $pdo->query("SELECT COUNT(*) FROM quotes")->fetchColumn();
$product_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status = 'delivered'")->fetchColumn() ?: 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord - OS Admin</title>
    <!-- On réutilise la police élégante du site -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-orange: #ff6b00;
            --sidebar-bg: #111111;
            --main-bg: #fdfdfd;
            --text-dark: #333;
            --text-light: #999;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            display: flex; 
            background: var(--main-bg); 
            color: var(--text-dark);
        }

        /* Sidebar Style */
        .sidebar { 
            width: 260px; 
            background: var(--sidebar-bg); 
            color: white; 
            height: 100vh; 
            position: fixed; 
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            z-index: 100;
        }

        .sidebar-header {
            padding: 30px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #222;
        }

        .sidebar-logo {
            height: 45px;
            width: 45px;
            object-fit: cover;
            border-radius: 12px;
        }

        .sidebar-header h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.1rem;
            letter-spacing: 2px;
            margin: 0;
            color: #fff;
            font-weight: 700;
        }

        .sidebar-header h2 span {
            color: var(--primary-orange);
            font-size: 0.7rem;
            display: block;
            letter-spacing: 4px;
            font-weight: 400;
            margin-top: -4px;
        }

        .sidebar-menu {
            flex-grow: 1;
            padding: 20px 0;
        }

        .sidebar a { 
            color: var(--text-light); 
            text-decoration: none; 
            display: flex;
            align-items: center;
            padding: 14px 25px; 
            font-size: 0.88rem; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid transparent;
            font-weight: 500;
        }

        .sidebar a:hover { 
            color: #fff;
            background: rgba(255, 107, 0, 0.05);
        }

        .sidebar a.active { 
            background: rgba(255, 107, 0, 0.08);
            color: var(--primary-orange); 
            border-left-color: var(--primary-orange);
            font-weight: 600;
        }

        .logout-btn {
            margin-top: auto;
            margin-bottom: 20px;
            color: #ff4d4d !important;
            border-top: 1px solid #222;
            padding-top: 20px !important;
        }

        /* Main Content */
        .main-content { 
            margin-left: 260px; 
            padding: 50px; 
            width: calc(100% - 260px); 
            min-height: 100vh;
        }

        h1 { 
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 40px;
            color: #1a1a1a;
        }

        .stats-grid { 
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px; 
            margin-bottom: 50px; 
        }

        .stat-card { 
            background: white; 
            padding: 25px; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); 
            transition: transform 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid #f0f0f0;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 { 
            margin: 0; 
            font-size: 0.85rem; 
            color: var(--text-light); 
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-card p { 
            margin: 15px 0 0; 
            font-size: 1.8rem; 
            font-weight: 700; 
            color: #1a1a1a;
            font-family: 'Outfit', sans-serif;
        }

        /* Recent Orders Table */
        .recent-section {
            background: white;
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.02);
            border: 1px solid #f0f0f0;
        }

        .recent-section h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.3rem;
            margin-bottom: 25px;
            color: #1a1a1a;
        }

        table { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 0 10px;
        }

        th { 
            text-align: left;
            padding: 15px 20px;
            color: var(--text-light);
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        td { 
            padding: 15px 20px;
            background: #fff;
            border-top: 1px solid #f9f9f9;
            border-bottom: 1px solid #f9f9f9;
            font-size: 0.92rem;
        }

        td:first-child { border-left: 1px solid #f9f9f9; border-radius: 12px 0 0 12px; font-weight: 600; color: var(--primary-orange); }
        td:last-child { border-right: 1px solid #f9f9f9; border-radius: 0 12px 12px 0; }

        tr:hover td {
            background: #fafafa;
        }

        .btn-view {
            color: var(--primary-orange);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            transition: opacity 0.2s;
        }

        .btn-view:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="../logoos.png" class="sidebar-logo">
        <h2>ADMIN <span>ORIGINES & SAVEURS</span></h2>
    </div>
    
    <div class="sidebar-menu">
        <a href="tableau-de-bord.php" class="active">Tableau de bord</a>
        <a href="gestion-plats.php">Gestion des Plats</a>
        <a href="gestion-commandes.php">Commandes</a>
        <a href="gestion-devis.php">Devis</a>
        <a href="../index.php" target="_blank">Voir le site</a>
    </div>

    <a href="deconnexion.php" class="logout-btn">Déconnexion</a>
</div>

<div class="main-content">
    <h1>Tableau de Bord</h1>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Commandes</h3>
            <p><?php echo $order_count; ?></p>
        </div>
        <div class="stat-card">
            <h3>Devis</h3>
            <p><?php echo $quote_count; ?></p>
        </div>
        <div class="stat-card">
            <h3>Au Menu</h3>
            <p><?php echo $product_count; ?></p>
        </div>
        <div class="stat-card">
            <h3>Revenus</h3>
            <p><?php echo number_format($total_revenue, 0, ',', ' '); ?> <span style="font-size: 0.9rem;">FCFA</span></p>
        </div>
    </div>

    <div class="recent-section">
        <h2>Dernières Commandes</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Montant</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
                while ($row = $stmt->fetch()) {
                    echo "<tr>
                        <td>#{$row['id']}</td>
                        <td>{$row['customer_name']}</td>
                        <td>".number_format($row['total_amount'], 0, ',', ' ')." FCFA</td>
                        <td>".date('d/m/Y', strtotime($row['created_at']))."</td>
                        <td><a href='../facture.php?id={$row['id']}' target='_blank' class='btn-view'>Facture</a></td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
