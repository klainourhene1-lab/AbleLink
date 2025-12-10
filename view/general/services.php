<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="AbleLink - Services d'inclusion professionnelle">
    <meta name="keywords" content="AbleLink, inclusion, handicap, emploi, accessibilité, recrutement inclusif">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AbleLink | Services</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/elegant-icons.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/slicknav.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div id="preloder">
        <div class="loader"></div>
    </div>

     <!-- Header Section Begin -->
   <!-- Header Begin -->
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
                        <nav class="header__nav__menu mobile-menu">
                            <ul class="main-nav">
    <li><a href="./index.php">Accueil</a></li>
    <li><a href="./about.php">À propos</a></li>
    <li><a  class="active" href="./services.php">Services</a></li>
    <li><a  href="./contact.php">Contact</a></li>

    <?php if (isset($_SESSION['user_id'])): ?>
        <li class="auth-btns">
            <a href="./profile.php" class="btn-nav btn-login">Mon Profil</a>
            <a href="./logout.php" class="btn-nav btn-register">Déconnexion</a>
        </li>
    <?php else: ?>
        <li class="auth-btns">
            <a href="./signin.php" class="btn-nav btn-login">Connexion</a>
            <a href="./signup.php" class="btn-nav btn-register">Inscription</a>
        </li>
    <?php endif; ?>
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
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>
    <!-- Header End -->


    <style>
           .breadcrumb__text h2 {
    font-size: 45px;
    font-weight: 700;
    color: #fff;
    text-shadow: 0 3px 10px rgba(100, 95, 255, 0.45);

    animation: pulsee 2.8s ease-in-out infinite;
}

/* Animation loop */
@keyframes pulsee {
    0% {
        transform: scale(1);
        text-shadow: 0 3px 10px rgba(195, 195, 231, 0.45);
    }
    50% {
        transform: scale(1.10);
        text-shadow: 0 6px 18px rgba(48, 42, 213, 0.6);
    }
    100% {
        transform: scale(1);
        text-shadow: 0 3px 10px rgba(22, 45, 197, 0.45);
    }
}

    </style>
    <!-- Breadcrumb -->
    <div class="breadcrumb-option spad set-bg" data-setbg="img/breadcrumbbg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb__text">
                        <h2>Nos Services</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    <section class="services-page spad">
        <div class="container">
            <div class="row">

                <!-- Service 1 -->
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="services__item">
                        <div class="services__item__icon">
                            <img src="img/icons/si-2.png" alt="">
                        </div>
                        <h4>Matching Emploi Inclusif</h4>
                        <p>AbleLink met en relation les chercheurs d’emploi en situation de handicap avec des offres accessibles adaptées à leurs compétences.</p>
                    </div>
                </div>

                <!-- Service 2 -->
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="services__item">
                        <div class="services__item__icon">
                            <img src="img/icons/si-1.png" alt="">
                        </div>
                        <h4>Accompagnement Accessibilité</h4>
                        <p>Nous aidons les entreprises à rendre leurs processus de recrutement inclusifs : entretiens accessibles, communication adaptée, aménagements.</p>
                    </div>
                </div>

                <!-- Service 3 -->
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="services__item">
                        <div class="services__item__icon">
                            <img src="img/icons/si-3.png" alt="">
                        </div>
                        <h4>Coaching Professionnel</h4>
                        <p>Nous accompagnons les candidats à renforcer leur confiance, optimiser leur CV, préparer les entretiens et construire un projet professionnel durable.</p>
                    </div>
                </div>

                <!-- Service 4 -->
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="../FrontOffice/evaluations-evenements.php" style="text-decoration: none; color: inherit; display: block;">
                        <div class="services__item">
                            <div class="services__item__icon">
                                <img src="img/icons/si-4.png" alt="">
                            </div>
                            <h4>Evenements Entreprise</h4>
                            <p>AbleLink collabore avec des entreprises engagées pour garantir des opportunités d'emploi équitables et un suivi sur le long terme.</p>
                        </div>
                    </a>
                    <?php else: ?>
                    <div class="services__item" style="cursor: default;">
                        <div class="services__item__icon">
                            <img src="img/icons/si-4.png" alt="">
                        </div>
                        <h4>Evenements Entreprise</h4>
                        <p>AbleLink collabore avec des entreprises engagées pour garantir des opportunités d'emploi équitables et un suivi sur le long terme.</p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Service 5 -->
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="services__item">
                        <div class="services__item__icon">
                            <img src="img/icons/si-2.png" alt="">
                        </div>
                        <h4>Formations & Ateliers</h4>
                        <p>Nous proposons des ateliers sur l’accessibilité, la sensibilisation au handicap, les compétences numériques et le développement personnel.</p>
                    </div>
                </div>

                <!-- Service 6 -->
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="services__item">
                        <div class="services__item__icon">
                            <img src="img/icons/si-1.png" alt="">
                        </div>
                        <h4>Outils de Recrutement Inclusif</h4>
                        <p>Notre plateforme offre des outils avancés pour permettre aux entreprises d'évaluer les profils et gérer les candidatures de façon inclusive.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- Footer -->
    <footer class="footer">
        <div class="container">

            <div class="footer__top">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
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
            </div>

            <div class="footer__option">
                <div class="row">

                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="footer__option__item">
                            <h5>À propos d’AbleLink</h5>
                            <p>AbleLink relie les demandeurs d’emploi en situation de handicap aux entreprises inclusives valorisant la diversité.</p>
                            <a href="#" class="read__more">Lire plus <span class="arrow_right"></span></a>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-3 col-sm-3">
                        <div class="footer__option__item">
                            <h5>Ressources</h5>
                            <ul>
                                <li><a href="#">Équipe</a></li>
                                <li><a href="#">Carrières</a></li>
                                <li><a href="#">Contact</a></li>
                                <li><a href="#">Partenaires</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-3 col-sm-3">
                        <div class="footer__option__item">
                            <h5>Explorer</h5>
                            <ul>
                                <li><a href="#">Offres d'emploi</a></li>
                                <li><a href="#">Formations</a></li>
                                <li><a href="#">Communauté</a></li>
                                <li><a href="#">Événements</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12">
                        <div class="footer__option__item">
                            <h5>Newsletter</h5>
                            <p>Restez informé des nouvelles opportunités et des actions pour l’inclusion.</p>
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
                        <p>
                            Copyright © 
                            <script>document.write(new Date().getFullYear());</script>
                            AbleLink | Pour un avenir professionnel inclusif
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </footer>

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
