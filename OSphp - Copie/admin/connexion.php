<?php
require_once '../includes/initialisation.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];

    // Création d'un admin par défaut si la table est vide
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    if ($stmt->fetchColumn() == 0) {
        $hashed = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)")
            ->execute(['admin', 'admin@os.ci', $hashed, 'admin']);
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = 'admin'");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id'] = $user['id'];
        header("Location: tableau-de-bord.php");
        exit();
    } else {
        $error = "Identifiants incorrects.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin - OS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Outfit:wght@700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f5f5f5; display: flex; height: 100vh; align-items: center; justify-content: center; margin: 0; }
        .login-card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); width: 100%; max-width: 360px; }
        h1 { font-family: 'Outfit', sans-serif; text-align: center; margin-bottom: 30px; font-size: 1.5rem; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666; }
        input { width: 100%; padding: 12px; border: 1px solid #eee; border-radius: 10px; box-sizing: border-box; outline: none; }
        button { width: 100%; padding: 14px; background: #ff6b00; color: white; border: none; border-radius: 50px; font-weight: 600; cursor: pointer; margin-top: 10px; }
        .error { background: #ffebee; color: #c62828; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 0.85rem; text-align: center; }
    </style>
</head>
<body>

<div class="login-card">
    <h1>ADMINISTRATION</h1>
    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="POST">
        <div class="form-group">
            <label>Utilisateur</label>
            <input type="text" name="username" required>
        </div>
        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">SE CONNECTER</button>
    </form>
</div>

</body>
</html>
