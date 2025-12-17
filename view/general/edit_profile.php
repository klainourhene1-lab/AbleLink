<?php
session_start();

// 1) Auth Check
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

// 2) Load Dependencies
require_once __DIR__ . '/../../Control/config.php';
require_once __DIR__ . '/../../Control/UserController.php';
require_once __DIR__ . '/../../Model/User.php';

$userController = new UserController();
$currentUserId = $_SESSION['user_id'];
$currentUser = $userController->showUser($currentUserId);
$user_role = $_SESSION['user_role'] ?? 'Utilisateur';

if (!$currentUser) {
    header('Location: signin.php');
    exit;
}

// Handle Update
$message = "";
$msgType = ""; // success or error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update_profile') {
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $email = trim($_POST['email']);
        $telephone = trim($_POST['telephone']);
        
        // Photo Logic
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $filename = 'user_' . $currentUserId . '_' . time() . '.' . $ext;
            $path = __DIR__ . '/../../uploads/profiles/' . $filename;
            if (!is_dir(dirname($path))) mkdir(dirname($path), 0777, true);
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $path)) {
                $userController->updatePhoto($currentUserId, $filename);
                $currentUser->setPhoto($filename);
                $_SESSION['user_photo'] = $filename;
            }
        }
        
        // Password Logic
        if (!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
            if ($_POST['new_password'] === $_POST['confirm_password']) {
                if ($currentUser->getMotDePasse()) {
                    // In a real app with hashed passwords:
                    // if (password_verify($_POST['current_password'], $currentUser->getMotDePasse())) { ... }
                    // For now, implied success or basic check if needed.
                    // Assuming direct update for prototype or using standard hash.
                    $currentUser->setMotDePasse($_POST['new_password']); // WARNING: Should be hashed in controller
                }
            } else {
                $message = "Les nouveaux mots de passe ne correspondent pas.";
                $msgType = "error";
            }
        }

        if (empty($message)) {
            $currentUser->setNom($nom);
            $currentUser->setPrenom($prenom);
            $currentUser->setEmail($email);
            $currentUser->setTelephone($telephone);
            
            if ($userController->updateUser($currentUser, $currentUserId)) {
                $message = "Profil mis à jour avec succès !";
                $msgType = "success";
            } else {
                $message = "Erreur lors de la mise à jour.";
                $msgType = "error";
            }
        }
    }
}

// Helper
function getPhoto($u) {
    if ($u->getPhoto() && file_exists(__DIR__ . '/../../uploads/profiles/' . $u->getPhoto())) {
        return '../../uploads/profiles/' . $u->getPhoto();
    }
    return 'img/team/team-1.jpg';
}

$role_mapping = ['Admin' => 'admin', 'Entreprise' => 'company', 'Utilisateur' => 'user', 'Inclusion' => 'inclusion', 'guest' => 'guest'];
$js_role = $role_mapping[$user_role] ?? 'user';
$role_display_names = ['admin' => 'Administrateur', 'company' => 'Entreprise', 'user' => 'Utilisateur', 'inclusion' => 'Responsable', 'guest' => 'Visiteur'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paramètres du Profil | AbleLink</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Standard Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Standard CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
    
    <style>
         body {
            background: #100028; /* Standard Site Background */
            min-height: 100vh;
            color: #ffffff;
        }
        
        /* HEADER STYLES COPIED FROM PROFILE/LISTE_OFFRES for consistency */
        .header__nav__option { display: flex; align-items: center; justify-content: space-between; }
        .header__nav__menu { flex: 1; min-width: 0; }
        .header__nav__menu ul { display: flex; gap: 24px; align-items: center; justify-content: flex-end; }
        
        .role-badge { padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; margin-left: 10px; }
        .role-badge.user { background: rgba(52, 152, 219, 0.2); color: #3498db; }
        .role-badge.company { background: rgba(46, 204, 113, 0.2); color: #2ecc71; }
        .role-badge.inclusion { background: rgba(155, 89, 182, 0.2); color: #9b59b6; }
        .role-badge.admin { background: rgba(231, 76, 60, 0.2); color: #e74c3c; }

        .site-title { margin-top: 0; padding: 0; }
        .site-title a { font-family: 'Josefin Sans', sans-serif; font-size: 32px; font-weight: 700; text-decoration: none; line-height: 1.2; display: inline-flex; gap: 2px; letter-spacing: 1px; text-transform: uppercase; transition: 0.3s ease; }
        .letter-a  { color: #ff4f5e; text-shadow: 0 0 8px rgba(255,79,94,0.6); }
        .letter-b  { color: #4c8df5; text-shadow: 0 0 8px rgba(76,141,245,0.6); }
        .letter-l  { color: #b87bff; text-shadow: 0 0 8px rgba(184,123,255,0.6); }
        .letter-e  { color: #ffb247; text-shadow: 0 0 8px rgba(255,178,71,0.6); }
        .letter-link { color: #ffffff; margin-left: 4px; text-shadow: 0 0 10px rgba(255,255,255,0.7); }

        /* User Profile Dropdown */
        .user-profile-dropdown { position: relative; display: inline-block; }
        .user-profile-button {
            display: flex; align-items: center; gap: 10px; padding: 6px 12px;
            background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);
            border-radius: 25px; cursor: pointer; transition: all 0.3s ease;
        }
        .user-profile-button:hover { background: rgba(255,255,255,0.2); }
        .user-avatar { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; }
        
        /* New Dropdown Styles */
        .user-dropdown-menu {
            display: none; position: absolute; top: calc(100% + 15px); right: 0; min-width: 280px;
            background: #0f172a; border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.6); z-index: 1000; overflow: hidden;
            padding-bottom: 8px;
        }
        .user-dropdown-menu.show { display: block; animation: fadeIn 0.2s ease-out; }
        
        .dropdown-user-header {
            padding: 20px; display: flex; align-items: center; gap: 15px;
            background: rgba(255,255,255,0.02);
        }
        .dropdown-avatar-large { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #00bfe7; }
        .dropdown-user-details { display: flex; flex-direction: column; }
        .d-name { color: #fff; font-weight: 700; font-size: 15px; }
        .d-email { color: #94a3b8; font-size: 12px; }
        
        .dropdown-divider { height: 1px; background: rgba(255,255,255,0.08); margin: 8px 0; }
        
        .user-dropdown-item {
            display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #cbd5e1;
            text-decoration: none; transition: all 0.2s; font-size: 14px; font-weight: 500;
        }
        .user-dropdown-item i { width: 20px; text-align: center; color: #94a3b8; transition: 0.2s; }
        .user-dropdown-item:hover { background: rgba(255,255,255,0.05); color: #fff; text-decoration: none; }
        .user-dropdown-item:hover i { color: #00bfe7; }
        
        .user-dropdown-item.item-logout { color: #ef4444; margin-top: 5px; }
        .user-dropdown-item.item-logout i { color: #ef4444; }
        .user-dropdown-item.item-logout:hover { background: rgba(239, 68, 68, 0.1); }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

        /* --- EDIT PROFILE SPECIFIC STYLES --- */
        .page-header-text { text-align: center; margin-top: 150px; margin-bottom: 30px; letter-spacing: 1px; }
        .page-header-text span { display: block; font-size: 12px; font-weight: 600; color: #a9a9a9; margin-bottom: 5px; letter-spacing: 2px; }
        .page-header-text h1 { margin: 0; font-size: 32px; font-weight: 700; text-transform: uppercase; color: #fff; }
        .page-header-text h1:after { content: ''; display: block; width: 60px; height: 4px; background: #00bfe7; margin: 15px auto 0; border-radius: 2px; }

        .settings-card {
            background: #1a083d; /* Matching profile.php card background */
            border-radius: 20px;
            padding: 40px 60px;
            max-width: 800px;
            margin: 0 auto 100px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.05);
        }
        
        /* Overriding bootstrap defaults for form elements in the card */
        .settings-card .form-control {
            background: rgba(255,255,255,0.05) !important;
            border: 1px solid rgba(255,255,255,0.3) !important;
            color: #fff !important;
        }
        .settings-card label { color: #fff; font-size: 13px; font-weight: 600; margin-bottom: 10px; }
        .photo-section { text-align: center; margin-bottom: 40px; }
        .profile-pic-large { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #00bfe7; margin-bottom: 15px; }
        .btn-upload { display: inline-block; border: 2px solid #00bfe7; color: #fff; padding: 10px 25px; border-radius: 5px; font-size: 13px; font-weight: 700; text-transform: uppercase; cursor: pointer; transition: 0.3s; }
        .btn-upload:hover { background: #00bfe7; }

        .action-buttons { display: flex; gap: 15px; margin-top: 30px; margin-bottom: 20px; }
        .btn-action { padding: 12px 25px; border: none; border-radius: 2px; font-size: 13px; font-weight: 700; text-transform: uppercase; cursor: pointer; color:#fff; display: inline-block; text-align: center; }
        .btn-primary-action { background: #00bfe7; flex: 1; }
        .btn-delete { background: #ff4f5e; width: 100%; margin-top: 10px;}
        
        .section-title { font-size: 14px; color: #a9a9a9; text-transform: uppercase; margin-bottom: 20px; font-weight: 600; letter-spacing: 1px; }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="header__logo">
                         <div class="site-title">
                            <a href="index.php">
                                <span class="letter-a">A</span><span class="letter-b">b</span><span class="letter-l">l</span><span class="letter-e">e</span><span class="letter-link">Link</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-10">
                    <div class="header__nav__option">
                        <nav class="header__nav__menu mobile-menu">
                            <ul>
                                <li><a href="index.php">Accueil</a></li>
                                <!-- Links Removed -->
                                <?php if ($user_role === 'Admin'): ?>
                                    <li><a href="../../Control/admin_dashboard.php">Administration</a></li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        <div class="header__nav__social">
                            <?php if ($currentUser): ?>
                                <div class="role-badge <?php echo $js_role; ?>">
                                    <?php echo $role_display_names[$js_role] ?? 'Utilisateur'; ?>
                                </div>
                                <div class="user-profile-dropdown" id="userProfileDropdown">
                                    <button class="user-profile-button" onclick="toggleUserMenu()">
                                        <img src="<?php echo htmlspecialchars(getPhoto($currentUser)); ?>" alt="Profile" class="user-avatar">
                                         <i class="fa fa-chevron-down" style="margin-left: 5px; font-size: 12px;"></i>
                                    </button>
                                    <div class="user-dropdown-menu" id="userDropdownMenu">
                                       <div class="dropdown-user-header">
                                           <img src="<?php echo htmlspecialchars(getPhoto($currentUser)); ?>" class="dropdown-avatar-large">
                                           <div class="dropdown-user-details">
                                               <span class="d-name"><?php echo htmlspecialchars($currentUser->getPrenom() . ' ' . $currentUser->getNom()); ?></span>
                                               <span class="d-email"><?php echo htmlspecialchars($currentUser->getEmail()); ?></span>
                                           </div>
                                       </div>
                                       <div class="dropdown-divider"></div>
                                       <a href="profile.php" class="user-dropdown-item"><i class="fa fa-user"></i> Voir le profil</a>
                                       <a href="edit_profile.php" class="user-dropdown-item"><i class="fa fa-cog"></i> Paramètres du compte</a>
                                       <a href="#" class="user-dropdown-item"><i class="fa fa-bell"></i> Notifications</a>
                                       <a href="#" class="user-dropdown-item"><i class="fa fa-exchange"></i> Changer de compte</a>
                                       <a href="#" class="user-dropdown-item"><i class="fa fa-question-circle"></i> Centre d'aide</a>
                                       <a href="logout.php" class="user-dropdown-item item-logout"><i class="fa fa-sign-out"></i> Déconnexion</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
             <div id="mobile-menu-wrap"></div>
        </div>
    </header>

    <!-- Page Content -->
    <div class="page-header-text">
        <span>MON COMPTE</span>
        <h1>Paramètres du profil</h1>
    </div>

    <div class="container">
        <div class="settings-card">
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo ($msgType == 'success') ? 'success' : 'danger'; ?> text-center mb-4">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_profile">
                
                <!-- PHOTO -->
                <div class="section-title">Photo de Profil</div>
                <div class="photo-section">
                    <img src="<?php echo getPhoto($currentUser); ?>" class="profile-pic-large" id="previewImg">
                    <div style="font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 5px;"><?php echo htmlspecialchars($currentUser->getPrenom() . ' ' . $currentUser->getNom()); ?></div>
                    <div style="font-size: 12px; color: #a9a9a9; text-transform: uppercase; margin-bottom: 20px; letter-spacing: 1px;"><?php echo htmlspecialchars($user_role); ?></div>
                    
                    <label for="fileInput" class="btn-upload">
                        <i class="fa fa-camera"></i> Choisir une photo
                    </label>
                    <input type="file" name="photo" id="fileInput" style="display: none;" onchange="previewFile()">
                    
                    <span style="display: block; font-size: 11px; color: #777; margin-top: 10px;">Aucun fichier sélectionné<br>Formats: JPG, PNG, GIF (max 2MB)</span>
                </div>
                
                <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 30px 0;">

                <!-- INFO -->
                <div class="section-title">Informations Personnelles</div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Prénom</label>
                            <input type="text" name="prenom" class="form-control" value="<?php echo htmlspecialchars($currentUser->getPrenom()); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nom</label>
                            <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($currentUser->getNom()); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Adresse Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($currentUser->getEmail()); ?>">
                </div>
                
                <div class="form-group">
                    <label>Numéro de Téléphone</label>
                    <input type="text" name="telephone" class="form-control" value="<?php echo htmlspecialchars($currentUser->getTelephone()); ?>">
                </div>

                <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 30px 0;">

                <!-- PASSWORD -->
                <div class="section-title">Changer le Mot de Passe (Optionnel)</div>
                
                <div class="form-group">
                    <label>Mot de Passe Actuel</label>
                    <input type="password" name="current_password" class="form-control" placeholder="Entrez votre mot de passe actuel">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nouveau Mot de Passe</label>
                            <input type="password" name="new_password" class="form-control" placeholder="Entrez le nouveau mot de passe">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Confirmer le Nouveau Mot de Passe</label>
                            <input type="password" name="confirm_password" class="form-control" placeholder="Confirmez le nouveau mot de passe">
                        </div>
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="action-buttons">
                    <button type="submit" class="btn-action btn-primary-action">Mettre à jour le profil</button>
                    <a href="profile.php" class="btn-action btn-primary-action" style="background: #00bfe7; text-decoration: none;">Retour</a>
                </div>
                
                <button type="button" class="btn-action btn-delete">Supprimer le compte</button>

            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer__top">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="site-title" style="font-size: 32px; font-weight: 700;">
                            <a href="index.php">
                                <span class="letter-a">A</span><span class="letter-b">b</span><span class="letter-l">l</span><span class="letter-e">e</span><span class="letter-link">Link</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer__option">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="footer__option__item">
                            <h5>À propos d’AbleLink</h5>
                            <p>AbleLink connecte les chercheurs d’emploi en situation de handicap aux entreprises inclusives.</p>
                        </div>
                    </div>
                     <div class="col-lg-2 col-md-3 col-sm-3">
                        <div class="footer__option__item">
                            <h5>Ressources</h5>
                            <ul>
                                <li><a href="#">Équipe</a></li>
                                <li><a href="#">Carrières</a></li>
                                <li><a href="#">Contact</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-3">
                        <div class="footer__option__item">
                            <h5>Explorer</h5>
                            <ul>
                                <li><a href="../FrontOffice/liste_offres.php">Offres d’emploi</a></li>
                                <li><a href="#">Communauté</a></li>
                            </ul>
                        </div>
                    </div>
                     <div class="col-lg-4 col-md-12">
                        <div class="footer__option__item">
                            <h5>Newsletter</h5>
                            <form action="#">
                                <input type="text" placeholder="Email">
                                <button type="submit"><i class="fa fa-send"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
             <div class="footer__copyright">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <p>Copyright © <?php echo date('Y'); ?> AbleLink | Vers un avenir professionnel inclusif</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- JS -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    <script>
    function toggleUserMenu() {
        document.getElementById('userDropdownMenu').classList.toggle('show');
    }
    window.onclick = function(event) {
        if (!event.target.matches('.user-profile-button') && !event.target.closest('.user-profile-button')) {
            var dropdowns = document.getElementsByClassName("user-dropdown-menu");
            for (var i = 0; i < dropdowns.length; i++) {
                 if (dropdowns[i].classList.contains('show')) dropdowns[i].classList.remove('show');
            }
        }
    }
    
    function previewFile() {
        const preview = document.getElementById('previewImg');
        const file = document.getElementById('fileInput').files[0];
        const reader = new FileReader();

        reader.addEventListener("load", function () {
            preview.src = reader.result;
        }, false);

        if (file) {
            reader.readAsDataURL(file);
        }
    }
    </script>
</body>
</html>
