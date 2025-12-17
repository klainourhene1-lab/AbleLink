# 🚀 GUIDE DE LANCEMENT - AbleLink

## Étapes pour lancer le projet

### 1️⃣ Démarrer XAMPP

1. **Ouvrir le panneau de contrôle XAMPP**
   - Double-cliquer sur `C:\xampp\xampp-control-panel.exe`
   - Ou rechercher "XAMPP" dans le menu Windows

2. **Démarrer Apache**
   - Cliquer sur le bouton **"Start"** à côté d'Apache
   - Attendre que le statut devienne vert ✅

3. **Démarrer MySQL**
   - Cliquer sur le bouton **"Start"** à côté de MySQL
   - Attendre que le statut devienne vert ✅

### 2️⃣ Configurer la base de données

1. **Ouvrir phpMyAdmin**
   - Aller dans votre navigateur : `http://localhost/phpmyadmin`

2. **Créer la base de données**
   - Cliquer sur "Nouvelle base de données" (ou "New")
   - Nom : `ablelink_db`
   - Encodage : `utf8mb4_unicode_ci`
   - Cliquer sur "Créer"

3. **Importer les tables**
   - Sélectionner la base `ablelink_db`
   - Aller dans l'onglet **"Importer"** (ou "Import")
   - Cliquer sur **"Choisir un fichier"**
   - Sélectionner : `C:\xampp\htdocs\projetweb\ablelink\db\setup.sql`
   - Cliquer sur **"Exécuter"** (ou "Go")

4. **Ajouter des données d'exemple (optionnel)**
   - Toujours dans phpMyAdmin, onglet **"Importer"**
   - Sélectionner : `C:\xampp\htdocs\projetweb\ablelink\db\sample_data.sql`
   - Cliquer sur **"Exécuter"**

### 3️⃣ Accéder à l'application

Ouvrez votre navigateur et allez à :

**🏠 Page d'accueil :**
```
http://localhost/projetweb/ablelink/
```

**📋 Autres pages principales :**
- Services : `http://localhost/projetweb/ablelink/services`
- À propos : `http://localhost/projetweb/ablelink/about`
- Contact : `http://localhost/projetweb/ablelink/contact`
- Histoires : `http://localhost/projetweb/ablelink/success-stories`
- Événements : `http://localhost/projetweb/ablelink/events`
- Statistiques : `http://localhost/projetweb/ablelink/stats`
- Historique : `http://localhost/projetweb/ablelink/historique`

**🔐 Administration :**
- Login Admin : `http://localhost/projetweb/ablelink/admin-login`
  - **Username** : `admin`
  - **Password** : `admin123`
- Dashboard Admin : `http://localhost/projetweb/ablelink/admin`

**👤 Connexion utilisateur :**
- Login : `http://localhost/projetweb/ablelink/login`
  - **Email** : `malekjafrar@gmail.com`
  - **Password** : `malek2005`

---

## ✅ Vérification rapide

Si tout fonctionne correctement, vous devriez voir :
- ✅ La page d'accueil s'affiche sans erreur
- ✅ Le menu de navigation fonctionne
- ✅ Les pages se chargent correctement
- ✅ Aucune erreur dans la console du navigateur (F12)

---

## 🐛 Problèmes courants

### ❌ Erreur 404 sur les pages
**Solution :** 
- Vérifier que `mod_rewrite` est activé dans Apache
- Vérifier que le fichier `.htaccess` existe dans le dossier `ablelink`

### ❌ Erreur de connexion à la base de données
**Solution :**
- Vérifier que MySQL est démarré dans XAMPP
- Vérifier dans `db/config.php` que les identifiants sont corrects :
  - `DB_USER` : `root`
  - `DB_PASSWORD` : `` (vide)
  - `DB_NAME` : `ablelink_db`

### ❌ Les styles CSS ne s'affichent pas
**Solution :**
- Vérifier que Apache est démarré
- Vérifier les chemins des fichiers CSS dans les vues
- Ouvrir la console du navigateur (F12) pour voir les erreurs

### ❌ "Access Denied" ou erreur 403
**Solution :**
- Vérifier les permissions du dossier
- Vérifier la configuration d'Apache

---

## 📁 Structure du projet (MVC)

```
ablelink/
├── app/
│   ├── Controllers/        ← Contrôleurs (logique)
│   ├── Models/             ← Modèles (base de données)
│   ├── Views/              ← Vues (affichage)
│   │   └── general/        ← Pages générales (about, services, contact)
│   └── Core/               ← Router et classes de base
├── db/                     ← Base de données
├── css/                    ← Styles
├── js/                     ← JavaScript
├── img/                    ← Images
└── index.php              ← Point d'entrée (Router)
```

---

## 🎯 URLs avec le Router MVC

Toutes les URLs sont maintenant routées via `index.php` :
- ✅ `/services` → `ServicesController`
- ✅ `/about` → `AboutController`
- ✅ `/contact` → `ContactController`
- ✅ `/success-stories` → `SuccessStoriesController`
- ✅ `/admin` → `AdminController`

Plus besoin d'accéder directement aux fichiers `.php` !

---

**Bon développement ! 🚀**

