<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Videograph Template">
    <meta name="keywords" content="Videograph, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AbleLink | Contact</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    <!-- Header Section Begin -->
   <!-- Header Begin -->
    <header class="header">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="header__logo">
                        <a href="./index.php"><img src="img/logo.png" alt="AbleLink Logo"></a>
                    </div>
                </div>
                <div class="col-lg-10">
                    <div class="header__nav__option">
                        <nav class="header__nav__menu mobile-menu">
                            <ul class="main-nav">
    <li><a href="./index.php">Accueil</a></li>
    <li><a href="./about.php">À propos</a></li>
    <li><a href="./services.php">Services</a></li>
    <li><a  class="active" href="./contact.php">Contact</a></li>

    <li class="auth-btns">
        <a href="./signin.php" class="btn-nav btn-login">Connexion</a>
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
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>
    <!-- Header End -->

    <!-- Breadcrumb Begin -->
    <div class="breadcrumb-option spad set-bg" data-setbg="img/breadcrumbbg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb__text">
                        <h2>Contactez-nous</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->
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

    .contact__widget__item:hover {
    border-color: #467cff;
    transform: translateY(-5px);
}
.contact__widget__item:hover .contact__widget__item__icon {
    background: #467cff;
    color: #fff;
}
</style>
    <!-- Contact Widget Section Begin -->
    <section class="contact-widget spad">
        <div class="container">
            <div class="row">

                <div class="col-lg-4 col-md-6 col-md-6 col-md-3">
                    <div class="contact__widget__item">
                        <div class="contact__widget__item__icon">
                            <i class="fa fa-map-marker"></i>
                        </div>
                        <div class="contact__widget__item__text">
                            <h4>Adresse</h4>
                            <p>Ariana<br>Esprit, Bloc H</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-md-6 col-md-3">
                    <div class="contact__widget__item">
                        <div class="contact__widget__item__icon">
                            <i class="fa fa-phone"></i>
                        </div>
                        <div class="contact__widget__item__text">
                            <h4>Hotline</h4>
                            <p>+216 96169711</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-md-6 col-md-3">
                    <div class="contact__widget__item">
                        <div class="contact__widget__item__icon">
                            <i class="fa fa-envelope"></i>
                        </div>
                        <div class="contact__widget__item__text">
                            <h4>Email</h4>
                            <p>AbleLink@gmail.com</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- Contact Widget Section End -->

    <!-- Call To Action Section Begin -->
    <section class="contact spad">
        <div class="container">
            <div class="row">

                <div class="col-lg-6 col-md-6">
                    <div class="contact__map">
                        <iframe
                            src="https://www.google.com/maps?q=ESPRIT%20Bloc%20H%2C%20El%20Ghazala%2C%20Ariana%2C%20Tunisia&hl=en&z=16&output=embed"
                            width="100%"
                            height="450"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>

                <!-- Formulaire de contact -->
                <div class="col-lg-6 col-md-6">
                    <div class="contact__form">
                        <h3>Entrer en contact</h3>
                        <form action="#">
                            <input type="text" placeholder="Nom">
                            <input type="text" placeholder="Email">
                            <input type="text" placeholder="Sujet">
                            <textarea placeholder="Message"></textarea>
                            <button type="submit" class="site-btn">Envoyer</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- Call To Action Section End -->

    <!-- Footer Section Begin -->
    <footer class="footer">
        <div class="container">

            <div class="footer__top">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="footer__top__logo">
                            <a href="#"><img src="img/logo.png" alt=""></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer__option">
                <div class="row">

                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="footer__option__item">
                            <h5>À propos d'AbleLink</h5>
                            <p>AbleLink est une plateforme qui connecte les chercheurs d’emploi en situation de handicap
                                avec des entreprises inclusives valorisant la diversité et l’égalité des chances.</p>
                            <a href="#" class="read__more">En savoir plus <span class="arrow_right"></span></a>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-3 col-sm-3">
                        <div class="footer__option__item">
                            <h5>Ressources</h5>
                            <ul>
                                <li><a href="#">Équipe</a></li>
                                <li><a href="#">Carrières</a></li>
                                <li><a href="#">Contactez-nous</a></li>
                                <li><a href="#">Nos partenaires</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-3 col-sm-3">
                        <div class="footer__option__item">
                            <h5>Explorer</h5>
                            <ul>
                                <li><a href="#">Offres d’emploi</a></li>
                                <li><a href="#">Formations</a></li>
                                <li><a href="#">Communauté</a></li>
                                <li><a href="#">Événements</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12">
                        <div class="footer__option__item">
                            <h5>Newsletter</h5>
                            <p>Restez informé des nouvelles opportunités d’emploi et des événements dédiés à l’inclusion.</p>
                            <form action="#">
                                <input type="text" placeholder="Email">
                                <button type="submit"><i class="fa fa-send"></i></button>
                        </div>
                    </div>

                </div>
            </div>

            <div class="footer__copyright">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <p class="footer__copyright__text">
                            Copyright ©
                            <script>document.write(new Date().getFullYear());</script>
                            AbleLink | Pour des carrières inclusives et accessibles
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </footer>
    <!-- Footer Section End -->

    <!-- Js Plugins -->
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
