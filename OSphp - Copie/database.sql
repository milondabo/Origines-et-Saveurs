-- Création de la base de données
CREATE DATABASE IF NOT EXISTS os_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE os_db;

-- Table des catégories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

-- Table des produits
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price INT NOT NULL,
    image VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Table des utilisateurs (Admin et Clients)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des commandes
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_address TEXT NOT NULL,
    payment_method VARCHAR(50),
    total_amount INT NOT NULL,
    status ENUM('pending', 'confirmed', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Détails des commandes
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price_at_purchase INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Table des devis
CREATE TABLE quotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    event_date DATE,
    guests INT,
    event_type VARCHAR(50),
    requirements TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertion des catégories initiales
INSERT INTO categories (name) VALUES ('Africain'), ('Européen'), ('Boisson'), ('Déssert');

-- Insertion de quelques produits de test (basés sur vos fichiers actuels)
INSERT INTO products (category_id, name, description, price, image, is_featured) VALUES 
(1, 'Garba Premium', 'Garba premium, servi avec oeuf et supplément alloco.', 3500, 'le_menu/Garba_Premium.jpg', 1),
(1, 'Attiéké et Poisson braisé', 'Poisson braisé accompagné d\'attiéké et bâton de manioc.', 3500, 'Poisson_braisé.jpg', 0),
(2, 'Burger Gourmet', 'Steak Black Angus, cheddar affiné, sauce maison.', 3000, 'burger.png', 1),
(2, 'Spaghetti Bolognaise', 'Pâtes al dente, sauce tomate à la viande hachée.', 4000, 'le_menu/Spaghetti_Bolognaise.jpg', 0),
(4, 'Glace Artisanale', 'Glace onctueuse au chocolat ou vanille de Madagascar.', 1500, 'le_menu/Glace_Artisanale.jpg', 0),
(1, 'Sauce Feuille', 'Mélange de feuilles locales, viande tendre, servi avec riz local.', 3000, 'saucefeuille.png', 0),
(1, 'Placali Sauce Gombo', 'Pâte de manioc fermentée avec une délicieuse sauce gombo.', 3000, 'placali.png', 0),
(3, 'Bissap Maison', 'Boisson rafraîchissante à la fleur d\'hibiscus.', 2000, 'le_menu/Jus_de_Bissap_Maison.jpg', 0),
(1, 'Foutou Banane Sauce Graine', 'Le célèbre foutou banane ivoirien avec sa sauce graine onctueuse.', 3500, 'foutou.jpg', 0);
