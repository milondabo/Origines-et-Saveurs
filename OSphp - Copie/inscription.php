<?php 
    require_once 'includes/initialisation.php';

    $error = "";
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = clean_input($_POST['username']);
        $email = clean_input($_POST['email']);
        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];

        if($password !== $confirm) {
            $error = "Les mots de passe ne correspondent pas.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            try {
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $hashed]);
                header("Location: connexion.php");
                exit();
            } catch(PDOException $e) {
                $error = "Cet utilisateur ou cet email existe déjà.";
            }
        }
    }

    $title = "Inscription";
    include 'head.php'; 
    include 'header.php'; 
?>

<main style="padding: 120px 20px; background: #fdfdfd; min-height: 80vh; display: flex; align-items: center; justify-content: center;">
    <div style="width: 100%; max-width: 450px; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 15px 40px rgba(0,0,0,0.05); border: 1px solid #f0f0f0;">
        <h1 style="font-family: 'Outfit', sans-serif; text-align: center; margin-bottom: 30px; color: #1a1a1a;">INSCRIPTION</h1>
        
        <?php if($error): ?>
            <div style="background: #ffebee; color: #c62828; padding: 12px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; text-align: center;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #666;">Nom d'utilisateur</label>
                <input type="text" name="username" required style="width: 100%; padding: 12px; border: 1px solid #eee; border-radius: 10px; box-sizing: border-box; outline: none;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #666;">Email</label>
                <input type="email" name="email" required style="width: 100%; padding: 12px; border: 1px solid #eee; border-radius: 10px; box-sizing: border-box; outline: none;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #666;">Mot de passe</label>
                <input type="password" name="password" required style="width: 100%; padding: 12px; border: 1px solid #eee; border-radius: 10px; box-sizing: border-box; outline: none;">
            </div>
            <div style="margin-bottom: 30px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #666;">Confirmer le mot de passe</label>
                <input type="password" name="confirm_password" required style="width: 100%; padding: 12px; border: 1px solid #eee; border-radius: 10px; box-sizing: border-box; outline: none;">
            </div>
            <button type="submit" style="width: 100%; padding: 14px; background: #ff6b00; border: none; color: white; border-radius: 50px; font-weight: 700; cursor: pointer; transition: 0.3s;">CRÉER MON COMPTE</button>
        </form>

        <p style="text-align: center; margin-top: 25px; font-size: 14px; color: #999;">
            Vous avez déjà un compte ? <a href="connexion.php" style="color: #ff6b00; font-weight: 600; text-decoration: none;">Connectez-vous</a>
        </p>
    </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
