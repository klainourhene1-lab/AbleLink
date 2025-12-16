# 🔐 Comptes Administrateurs - AbleLink

## Informations de Connexion

### Admin 1
- **Email**: `ahmedmohsen@gmail.com`
- **Mot de passe temporaire**: `admin123`
- **Rôle**: Administrateur

### Admin 2
- **Email**: `nourbouabid@gmail.com`
- **Mot de passe temporaire**: `admin123`
- **Rôle**: Administrateur

---

## ⚠️ IMPORTANT - Sécurité

> [!CAUTION]
> **Changez ces mots de passe immédiatement après la première connexion!**
> 
> Les mots de passe temporaires sont destinés uniquement pour le premier accès.

---

## Comment se connecter

1. Accédez à: **`http://localhost/projetweb/ablelink/auth/login`**
2. Entrez votre email admin
3. Entrez le mot de passe: `admin123`
4. Cliquez sur **"Se connecter"**

Vous serez redirigé vers la page d'administration (**`/success-stories/history`**) où vous pourrez:
- ✅ Voir toutes les histoires (approved, pending, rejected)
- ✅ Approuver ou rejeter les histoires
- ✅ Modifier n'importe quelle histoire
- ✅ Supprimer n'importe quelle histoire
- ✅ Gérer tous les commentaires

---

## Créer un nouveau compte utilisateur

Les nouveaux utilisateurs peuvent s'inscrire à: **`http://localhost/projetweb/ablelink/auth/register`**

**Capacités des utilisateurs normaux:**
- ✅ Partager leurs propres histoires
- ✅ Modifier UNIQUEMENT leurs propres histoires
- ✅ Supprimer UNIQUEMENT leurs propres histoires
- ✅ Commenter toutes les histoires
- ❌ Ne peuvent PAS modifier/supprimer les histoires des autres

---

## Pages importantes

| Page | URL | Accès |
|------|-----|-------|
| **Login** | `/auth/login` | Public |
| **Register** | `/auth/register` | Public |
| **Success Stories** | `/success-stories` | Public |
| **Admin Panel** | `/success-stories/history` | Admin uniquement |
| **Créer histoire** | `/success-stories/create` | Utilisateurs connectés |
| **Logout** | `/auth/logout` | Utilisateurs connectés |
