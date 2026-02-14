<?php
require_once '../includes/initialisation.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: connexion.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM quotes ORDER BY created_at DESC");
$quotes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demandes de Devis - OS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-orange: #ff6b00; --sidebar-bg: #111111; --main-bg: #fdfdfd; }
        body { font-family: 'Inter', sans-serif; margin: 0; display: flex; background: var(--main-bg); }
        
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

        .main-content { margin-left: 260px; padding: 50px; width: calc(100% - 260px); }
        h1 { font-family: 'Outfit', sans-serif; font-size: 2rem; margin-bottom: 30px; }

        .quotes-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 25px; }
        .quote-card { background: white; border-radius: 20px; padding: 30px; border: 1px solid #f0f0f0; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        
        .quote-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f9f9f9; padding-bottom: 15px; }
        .quote-header h3 { margin: 0; font-family: 'Outfit', sans-serif; color: var(--primary-orange); }
        .quote-date { font-size: 0.8rem; color: #999; }

        .quote-details { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .detail-item label { display: block; font-size: 0.75rem; color: #999; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
        .detail-item span { font-weight: 600; font-size: 0.95rem; }

        .quote-requirements { background: #fdfdfd; padding: 15px; border-radius: 12px; border-left: 3px solid #eee; }
        .quote-requirements label { font-size: 0.75rem; color: #999; display: block; margin-bottom: 8px; }
        .quote-requirements p { margin: 0; font-size: 0.9rem; line-height: 1.5; color: #555; }

        .empty-state { text-align: center; padding: 100px; color: #999; grid-column: 1 / -1; }
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
        <a href="gestion-commandes.php">Commandes</a>
        <a href="gestion-devis.php" class="active">Devis</a>
        <a href="../index.php" target="_blank">Voir le site</a>
    </div>
    <a href="deconnexion.php" class="logout-btn">Déconnexion</a>
</div>

<div class="main-content">
    <h1>Demandes de Devis</h1>

    <div class="quotes-grid">
        <?php if(empty($quotes)): ?>
            <div class="empty-state">
                <div style="font-size: 3rem;">📄</div>
                <p>Aucune demande de devis pour le moment.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($quotes as $q): ?>
            <div class="quote-card">
                <div class="quote-header">
                    <h3><?php echo $q['event_type']; ?></h3>
                    <span class="quote-date"><?php echo date('d/m/Y', strtotime($q['created_at'])); ?></span>
                </div>

                <div class="quote-details">
                    <div class="detail-item">
                        <label>Client</label>
                        <span><?php echo $q['name']; ?></span>
                    </div>
                    <div class="detail-item">
                        <label>Date de l'événement</label>
                        <span><?php echo date('d/m/Y', strtotime($q['event_date'])); ?></span>
                    </div>
                    <div class="detail-item">
                        <label>Contact</label>
                        <span><?php echo $q['phone']; ?></span>
                    </div>
                    <div class="detail-item">
                        <label>Invités</label>
                        <span><?php echo $q['guests']; ?> personnes</span>
                    </div>
                    <div class="detail-item" style="grid-column: span 2;">
                        <label>Email</label>
                        <span><?php echo $q['email']; ?></span>
                    </div>
                </div>

                <div class="quote-requirements">
                    <label>Exigences particulières</label>
                    <p><?php echo $q['requirements'] ?: "Aucune exigence particulière spécifiée."; ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
