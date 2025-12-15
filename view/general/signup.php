<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../Control/UserController.php';
require_once __DIR__ . '/../../Model/User.php';

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
        /* =============== FORM SECTION =============== */
        .auth-wrapper {
            min-height: 100vh;
            background: #100028;
            display: flex;
            align-items: center;
            padding: 150px 15px;
        }

        .auth-card {
            display: flex;
            width: 100%;
            max-width: 950px;
            margin: 0 auto;
            background: rgba(16, 0, 40, 0.92);
            border: 1px solid #2a2144;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.35);
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
            padding: 50px 40px;
            overflow-y: auto;
            max-height: 700px;
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
            font-size: 14px;
            margin-bottom: 5px;
        }

        .auth-form .form-control {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.5);
            height: 45px;
            border-radius: 6px;
            color: #fff;
        }

        .auth-form .form-control::placeholder {
            color: #777;
        }

        .auth-footer {
            text-align: center;
            margin-top: 20px;
            color: #bbb;
        }

        .auth-footer a {
            color: #00bfe7;
        }

        .error {
            color: #ff6b6b;
            margin-top: 5px;
            font-size: 12px;
        }

        .error-box {
            background: rgba(255,107,107,0.2);
            border: 1px solid rgba(255,107,107,0.3);
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            color: #ff6b6b;
            text-align: center;
        }

        .success {
            background: rgba(16,185,129,0.2);
            border: 1px solid rgba(16,185,129,0.3);
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            color: #34d399;
            text-align: center;
        }

        .site-btn {
            background: #00bfe7;
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }

        .site-btn:hover {
            background: #0099c7;
            transform: translateY(-2px);
        }

        /* Style pour le champ select */
        .auth-form select.form-control {
            background: transparent;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.5);
            height: 45px;
            border-radius: 6px;
            padding: 0 12px;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2300bfe7' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 12px;
            cursor: pointer;
        }

        .auth-form select.form-control:focus {
            border-color: #00bfe7;
            box-shadow: 0 0 0 2px rgba(0, 191, 231, 0.1);
        }

        .auth-form select.form-control option {
            background: #100028;
            color: #fff;
            padding: 10px;
        }

        /* Style pour le bouton masquer/afficher le mot de passe */
        .password-wrapper {
            position: relative;
            width: 100%;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            padding: 5px;
            z-index: 10;
            font-size: 16px;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .password-toggle:hover {
            color: #00bfe7;
            background: rgba(0, 191, 231, 0.1);
        }

        .password-toggle:active {
            transform: translateY(-50%) scale(0.95);
        }

        /* Ajuster le padding du champ mot de passe pour faire place au bouton */
        .password-wrapper .form-control {
            padding-right: 45px !important;
        }

        /* CAPTCHA Styling */
        .captcha-container {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
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
            background: rgba(0, 191, 231, 0.1);
            border: 1px solid #00bfe7;
            color: #00bfe7;
            font-size: 18px;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .refresh-captcha:hover {
            background: rgba(0, 191, 231, 0.2);
            transform: rotate(90deg);
        }

        /* Password Strength Meter */
        .password-strength {
            margin-top: 8px;
            height: 8px;
            width: 100%;
            border-radius: 5px;
            background: rgba(255,255,255,0.1);
            overflow: hidden;
            margin-bottom: 5px;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease;
            border-radius: 5px;
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

        /* Form spacing */
        .mb-3 {
            margin-bottom: 20px !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .auth-card {
                flex-direction: column;
            }
            
            .auth-image, .auth-form {
                width: 100%;
            }
            
            .auth-image {
                height: 250px;
            }
            
            .auth-form {
                padding: 30px 20px;
                max-height: none;
            }
            
            .captcha-container {
                flex-direction: column;
                gap: 10px;
                align-items: stretch;
            }
            
            .captcha-code {
                min-width: auto;
            }
            
            .refresh-captcha {
                align-self: center;
            }
        }

        /* Style la barre de défilement (scrollbar) */
        .auth-form::-webkit-scrollbar {
            width: 8px;
        }

        .auth-form::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 4px;
        }

        .auth-form::-webkit-scrollbar-thumb {
            background: #000000;
            border-radius: 4px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .auth-form::-webkit-scrollbar-thumb:hover {
            background: #222222;
        }

        /* Pour Firefox */
        .auth-form {
            scrollbar-width: thin;
            scrollbar-color: #000000 rgba(0, 0, 0, 0.3);
        }

        /* Version simple - effet d'ombre bleue */
        .auth-card {
            transition: all 0.4s ease;
        }

        .auth-card:hover {
            box-shadow: 
                0 0 25px rgba(0, 191, 231, 0.4),
                0 0 35px rgba(58, 76, 237, 0.25),
                0 15px 45px rgba(0, 0, 0, 0.5);
            border-color: rgba(0, 191, 231, 0.6);
            transform: translateY(-3px);
        }

        /* Effet sur les bordures des champs */
        .auth-form .form-control {
            transition: all 0.3s ease;
        }

        .auth-form .form-control:hover {
            border-color: #00bfe7;
            box-shadow: 0 0 8px rgba(0, 191, 231, 0.3);
        }

        .auth-form .form-control:focus {
            border-color: #3a4ced;
            box-shadow: 0 0 12px rgba(58, 76, 237, 0.4);
        }

        /* =============== HEADER =============== */
        .header__nav__menu ul {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 1px;
            width: 100%;
        }

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
                    <style> 
                        .site-title {
                            margin-top: 10px;
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
                        }
                    </style>
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
                                <a href="./signup.php" class="btn-nav btn-register active">Inscription</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

        </div>
    </div>
</header>
<!-- AI ACCESSIBILITY ASSISTANT -->
<div class="ai-assistant-btn" onclick="toggleAIAssistant()">
    <i class="fa fa-wheelchair"></i> Assistant
</div>

<div class="ai-box" id="aiBox">
    <div class="ai-header">Assistant AbleLink</div>

    <div class="ai-chat" id="aiChat">
        <div class="ai-msg">
            Bonjour 👋, je suis votre assistant AbleLink.<br>
            Je peux vous aider à créer un compte, remplir le formulaire ou comprendre les champs.
        </div>
    </div>

    <div class="ai-input">
        <input type="text" id="aiMessage" placeholder="Écrire un message...">
        <button type="button" onclick="sendAIMessage()">➤</button>
    </div>
</div>
<style>
    /* === AI Assistant Button === */
.ai-assistant-btn {
    position: fixed;
    bottom: 25px;
    right: 25px;
    background: #3a4ced;
    color: #fff;
    padding: 10px 16px;
    border-radius: 999px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0,0,0,0.4);
    display: flex;
    align-items: center;
    gap: 8px;
    z-index: 1000;
    transition: 0.25s;
}

.ai-assistant-btn i {
    font-size: 16px;
}

.ai-assistant-btn:hover {
    background: #290667;
    transform: translateY(-2px);
}

/* === AI Assistant Box === */
.ai-box {
    position: fixed;
    bottom: 80px;
    right: 25px;
    width: 320px;
    height: 420px;
    background: rgba(16, 0, 40, 0.96);
    border: 1px solid #3a4ced;
    border-radius: 14px;
    display: none;        /* مخفي في البداية */
    flex-direction: column;
    overflow: hidden;
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    box-shadow: 0 10px 40px rgba(0,0,0,0.6);
    z-index: 1000;
}

.ai-header {
    padding: 12px 15px;
    background: #3a4ced;
    color: #fff;
    font-weight: 600;
    font-size: 14px;
}

.ai-chat {
    flex: 1;
    padding: 10px;
    overflow-y: auto;
    font-size: 13px;
    color: #eee;
}

.ai-input {
    display: flex;
    padding: 8px;
    background: #140038;
    gap: 8px;
}

.ai-input input {
    flex: 1;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 8px;
    padding: 8px 10px;
    font-size: 13px;
    color: #fff;
}

.ai-input input::placeholder {
    color: #aaa;
}

.ai-input button {
    background: #3a4ced;
    border: none;
    border-radius: 8px;
    padding: 0 12px;
    font-size: 14px;
    color: #fff;
    cursor: pointer;
    transition: 0.2s;
}

.ai-input button:hover {
    background: #290667;
}

/* Messages */
.ai-msg, .user-msg {
    margin-bottom: 8px;
    padding: 8px 10px;
    border-radius: 8px;
    max-width: 90%;
    line-height: 1.4;
}

.ai-msg {
    background: rgba(58, 76, 237, 0.25);
    align-self: flex-start;
}

.user-msg {
    background: rgba(0, 191, 231, 0.25);
    align-self: flex-end;
    text-align: right;
}

</style>
<script>
function toggleAIAssistant() {
    const box = document.getElementById('aiBox');
    if (box.style.display === 'flex') {
        box.style.display = 'none';
    } else {
        box.style.display = 'flex';
    }
}
</script>
<script>
function addMessage(content, type) {
    const chat = document.getElementById('aiChat');
    const div = document.createElement('div');
    div.className = type === 'user' ? 'user-msg' : 'ai-msg';
    div.innerHTML = content;
    chat.appendChild(div);
    chat.scrollTop = chat.scrollHeight;
}

function sendAIMessage() {
    const input = document.getElementById('aiMessage');
    let text = input.value.trim();
    if (!text) return;

    // رسالة المستخدم
    addMessage(text, 'user');
    input.value = '';

    // إجابة بسيطة حسب المحتوى (نسخة أولية)
    let reply = "Je suis là pour vous aider à remplir le formulaire d'inscription.";

    if (text.toLowerCase().includes("inscription") || text.toLowerCase().includes("compte")) {
        reply = "Pour créer un compte : remplissez votre nom, prénom, email, téléphone, mot de passe et choisissez votre rôle. Je peux vous expliquer chaque champ.";
    } else if (text.toLowerCase().includes("mot de passe")) {
        reply = "Votre mot de passe doit contenir au moins 6 caractères. Vous pouvez aussi utiliser le bouton pour générer un mot de passe fort automatiquement.";
    } else if (text.toLowerCase().includes("email")) {
        reply = "Utilisez une adresse email valide, par exemple : nom.prenom@example.com.";
    } else if (text.toLowerCase().includes("rôle") || text.toLowerCase().includes("role")) {
        reply = "Choisissez 'Utilisateur' si vous êtes un candidat, 'Entreprise' si vous représentez une société. Le rôle 'Admin' est réservé à l'équipe AbleLink.";
    } else if (text.toLowerCase().includes("aide")) {
        reply = "Je peux vous aider avec : inscription, connexion, mot de passe, email, rôle, ou explication du CAPTCHA.";
    } else if (text.toLowerCase().includes("captcha")) {
        reply = "Recopiez le code affiché dans la boîte violette dans le champ en dessous. Si vous ne le lisez pas bien, cliquez sur le bouton 🔄 pour générer un nouveau code.";
    }

    setTimeout(() => {
        addMessage(reply, 'ai');
    }, 400);
}
</script>

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
<div class="profile-progress">
    <div class="profile-progress-top">
        <span class="profile-progress-label">Complétion du profil</span>
        <span id="profileProgressValue">0%</span>
    </div>

    <div class="profile-progress-bar">
        <div id="profileProgressBarInner"></div>
    </div>

    <small class="profile-progress-hint">
       
    </small>
</div>
<style>
.profile-progress {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.15);
    padding: 16px;
    border-radius: 10px;
    margin-bottom: 20px;
    color: #fff;
}

.profile-progress-top {
    display: flex;
    justify-content: space-between;
    margin-bottom: 6px;
}

.profile-progress-label {
    font-size: 14px;
    opacity: .8;
}

.profile-progress-bar {
    width: 100%;
    height: 8px;
    background: rgba(255,255,255,0.12);
    border-radius: 6px;
    overflow: hidden;
}

#profileProgressBarInner {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, #1a73e8, #4285f4, #00baff);
    transition: .3s;
    border-radius: 6px;
}

</style>
<script>
document.addEventListener("DOMContentLoaded", () => {

    const fields = [
        "nom",
        "prenom",
        "email",
        "telephone",
        "password",
        "confirm_password"
    ];

    function updateProfileProgress() {
        let filled = 0;

        fields.forEach(name => {
            const input = document.querySelector(`[name="${name}"]`);
            if (!input) return;

            if (name === "confirm_password") {
                const pass = document.querySelector('[name="password"]').value.trim();
                if (input.value.trim() !== "" && input.value.trim() === pass) {
                    filled++;
                }
            } else {
                if (input.value.trim() !== "") filled++;
            }
        });

        const percent = Math.round((filled / fields.length) * 100);

        // Update text + bar width
        document.getElementById("profileProgressValue").textContent = percent + "%";
        document.getElementById("profileProgressBarInner").style.width = percent + "%";

        // BLUE ONLY EFFECT (Glow)
        const bar = document.getElementById("profileProgressBarInner");
        bar.style.boxShadow = `0 0 12px rgba(0, 150, 255, ${percent/130})`;
    }

    // Update on input
    document.querySelectorAll("input").forEach(input =>
        input.addEventListener("input", updateProfileProgress)
    );

    updateProfileProgress();
});
</script>



            <form method="POST" id="signupForm">

                <div class="mb-3">
                    <label>Nom</label>
                    <input type="text" name="nom" class="form-control" placeholder="Votre nom"  data-label="Nom"
                           value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>">
                    <?php if (isset($errors['nom'])): ?><span class="error"><?php echo $errors['nom']; ?></span><?php endif; ?>
                </div>

                <div class="mb-3">
                    <label>Prénom</label>
                    <input type="text" name="prenom" class="form-control" placeholder="Votre prénom"  data-label="Prenom"
                           value="<?php echo htmlspecialchars($_POST['prenom'] ?? ''); ?>">
                    <?php if (isset($errors['prenom'])): ?><span class="error"><?php echo $errors['prenom']; ?></span><?php endif; ?>
                </div>

                <div class="mb-3">
                    <label>Adresse Email</label>
                    <input type="text" name="email" class="form-control" placeholder="vous@example.com"  data-label="Adresse email"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    <?php if (isset($errors['email'])): ?><span class="error"><?php echo $errors['email']; ?></span><?php endif; ?>
                </div>

                <div class="mb-3">
                    <label>Téléphone</label>
                    <input type="text" name="telephone" class="form-control" placeholder="+216 xx xxx xxx"  data-label="Telephone"
                           value="<?php echo htmlspecialchars($_POST['telephone'] ?? ''); ?>">
                    <?php if (isset($errors['telephone'])): ?><span class="error"><?php echo $errors['telephone']; ?></span><?php endif; ?>
                </div>

                <!-- Pour le champ "Mot de passe" -->
                <div class="mb-3">
    <label>Mot de passe</label>

    <div class="password-wrapper">

        <!-- Champ du mot de passe -->
        <input type="password" name="password" id="passwordField"  data-label="Mot de passe"
               class="form-control" placeholder="Créer un mot de passe">

        <!-- Bouton SHOW/HIDE -->
        <button type="button" class="password-toggle" id="togglePassword">
            <i class="fa fa-eye"></i>
        </button>

        <!-- Bouton AI : Générer mot de passe -->
        <button type="button" class="generate-btn" onclick="generatePassword()">
            🤖AI
        </button>
    </div>


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
<style>
    .generate-btn {
    position: absolute;
    right: 45px;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0, 191, 231, 0.15);
    border: 1px solid rgba(0, 191, 231, 0.3);
    color: #00c8ff;
    padding: 6px 10px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    transition: 0.25s ease;
}

.generate-btn:hover {
    background: rgba(0, 191, 231, 0.3);
    border-color: #00c8ff;
    color: #fff;
}

.generate-btn:active {
    transform: translateY(-50%) scale(0.95);
}

</style>
<script>
function generatePassword() {
    const words = ["Nova", "Pulse", "Sky", "Cyber", "Astra", "Flux", "Core", "Shadow", "Quantum", "Nebula"];
    const symbols = ["!", "#", "$", "%", "&", "?", "*"];
    
    const word1 = words[Math.floor(Math.random() * words.length)];
    const word2 = words[Math.floor(Math.random() * words.length)];
    const number = Math.floor(10 + Math.random() * 90);
    const symbol = symbols[Math.floor(Math.random() * symbols.length)];

    const password = word1 + word2 + symbol + number;

    document.getElementById("passwordField").value = password;

    // Trigger strength bar update
    passwordField.dispatchEvent(new Event("input"));
}
</script>

                <!-- Pour le champ "Confirmer le mot de passe" -->
                <div class="mb-3">
                    <label>Confirmer le mot de passe</label>
                    <div class="password-wrapper">
                        <input type="password" name="confirm_password" id="confirmPasswordField"  data-label="confirmer le mot de passe"
                               class="form-control" placeholder="Répéter le mot de passe">
                        <button type="button" class="password-toggle" id="toggleConfirmPassword">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                    <?php if (isset($errors['confirm_password'])): ?>
                        <span class="error"><?php echo $errors['confirm_password']; ?></span>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label>Je suis :</label>
                    <select name="user_type" class="form-control"  data-label="selectionnez votre role">
                        <option value="">Sélectionnez votre rôle</option>
                        <option value="Utilisateur" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] == 'Utilisateur') ? 'selected' : ''; ?>>Utilisateur</option>
                        <option value="Entreprise" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] == 'Entreprise') ? 'selected' : ''; ?>>Entreprise</option>
                        <option value="Admin" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                    <?php if (isset($errors['user_type'])): ?>
                        <span class="error"><?php echo $errors['user_type']; ?></span>
                    <?php endif; ?>
                </div>

                <!-- CAPTCHA Simple -->
                <div class="mb-3">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                        <label style="margin: 0;"  >Code de vérification :</label>
                        <a href="https://chat.openai.com/" target="_blank" 
                           style="color: #00bfe7; font-size: 14px; text-decoration: none;"
                           title="Aide pour le CAPTCHA">
                            <i class="fa fa-question-circle"></i> Aide
                        </a>
                    </div>
                    <div class="captcha-container">
                        <div class="captcha-code" id="captchaDisplay"><?php echo isset($_SESSION['captcha']) ? $_SESSION['captcha'] : 'ERROR'; ?></div>
                        <button type="button" class="refresh-captcha" onclick="refreshCaptcha()" title="Rafraîchir le code">
                            🔄
                        </button>
                    </div>
                    <input type="text" name="captcha" data-label="code de verification" class="form-control" placeholder="Entrez le code ci-dessus" maxlength="5">
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
    const togglePasswordBtn = document.getElementById('togglePassword');
    const toggleConfirmPasswordBtn = document.getElementById('toggleConfirmPassword');
    
    // Fonction pour masquer/afficher le mot de passe
    function togglePasswordVisibility(inputField, toggleButton) {
        const eyeIcon = toggleButton.querySelector('i');
        
        if (inputField.type === 'password') {
            inputField.type = 'text';
            eyeIcon.className = 'fa fa-eye-slash';
            toggleButton.setAttribute('title', 'Masquer le mot de passe');
        } else {
            inputField.type = 'password';
            eyeIcon.className = 'fa fa-eye';
            toggleButton.setAttribute('title', 'Afficher le mot de passe');
        }
    }
    
    // Événements pour les boutons d'affichage/masquage
    togglePasswordBtn.addEventListener('click', function() {
        togglePasswordVisibility(passwordInput, this);
    });
    
    toggleConfirmPasswordBtn.addEventListener('click', function() {
        togglePasswordVisibility(confirmPasswordInput, this);
    });
    
    // Ajouter les tooltips par défaut
    togglePasswordBtn.setAttribute('title', 'Afficher le mot de passe');
    toggleConfirmPasswordBtn.setAttribute('title', 'Afficher le mot de passe');
    
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
<script>
// الصوت
const synth = window.speechSynthesis;

function speak(text) {
    let utter = new SpeechSynthesisUtterance(text);
    utter.lang = "fr-FR"; // تبدلها عربي إذا تحب
    utter.rate = 1;
    utter.pitch = 1;

    synth.cancel(); // يلغي أي صوت قبل
    synth.speak(utter);
}

// كي تضغط على أي input → يقرأ اسمو
document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll("input, select").forEach(input => {
        
        input.addEventListener("focus", function() {
            const label = this.getAttribute("data-label");

            if (label) {
                speak(label);
            }
        });

    });

});
</script>

</body>
</html>