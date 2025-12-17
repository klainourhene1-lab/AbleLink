<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../models/User.php';

// Fonction pour générer un CAPTCHA simple
function generateCaptcha() {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $captcha = '';
    for ($i = 0; $i < 5; $i++) {
        $captcha .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $captcha;
}

// Générer le CAPTCHA si pas déjà fait
if (!isset($_SESSION['captcha'])) {
    $_SESSION['captcha'] = generateCaptcha();
}

$errors = [];
$success = "";

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération sécurisée des champs
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $user_type = trim($_POST['user_type'] ?? '');
    $captcha_input = trim($_POST['captcha'] ?? '');

    // ======================
    //     VALIDATION PHP
    // ======================

    // CAPTCHA Validation
    if (empty($captcha_input)) {
        $errors['captcha'] = "Veuillez entrer le code CAPTCHA.";
    } elseif (!isset($_SESSION['captcha']) || $captcha_input != $_SESSION['captcha']) {
        $errors['captcha'] = "Code CAPTCHA incorrect. Veuillez réessayer.";
        $_SESSION['captcha'] = generateCaptcha();
    }

    // Nom
    if (empty($nom)) {
        $errors['nom'] = "Le nom est obligatoire.";
    } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s-]+$/', $nom)) {
        $errors['nom'] = "Le nom ne doit contenir que des lettres.";
    }

    // Prénom
    if (empty($prenom)) {
        $errors['prenom'] = "Le prénom est obligatoire.";
    } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s-]+$/', $prenom)) {
        $errors['prenom'] = "Le prénom ne doit contenir que des lettres.";
    }

    // Email
    if (empty($email)) {
        $errors['email'] = "L'email est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Format email invalide.";
    }

    // Téléphone (format tunisien)
    if (!empty($telephone)) {
        if (!preg_match('/^(2|4|5|9)[0-9]{7}$/', $telephone)) {
            $errors['telephone'] = "Numéro de téléphone invalide (format tunisien).";
        }
    }

    // Password
    if (empty($password)) {
        $errors['password'] = "Le mot de passe est obligatoire.";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Le mot de passe doit contenir au moins 6 caractères.";
    }

    // Confirm Password
    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Veuillez confirmer votre mot de passe.";
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = "Les mots de passe ne correspondent pas.";
    }

    // Role
    if (empty($user_type)) {
        $errors['user_type'] = "Sélectionnez un rôle.";
    } elseif ($user_type === "Admin") {
        $errors['user_type'] = "Vous ne pouvez pas créer un compte Admin.";
    }

    // Vérifier si email existe déjà
    if (empty($errors)) {
        $controller = new UserController();
        $existingUser = $controller->getUserByEmail($email);

        if ($existingUser) {
            $errors['email'] = "Cet email existe déjà.";
        }
    }

    // ======================
    //  INSERTION SI OK
    // ======================
    if (empty($errors)) {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Créer objet user
        $user = new User(
            null,
            $nom,
            $prenom,
            $email,
            $telephone,
            $hashedPassword,
            ucfirst($user_type)
        );

        // Enregistrer
        if ($controller->addUser($user)) {
            $success = "Compte créé avec succès ! Vous pouvez vous connecter.";
            $_SESSION['captcha'] = generateCaptcha();
        } else {
            $errors['general'] = "Une erreur est survenue lors de la création du compte.";
        }
    } else {
        $_SESSION['captcha'] = generateCaptcha();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="AbleLink - Inscription">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AbleLink - Inscription</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Template CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/elegant-icons.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/slicknav.min.css">
    <link rel="stylesheet" href="css/style.css">

    <!-- Custom SIGN-UP Styling -->
    <style>
        .auth-wrapper {
            min-height: 100vh;
            background: #100028;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 130px 15px;
        }

        .auth-card {
            display: flex;
            flex-direction: row;
            width: 100%;
            max-width: 950px;
            min-height: 200px;
            background: rgba(16, 0, 40, 0.92);
            border: 1px solid #2a2144;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.35);
        }

        .auth-image {
            width: 50%;
        }

        .auth-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .auth-form {
            width: 50%;
            padding: 45px 40px;
        }

        .auth-form h2 {
            color: #fff;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .auth-form p {
            color: #adadad;
            margin-bottom: 25px;
        }

        .auth-form label {
            color: #fff;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .auth-form .form-control {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.5);
            color: #fff;
            height: 45px;
            border-radius: 6px;
        }

        .auth-form .form-control::placeholder {
            color: #777;
        }

        .auth-footer {
            margin-top: 20px;
            text-align: center;
            color: #bbb;
        }
      
        .auth-footer a {
            color: #00bfe7;
        }

        #select {
            background: #060a30ff !important;
            color: #fff !important;
            border: 1px solid #020009ff !important;
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 15px;
            appearance: none;
            outline: none;
            transition: 0.2s ease;
        }

        /* CAPTCHA Styling */
        .captcha-container {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .captcha-code {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 12px 20px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 20px;
            letter-spacing: 3px;
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
            min-width: 120px;
            text-align: center;
            font-family: 'Courier New', monospace;
        }

        .refresh-captcha {
            background: none;
            border: none;
            color: #00bfe7;
            font-size: 20px;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .refresh-captcha:hover {
            background: rgba(0, 191, 231, 0.1);
            transform: rotate(90deg);
        }

        @media (max-width: 768px) {
            .auth-card { flex-direction: column; }
            .auth-image, .auth-form { width: 100%; }
            .captcha-container {
                flex-direction: column;
                gap: 10px;
            }
        }

        .error { color: #ff6b6b; font-size: 14px; display: block; margin-top: 5px; }
        .success { background: rgba(16,185,129,0.2); color: #34d399; padding: 10px; border-radius: 6px; text-align:center; margin-bottom: 15px; }
        
        /* Password Strength Meter */
        .password-strength {
            margin-top: 8px;
            height: 8px;
            width: 100%;
            border-radius: 5px;
            background: rgba(255,255,255,0.2);
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease;
        }

        .strength-weak { background: #ff4b4b; }
        .strength-medium { background: #f1c40f; }
        .strength-strong { background: #2ecc71; }

        .strength-label {
            margin-top: 5px;
            font-size: 13px;
            color: #ccc;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .strength-label i {
            font-size: 14px;
        }

        /* --- STYLES POUR LES BOUTONS CONNEXION/INSCRIPTION --- */
        
        /* Alignement de la navigation */
        .header__nav__menu ul {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 1px;
            width: 100%;
        }

        /* Boîte transparente avec bordure bleue */
        .auth-btns {
            display: flex !important;
            flex-direction: row !important;
            gap: 15px;
            align-items: center;
            margin-left: auto !important;
            border: 2px solid #3a4ced;
            border-radius: 12px;
            padding: 8px 15px;
            background-color: transparent;
            box-shadow: 0 3px 10px rgba(58, 76, 237, 0.1);
            position: relative;
            right: -300px;
        }

        /* Boutons */
        .btn-nav {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-block;
            width: 120px;
            text-align: center;
            transition: all 0.3s ease;
            font-size: 14px;
            letter-spacing: 0.3px;
            cursor: pointer;
            text-decoration: none;
            border: none;
        }

        .btn-login,
        .btn-register {
            background: #3a4ced;
            color: #fff;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        }

        .btn-login:hover,
        .btn-register:hover {
            background: #290667;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.25);
        }

        .hero__btn {
            display: inline-block;
            padding: 14px 36px;
            background: #070e4bff;
            border: 2px solid rgba(255, 255, 255, 0.38);
            color: #fff;
            font-size: 18px;
            border-radius: 14px;
            backdrop-filter: blur(6px);
            transition: 0.3s ease;
        }

        .hero__btn:hover {
            background: rgba(255, 255, 255, 0.25);
            border-color: #fff;
            transform: translateY(-3px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .auth-btns {
                gap: 10px;
                padding: 6px 12px;
            }
            
            .btn-nav {
                width: 100px;
                padding: 8px 15px;
                font-size: 13px;
            }
        }

        .site-btn {
            background: #00bfe7;
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .site-btn:hover {
            background: #0099c7;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

<!-- PRELOADER -->
<div id="preloder">
    <div class="loader"></div>
</div>

<!-- HEADER -->
<header class="header">
    <div class="container">
        <div class="row">

            <div class="col-lg-2">
                <div class="header__logo">
                                       <div class="site-title">
        <a href="./index.php">
            <span class="letter-a">A</span>
            <span class="letter-b">b</span>
            <span class="letter-l">l</span>
            <span class="letter-e">e</span>
            <span class="letter-link">Link</span>
        </a>
    </div>
    <style> .site-title {
       margin-top: -0px; /* Monte titre */

    padding: 0;
}

.site-title a {
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
.site-title a:hover {
    transform: scale(1.05);
    filter: drop-shadow(0 0 12px rgba(0, 187, 255, 0.6));
}

.site-title a span:hover {
    transform: translateY(-2px);
    display: inline-block;
    transition: 0.2s ease;
}</style>
                </div>
            </div>

            <div class="col-lg-10">
                <div class="header__nav__option">
                    <nav class="header__nav__menu mobile-menu">
                        <ul class="main-nav">
                            <li><a href="./index.php">Accueil</a></li>
                            <li><a href="./about.php">À propos</a></li>
                            <li><a href="./services.php">Services</a></li>
                            <li><a href="./contact.php">Contact</a></li>

                            <li class="auth-btns">
                                <a href="./signin.php" class="btn-nav btn-login">Connexion</a>
                                <a href="./signup.php"  class="active" class="btn-nav btn-register active">Inscription</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

        </div>
    </div>
</header>

<!-- SIGN-UP SECTION -->
<section class="auth-wrapper">

    <div class="auth-card">

        <div class="auth-image">
            <img src="img/hero/new-hero.jpg" alt="">
        </div>

        <div class="auth-form">

            <h2>Créer un compte</h2>
            <p>Rejoignez AbleLink et accédez à des opportunités inclusives.</p>

            <?php if (!empty($success)): ?>
                <div class="success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (isset($errors['general'])): ?>
                <div class="error"><?php echo $errors['general']; ?></div>
            <?php endif; ?>

            <form method="POST" id="signupForm">

                <div class="mb-3">
                    <label>Nom</label>
                    <input type="text" name="nom" class="form-control" placeholder="Votre nom"
                           value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>">
                    <?php if (isset($errors['nom'])): ?><span class="error"><?php echo $errors['nom']; ?></span><?php endif; ?>
                </div>

                <div class="mb-3">
                    <label>Prénom</label>
                    <input type="text" name="prenom" class="form-control" placeholder="Votre prénom"
                           value="<?php echo htmlspecialchars($_POST['prenom'] ?? ''); ?>">
                    <?php if (isset($errors['prenom'])): ?><span class="error"><?php echo $errors['prenom']; ?></span><?php endif; ?>
                </div>

                <div class="mb-3">
                    <label>Adresse Email</label>
                    <input type="text" name="email" class="form-control" placeholder="vous@example.com"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    <?php if (isset($errors['email'])): ?><span class="error"><?php echo $errors['email']; ?></span><?php endif; ?>
                </div>

                <div class="mb-3">
                    <label>Téléphone</label>
                    <input type="text" name="telephone" class="form-control" placeholder="+216 xx xxx xxx"
                           value="<?php echo htmlspecialchars($_POST['telephone'] ?? ''); ?>">
                    <?php if (isset($errors['telephone'])): ?><span class="error"><?php echo $errors['telephone']; ?></span><?php endif; ?>
                </div>

                <div class="mb-3">
                    <label>Mot de passe</label>
                    <input type="password" name="password" id="passwordField"
                           class="form-control" placeholder="Créer un mot de passe">

                    <!-- BARRE DE FORCE DU MOT DE PASSE -->
                    <div class="password-strength">
                        <div id="strengthBar" class="password-strength-bar"></div>
                    </div>
                    <div class="strength-label" id="strengthLabel">
                        <i class="fa fa-lock"></i> Sécurité du mot de passe
                    </div>

                    <?php if (isset($errors['password'])): ?>
                        <span class="error"><?php echo $errors['password']; ?></span>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label>Confirmer le mot de passe</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Répéter le mot de passe">
                    <?php if (isset($errors['confirm_password'])): ?><span class="error"><?php echo $errors['confirm_password']; ?></span><?php endif; ?>
                </div>

                <div class="mb-3">
                    <label>Je suis :</label>
                    <select name="user_type" class="form-control" id="select">
                        <option value="">Sélectionnez votre rôle</option>
                        <option value="Utilisateur" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] == 'Utilisateur') ? 'selected' : ''; ?>>Utilisateur</option>
                        <option value="Entreprise" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] == 'Entreprise') ? 'selected' : ''; ?>>Entreprise</option>
                        <option value="Admin" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                    <?php if (isset($errors['user_type'])): ?><span class="error"><?php echo $errors['user_type']; ?></span><?php endif; ?>
                </div>

                <!-- CAPTCHA Simple -->
                <div class="mb-3">
                    <label>Code de vérification :</label>
                    <div class="captcha-container">
                        <div class="captcha-code" id="captchaDisplay"><?php echo isset($_SESSION['captcha']) ? $_SESSION['captcha'] : 'ERROR'; ?></div>
                        <button type="button" class="refresh-captcha" onclick="refreshCaptcha()" title="Rafraîchir le code">
                            🔄
                        </button>
                    </div>
                    <input type="text" name="captcha" class="form-control" placeholder="Entrez le code ci-dessus" maxlength="5">
                    <?php if (isset($errors['captcha'])): ?>
                        <span class="error"><?php echo $errors['captcha']; ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="site-btn w-100 mt-2">Créer le compte</button>
            </form>

            <div class="auth-footer">
                Vous avez déjà un compte ? <a href="signin.php">Se connecter</a>
            </div>

        </div>

    </div>

</section>

<!-- JavaScript pour le CAPTCHA et la validation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const passwordInput = document.querySelector('input[name="password"]');
    const confirmPasswordInput = document.querySelector('input[name="confirm_password"]');
    const userTypeSelect = document.querySelector('select[name="user_type"]');
    const captchaInput = document.querySelector('input[name="captcha"]');
    
    // Fonction pour rafraîchir le CAPTCHA
    window.refreshCaptcha = function() {
        fetch('refresh_captcha.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('captchaDisplay').textContent = data.captcha;
                captchaInput.value = '';
                hideFieldError(captchaInput);
            })
            .catch(error => {
                console.error('Erreur lors du rafraîchissement du CAPTCHA:', error);
            });
    }
    
    function showFieldError(field, message) {
        hideFieldError(field);
        
        const errorElement = document.createElement('span');
        errorElement.className = 'error';
        errorElement.textContent = message;
        errorElement.style.display = 'block';
        errorElement.style.marginTop = '5px';
        
        field.parentNode.appendChild(errorElement);
        field.style.borderColor = '#ff6b6b';
    }
    
    function hideFieldError(field) {
        const existingError = field.parentNode.querySelector('.error');
        if (existingError) {
            existingError.remove();
        }
        field.style.borderColor = '';
    }
    
    function validatePassword() {
        const password = passwordInput.value;
        
        if (password.length > 0 && password.length < 6) {
            showFieldError(passwordInput, 'Le mot de passe doit contenir au moins 6 caractères');
            return false;
        }
        
        hideFieldError(passwordInput);
        return true;
    }
    
    function validatePasswordConfirmation() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (confirmPassword.length > 0 && password !== confirmPassword) {
            showFieldError(confirmPasswordInput, 'Les mots de passe ne correspondent pas');
            return false;
        }
        
        hideFieldError(confirmPasswordInput);
        return true;
    }
    
    function validateUserType() {
        if (userTypeSelect.value === 'Admin') {
            showFieldError(userTypeSelect, 'Vous ne pouvez pas créer un compte Admin.');
            return false;
        }
        
        if (userTypeSelect.value === '') {
            showFieldError(userTypeSelect, 'Veuillez sélectionner un rôle');
            return false;
        }
        
        hideFieldError(userTypeSelect);
        return true;
    }
    
    function validateCaptcha() {
        const captcha = captchaInput.value.trim();
        
        if (captcha.length === 0) {
            showFieldError(captchaInput, 'Veuillez entrer le code CAPTCHA');
            return false;
        }
        
        if (captcha.length !== 5) {
            showFieldError(captchaInput, 'Le code CAPTCHA doit contenir 5 caractères');
            return false;
        }
        
        hideFieldError(captchaInput);
        return true;
    }
    
    function validateForm() {
        let isValid = true;
        
        if (!validatePassword()) isValid = false;
        if (!validatePasswordConfirmation()) isValid = false;
        if (!validateUserType()) isValid = false;
        if (!validateCaptcha()) isValid = false;
        
        return isValid;
    }
    
    // Événements de validation en temps réel
    passwordInput.addEventListener('input', validatePassword);
    passwordInput.addEventListener('blur', validatePassword);
    
    confirmPasswordInput.addEventListener('input', validatePasswordConfirmation);
    confirmPasswordInput.addEventListener('blur', validatePasswordConfirmation);
    
    userTypeSelect.addEventListener('change', validateUserType);
    
    captchaInput.addEventListener('input', function() {
        if (this.value.length === 5) {
            validateCaptcha();
        }
    });
    
    // Validation finale avant soumission
    form.addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            
            const firstError = document.querySelector('.error');
            if (firstError) {
                firstError.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }
        }
    });
    
    // Auto-focus sur le premier champ si des erreurs existent
    if (document.querySelector('.error')) {
        setTimeout(() => {
            const firstEmptyField = document.querySelector('input:invalid, select:invalid') || 
                                  document.querySelector('input[value=""], select[value=""]');
            if (firstEmptyField) {
                firstEmptyField.focus();
            }
        }, 300);
    }
    
    // Animation des messages
    const messages = document.querySelectorAll('.success, .error');
    messages.forEach(message => {
        message.style.opacity = '0';
        message.style.transition = 'opacity 0.5s ease';
        
        setTimeout(() => {
            message.style.opacity = '1';
        }, 100);
    });
    
    // Nettoyage automatique des messages de succès
    setTimeout(() => {
        const successMessages = document.querySelectorAll('.success');
        successMessages.forEach(message => {
            message.style.transition = 'opacity 0.5s ease';
            message.style.opacity = '0';
            setTimeout(() => message.remove(), 500);
        });
    }, 5000);
});

// Password strength indicator
const passwordField = document.getElementById('passwordField');
const strengthBar = document.getElementById('strengthBar');
const strengthLabel = document.getElementById('strengthLabel');

passwordField.addEventListener('input', function() {
    const value = passwordField.value;
    let strength = 0;

    if (value.length >= 6) strength++;
    if (/[A-Z]/.test(value)) strength++;
    if (/[0-9]/.test(value)) strength++;
    if (/[\W]/.test(value)) strength++;

    if (strength === 0) {
        strengthBar.style.width = "0%";
        strengthLabel.innerHTML = `<i class="fa fa-lock"></i> Sécurité du mot de passe`;
    }

    if (strength === 1) {
        strengthBar.className = "password-strength-bar strength-weak";
        strengthBar.style.width = "33%";
        strengthLabel.innerHTML = `<i class="fa fa-lock"></i> Faible`;
    }

    if (strength === 2) {
        strengthBar.className = "password-strength-bar strength-medium";
        strengthBar.style.width = "66%";
        strengthLabel.innerHTML = `<i class="fa fa-lock"></i> Moyen`;
    }

    if (strength >= 3) {
        strengthBar.className = "password-strength-bar strength-strong";
        strengthBar.style.width = "100%";
        strengthLabel.innerHTML = `<i class="fa fa-lock"></i> Fort`;
    }
});
</script>

<!-- JS -->
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.magnific-popup.min.js"></script>
<script src="js/mixitup.min.js"></script>
<script src="js/masonry.pkgd.min.js"></script>
<script src="js/jquery.slicknav.js"></script>
<script src="js/owl.carousel.min.js"></script>
<script src="js/main.js"></script>

</body>
</html>