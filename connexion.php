<?php 
    require_once 'includes/initialisation.php';

    // Si déjà connecté, redirection vers l'accueil
    if(isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }

    $error = "";
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = clean_input($_POST['username']);
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['username'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    }

    $title = "Connexion";
    include 'head.php'; 
    include 'header.php'; 
?>

<main style="padding: 120px 20px; background: #fdfdfd; min-height: 80vh; display: flex; align-items: center; justify-content: center;">
    <div style="width: 100%; max-width: 400px; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 15px 40px rgba(0,0,0,0.05); border: 1px solid #f0f0f0;">
        <h1 style="font-family: 'Outfit', sans-serif; text-align: center; margin-bottom: 30px; color: #1a1a1a;">CONNEXION</h1>
        
        <?php if($error): ?>
            <div style="background: #ffebee; color: #c62828; padding: 12px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; text-align: center;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #666;">Utilisateur</label>
                <input type="text" name="username" required style="width: 100%; padding: 12px; border: 1px solid #eee; border-radius: 10px; box-sizing: border-box; outline: none;">
            </div>
            <div style="margin-bottom: 30px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #666;">Mot de passe</label>
                <input type="password" name="password" required style="width: 100%; padding: 12px; border: 1px solid #eee; border-radius: 10px; box-sizing: border-box; outline: none;">
            </div>
            <button type="submit" style="width: 100%; padding: 14px; background: #ff6b00; border: none; color: white; border-radius: 50px; font-weight: 700; cursor: pointer; transition: 0.3s;">SE CONNECTER</button>
        </form>

        <p style="text-align: center; margin-top: 25px; font-size: 14px; color: #999;">
            Pas encore de compte ? <a href="inscription.php" style="color: #ff6b00; font-weight: 600; text-decoration: none;">Inscrivez-vous</a>
        </p>
    </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
