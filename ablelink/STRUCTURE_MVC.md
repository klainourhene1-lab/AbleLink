# 📁 STRUCTURE MVC - AbleLink

## Architecture Model-View-Controller (MVC)

Le projet suit une architecture MVC propre et organisée :

```
ablelink/
├── app/                          ← Dossier principal de l'application MVC
│   ├── Controllers/              ← CONTROLLERS (Logique métier)
│   │   ├── AboutController.php
│   │   ├── AdminController.php
│   │   ├── AdminLoginController.php
│   │   ├── ContactController.php
│   │   ├── EventsController.php
│   │   ├── HomeController.php
│   │   ├── LoginController.php
│   │   ├── LogoutController.php
│   │   ├── ServicesController.php
│   │   └── SuccessStoriesController.php
│   │
│   ├── Models/                   ← MODELS (Accès aux données)
│   │   ├── Comment.php
│   │   ├── Event.php
│   │   ├── Evaluation.php
│   │   ├── Participation.php
│   │   └── SuccessStory.php
│   │
│   ├── Views/                    ← VIEWS (Affichage)
│   │   ├── layout.php           ← Layout principal
│   │   ├── general/             ← Vues générales
│   │   │   ├── about.php
│   │   │   ├── contact.php
│   │   │   └── services.php
│   │   ├── success_stories/     ← Vues des success stories
│   │   │   ├── index.php        ← Liste des stories
│   │   │   ├── form.php         ← Formulaire création/édition
│   │   │   ├── comments.php     ← Détails avec commentaires
│   │   │   └── history.php      ← Historique
│   │   ├── admin/               ← Vues administrateur
│   │   │   ├── index.php
│   │   │   ├── login.php
│   │   │   ├── stories.php
│   │   │   ├── comments.php
│   │   │   └── ...
│   │   └── events/              ← Vues des événements
│   │       └── index.php
│   │
│   └── Core/                     ← Classes de base
│       ├── Controller.php       ← Classe de base des contrôleurs
│       ├── Model.php            ← Classe de base des modèles
│       └── Router.php           ← Routeur MVC
│
├── index.php                     ← Point d'entrée unique (Router)
├── .htaccess                     ← Configuration Apache (routage)
│
└── [Fichiers statiques]
    ├── css/
    ├── js/
    └── img/
```

---

## 🔄 Flux MVC

### 1. Requête utilisateur
```
http://localhost/projetweb/ablelink/success-stories
```

### 2. Router (`index.php`)
- Analyse l'URL
- Détermine le contrôleur et la méthode
- Instancie le contrôleur

### 3. Controller (`SuccessStoriesController`)
- Récupère les données via le Model
- Prépare les données pour la vue
- Appelle la méthode `render()`

### 4. Model (`SuccessStory`)
- Interagit avec la base de données
- Retourne les données au contrôleur

### 5. View (`app/Views/success_stories/index.php`)
- Reçoit les données du contrôleur
- Affiche le HTML avec les données
- Utilise le layout principal

---

## 📍 Routes et Vues

| URL | Controller | Méthode | View |
|-----|-----------|---------|------|
| `/success-stories` | `SuccessStoriesController` | `index()` | `app/Views/success_stories/index.php` |
| `/services` | `ServicesController` | `index()` | `app/Views/general/services.php` |
| `/about` | `AboutController` | `index()` | `app/Views/general/about.php` |
| `/contact` | `ContactController` | `index()` | `app/Views/general/contact.php` |
| `/admin` | `AdminController` | `index()` | `app/Views/admin/index.php` |
| `/admin-login` | `AdminLoginController` | `index()` | `app/Views/admin/login.php` |

---

## ✅ Avantages de cette architecture MVC

1. **Séparation des responsabilités**
   - Controllers : Logique métier
   - Models : Accès aux données
   - Views : Affichage uniquement

2. **Maintenabilité**
   - Code organisé et structuré
   - Facile à modifier et étendre

3. **Réutilisabilité**
   - Models réutilisables dans différents contrôleurs
   - Views modulaires avec layout commun

4. **URLs propres**
   - Pas de `.php` dans les URLs
   - Routes descriptives et SEO-friendly

5. **Sécurité**
   - Point d'entrée unique (`index.php`)
   - Pas d'accès direct aux fichiers PHP
   - Validation centralisée

---

## 🔍 Comment vérifier que c'est bien MVC ?

1. **Vérifier la structure :**
   - Les vues sont dans `app/Views/`
   - Les contrôleurs sont dans `app/Controllers/`
   - Les modèles sont dans `app/Models/`

2. **Vérifier le routage :**
   - Toutes les requêtes passent par `index.php`
   - Les URLs ne contiennent pas de `.php`
   - Le Router détermine quel contrôleur appeler

3. **Vérifier la séparation :**
   - Les vues n'ont pas de logique métier
   - Les contrôleurs n'ont pas de requêtes SQL directes
   - Les modèles sont indépendants des vues

---

## 📝 Exemple de code MVC

### Controller (`SuccessStoriesController.php`)
```php
public function index(): void {
    $model = new SuccessStory();
    $stories = $model->listPublicWithFilters([...]);
    $this->render('success_stories/index', ['stories' => $stories]);
}
```

### Model (`SuccessStory.php`)
```php
public function listPublicWithFilters($filters): array {
    // Requête SQL ici
    return $stories;
}
```

### View (`app/Views/success_stories/index.php`)
```php
<?php foreach ($stories as $story): ?>
    <div class="story-card">
        <h3><?= htmlspecialchars($story['title']) ?></h3>
    </div>
<?php endforeach; ?>
```

---

**✅ Architecture MVC conforme aux bonnes pratiques !**

