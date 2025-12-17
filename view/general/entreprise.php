<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit;
}

// Only entreprise role can access this page
if ($_SESSION['user_role'] !== 'Entreprise') {
    header("Location: index.php");
    exit;
}

// Load user info
require_once __DIR__ . '/../../Control/config.php';
require_once __DIR__ . '/../../Control/UserController.php';
require_once __DIR__ . '/../../Model/User.php';
$controller = new UserController();
$user = $controller->showUser($_SESSION['user_id']);

$errors = [];
$success = "";

// Function to get photo URL - CORRIGÉE
function getPhotoUrl($photo, $prenom, $nom) {
    if ($photo && file_exists(__DIR__ . '/../../uploads/profiles/' . $photo)) {
        return '/googo/uploads/profiles/' . $photo;
    }
    return 'img/team/team-1.jpg'; // Photo par défaut
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom              = trim($_POST['nom'] ?? '');
    $prenom           = trim($_POST['prenom'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $telephone        = trim($_POST['telephone'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // --- Validation ---
    if (empty($nom)) {
        $errors['nom'] = "Last name is required";
    }

    if (empty($prenom)) {
        $errors['prenom'] = "First name is required";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    } elseif ($email !== $user->getEmail() && $controller->emailExists($email)) {
        $errors['email'] = "Email already exists";
    }

    // Password change logic (optional)
    if (!empty($new_password) || !empty($confirm_password) || !empty($current_password)) {
        if (empty($current_password)) {
            $errors['current_password'] = "Current password is required to change password";
        } elseif (!$user->verifyPassword($current_password)) {
            $errors['current_password'] = "Current password is incorrect";
        } elseif (strlen($new_password) < 6) {
            $errors['new_password'] = "New password must be at least 6 characters";
        } elseif ($new_password !== $confirm_password) {
            $errors['confirm_password'] = "New passwords do not match";
        }
    }

    // Photo upload - VERSION CORRIGÉE
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (in_array($_FILES['photo']['type'], $allowed_types)) {
            if ($_FILES['photo']['size'] <= $max_size) {
                // Generate unique filename
                $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $new_filename = 'user_' . $_SESSION['user_id'] . '_' . time() . '.' . $file_extension;
                $upload_path = __DIR__ . '/../../uploads/profiles/' . $new_filename;
                
                // Create directory if it doesn't exist
                if (!is_dir(dirname($upload_path))) {
                    mkdir(dirname($upload_path), 0777, true);
                }
                
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                    // Update database
                    if ($controller->updatePhoto($_SESSION['user_id'], $new_filename)) {
                        // Update user object and session
                        $user->setPhoto($new_filename);
                        $_SESSION['user_photo'] = $new_filename;
                        $success = "Photo mise à jour avec succès!";
                    } else {
                        $errors['photo'] = "Erreur base de données lors de la mise à jour";
                    }
                } else {
                    $errors['photo'] = "Erreur lors de l'upload du fichier";
                }
            } else {
                $errors['photo'] = "La photo ne doit pas dépasser 2MB";
            }
        } else {
            $errors['photo'] = "Format non supporté (JPEG, PNG, GIF seulement)";
        }
    }

    // If everything OK, update user
    if (empty($errors)) {
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setEmail($email);
        $user->setTelephone($telephone);

        if (!empty($new_password)) {
            $user->setMotDePasse($new_password); // hashes inside setter
        }

        if ($controller->updateUser($user, $_SESSION['user_id'])) {
            $success = $success ? $success . " Profil mis à jour!" : "Profil mis à jour avec succès!";

            // refresh object + session
            $user = $controller->showUser($_SESSION['user_id']);
            $_SESSION['user_email']  = $user->getEmail();
            $_SESSION['user_nom']    = $user->getNom();
            $_SESSION['user_prenom'] = $user->getPrenom();
            $_SESSION['user_photo']  = $user->getPhoto();
        } else {
            $errors['general'] = "Error updating profile. Please try again.";
        }
    }
}

// helper
function e($str) { return htmlspecialchars($str, ENT_QUOTES, 'UTF-8'); }

$stay_open = $_SERVER['REQUEST_METHOD'] === 'POST';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="AbleLink - Espace Entreprise">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Entreprise - AbleLink</title>

    <!-- Template CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/elegant-icons.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/slicknav.min.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        body {
            background: #100028;
            min-height: 100vh;
        }

        .entreprise-section {
            background: #100028;
            padding-top: 150px;
            padding-bottom: 100px;
            display: block;
        }

        .profile-section {
            background: #100028;
            padding-top: 150px;
            padding-bottom: 100px;
            display: <?php echo $stay_open ? 'block' : 'none'; ?>;
        }

        .entreprise-card, .profile-card {
            background: #1a083d;
            padding: 35px;
            border-radius: 20px;
            color: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.45);
            margin-bottom: 40px;
        }

        .profile-card {
            padding: 40px 35px;
        }

        .entreprise-card h3 {
            margin-bottom: 20px;
            font-size: 22px;
            font-weight: 600;
        }

        .feature-box {
            background: #271053;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            transition: .3s;
            height: 100%;
        }

        .feature-box:hover {
            background: #321b67;
            transform: translateY(-4px);
        }

        .site-btn {
            background: #00bfe7;
            border: none;
            padding: 12px 30px;
            color: white;
            border-radius: 6px;
            transition: .3s;
            text-decoration: none;
            display: inline-block;
        }

        .site-btn:hover {
            background: #0099c7;
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        /* Profile menu styles - GARDER COMME AVANT */
        .profile-top {
            position: relative;
            display: inline-block;
            margin-left: 25px;
        }
        .profile-top img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            object-fit: cover;
            border: 2px solid #00bfe7;
        }
        .profile-menu {
            position: absolute;
            right: 0;
            top: 60px;
            width: 260px;
            background: #0d1224;
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            display: none;
            z-index: 999;
        }

        .profile-menu-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 15px;
        }
        .profile-menu-header img {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #00bfe7;
        }
        .profile-menu-header h4 {
            color: white;
            margin: 0;
            font-size: 16px;
        }
        .profile-menu-header span {
            font-size: 13px;
            color: #a9a9a9;
        }
        .profile-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .profile-menu ul li {
            margin-bottom: 12px;
        }
        .profile-menu ul li a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            padding: 8px 0;
            transition: 0.2s;
        }
        .profile-menu ul li a:hover {
            color: #00bfe7;
            padding-left: 5px;
        }
        .logout-btn {
            color: #ff4a4a !important;
        }

        /* Profile form styles - GARDER COMME AVANT */
        .profile-name {
            text-align: center;
            margin-bottom: 5px;
            font-size: 22px;
            font-weight: 700;
            margin-top: 20px;
        }

        .profile-role {
            text-align: center;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #adadad;
            margin-bottom: 25px;
        }

        .profile-card h4 {
            font-size: 18px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .profile-form label {
            color: #ffffff;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .profile-form .form-control {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.5);
            color: #ffffff;
            height: 44px;
            border-radius: 6px;
        }

        .profile-form .form-control::placeholder {
            color: #777;
        }

        .profile-form .form-group {
            margin-bottom: 18px;
        }

        .profile-messages .success,
        .profile-messages .error {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .profile-messages .success {
            background: rgba(40, 167, 69, 0.12);
            border: 1px solid rgba(40, 167, 69, 0.45);
            color: #9fffa9;
        }

        .profile-messages .error {
            background: rgba(220, 53, 69, 0.12);
            border: 1px solid rgba(220, 53, 69, 0.5);
            color: #ffb3be;
        }

        .field-error {
            color: #ff9aa2;
            font-size: 13px;
            margin-top: 4px;
            display: block;
        }

        .profile-actions {
            margin-top: 15px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .profile-actions .site-btn {
            width: auto;
            padding-left: 25px;
            padding-right: 25px;
        }

        .profile-actions .btn-danger {
            background: #dc3545;
            border: none;
        }

        .profile-actions .btn-danger:hover {
            background: #c82333;
        }

        /* AJOUT: Bouton photo stylé */
        .btn-neon {
            background: transparent;
            color: #00e7ff;
            border: 2px solid #00e7ff;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            text-shadow: 0 0 5px #00e7ff;
            box-shadow: 0 0 10px rgba(0, 231, 255, 0.3);
            cursor: pointer;
            display: inline-block;
            margin-bottom: 10px;
        }

        .btn-neon:hover {
            background: #00e7ff;
            color: #100028;
            box-shadow: 0 0 20px rgba(0, 231, 255, 0.6);
            transform: translateY(-2px);
        }

        /* Text colors - GARDER COMME AVANT */
        body,
        h1, h2, h3, h4, h5, h6,
        p, a, span, li {
            color: #ffffff !important;
        }

        i {
            color: #ffffff !important;
        }

        .feature-box h4 {
            color: #ffffff !important;
            margin-bottom: 10px;
        }

        .feature-box p {
            color: #c0c0c0 !important;
            margin-bottom: 15px;
        }

        .section-title.center-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-title span {
            color: #00bfe7 !important;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .section-title h2 {
            color: white !important;
            font-size: 34px;
            font-weight: 700;
            margin-top: 10px;
        }
    </style>
</head>

<body class="dark-homepage">
<!-- Page Preloder -->
<div id="preloder">
    <div class="loader"></div>
</div>

<!-- Header Section Begin - REMETTRE LE LOGO COMME AVANT -->
<header class="header">
    <div class="container">
        <div class="row">
            <div class="col-lg-2">
                              <div class="site-title1">
        <a href="./index.php">
            <span class="letter-a">A</span>
            <span class="letter-b">b</span>
            <span class="letter-l">l</span>
            <span class="letter-e">e</span>
            <span class="letter-link">Link</span>
        </a>
    </div>
    <style> .site-title1 {
       margin-top: 35px; /* Monte titre */

    padding: 0;
}

.site-title1 a {
    font-family: 'Josefin Sans', sans-serif;
    font-size: 32px;
    font-weight: 700;
    text-decoration: none;
    line-height: 1.2;
    display: inline-flex;
    gap: 2px;
    letter-spacing: 1px;
    text-transform: uppercase;
    transition: 0.3s ease;
}

/* ====== COULEURS MODERNES ====== */
.letter-a  { color: #ff4f5e; text-shadow: 0 0 8px rgba(255,79,94,0.6); }
.letter-b  { color: #4c8df5; text-shadow: 0 0 8px rgba(76,141,245,0.6); }
.letter-l  { color: #b87bff; text-shadow: 0 0 8px rgba(184,123,255,0.6); }
.letter-e  { color: #ffb247; text-shadow: 0 0 8px rgba(255,178,71,0.6); }
.letter-link { 
    color: #ffffff; 
    margin-left: 4px;
    text-shadow: 0 0 10px rgba(255,255,255,0.7);
}

/* ====== HOVER ANIMATION ====== */
.site-title1 a:hover {
    transform: scale(1.05);
    filter: drop-shadow(0 0 12px rgba(0, 187, 255, 0.6));
}

.site-title1 a span:hover {
    transform: translateY(-2px);
    display: inline-block;
    transition: 0.2s ease;
}</style>
            </div>

            <div class="col-lg-10">
                <div class="header__nav__option">

                    <!-- PROFILE BUTTON - GARDER COMME AVANT -->
                    <div class="profile-top">
                        <img src="<?php echo getPhotoUrl($user->getPhoto(), $user->getPrenom(), $user->getNom()); ?>" 
                             onclick="toggleProfileMenu()">

                        <div class="profile-menu" id="profileDropdown">
                            <div class="profile-menu-header">
                                <img src="<?php echo getPhotoUrl($user->getPhoto(), $user->getPrenom(), $user->getNom()); ?>">
                                <div>
                                    <h4><?php echo e($user->getPrenom() . ' ' . $user->getNom()); ?></h4>
                                    <span><?php echo e($user->getEmail()); ?></span>
                                </div>
                            </div>

                            <ul>
                                <?php if ($_SESSION['user_role'] === 'Entreprise'): ?>
                                    <li><a href="entreprise.php"><i class="fa fa-building"></i> Espace Entreprise</a></li>
                                <?php endif; ?>
                                <li><a href="#" onclick="showProfile(); return false;"><i class="fa fa-user"></i> Voir le profil</a></li>
                                <li><a href="#"><i class="fa fa-cog"></i> Paramètres du compte</a></li>
                                <li><a href="#"><i class="fa fa-bell"></i> Notifications</a></li>
                                <li><a href="#"><i class="fa fa-exchange"></i> Changer de compte</a></li>
                                <li><a href="#"><i class="fa fa-question-circle"></i> Centre d'aide</a></li>
                                <li><a class="logout-btn" href="logout.php"><i class="fa fa-sign-out"></i> Déconnexion</a></li>
                            </ul>
                        </div>
                    </div>

                    <div id="mobile-menu-wrap"></div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- Header End -->

<!-- ===================== PROFILE SECTION ===================== -->
<section class="profile-section spad" id="profile-section">
    <div class="container">
        <div class="section-title center-title">
           <span>Mon Compte</span>
           <h2>Paramètres du Profil</h2>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="profile-card">

                    <!-- Messages -->
                    <div class="profile-messages">
                        <?php if ($success): ?>
                            <div class="success"><?php echo e($success); ?></div>
                        <?php endif; ?>

                        <?php if (isset($errors['general'])): ?>
                            <div class="error"><?php echo e($errors['general']); ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- NOM + RÔLE SEULEMENT (sans avatar) -->
                    <div class="profile-name">
                        <?php echo e($user->getPrenom() . ' ' . $user->getNom()); ?>
                    </div>
                    <div class="profile-role">
                        <?php echo e($user->getRole()); ?>
                    </div>

                    <!-- Profile form AVEC FONCTIONNALITÉ PHOTO -->
                    <form method="POST" action="" class="profile-form" enctype="multipart/form-data">
                        <!-- SECTION PHOTO AJOUTÉE -->
                        <h4>Photo de Profil</h4>

                        <div class="form-group text-center">
                            <!-- Aperçu de la photo -->
                            <div class="profile-avatar-large mb-3">
                                <img id="photo-preview" 
                                     src="<?php echo getPhotoUrl($user->getPhoto(), $user->getPrenom(), $user->getNom()); ?>" 
                                     alt="Photo de profil" 
                                     style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #00bfe7;">
                            </div>
                            
                            <!-- Bouton personnalisé -->
                            <label for="photo" class="btn-neon">
                                <i class="fa fa-camera"></i> CHOISIR UNE PHOTO
                            </label>
                           
                            <!-- Input file caché -->
                            <input type="file" id="photo" name="photo" accept="image/jpeg,image/jpg,image/png,image/gif" 
                                   style="display: none;" onchange="previewPhoto(this)">
                            
                            <!-- Message d'information -->
                            <div id="file-info" style="color: #aaa; font-size: 12px; margin-top: 5px;">
                                Aucun fichier sélectionné
                            </div>
                            
                            <div style="color: #aaa; font-size: 12px; margin-top: 5px;">
                                Formats: JPG, PNG, GIF (max 2MB)
                            </div>
                            
                            <!-- Affichage des erreurs photo -->
                            <?php if (isset($errors['photo'])): ?>
                                <span class="field-error"><?php echo e($errors['photo']); ?></span>
                            <?php endif; ?>
                        </div>

                        <hr style="border-color: rgba(255,255,255,0.1); margin: 25px 0;">

                        <h4>Informations Personnelles</h4>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="prenom">Prénom</label>
                                    <input id="prenom" name="prenom"
                                           class="form-control"
                                           value="<?php echo e($user->getPrenom()); ?>">
                                    <?php if (isset($errors['prenom'])): ?>
                                        <span class="field-error"><?php echo e($errors['prenom']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nom">Nom</label>
                                    <input id="nom" name="nom"
                                           class="form-control"
                                           value="<?php echo e($user->getNom()); ?>">
                                    <?php if (isset($errors['nom'])): ?>
                                        <span class="field-error"><?php echo e($errors['nom']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Adresse Email</label>
                            <input id="email" name="email"
                                   class="form-control"
                                   value="<?php echo e($user->getEmail()); ?>">
                            <?php if (isset($errors['email'])): ?>
                                <span class="field-error"><?php echo e($errors['email']); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="telephone">Numéro de Téléphone</label>
                            <input id="telephone" name="telephone"
                                   class="form-control"
                                   value="<?php echo e($user->getTelephone()); ?>"
                                   placeholder="Enter your phone number">
                        </div>

                        <hr style="border-color: rgba(255,255,255,0.1); margin: 25px 0;">

                        <h4>Changer le Mot de Passe (optionnel)</h4>

                        <div class="form-group">
                            <label for="current_password">Mot de Passe Actuel</label>
                            <input type="password" id="current_password" name="current_password"
                                   class="form-control" placeholder="Enter current password">
                            <?php if (isset($errors['current_password'])): ?>
                                <span class="field-error"><?php echo e($errors['current_password']); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="new_password">Nouveau Mot de Passe</label>
                                    <input type="password" id="new_password" name="new_password"
                                           class="form-control" placeholder="Enter new password">
                                    <?php if (isset($errors['new_password'])): ?>
                                        <span class="field-error"><?php echo e($errors['new_password']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="confirm_password">Confirmer le Nouveau Mot de Passe</label>
                                    <input type="password" id="confirm_password" name="confirm_password"
                                           class="form-control" placeholder="Confirm new password">
                                    <?php if (isset($errors['confirm_password'])): ?>
                                        <span class="field-error"><?php echo e($errors['confirm_password']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="profile-actions">
                            <button type="submit" class="site-btn">Mettre à Jour le Profil</button>
                            <button type="button" class="site-btn" style="background:#00bfe7; border:none;" onclick="hideProfile()">Retour au Dashboard</button>

                            <?php if ($_SESSION['user_role'] !== 'admin'): ?>
                                <a href="delete_account.php"
                                   class="site-btn btn-danger"
                                   onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">
                                   Supprimer le Compte
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</section>
<!-- ===================== PROFILE SECTION END ===================== -->

<!-- ===================== ENTREPRISE DASHBOARD SECTION ===================== -->
<section class="entreprise-section spad" id="entreprise-section">
    <div class="container">
        <div class="entreprise-header">
            <h2>Espace Entreprise</h2>
            <p style="color:#adadad">Gérez votre entreprise, publiez vos offres, et trouvez les meilleurs talents.</p>
        </div>

        <div class="entreprise-card">
            <h3>Bienvenue, <?php echo e($user->getPrenom() . " " . $user->getNom()); ?></h3>
            <p style="color:#c0c0c0">Voici votre tableau de bord entreprise.</p>

            <br>

            <div class="row">
                <!-- POST A JOB -->
                <div class="col-lg-4">
                    <div class="feature-box">
                        <h4><i class="fa fa-briefcase"></i> Publier une Offre</h4>
                        <p>Créez des annonces d'emploi accessibles aux candidats.</p>
                        <a href="#" class="site-btn mt-3">Créer une Offre</a>
                    </div>
                </div>

                <!-- MANAGE APPLICATIONS -->
                <div class="col-lg-4">
                    <div class="feature-box">
                        <h4><i class="fa fa-users"></i> Candidatures</h4>
                        <p>Consultez les candidats qui ont postulé à vos offres.</p>
                        <a href="#" class="site-btn mt-3">Voir les Candidatures</a>
                    </div>
                </div>

                <!-- COMPANY INFO -->
                <div class="col-lg-4">
                    <div class="feature-box">
                        <h4><i class="fa fa-building" ></i> Profil Entreprise</h4>
                        <p>Mettez à jour les informations de votre entreprise.</p>
                        <a href="#" onclick="showProfile()" class="site-btn mt-3">Modifier Profil</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ===================== ENTREPRISE DASHBOARD SECTION END ===================== -->

<!-- Scripts -->
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.magnific-popup.min.js"></script>
<script src="js/mixitup.min.js"></script>
<script src="js/masonry.pkgd.min.js"></script>
<script src="js/jquery.slicknav.js"></script>
<script src="js/owl.carousel.min.js"></script>
<script src="js/main.js"></script>

<script>
function toggleProfileMenu() {
    const menu = document.getElementById("profileDropdown");
    menu.style.display = menu.style.display === "block" ? "none" : "block";
}

function showProfile() {
    // Hide entreprise section
    document.getElementById("entreprise-section").style.display = "none";
    // Show profile section
    document.getElementById("profile-section").style.display = "block";
    // Close profile menu if open
    document.getElementById("profileDropdown").style.display = "none";
    // Scroll to top smoothly
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

function hideProfile() {
    // Show entreprise section
    document.getElementById("entreprise-section").style.display = "block";
    // Hide profile section
    document.getElementById("profile-section").style.display = "none";
    // Scroll to top smoothly
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// FONCTION POUR LA PHOTO AJOUTÉE
function previewPhoto(input) {
    const fileInfo = document.getElementById('file-info');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById('photo-preview').src = e.target.result;
        }
        reader.readAsDataURL(file);
        
        // Affiche le nom du fichier
        fileInfo.textContent = 'Fichier sélectionné: ' + file.name;
        fileInfo.style.color = '#00bfe7';
        
    } else {
        fileInfo.textContent = 'Aucun fichier sélectionné';
        fileInfo.style.color = '#aaa';
    }
}

// Close profile menu when clicking outside
document.addEventListener('click', function(event) {
    const profileMenu = document.getElementById('profileDropdown');
    const profileImg = document.querySelector('.profile-top img');
    
    if (!profileMenu.contains(event.target) && event.target !== profileImg) {
        profileMenu.style.display = 'none';
    }
});

// Auto-show profile section if there are form submissions
<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
document.addEventListener('DOMContentLoaded', function() {
    showProfile();
});
<?php endif; ?>
</script>

</body>
</html>