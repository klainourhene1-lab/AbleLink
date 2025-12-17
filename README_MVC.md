# 🚀 AbleLink - Plateforme d'Emploi Inclusive

> Une plateforme web moderne connectant les chercheurs d'emploi en situation de handicap aux entreprises inclusives.

---

## 📋 Table des Matières

1. [Description](#description)
2. [Installation de PHP](#installation-de-php)
3. [Installation du Projet](#installation-du-projet)
4. [Utilisation](#utilisation)
5. [Architecture MVC](#architecture-mvc)
6. [Contribution](#contribution)
7. [License](#license)

---

## 📖 Description

**AbleLink** est une application web qui facilite :

- **Chercheurs d'emploi** : Recherche d'offres d'emploi, création de profil, création d'histoires de succès
- **Entreprises** : Publication d'offres d'emploi, gestion des candidatures, promotion de l'inclusion
- **Administrateurs** : Modération du contenu, gestion des utilisateurs, suivi des événements
- **Réseau social** : Amis, posts, commentaires, notifications en temps réel

### Caractéristiques Principales

✅ **Authentification sécurisée** - Inscription, connexion, mot de passe oublié  
✅ **Profils utilisateurs** - Photos, statistiques, amis  
✅ **Offres d'emploi** - Publication et recherche  
✅ **Histoires de succès** - Témoignages du succès d'inclusivité  
✅ **Événements** - Gestion et évaluation d'événements  
✅ **Système de notation** - Like, commentaires, engagement  
✅ **Admin Dashboard** - Modération et statistiques  
✅ **Responsive Design** - Mobile, tablette, desktop  

---

## 🛠️ Installation de PHP

### Prérequis Système

- **Windows 10/11** ou **macOS** ou **Linux**
- **Navigateur web** moderne (Chrome, Firefox, Safari, Edge)
- **Éditeur de code** (VS Code recommandé)

### Option 1 : XAMPP (Recommandé - Windows/Mac/Linux)

1. **Télécharger XAMPP**
   ```
   https://www.apachefriends.org/
   ```

2. **Installer XAMPP**
   - Exécuter l'installateur
   - Sélectionner Apache, MySQL, PHP
   - Choisir le dossier d'installation (ex: `C:\xampp`)

3. **Démarrer les services**
   - Ouvrir XAMPP Control Panel
   - Cliquer sur "Start" pour Apache et MySQL

4. **Vérifier l'installation**
   ```
   http://localhost/
   ```

### Option 2 : PHP Natif

1. **Télécharger PHP** (https://www.php.net/downloads)
2. **Extraire vers** `C:\php` ou `/usr/local/php`
3. **Configurer le PATH** système
4. **Vérifier l'installation**
   ```bash
   php -v
   ```

### MySQL Installation

1. **Télécharger MySQL Server** (https://dev.mysql.com/downloads/mysql/)
2. **Installer et configurer**
3. **Vérifier la connexion**
   ```bash
   mysql -u root -p
   ```

---

## 📦 Installation du Projet

### 1. Cloner/Copier le Projet

```bash
cd C:\xampp\htdocs
git clone <repository-url> yerabby
```

### 2. Créer la Base de Données

```bash
mysql -u root -p
CREATE DATABASE projet;
USE projet;
SOURCE C:\xampp\htdocs\yerabby\projet.sql;
```

### 3. Configurer les Identifiants de Connexion

Éditer `Control/config.php` :

```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Votre mot de passe MySQL
define('DB_NAME', 'projet');
?>
```

### 4. Vérifier les Permissions

```bash
# Windows
# Donner les droits d'accès au dossier uploads/
# Clic droit > Propriétés > Sécurité > Modifier

# Linux/Mac
chmod -R 755 uploads/
chmod -R 755 view/
```

### 5. Accéder à l'Application

```
http://localhost/yerabby/view/general/index.php
```

---

## 🚀 Utilisation

### Accès Administrateur

```
Email : ahmedmohsen@gmail.com
Mot de passe : fagestohsel2005
URL : http://localhost/yerabby/Control/admin_dashboard.php
```

### Accès Entreprise

1. S'inscrire comme "Entreprise"
2. Remplir le profil complet
3. Publier des offres d'emploi
4. Gérer les candidatures

### Accès Utilisateur

1. S'inscrire comme "Utilisateur"
2. Compléter le profil
3. Rechercher des offres d'emploi
4. Se connecter avec le réseau social
5. Créer des histoires de succès

### Fonctionnalités Clés

#### 🔐 Authentification
- Inscription avec CAPTCHA
- Connexion sécurisée
- Récupération du mot de passe (email)
- Google OAuth

#### 👤 Profil
- Photo de profil
- Statistiques (amis, posts)
- Historique personnel
- Paramètres de compte

#### 💼 Offres d'Emploi
- `http://localhost/yerabby/view/FrontOffice/liste_offres.php`

#### 📝 Histoires de Succès
- `http://localhost/yerabby/view/general/temoignages.php`

#### 📊 Admin
- Modération du contenu
- Gestion des utilisateurs
- Statistiques des événements
- Dashboard complet

---

## 🏗️ Architecture MVC

### Structure des Dossiers

```
yerabby/
├── Model/                  # Couche données
│   ├── Database.php
│   ├── User.php
│   ├── UserModel.php
│   ├── StoryModel.php
│   ├── EventModel.php
│
├── Control/               # Couche logique métier
│   ├── config.php
│   ├── UserController.php
│   ├── EventController.php
│   ├── AdminController.php
│
├── view/                  # Couche présentation
│   ├── general/
│   ├── FrontOffice/
│   └── Backoffice/
│
├── uploads/              # Fichiers utilisateurs
├── config/               # Configuration (mailer, etc)
└── PHPMailer/           # Bibliothèque email
```

### Principes MVC

| Composant | Responsabilité |
|-----------|-----------------|
| **Model** | Données, base de données, logique métier |
| **View** | Interface utilisateur, HTML/CSS/JS |
| **Control** | Logique applicative, requêtes utilisateur |

---

## 🤝 Contributions

### Merci à nos contributeurs ! 🙏

Ce projet n'aurait pas été possible sans la contribution généreuse de nos développeurs et collaborateurs. Chaque contribution, qu'elle soit majeure ou mineure, est précieuse pour améliorer **AbleLink** et soutenir notre mission d'inclusion professionnelle.

Si vous souhaitez rejoindre notre équipe et contribuer à ce projet académique, nous vous accueillons chaleureusement !

---

### 👥 Contributeurs

| Contributeur | Rôle | GitHub |
|--------------|------|--------|
| [nourhene klai ] | 🎯 Développement Principal | [@nourhene](https://github.com/nourheneklai) |
| [mohammed amine challouf] | 🔧 Backend & Base de Données | [amine](https://github.com/mohammedaminechallouf) |
| [malek jafrar] | 🎨 Frontend & Interface Utilisateur | [malek](https://github.com/malekjafrar) |
| [iness misaoui] | 🧪 Tests & Documentation | [iness](https://github.com/inessmisoui) 
| [adem friaa] | 🧪 Tests & Documentation | [adem](https://github.com/ademfriaa) ||

---

### 📝 Comment Contribuer

Nous accueillons les contributions de tous les niveaux ! Voici comment vous pouvez participer :

#### 1️⃣ Préparer votre environnement

**Fork le repository**
```bash
# Accédez à https://github.com/ablelink/yerabby
# Cliquez sur "Fork" en haut à droite
```

**Clonez votre fork localement**
```bash
git clone https://github.com/VOTRE-USERNAME/yerabby.git
cd yerabby
```

**Ajoutez le repository original comme remote**
```bash
git remote add upstream https://github.com/ablelink/yerabby.git
```

#### 2️⃣ Créer une branche pour votre feature

```bash
# Mettez à jour votre branche main
git fetch upstream
git checkout main
git merge upstream/main

# Créez une branche descriptive
git checkout -b feature/nom-de-votre-feature
```

**Convention de nommage des branches :**
- `feature/` - Pour une nouvelle fonctionnalité
- `bugfix/` - Pour une correction de bug
- `docs/` - Pour la documentation
- `refactor/` - Pour du refactorisation
- `test/` - Pour les tests

#### 3️⃣ Faire vos modifications

- Respectez les standards de code (voir section ci-dessous)
- Écrivez des commentaires clairs en français
- Testez votre code localement
- Committez régulièrement avec des messages clairs

#### 4️⃣ Faire un commit avec un message clair

```bash
# Format recommandé
git commit -m "Type: Description brève et claire"
```

**Types de commits recommandés :**
```
feat:     Une nouvelle fonctionnalité
fix:      Une correction de bug
docs:     Modifications de documentation
style:    Changements de formatage/style
refactor: Refactorisation du code
perf:     Améliorations de performance
test:     Ajout de tests
chore:    Tâches de maintenance
```

**Exemples de bons commits :**
```bash
git commit -m "feat: Ajouter système de notifications pour les demandes d'amis"
git commit -m "fix: Corriger erreur d'authentification Google OAuth"
git commit -m "docs: Mettre à jour guide d'installation de PHP"
git commit -m "refactor: Simplifier la logique de UserController"
```

#### 5️⃣ Pousser votre branche et créer une Pull Request

```bash
# Poussez votre branche
git push origin feature/nom-de-votre-feature
```

**Sur GitHub :**
1. Allez sur votre fork
2. Cliquez sur "Compare & pull request"
3. Vérifiez que la branche de base est `upstream:main`
4. Remplissez le titre et la description
5. Cliquez sur "Create pull request"

**Template de Pull Request :**
```markdown
## 📋 Description
[Décrivez vos modifications]

## 🎯 Type de changement
- [ ] Bug fix
- [ ] Nouvelle fonctionnalité
- [ ] Amélioration
- [ ] Documentation

## ✅ Checklist
- [ ] Mon code suit les standards du projet
- [ ] J'ai testé mes modifications
- [ ] J'ai mis à jour la documentation
- [ ] Aucun warning/erreur dans mon code

## 🔗 Issue liée (si applicable)
Closes #[numéro de l'issue]
```

---

### 📐 Standards de Code

Pour maintenir une qualité de code cohérente, veuillez respecter ces standards :

#### Indentation et Formatage
```php
// ✅ CORRECT - 4 espaces
class UserController {
    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateur WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// ❌ INCORRECT - 2 espaces ou tabs
class UserController {
  public function getUserByEmail($email) {
    // ...
  }
}
```

#### Nommage des variables et fonctions
```php
// ✅ CORRECT - camelCase
$userName = "Ahmed";
$userEmail = "ahmed@example.com";
public function getUserById($id) { }

// ❌ INCORRECT
$user_name = "Ahmed";
$userEmail = "ahmed@example.com";
public function get_user_by_id($id) { }
```

#### Nommage des classes
```php
// ✅ CORRECT - PascalCase
class UserController { }
class UserModel { }
class AuthenticationService { }

// ❌ INCORRECT
class userController { }
class user_model { }
```

#### Sécurité : Prévention de l'injection SQL
```php
// ❌ DANGEREUX - Injection SQL possible
$user = $conn->query("SELECT * FROM utilisateur WHERE email='$email'");

// ✅ SECURE - Requêtes préparées
$stmt = $conn->prepare("SELECT * FROM utilisateur WHERE email = :email");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();
```

#### Commentaires
```php
// ✅ BON - Commentaires clairs et utiles
// Vérifier si l'email existe déjà dans la base de données
$stmt = $conn->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = :email");
$stmt->execute(['email' => $email]);
return $stmt->fetchColumn() > 0;

// ❌ MAUVAIS - Commentaires inutiles
// Boucle pour chaque utilisateur
foreach ($users as $user) {
    $name = $user->getName();
}
```

#### Validation des entrées
```php
// ✅ BON
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    throw new Exception("Email invalide");
}

// ❌ MAUVAIS
$email = $_POST['email'];
```

---

### 🔍 Avant de créer une Pull Request

1. **Testez votre code**
   ```bash
   # Ouvrez votre navigateur et testez les fonctionnalités
   http://localhost/yerabby/view/general/index.php
   ```

2. **Vérifiez les erreurs PHP**
   - Consultez les logs Apache : `C:\xampp\apache\logs\error.log`
   - Vérifiez qu'aucune notice ou warning n'est émise

3. **Assurez-vous que votre branche est à jour**
   ```bash
   git fetch upstream
   git rebase upstream/main
   ```

4. **Exécutez une dernière fois vos tests**
   - Vérifiez les fonctionnalités que vous avez modifiées
   - Testez les cas limites et les erreurs

---

### 💬 Questions ou Problèmes ?

- 📧 **Email** : support@ablelink.com
- 💬 **Discussions** : Ouvrez une "Discussion" sur GitHub
- 🐛 **Bug** : Ouvrez une "Issue" avec le label `bug`
- 💡 **Feature Request** : Ouvrez une "Issue" avec le label `enhancement`

---

### 📚 Ressources Utiles

- [Git Workflow Guide](https://guides.github.com/introduction/flow/)
- [GitHub Pull Request Tutorial](https://docs.github.com/en/pull-requests)
- [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/)
- [PHP Standards](https://www.php-fig.org/psr/psr-12/)

---

### 🙌 Merci de contribuer à AbleLink !

Votre contribution aide des milliers de personnes en situation de handicap à trouver l'emploi idéal. Ensemble, nous construisons une plateforme plus inclusive et équitable.

**N'hésitez pas à demander de l'aide !** 🤝

---

## 📄 License

Ce projet est sous licence **MIT License**.

### MIT License - Résumé

Vous êtes autorisé à :
- ✅ Utiliser commercialement
- ✅ Modifier le code
- ✅ Distribuer
- ✅ Utiliser en privé

Conditions :
- ⚠️ Inclure une notice de license et copyright
- ⚠️ Les modifications doivent être documentées

**Pas de garantie** - Le logiciel est fourni "tel quel"

---

## 📞 Support et Contact

### Ressources

- 📧 Email : support@ablelink.com
- 🌐 Site : https://www.ablelink.com
- 📱 Téléphone : +216 12345678

### Signaler un Bug

1. Vérifier que le bug n'a pas déjà été signalé
2. Créer une issue avec description et captures d'écran

### Demander une Fonctionnalité

1. Vérifier que la feature n'existe pas
2. Créer une issue de type "Feature Request"

---

## 📚 Documentation Supplémentaire

- [Architecture MVC Détaillée](./README_STRUCTURE_MVC.md)
- [Intégration Success Stories](./INTEGRATION_SUCCESS_STORIES_FINAL.md)
- [Configuration Gmail](./GMAIL_SETUP.md)

---

<div align="center">

**Fait avec ❤️ pour l'inclusion professionnelle**

[⬆ Retour au top](#ablelink---plateforme-demploi-inclusive)

</div>


