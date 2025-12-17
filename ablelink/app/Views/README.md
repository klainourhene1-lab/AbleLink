# 📂 Dossier Views - Architecture MVC

Ce dossier contient **TOUTES les vues (Views)** de l'application dans une architecture MVC.

## 🎯 Structure

```
Views/
├── layout.php              ← Layout principal (header, footer communs)
│
├── general/                ← Pages générales
│   ├── about.php          ← Page "À propos"
│   ├── contact.php        ← Page "Contact"
│   └── services.php       ← Page "Services"
│
├── success_stories/        ← Pages des success stories
│   ├── index.php          ← Liste des stories (vue principale)
│   ├── form.php           ← Formulaire création/édition
│   ├── comments.php       ← Détails d'une story avec commentaires
│   └── history.php        ← Historique des stories
│
├── admin/                  ← Pages administrateur
│   ├── index.php          ← Dashboard admin
│   ├── login.php          ← Connexion admin
│   ├── stories.php        ← Gestion des stories
│   ├── comments.php       ← Gestion des commentaires
│   ├── reported.php       ← Commentaires signalés
│   ├── stats.php          ← Statistiques
│   └── story.php          ← Détails d'une story (admin)
│
└── events/                 ← Pages des événements
    └── index.php          ← Liste des événements
```

## 🔗 Correspondance Routes → Vues

| Route | Controller | Vue |
|-------|-----------|-----|
| `/success-stories` | `SuccessStoriesController::index()` | `success_stories/index.php` |
| `/services` | `ServicesController::index()` | `general/services.php` |
| `/about` | `AboutController::index()` | `general/about.php` |
| `/contact` | `ContactController::index()` | `general/contact.php` |
| `/admin` | `AdminController::index()` | `admin/index.php` |

## ✅ Comment ça fonctionne ?

1. L'utilisateur accède à une URL (ex: `/success-stories`)
2. Le **Router** (`app/Core/Router.php`) redirige vers le **Controller** approprié
3. Le **Controller** récupère les données via le **Model**
4. Le **Controller** appelle `$this->render('success_stories/index', $data)`
5. La **View** (`app/Views/success_stories/index.php`) est chargée avec les données
6. Le **layout.php** enveloppe la vue (header + footer)

## 📌 Points importants

- ✅ Toutes les vues sont dans ce dossier
- ✅ Pas de logique métier dans les vues (juste affichage)
- ✅ Les données sont passées depuis les contrôleurs
- ✅ Le layout commun est dans `layout.php`

**C'est bien une architecture MVC !** 🎉

