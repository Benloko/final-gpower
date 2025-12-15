# Gpower - Instructions d'Installation et Utilisation

## Installation

### 1. Importer la Base de Données
Ouvrez phpMyAdmin ou un client MySQL et importez le fichier `database.sql`:
```sql
mysql -u root -p
CREATE DATABASE gpower_db;
USE gpower_db;
SOURCE database.sql;
```

### 2. Configurer la Connexion à la Base de Données
Ouvrez `/config/database.php` et modifiez les informations de connexion:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');
define('DB_NAME', 'gpower_db');
```

### 3. Configurer l'URL de Base
Dans `/config/config.php`, changez l'URL:
```php
define('BASE_URL', 'http://localhost/gpower');
// ou
define('BASE_URL', 'http://votre-domaine.com');
```

### 4. Donner les Permissions aux Dossiers
```bash
chmod -R 755 /chemin/vers/gpower
chmod -R 777 /chemin/vers/gpower/uploads
```

### 5. Accéder au Panneau d'Administration
- URL: `http://localhost/gpower/admin/`
- Nom d'utilisateur: `admin`
- Mot de passe: `admin123`

**IMPORTANT**: Changez le mot de passe par défaut immédiatement!

## Utilisation

### Gestion des Catégories
1. Connectez-vous au panneau admin
2. Cliquez sur "Categories"
3. Cliquez sur "Add New Category"
4. Remplissez le nom, la description
5. Téléchargez une image
6. Sauvegardez

### Gestion des Produits
1. Cliquez sur "Products" → "Add New Product"
2. Remplissez:
   - Nom du produit
   - Catégorie
   - Prix
   - Description
   - Spécifications
3. Téléchargez l'image principale
4. Ajoutez plusieurs images dans la galerie
5. Cochez "Featured" pour afficher sur la page d'accueil
6. Sauvegardez

### Configuration WhatsApp
1. Allez dans "Settings"
2. Entrez votre numéro WhatsApp (sans + ni espaces)
3. Exemple: 1234567890
4. Les boutons WhatsApp utiliseront ce numéro

### Changement de Langue
Le site supporte plusieurs langues:
- Anglais (EN)
- Français (FR)

L'utilisateur peut changer la langue via le sélecteur en haut du site.

**Note**: L'admin travaille en anglais, seul le frontend est multilingue.

## Fonctionnalités Principales

✅ Design responsive (mobile, tablette, desktop)
✅ Gestion complète des produits
✅ Gestion des catégories
✅ Galerie d'images pour chaque produit
✅ Intégration WhatsApp sur chaque produit
✅ Système multilingue
✅ Panneau d'administration sécurisé
✅ Recherche et filtrage des produits
✅ Produits vedettes
✅ Pages About et Contact

## Structure des Fichiers

- `/admin/` - Panneau d'administration
- `/assets/` - CSS, JS, images
- `/config/` - Configuration
- `/includes/` - Header, footer partagés
- `/lang/` - Fichiers de langue
- `/uploads/` - Images téléchargées
- `index.php` - Page d'accueil
- `products.php` - Liste des produits
- `product-details.php` - Détails d'un produit

## Sécurité

⚠️ Changez le mot de passe admin par défaut
⚠️ Utilisez des mots de passe forts
⚠️ Activez HTTPS en production
⚠️ Sauvegardez régulièrement la base de données

## Support

Pour toute question, contactez l'administrateur du site.
