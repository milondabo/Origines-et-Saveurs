<?php 
    require_once 'includes/initialisation.php';
    
    // Si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
    if (!isset($_SESSION['user_id'])) {
        header("Location: connexion.php");
        exit();
    }

    $title = "Mes Commandes";
    include 'head.php'; 
    include 'header.php'; 

    $user_id = $_SESSION['user_id'];
    
    // Récupération des commandes de l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll();

    $status_map = [
        'pending' => ['label' => 'En attente', 'class' => 'status-pending'],
        'confirmed' => ['label' => 'Confirmée', 'class' => 'status-confirmed'],
        'delivered' => ['label' => 'Livrée', 'class' => 'status-delivered'],
        'cancelled' => ['label' => 'Annulée', 'class' => 'status-cancelled'],
    ];
?>

<main>
    <section class="page-header" style="background: linear-gradient(135deg, #1a1a1a 0%, #333 100%); padding: 60px 0; color: white; text-align: center;">
        <div class="container">
            <h1 style="font-family: 'Outfit', sans-serif; font-size: 2.5rem; margin-bottom: 10px;">MES COMMANDES</h1>
            <p style="color: #ff6b00; font-weight: 500;">Suivez l'état de vos gourmandises en temps réel.</p>
        </div>
    </section>

    <section class="orders-section" style="padding: 80px 0; background: #fdfdfd; min-height: 60vh;">
        <div class="container" style="max-width: 1000px; margin: 0 auto;">
            
            <?php if (empty($orders)): ?>
                <div style="text-align: center; padding: 40px; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
                    <div style="font-size: 4rem; margin-bottom: 20px;">🥡</div>
                    <h3>Vous n'avez pas encore passé de commande.</h3>
                    <p style="color: #666; margin-bottom: 30px;">C'est le moment de découvrir nos délices !</p>
                    <a href="menu.php" class="cta-button primary">Découvrir le menu</a>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 25px;">
                    <?php foreach ($orders as $o): ?>
                        <?php $s = $status_map[$o['status']]; ?>
                        <div style="background: white; padding: 30px; border-radius: 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.03); border: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                            <div>
                                <span style="color: #999; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Commande #<?php echo str_pad($o['id'], 5, '0', STR_PAD_LEFT); ?></span>
                                <h3 style="margin: 5px 0; font-family: 'Outfit', sans-serif;">Passée le <?php echo date('d/m/Y', strtotime($o['created_at'])); ?></h3>
                                <p style="color: #ff6b00; font-weight: 700; margin-top: 5px; font-size: 1.1rem;"><?php echo number_format($o['total_amount'], 0, ',', ' '); ?> FCFA</p>
                            </div>

                            <div style="text-align: center;">
                                <div style="margin-bottom: 10px;">
                                    <span class="status-badge <?php echo $s['class']; ?>" style="padding: 8px 20px; border-radius: 30px; font-size: 0.85rem; font-weight: 700;">
                                        <?php echo $s['label']; ?>
                                    </span>
                                </div>
                                <a href="facture.php?id=<?php echo $o['id']; ?>" style="color: #666; font-size: 0.85rem; text-decoration: none; border-bottom: 1px solid #ccc;">Voir la facture</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </section>
</main>

<style>
    .status-pending { background: #fff8e1; color: #f57c00; }
    .status-confirmed { background: #e3f2fd; color: #1976d2; }
    .status-delivered { background: #e8f5e9; color: #2e7d32; }
    .status-cancelled { background: #ffebee; color: #c62828; }
</style>

<?php include 'footer.php'; ?>
</body>
</html>
