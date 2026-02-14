<?php
require_once '../includes/initialisation.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: connexion.php");
    exit();
}

// Traitement de l'ajout/modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_product'])) {
        $id = $_POST['id'];
        $name = clean_input($_POST['name']);
        $description = clean_input($_POST['description']);
        $price = intval($_POST['price']);
        $category_id = intval($_POST['category_id']);
        $image_path = $_POST['current_image'];

        // Gestion de l'upload d'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $filename = time() . '_' . $_FILES['image']['name'];
            $target = '../le_menu/' . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image_path = 'le_menu/' . $filename;
            }
        }

        if ($id) {
            $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, image = ? WHERE id = ?");
            $stmt->execute([$name, $description, $price, $category_id, $image_path, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category_id, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $category_id, $image_path]);
        }
    }

    if (isset($_POST['delete_id'])) {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$_POST['delete_id']]);
    }
}

$products = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Plats - OS Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-orange: #ff6b00; --sidebar-bg: #111111; --main-bg: #fdfdfd; }
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

        .main-content { margin-left: 260px; padding: 50px; width: calc(100% - 260px); }
        h1 { font-family: 'Outfit', sans-serif; font-size: 2rem; margin-bottom: 30px; }

        .layout-grid { display: grid; grid-template-columns: 1fr 350px; gap: 40px; }

        /* Form Card */
        .form-card { background: white; padding: 30px; border-radius: 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.02); border: 1px solid #f0f0f0; height: fit-content; position: sticky; top: 50px; }
        .form-card h2 { font-family: 'Outfit', sans-serif; font-size: 1.2rem; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 0.85rem; color: #666; margin-bottom: 5px; font-weight: 600; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #eee; border-radius: 10px; outline: none; font-family: inherit; box-sizing: border-box; }
        
        .btn-save { width: 100%; padding: 12px; background: var(--primary-orange); color: white; border: none; border-radius: 50px; font-weight: 600; cursor: pointer; margin-top: 10px; }
        .btn-reset { width: 100%; padding: 10px; background: #eee; color: #666; border: none; border-radius: 50px; font-weight: 600; cursor: pointer; margin-top: 10px; font-size: 0.8rem; }

        /* Product Table */
        .products-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .products-table th { text-align: left; padding: 15px; color: #999; font-size: 0.8rem; text-transform: uppercase; border-bottom: 1px solid #eee; }
        .products-table td { padding: 15px; border-bottom: 1px solid #f9f9f9; vertical-align: middle; }
        .prod-img { width: 50px; height: 50px; border-radius: 10px; object-fit: cover; }
        .prod-name { font-weight: 600; margin: 0; font-size: 0.95rem; }
        .prod-cat { font-size: 0.8rem; color: #999; }
        
        .action-btns { display: flex; gap: 10px; }
        .btn-edit { color: #4a90e2; text-decoration: none; font-size: 0.85rem; font-weight: 600; background: none; border: none; cursor: pointer; }
        .btn-delete { color: #ff4d4d; background: none; border: none; font-size: 0.85rem; font-weight: 600; cursor: pointer; }
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
        <a href="gestion-plats.php" class="active">Gestion des Plats</a>
        <a href="gestion-commandes.php">Commandes</a>
        <a href="gestion-devis.php">Devis</a>
        <a href="../index.php" target="_blank">Voir le site</a>
    </div>
    <a href="deconnexion.php" class="logout-btn">Déconnexion</a>
</div>

<div class="main-content">
    <h1>Gestion du Menu</h1>

    <div class="layout-grid">
        <div class="table-container">
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Plat</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td style="display: flex; align-items: center; gap: 15px;">
                                <img src="../<?php echo $p['image']; ?>" class="prod-img">
                                <div>
                                    <p class="prod-name"><?php echo $p['name']; ?></p>
                                </div>
                            </td>
                            <td><span class="prod-cat"><?php echo $p['category_name']; ?></span></td>
                            <td style="font-weight: 700;"><?php echo number_format($p['price'], 0, ',', ' '); ?> FCFA</td>
                            <td>
                                <div class="action-btns">
                                    <button onclick="editProduct(<?php echo htmlspecialchars(json_encode($p)); ?>)" class="btn-edit">Modifier</button>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce plat ?');">
                                        <input type="hidden" name="delete_id" value="<?php echo $p['id']; ?>">
                                        <button type="submit" class="btn-delete">Effacer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="form-container">
            <div class="form-card">
                <h2 id="form-title">Nouveau Plat</h2>
                <form method="POST" enctype="multipart/form-data" id="productForm">
                    <input type="hidden" name="id" id="prod-id">
                    <input type="hidden" name="current_image" id="prod-current-image">
                    
                    <div class="form-group">
                        <label>Nom du plat</label>
                        <input type="text" name="name" id="prod-name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Catégorie</label>
                        <select name="category_id" id="prod-cat" required>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Prix (FCFA)</label>
                        <input type="number" name="price" id="prod-price" required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" id="prod-desc" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Image (Laisser vide pour garder l'actuelle)</label>
                        <input type="file" name="image" accept="image/*">
                    </div>

                    <button type="submit" name="save_product" class="btn-save">Enregistrer</button>
                    <button type="button" class="btn-reset" onclick="resetForm()">Réinitialiser</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editProduct(p) {
    document.getElementById('form-title').innerText = "Modifier : " + p.name;
    document.getElementById('prod-id').value = p.id;
    document.getElementById('prod-name').value = p.name;
    document.getElementById('prod-price').value = p.price;
    document.getElementById('prod-desc').value = p.description;
    document.getElementById('prod-cat').value = p.category_id;
    document.getElementById('prod-current-image').value = p.image;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetForm() {
    document.getElementById('form-title').innerText = "Nouveau Plat";
    document.getElementById('productForm').reset();
    document.getElementById('prod-id').value = "";
}
</script>

</body>
</html>
