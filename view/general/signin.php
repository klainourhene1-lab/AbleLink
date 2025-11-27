<?php
session_start();
require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../models/User.php';

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

                // ----------- LOGIN SUCCESSFUL ------------
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['user_role'] = $user->getRole();

                error_log("Login successful for: " . $email . " with role: " . $user->getRole());

                // ----------- REDIRECTION BY ROLE ------------
                switch($user->getRole()) {
                    case "Admin":
                         header("Location: backoffice/index1.php");
                        exit;

                    case "Entreprise":
                        header("Location: entreprise.php");
                        exit;

                    case "Utilisateur":
                        header("Location: profile.php");
                        exit;

                    default:
                        header("Location: index.php");
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
                        <input type="text" class="form-control" name="email"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : (isset($_COOKIE['user_email']) ? $_COOKIE['user_email'] : ''); ?>"
                            placeholder="vous@example.com">
                        <?php if (isset($errors['email'])): ?>
                            <div class="error"><?php echo $errors['email']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label>Mot de passe</label>
                        <input type="password" class="form-control" name="password" placeholder="Votre mot de passe">
                        <?php if (isset($errors['password'])): ?>
                            <div class="error"><?php echo $errors['password']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="auth-extra">
                        <div>
                            <input type="checkbox" name="remember" id="remember"
                                <?php echo isset($_COOKIE['user_email']) ? 'checked' : ''; ?>>
                            <label for="remember">Se souvenir de moi</label>
                        </div>
                        <a href="#">Mot de passe oublié ?</a>
                    </div>

                    <button type="submit" class="site-btn w-100">Connexion</button>
                </form>

                <div class="auth-footer">
                    Vous n'avez pas de compte ? <a href="signup.php">Créer un compte</a>
                </div>
            </div>
        </div>
    </section>

</body>
</html>