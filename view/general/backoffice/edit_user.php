<?php
require_once __DIR__ . '/../../../Control/config.php';
require_once __DIR__ . '/../../../Control/UserController.php';
require_once __DIR__ . '/../../../Model/User.php';

if (!isset($_GET['id'])) {
    die("ID utilisateur manquant.");
}

$controller = new UserController();
$user = $controller->showUser($_GET['id']);

if (!$user) {
    die("Utilisateur introuvable.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un utilisateur - Nexus Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- SAME CSS AS ADD-USER -->
    <style>
        :root {
            --primary: #7c3aed;
            --primary-light: #8b5cf6;
            --secondary: #0ea5e9;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --text: #e2e8f0;
            --text-light: #94a3b8;
            --card-bg: rgba(30, 41, 59, 0.7);
            --sidebar-width: 260px;
            --border-radius: 16px;
            --shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            --glow: 0 0 20px rgba(124, 58, 237, 0.3);
            --transition: .3s;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f172a, #1e1b4b);
            color: var(--text);
            overflow-x: hidden;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: rgba(15,23,42,0.95);
            backdrop-filter: blur(10px);
            padding: 25px 20px;
            border-right: 1px solid rgba(255,255,255,0.08);
            position: fixed;
            top: 0; left: 0;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
        }

        .logo-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 12px;
            display: flex; justify-content: center; align-items: center;
            color: #fff;
            font-size: 20px;
            box-shadow: var(--glow);
        }

        .logo-text {
            font-size: 22px;
            font-weight: 700;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links { display: flex; flex-direction: column; gap: 8px; }

        .nav-link {
            text-decoration: none;
            color: var(--text-light);
            padding: 14px 16px;
            border-radius: 12px;
            display: flex;
            gap: 12px;
            align-items: center;
            position: relative;
            transition: var(--transition);
        }

        .nav-link.active,
        .nav-link:hover {
            background: rgba(124,58,237,0.15);
            color: #fff;
            box-shadow: var(--glow);
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 35px;
            width: calc(100% - var(--sidebar-width));
        }

        .header-title h1 {
            font-size: 30px;
            background: linear-gradient(to right, #fff, var(--text-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-title p { color: var(--text-light); }

        /* FORM CARD */
        .form-container {
            width: 900px;
            margin: 40px auto;
            background: var(--card-bg);
            padding: 40px;
            border-radius: var(--border-radius);
            backdrop-filter: blur(12px);
            box-shadow: var(--shadow);
            border: 1px solid rgba(255,255,255,0.08);
        }

        .form-title {
            font-size: 24px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group { margin-bottom: 18px; }

        label {
            font-size: 14px;
            color: var(--text-light);
            margin-bottom: 5px;
            display: block;
        }

        .form-control {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(15,23,42,0.8);
            color: #fff;
            font-size: 15px;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(124,58,237,0.25);
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--glow);
        }

        .btn-red {
            background: var(--danger);
            color: #fff;
            margin-left: 10px;
        }

        .btn-red:hover { opacity: 0.8; }
    </style>

</head>
<body>

<div class="app-container">

    <!-- SIDEBAR -->
    <div class="sidebar">
               <div class="logo site-title">
    <a href="dashboard.php" style="text-decoration:none; display:flex; align-items:center; gap:10px;">
        <span class="letter-a">A</span>
        <span class="letter-b">B</span>
        <span class="letter-l">L</span>
        <span class="letter-e">E</span>
        <span class="letter-link">LINK</span>
    </a>
</div>
<style>
    /* ===== AbleLink Logo (Admin Version) ===== */

.site-title a {
    font-family: 'Josefin Sans', sans-serif;
    font-size: 26px;
    font-weight: 700;
    letter-spacing: 1px;
    display: flex;
    align-items: center;
    gap: 3px;
    transition: 0.3s ease;
}

.letter-a  { color: #FF4F5E; text-shadow: 0 0 6px rgba(255,79,94,0.7); }
.letter-b  { color: #4C8DF5; text-shadow: 0 0 6px rgba(76,141,245,0.7); }
.letter-l  { color: #B87BFF; text-shadow: 0 0 6px rgba(184,123,255,0.7); }
.letter-e  { color: #FFB247; text-shadow: 0 0 6px rgba(255,178,71,0.7); }

.letter-link {
    color: #FFFFFF;
    margin-left: 5px;
    text-shadow: 0 0 10px rgba(255,255,255,0.8);
}

/* Hover animation */
.site-title:hover a {
    transform: scale(1.05);
}

.site-title a span:hover {
    transform: translateY(-2px);
    transition: 0.2s ease;
}

</style>

        <div class="nav-links">
            <a href="index1.php" class="nav-link"><i class="fas fa-home"></i> Tableau de Bord</a>

            <div class="nav-link active"><i class="fas fa-users"></i> Utilisateurs</div>

            <div style="margin-left:35px;">
                <a href="user_list.php" class="nav-link"><i class="fas fa-list"></i> Liste des utilisateurs</a>
                <a href="add_user.php" class="nav-link"><i class="fas fa-user-plus"></i> Ajouter un utilisateur</a>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <div class="header-title">
            <h1>Modifier un utilisateur</h1>
            <p>Éditez les informations de l'utilisateur</p>
        </div>

        <div class="form-container">
            <div class="form-title">
                <i class="fas fa-edit"></i>
                <span>Informations de l'utilisateur</span>
            </div>

            <form method="POST" action="update_user_process.php" class="form-grid">

                <input type="hidden" name="id" value="<?= $user->getId(); ?>">

                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" class="form-control" value="<?= $user->getNom(); ?>">
                </div>

                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="prenom" class="form-control" value="<?= $user->getPrenom(); ?>">
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?= $user->getEmail(); ?>">
                </div>

                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="text" name="telephone" class="form-control" value="<?= $user->getTelephone(); ?>">
                </div>

                <div class="form-group">
                    <label>Rôle</label>
                    <select name="role" class="form-control">
                        <option value="user" <?= $user->getRole() == 'user' ? 'selected' : '' ?>>Utilisateur</option>
                        <option value="entreprise" <?= $user->getRole() == 'entreprise' ? 'selected' : '' ?>>Entreprise</option>
                        <option value="admin" <?= $user->getRole() == 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Statut</label>
                    <select name="statut" class="form-control">
                        <option value="actif" <?= $user->getStatut() == 'actif' ? 'selected' : '' ?>>Actif</option>
                        <option value="banni" <?= $user->getStatut() == 'banni' ? 'selected' : '' ?>>Banni</option>
                    </select>
                </div>

                <div class="form-group" style="grid-column:1 / -1;">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>

                    <a href="user_list.php" class="btn btn-red">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>

            </form>
        </div>

    </div>

</div>

</body>
</html>