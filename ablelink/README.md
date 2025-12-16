# AbleLink - Plateforme d'Emploi Inclusive

## À Propos du Projet

**AbleLink** est une plateforme web inclusive conçue pour aider les personnes handicapées à trouver du travail dans des entreprises engagées envers l'accessibilité et l'égalité. Le projet permet aux utilisateurs de partager des **histoires de succès** et de laisser des **commentaires** avec des likes.

## Fonctionnalités

### 1. **Success Stories (Histoires de Succès)**
- ✅ Créer une nouvelle histoire de succès
- ✅ Lire toutes les histoires
- ✅ Modifier une histoire existante
- ✅ Supprimer une histoire
- ✅ Aimer une histoire (système de likes)
- ✅ Filtrer par catégorie

### 2. **Commentaires**
- ✅ Ajouter des commentaires sur les histoires
- ✅ Voir tous les commentaires d'une histoire
- ✅ Supprimer un commentaire
- ✅ Liker un commentaire
- ✅ Afficher l'auteur et la date

## Structure du Projet

```
ablelink/
├── index.html                 # Page d'accueil
├── success-stories.html       # Page des histoires de succès
├── api/
│   ├── success_stories.php    # API REST pour les histoires
│   └── comments.php           # API REST pour les commentaires
├── db/
│   ├── database.php           # Configuration de la base de données
│   └── setup.sql              # Script de création des tables
├── js/
│   └── app.js                 # Logique JavaScript (CRUD)
├── css/
│   └── (fichiers CSS)
└── img/
    └── (images du projet)
```

## Installation

### Étape 1 : Créer la base de données

1. Ouvrez **phpMyAdmin** (http://localhost/phpmyadmin)
2. Créez une nouvelle base de données nommée `ablelink_db`
3. Exécutez le script SQL situé dans `db/setup.sql`

Ou exécutez directement cette commande SQL :

```sql
-- Créer la base de données
CREATE DATABASE IF NOT EXISTS ablelink_db;
USE ablelink_db;

-- Table pour les Success Stories
CREATE TABLE IF NOT EXISTS success_stories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255),
    likes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table pour les Commentaires
CREATE TABLE IF NOT EXISTS comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    story_id INT NOT NULL,
    author VARCHAR(255) NOT NULL,
    comment_text TEXT NOT NULL,
    likes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (story_id) REFERENCES success_stories(id) ON DELETE CASCADE
);
```

### Étape 2 : Configuration

1. Assurez-vous que **XAMPP** est lancé (Apache + MySQL)
2. Vérifiez que les identifiants de la base de données dans `db/database.php` correspondent à votre configuration :
   - Host: `localhost`
   - Database: `ablelink_db`
   - User: `root`
   - Password: `` (vide par défaut)

### Étape 3 : Accéder à l'application

- Accueil : `http://localhost/projetweb/ablelink/index.html`
- Success Stories : `http://localhost/projetweb/ablelink/success-stories.html`

## Utilisation

### Ajouter une Success Story

1. Cliquez sur "Partager votre histoire"
2. Remplissez le formulaire avec :
   - **Titre** : Le titre de votre histoire
   - **Votre Nom** : Votre prénom et nom
   - **Catégorie** : Sélectionnez une catégorie
   - **Votre Histoire** : Racontez votre expérience
   - **URL de l'image** (optionnel) : Un lien vers une image
3. Cliquez sur "Publier"

### Commenter une Story

1. Sur une histoire, cliquez sur "Commentaires"
2. Entrez votre nom et votre commentaire
3. Cliquez sur "Publier le Commentaire"

### Modifier/Supprimer

- Cliquez sur "Modifier" pour éditer une histoire
- Cliquez sur "Supprimer" pour la supprimer définitivement

### Liker

- Cliquez sur l'icône ❤️ pour aimer une histoire ou un commentaire

## API REST

### Success Stories

#### GET - Récupérer toutes les histoires
```
GET /api/success_stories.php
```

#### GET - Récupérer une histoire spécifique
```
GET /api/success_stories.php/{id}
```

#### POST - Créer une nouvelle histoire
```
POST /api/success_stories.php
Content-Type: application/json

{
  "title": "Mon Histoire",
  "author": "Nom de l'Auteur",
  "category": "Career Growth",
  "description": "Ma longue description...",
  "image": "http://example.com/image.jpg"
}
```

#### PUT - Modifier une histoire
```
PUT /api/success_stories.php/{id}
Content-Type: application/json

{
  "title": "Titre Modifié",
  "author": "Auteur",
  "category": "Technology",
  "description": "Nouvelle description...",
  "image": "http://example.com/new-image.jpg"
}
```

#### DELETE - Supprimer une histoire
```
DELETE /api/success_stories.php/{id}
```

#### POST - Aimer une histoire
```
POST /api/success_stories.php/{id}/like
```

### Commentaires

#### GET - Récupérer les commentaires d'une histoire
```
GET /api/comments.php?story_id={story_id}
```

#### POST - Créer un commentaire
```
POST /api/comments.php
Content-Type: application/json

{
  "story_id": 1,
  "author": "Nom du Commentateur",
  "comment_text": "Mon commentaire..."
}
```

#### DELETE - Supprimer un commentaire
```
DELETE /api/comments.php/{id}
```

#### POST - Aimer un commentaire
```
POST /api/comments.php/{id}/like
```

## Technologies Utilisées

- **Frontend** : HTML5, CSS3, JavaScript (Vanilla)
- **Backend** : PHP 7.4+
- **Base de Données** : MySQL
- **Framework CSS** : Bootstrap 5
- **Icons** : Font Awesome 6

## Dépendances

- XAMPP (Apache + PHP + MySQL)
- Navigateur moderne (Chrome, Firefox, Safari, Edge)

## Fichiers Clés

- `index.html` - Page d'accueil
- `success-stories.html` - Page principale des histoires avec CRUD
- `api/success_stories.php` - API pour les histoires (GET, POST, PUT, DELETE)
- `api/comments.php` - API pour les commentaires (GET, POST, DELETE)
- `db/database.php` - Connexion à la base de données
- `js/app.js` - Logique JavaScript complète (CRUD + UI)

## Fonctionnalités Futures

- 🔐 Authentification utilisateur
- 🔍 Système de recherche avancée
- ⭐ Notation des entreprises
- 📱 Application mobile
- 🌍 Multilingue
- 📧 Notifications par email
- 📊 Tableau de bord d'administration

## Troubleshooting

### La base de données ne se connecte pas
- Vérifiez que MySQL est lancé dans XAMPP
- Vérifiez les identifiants dans `db/database.php`
- Vérifiez que la base de données `ablelink_db` existe

### Les stories ne s'affichent pas
- Vérifiez la console (F12) pour les erreurs
- Vérifiez que l'API retourne du JSON valide
- Assurez-vous que Apache est lancé

### Les fichiers CSS/JS ne se chargent pas
- Vérifiez les chemins relatifs dans les fichiers HTML
- Vérifiez la structure des dossiers

## Support

Pour toute question ou problème, veuillez contacter l'équipe AbleLink.

## Licence

Ce projet est sous licence MIT. Voir le fichier LICENSE pour plus de détails.

---

**Créé avec ❤️ par l'équipe AbleLink**
