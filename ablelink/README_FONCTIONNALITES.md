# AbleLink - Guide des Fonctionnalités

## 🎯 Objectif du Projet
AbleLink est une plateforme web pour aider les personnes handicapées à trouver du travail. Le site permet aux utilisateurs de partager des témoignages de succès (Success Stories) et d'interagir via des commentaires et likes.

## 📋 Fonctionnalités Implémentées

### 1. Success Stories (Témoignages)

#### Pour les Utilisateurs :
- ✅ **Créer une Success Story** : Les utilisateurs peuvent partager leur histoire de succès
- ✅ **Lire les Stories** : Voir toutes les stories approuvées sur le site
- ✅ **Liker une Story** : Système de likes pour montrer son appréciation
- ✅ **Commenter** : Laisser des commentaires sur les stories
- ✅ **Modifier/Supprimer** : Gérer ses propres stories

#### Pour l'Admin :
- ✅ **Modération** : Valider ou rejeter les stories soumises
- ✅ **Voir toutes les Stories** : Liste complète avec statuts (approuvée/en attente/rejetée)
- ✅ **Statistiques** : Dashboard avec graphiques et métriques

### 2. Commentaires

- ✅ **Ajouter un commentaire** : Sur n'importe quelle story
- ✅ **Liker un commentaire** : Système de likes pour les commentaires
- ✅ **Supprimer un commentaire** : Par l'auteur ou l'admin

### 3. Système de Modération

- ✅ **Stories en attente** : Affichées dans le dashboard admin
- ✅ **Approuver** : Bouton pour valider une story
- ✅ **Rejeter** : Bouton pour rejeter une story
- ✅ **Filtrage automatique** : Seules les stories approuvées sont visibles publiquement

## 🗄️ Structure de la Base de Données

### Table `success_stories`
- `id` : Identifiant unique
- `title` : Titre de la story
- `author` : Auteur de la story
- `content` : Contenu de la story
- `likes` : Nombre de likes
- `status` : Statut (pending, approved, rejected)
- `created_at` : Date de création

### Table `comments`
- `id` : Identifiant unique
- `story_id` : ID de la story associée
- `author` : Auteur du commentaire
- `content` : Contenu du commentaire
- `likes` : Nombre de likes
- `created_at` : Date de création

## 🚀 Installation

### 1. Créer la Base de Données
Accédez à : `http://localhost/projetweb/ablelink/db/check_database.php`

Ce script va :
- Créer la base de données `ablelink_db` si elle n'existe pas
- Créer les tables nécessaires
- Ajouter la colonne `status` pour la modération

### 2. Accéder au Site
- **Accueil** : `http://localhost/projetweb/ablelink/`
- **Success Stories** : `http://localhost/projetweb/ablelink/success-stories`
- **Dashboard Admin** : `http://localhost/projetweb/ablelink/admin`

## 📝 Workflow de Modération

1. **Utilisateur partage une story** → Statut : `pending`
2. **Admin voit la story dans le dashboard** → Section "Stories en Attente"
3. **Admin peut** :
   - ✅ **Approuver** → La story devient visible publiquement
   - ❌ **Rejeter** → La story est masquée
   - 👁️ **Voir** → Consulter les détails et commentaires
4. **Stories approuvées** → Visibles sur le site public
5. **Stories rejetées** → Masquées du site public

## 🎨 Interface

- **Design moderne** : Fond sombre avec dégradé bleu-violet
- **Logo coloré** : AbleLink avec lettres animées
- **Responsive** : Adapté mobile et desktop
- **Dashboard Admin** : Interface complète avec graphiques

## ✅ Checklist de Fonctionnalités

- [x] CRUD Success Stories
- [x] CRUD Commentaires
- [x] Système de likes (stories et commentaires)
- [x] Modération admin (approve/reject)
- [x] Dashboard admin avec statistiques
- [x] Filtrage des stories par statut
- [x] Interface moderne et responsive
- [x] Base de données configurée

## 🔧 Prochaines Étapes Suggérées

1. Système d'authentification utilisateur
2. Profils utilisateurs
3. Notifications pour les stories approuvées/rejetées
4. Recherche avancée
5. Catégorisation des stories
6. Upload d'images pour les stories



