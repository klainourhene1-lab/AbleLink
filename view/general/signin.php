<?php
session_start();
require_once __DIR__ . '/../../Control/UserController.php';
require_once __DIR__ . '/../../Model/User.php';


$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $controller = new UserController();
    $user = $controller->getUserByEmail($email);

    // ----------- EMAIL NOT FOUND ------------
    if (!$user) {
        $errors['email'] = "Email does not exist";
    } 
    else {

        // ----------- PASSWORD WRONG ------------
        if (!$user->verifyPassword($password)) {
            $errors['password'] = "Incorrect password";

            error_log("Password verification failed for: " . $email);
            error_log("Stored hash: " . $user->getMotDePasse());
            error_log("Input password: " . $password);
        } 
        else {

            // ----------- CHECK BANNED USER ------------
            if ($user->getStatut() === 'banni') {
                $errors['general'] = "Votre compte a été banni. Veuillez contacter l'administration.";
            } 
            else {
// ----------- REMEMBER ME Email + Password ------------
if (isset($_POST['remember'])) {
    setcookie('user_email', $email, time() + (30 * 24 * 60 * 60), "/");
    setcookie('user_password', $password, time() + (30 * 24 * 60 * 60), "/");
} else {
    setcookie('user_email', '', time() - 3600, "/");
    setcookie('user_password', '', time() - 3600, "/");
}

                // ----------- LOGIN SUCCESSFUL ------------
                $_SESSION['user_id'] = $user->getId();
                
                // Check if user is admin in database and force role
                if ($user->getRole() === 'Admin') {
                    $_SESSION['user_role'] = 'Admin';
                } else {
                    $_SESSION['user_role'] = $user->getRole();
                }

                error_log("Login successful for: " . $email . " with role: " . $user->getRole());

                // ----------- REMEMBER ME SIMPLE (email) ------------
if (isset($_POST['remember'])) {
    setcookie('user_email', $email, time() + (30 * 24 * 60 * 60), "/");
} else {
    // Supprimer cookie si décoché
    setcookie('user_email', '', time() - 3600, "/");
}

                // ----------- REDIRECTION BY ROLE ------------
                switch($_SESSION['user_role']) {
                    case "Admin":
                        header("Location: ../../Control/admin_dashboard.php");
                        exit;

                    case "Entreprise":
                        // Redirect to events page
                        header("Location: entreprise.php");
                        exit;

                    case "Utilisateur":
                        // Redirect to events page
                        header("Location: profile.php");
                        exit;

                    default:
                        // Default redirect to events page
                        header("Location: profile.php");
                        exit;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    
    <meta charset="UTF-8">
    <meta name="description" content="AbleLink - Connexion">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AbleLink - Connexion</title>

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

    <!-- Sign-in Custom CSS -->
    <style>
        .auth-wrapper {min-height: 100vh;background: #100028;display: flex;align-items: center;padding: 60px 15px;}
        .auth-card {display: flex;width: 100%;max-width: 950px;margin: 0 auto;background: rgba(16, 0, 40, 0.92);border: 1px solid #2a2144;border-radius: 14px;overflow: hidden;box-shadow: 0 10px 40px rgba(0,0,0,0.35);}
        .auth-image {width: 50%;}
        .auth-image img {width: 100%;height: 100%;object-fit: cover;}
        .auth-form {width: 50%;padding: 50px 40px;}
        .auth-form h2 {color: #fff;font-size: 28px;font-weight: 700;margin-bottom: 10px;}
        .auth-form p {color: #adadad;margin-bottom: 25px;}
        .auth-form label {color: #fff;font-size: 14px;margin-bottom: 5px;}
        .auth-form .form-control {background: transparent;border: 1px solid rgba(255,255,255,0.5);height: 45px;border-radius: 6px;color: #fff;}
        .auth-form .form-control::placeholder {color: #777;}
        .auth-extra {display: flex;justify-content: space-between;margin-bottom: 20px;font-size: 14px;color: white;}
        .auth-extra a {color: #00bfe7;}
        .auth-footer {text-align: center;margin-top: 20px;color: #bbb;}
        .auth-footer a {color: #00bfe7;}
        .error {color: #ff6b6b; margin-top: 5px; font-size: 12px;}
        .error-box {background: rgba(255,107,107,0.2);border: 1px solid rgba(255,107,107,0.3);padding: 10px;border-radius: 6px;margin-bottom: 15px;color: #ff6b6b;text-align:center;}
        .site-btn {background: #00bfe7;border: none;color: white;padding: 12px 25px;border-radius: 6px;font-weight: 600;transition: all 0.3s ease;}
        .site-btn:hover {background: #0099c7;transform: translateY(-2px);}
        @media (max-width: 768px) {.auth-card {flex-direction: column;}.auth-image, .auth-form {width: 100%;}}
    </style>
</head>

    <style>
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
             /* POUR PUSHER ENCORE PLUS À DROITE */
            position: relative;
            right: -300px; /* ZID HEDHI BECH TZID AL IMIN */
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
        }    /* Version simple - effet d'ombre bleue */
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
        transition: all 0.4s ease;
    }

    .auth-card:hover {
        box-shadow: 
            0 0 25px rgba(0, 191, 231, 0.4),  /* Bleu clair */
            0 0 35px rgba(58, 76, 237, 0.25), /* Bleu foncé */
            0 15px 45px rgba(0, 0, 0, 0.5);   /* Ombre noire */
        border-color: rgba(0, 191, 231, 0.6);
        transform: translateY(-3px);
    }

    /* Effet sur les bordures des champs */
    .auth-form .form-control {
        background: transparent;
        border: 1px solid rgba(255,255,255,0.5);
        height: 45px;
        border-radius: 6px;
        color: #fff;
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
 
    </style>
<body>
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
       margin-top: 10px; /* Monte titre */

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
    <li class="active"><a href="./index.php">Accueil</a></li>
    <li><a href="./about.php">À propos</a></li>
    <li><a href="./services.php">Services</a></li>
    <li><a href="./contact.php">Contact</a></li>

    <li class="auth-btns">
        <a href="./signin.php" class="active" class="btn-nav btn-login">Connexion</a>
        <a href="./signup.php" class="btn-nav btn-register">Inscription</a>
    </li>
</ul>

    <style>
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
             /* POUR PUSHER ENCORE PLUS À DROITE */
            position: relative;
            right: -300px; /* ZID HEDHI BECH TZID AL IMIN */
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
    </style>

                         
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="auth-wrapper">
        <div class="auth-card">
            <!-- Image à gauche -->
            <div class="auth-image">
                <img src="img/hero/new-hero.jpg" alt="">
            </div>

            <!-- Formulaire -->
            <div class="auth-form">
                <h2>Bienvenue</h2>
                <p>Connectez-vous pour accéder à votre compte AbleLink</p>

                <?php if (isset($errors['general'])): ?>
                    <div class="error-box"><?php echo $errors['general']; ?></div>
                <?php endif; ?>

                <form action="" method="POST">
                    
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="text" class="form-control" name="email" data-label="email"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : (isset($_COOKIE['user_email']) ? $_COOKIE['user_email'] : ''); ?>"
                            placeholder="vous@example.com">
                        <?php if (isset($errors['email'])): ?>
                            <div class="error"><?php echo $errors['email']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
    <label>Mot de passe</label>
    <div class="password-wrapper">
       <input 
    type="password" data-label="mot de passe"
    class="form-control"
    name="password"
    id="passwordField"
    placeholder="Votre mot de passe"
    value=""
    autocomplete="new-password"
/>

        
        <button type="button" class="password-toggle">
            <i class="fa fa-eye"></i>
        </button>
    </div>

    <?php if (isset($errors['password'])): ?>
        <div class="error"><?php echo $errors['password']; ?></div>
    <?php endif; ?>
</div>
 <style>
    /* Style pour le bouton masquer/afficher le mot de passe */
    .password-wrapper {
        position: relative;
        width: 100%;
    }
    
    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        color: rgba(255, 255, 255, 0.5);
        cursor: pointer;
        padding: 6px;
        z-index: 10;
        font-size: 18px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .password-toggle:hover {
        color: #00bfe7;
        background: rgba(0, 191, 231, 0.15);
        box-shadow: 0 0 10px rgba(0, 191, 231, 0.3);
    }
    
    .password-toggle:active {
        transform: translateY(-50%) scale(0.9);
    }
    
    .password-toggle i {
        transition: transform 0.3s ease;
    }
    
    .password-toggle:hover i {
        transform: scale(1.1);
    }
    
    /* Quand le mot de passe est visible */
    .password-toggle .fa-eye-slash {
        color: #00bfe7;
    }
    
    /* Ajuster le padding du champ mot de passe pour faire place au bouton */
    .password-wrapper .form-control {
        padding-right: 45px !important;
    }
</style>
        <script>
    // Fonction pour masquer/afficher le mot de passe
function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const toggleButton = passwordField.nextElementSibling;
    const eyeIcon = toggleButton.querySelector('i');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        eyeIcon.className = 'fa fa-eye-slash';
        toggleButton.setAttribute('title', 'Masquer le mot de passe');
    } else {
        passwordField.type = 'password';
        eyeIcon.className = 'fa fa-eye';
        toggleButton.setAttribute('title', 'Afficher le mot de passe');
    }
}

// Alternative avec événements au lieu de onclick inline (plus moderne)
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter les événements aux boutons existants
    const toggleButtons = document.querySelectorAll('.password-toggle');
    toggleButtons.forEach(button => {
        // Enlever l'ancien onclick
        button.removeAttribute('onclick');
        
        // Ajouter le nouvel événement
        button.addEventListener('click', function() {
            const passwordField = this.previousElementSibling;
            const eyeIcon = this.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.className = 'fa fa-eye-slash';
                this.setAttribute('title', 'Masquer le mot de passe');
            } else {
                passwordField.type = 'password';
                eyeIcon.className = 'fa fa-eye';
                this.setAttribute('title', 'Afficher le mot de passe');
            }
        });
    });
    
    // Ajouter un tooltip par défaut
    toggleButtons.forEach(button => {
        button.setAttribute('title', 'Afficher le mot de passe');
    });
});
</script>
        
                 

                    <div class="auth-extra">
                        <div>
                            <input type="checkbox" name="remember" id="remember"
                                <?php echo isset($_COOKIE['user_email']) ? 'checked' : ''; ?>>
                            <label for="remember">Se souvenir de moi</label>
                        </div>
                       <a href="forgot-password.php">Mot de passe oublié ?</a>
                    </div>
                  

                    <button type="submit" class="site-btn w-100">Connexion</button>
           <a href="google.php" class="google-btn-neon">
    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg">
    Continue with Google
</a>


<style>.google-btn-neon {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;

    width: 100%;
    padding: 10px 1px;

    background: rgba(255, 255, 255, 0.06); /* خلفية مرخوفة */
    border: 2px solid rgba(255, 255, 255, 0.15);
    border-radius: 10px;

    color: #fff;
    font-size: 16px;
    font-weight: 600;
    text-decoration: none;

    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
margin-top: 7px;
    transition: 0.25s ease-in-out;
}

.google-btn-neon img {
    width: 20px;
    height: 20px;
    filter: brightness(1.2);
}

.google-btn-neon:hover {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(0, 178, 255, 0.6);
    box-shadow: 0 0 18px rgba(0, 178, 255, 0.45);
    transform: translateY(-2px);
}

.google-btn-neon:active {
    transform: scale(0.97);
    box-shadow: 0 0 10px rgba(0, 178, 255, 0.35);
}
</style>

                </form>

                <div class="auth-footer">
                    Vous n'avez pas de compte ? <a href="signup.php">Créer un compte</a>
                </div>
            </div>
        </div>
    </section>
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
