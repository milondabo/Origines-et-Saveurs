<img width="959" height="476" alt="Acceuil1" src="https://github.com/user-attachments/assets/a409ff35-f367-4d35-bdb1-a06314e93261" /># Origine & Saveur - Application de Gestion de Restaurant
 Une solution élégante pour allier gastronomie et gestion numérique.

# Le problème résolu
Beaucoup de petits restaurants gèrent encore leurs commandes et leurs devis manuellement, ce qui entraîne des erreurs et une perte de temps. **Origine & Saveur** centralise la présentation des plats, la prise de commande et la gestion administrative (devis, facturation) dans une interface intuitive, permettant au restaurateur de se concentrer sur sa cuisine.

# Technologies utilisées
* **Frontend :** HTML5, CSS3 (Flexbox/Grid), JavaScript (ES6+)
* **Backend :** PHP 8.x
* **Base de données :** MySQL
* **Déploiement :** InfinityFree (Hébergement FTP)

# Installation et Lancement
Pour lancer ce projet localement :

1. **Cloner le projet :**
   ```bash
   git clone https://github.com/Milondabo/Origines-et-Sauveurs.git

2. **Consultation**
Le projet est consultable en direct ici :
[originesetsaveurs.infinityfreeapp.com](http://originesetsaveurs.infinityfreeapp.com)

3. **Configuration de la Base de Données :**
   * Créez une base de données MySQL localement (via XAMPP/WAMP).
   * Importez le fichier `votre_fichier.sql` (disponible dans le dossier ) pour recréer la structure et les données.
   * Modifiez le fichier `includes/db.php` avec vos accès locaux :
     ```php
     $host = 'localhost';
     $dbname = 'votre_nom_bdd';
     $user = 'root';
     $pass = '';
     ```
     //en général avec XAMPP

4. **Lancement :**
   * Placez le dossier du projet dans votre répertoire `htdocs` ou `www`.
   * Accédez au site via `http://localhost/Origines-et-Sauveurs`.
  
  **NB :**
  Les images utilisées dans le projets sont celles à la racines, vous pourez reffaire les routes ou les enregistrez dqns le dossier `OSphp-Copie`
