<?php
session_start();
require_once __DIR__ . '/../../../controllers/UserController.php';
require_once __DIR__ . '/../../../models/User.php';

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new UserController();

    // Collect inputs
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $telephone = trim($_POST['telephone'] ?? '');
    $role = $_POST['user_type'] ?? 'Utilisateur';

    // Validation
    if (empty($nom)) {
        $errors['nom'] = "Le nom est obligatoire";
    }

    if (empty($prenom)) {
        $errors['prenom'] = "Le prénom est obligatoire";
    }

    if (empty($email)) {
        $errors['email'] = "L'email est obligatoire";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Format d'email invalide";
    } elseif ($controller->emailExists($email)) {
        $errors['email'] = "Cet email existe déjà";
    }

    if (empty($password)) {
        $errors['password'] = "Le mot de passe est obligatoire";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Le mot de passe doit contenir au moins 6 caractères";
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Les mots de passe ne correspondent pas";
    }

    if (empty($role)) {
        $errors['user_type'] = "Veuillez sélectionner votre rôle";
    }

    // If valid
    if (empty($errors)) {
        $user = new User();
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setEmail($email);
        $user->setMotDePasse($password); 
        $user->setTelephone($telephone);
        $user->setRole($role);
        $user->setStatut('actif');

        if ($controller->addUser($user)) {
            $success = "Utilisateur créé avec succès !";
            $_POST = array(); 
        } else {
            $errors['general'] = "Erreur lors de la création du compte. Veuillez réessayer.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Admin - Ajouter un Utilisateur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #7c3aed;
            --primary-light: #8b5cf6;
            --secondary: #0ea5e9;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --dark-light: #1e293b;
            --text: #e2e8f0;
            --text-light: #94a3b8;
            --card-bg: rgba(30, 41, 59, 0.7);
            --sidebar-width: 260px;
            --header-height: 70px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --border-radius: 16px;
            --shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            --glow: 0 0 20px rgba(124, 58, 237, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            padding: 25px 20px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            transition: var(--transition);
            z-index: 100;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
            padding: 0 10px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            box-shadow: var(--glow);
        }

        .logo-text {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            color: var(--text-light);
            border-radius: 12px;
            transition: var(--transition);
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }

        .nav-link:hover::before {
            left: 100%;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(124, 58, 237, 0.1);
            color: white;
            box-shadow: 0 0 15px rgba(124, 58, 237, 0.2);
        }

        .nav-link i {
            font-size: 20px;
            width: 24px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 25px;
            transition: var(--transition);
            min-height: 100vh;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 15px 0;
            animation: slideDown 0.5s ease-out;
        }

        .header-title h1 {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(to right, white, var(--text-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-title p {
            color: var(--text-light);
            font-size: 14px;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            box-shadow: var(--shadow);
            cursor: pointer;
            transition: var(--transition);
        }

        .user-avatar:hover {
            transform: scale(1.05);
            box-shadow: var(--glow);
        }

        /* Form Container */
        .form-container {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 40px;
            box-shadow: var(--shadow);
            border: 1px solid rgba(255, 255, 255, 0.05);
            max-width: 800px;
            margin: 0 auto;
            animation: fadeIn 0.8s ease-out;
        }

        .form-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
        }

        .form-title i {
            color: var(--primary);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--text);
            font-weight: 500;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: var(--text);
            font-family: inherit;
            transition: var(--transition);
            font-size: 14px;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .form-control::placeholder {
            color: var(--text-light);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--glow);
        }

        .btn-block {
            width: 100%;
            justify-content: center;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.2);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .error {
            color: var(--danger);
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        /* Floating Elements */
        .floating-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .floating-element {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), transparent);
            opacity: 0.1;
            animation: float 20s infinite linear;
        }

        .floating-element:nth-child(1) {
            width: 300px;
            height: 300px;
            top: 10%;
            left: 5%;
            animation-duration: 25s;
        }

        .floating-element:nth-child(2) {
            width: 200px;
            height: 200px;
            top: 60%;
            right: 10%;
            animation-duration: 20s;
            animation-direction: reverse;
        }

        .floating-element:nth-child(3) {
            width: 150px;
            height: 150px;
            bottom: 10%;
            left: 20%;
            animation-duration: 15s;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(20px, 20px) rotate(90deg); }
            50% { transform: translate(0, 40px) rotate(180deg); }
            75% { transform: translate(-20px, 20px) rotate(270deg); }
            100% { transform: translate(0, 0) rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            .form-container {
                padding: 25px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="floating-elements">
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
    </div>

    <div class="app-container">
        <!-- Sidebar à gauche -->
        <div class="sidebar">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-cube"></i>
                </div>
                <div class="logo-text">AbleLink</div>
            </div>

            <div class="nav-links">
                <a href="index1.php" class="nav-link">
                    <i class="fas fa-home"></i>
                    <span>Tableau de Bord</span>
                </a>

                <div class="nav-link active" style="cursor:pointer; background: rgba(124, 58, 237, 0.1);">
                    <i class="fas fa-users"></i>
                    <span>Utilisateurs</span>
                </div>

                <div style="margin-left: 35px; display: flex; flex-direction: column; gap: 5px; margin-top: 10px;">
                    <a href="user_list.php" class="nav-link" style="padding-left: 40px; font-size: 14px;">
                        <i class="fas fa-list"></i> Liste des utilisateurs
                    </a>
                    <a href="add_user.php" class="nav-link active" style="padding-left: 40px; font-size: 14px; background: rgba(124, 58, 237, 0.2);">
                        <i class="fas fa-user-plus"></i> Ajouter un utilisateur
                    </a>
                </div>

               
            </div>

            <div style="margin-top: auto; padding: 20px 0;">
                
            </div>
        </div>

        <!-- Main Content à droite -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-title">
                    <h1>Ajouter un Utilisateur</h1>
                    <p>Créez un nouveau compte utilisateur dans le système</p>
                </div>
                
            </div>

            <!-- Formulaire d'ajout -->
            <div class="form-container">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $errors['general']; ?>
                    </div>
                <?php endif; ?>

                <div class="form-title">
                    <i class="fas fa-user-plus"></i>
                    <span>Informations de l'utilisateur</span>
                </div>

                <form method="POST" class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nom *</label>
                        <input type="text" name="nom" class="form-control" placeholder="Entrez le nom"
                               value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>" required>
                        <?php if (isset($errors['nom'])): ?>
                            <span class="error"><?php echo $errors['nom']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Prénom *</label>
                        <input type="text" name="prenom" class="form-control" placeholder="Entrez le prénom"
                               value="<?php echo htmlspecialchars($_POST['prenom'] ?? ''); ?>" required>
                        <?php if (isset($errors['prenom'])): ?>
                            <span class="error"><?php echo $errors['prenom']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label">Adresse Email *</label>
                        <input type="email" name="email" class="form-control" placeholder="exemple@domain.com"
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <span class="error"><?php echo $errors['email']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="telephone" class="form-control" placeholder="+216 XX XXX XXX"
                               value="<?php echo htmlspecialchars($_POST['telephone'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Rôle *</label>
                        <select name="user_type" class="form-control" required>
                            <option value="">Sélectionnez un rôle</option>
                            <option value="user" <?php echo (($_POST['user_type'] ?? '') === 'Utilisateur') ? 'selected' : ''; ?>>Utilisateur</option>
                            <option value="entreprise" <?php echo (($_POST['user_type'] ?? '') === 'Entreprise') ? 'selected' : ''; ?>>Entreprise</option>
                            <option value="admin" <?php echo (($_POST['user_type'] ?? '') === 'Admin') ? 'selected' : ''; ?>>Admin</option>
                        </select>
                        <?php if (isset($errors['user_type'])): ?>
                            <span class="error"><?php echo $errors['user_type']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mot de passe *</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimum 6 caractères" required>
                        <?php if (isset($errors['password'])): ?>
                            <span class="error"><?php echo $errors['password']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirmer le mot de passe *</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Répétez le mot de passe" required>
                        <?php if (isset($errors['confirm_password'])): ?>
                            <span class="error"><?php echo $errors['confirm_password']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group full-width">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-user-plus"></i> Créer l'utilisateur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Script pour gérer le menu utilisateur si nécessaire
        document.addEventListener("DOMContentLoaded", function () {
            console.log("Page d'ajout d'utilisateur chargée");
        });
    </script>
</body>
</html>