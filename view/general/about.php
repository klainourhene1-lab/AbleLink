<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Videograph Template">
    <meta name="keywords" content="Videograph, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AbleLink | À propos</title>

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
    <li><a class="active" href="./about.php">À propos</a></li>
    <li><a href="./services.php">Services</a></li>
    <li><a href="./contact.php">Contact</a></li>

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

    <!-- Breadcrumb Begin -->
    <div class="breadcrumb-option spad set-bg" data-setbg="img/breadcrumbbg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb__text">
                        <h2>À propos de nous</h2>
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

    /* ======== GLOBAL ABOUT IMAGE STYLE ======== */
.about__pic__item {
    width: 100%;
    height: 260px;
    border-radius: 18px;
    background-size: cover !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
    margin-bottom: 22px;
    overflow: hidden;
}
/* ======== HOVER EFFECT ======== */
.about__pic__item:hover {
    transform: translateY(-8px) scale(1.03);
    box-shadow: 0 18px 40px rgba(0,0,0,0.15);
}
/* ZOOM ANIMATION INSIDE */
.about__pic__item:hover::after {
    transform: scale(1.15);
}
</style>
    <!-- About Section Begin -->
    <section class="about spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="about__pic">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="about__pic__item about__pic__item--large set-bg"
                                    data-setbg="img/about/about1.jpg"></div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="about__pic__item set-bg" data-setbg="img/about/about2.jpg"></div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="about__pic__item set-bg" data-setbg="img/about/about3.jpg"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Texte -->
                <div class="col-lg-6">
                    <div class="about__text">
                        <div class="section-title">
                            <span>À propos d'AbleLink</span>
                            <h2>Qui sommes-nous ?</h2>
                            <p>AbleLink est une plateforme d’emploi inclusive dédiée à connecter les personnes en situation de handicap
                                à des opportunités professionnelles équitables. Notre mission est de promouvoir l’accessibilité,
                                l’inclusion et l'autonomisation professionnelle en rapprochant employeurs et candidats talentueux.</p>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="services__item">
                                    <div class="services__item__icon">
                                        <img src="img/icons/si-3.png" alt="">
                                    </div>
                                    <h4>Recrutement inclusif</h4>
                                    <p>Nous aidons les entreprises à recruter des talents en situation de handicap de manière juste et efficace.</p>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="services__item">
                                    <div class="services__item__icon">
                                        <img src="img/icons/si-4.png" alt="">
                                    </div>
                                    <h4>Accompagnement accessibilité</h4>
                                    <p>Nous offrons des conseils pour garantir que les lieux de travail et processus de recrutement soient 100% accessibles.</p>
                                </div>
                            </div>
                        </div>

                        <div class="about__text__desc">
                            <p>
                                AbleLink est guidée par les valeurs d’égalité, de dignité et d’inclusion sociale.
                                La plateforme est conçue en collaboration avec des organisations spécialisées dans l'accompagnement
                                des personnes en situation de handicap, afin d’offrir à chaque candidat de réelles opportunités
                                de réussite professionnelle.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- About Section End -->

    <!-- Testimonial Section Begin -->
    <section class="testimonial spad set-bg" data-setbg="img/testimonialbg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title center-title">
                        <span>Apprécié par nos utilisateurs</span>
                        <h2>Ce que disent nos utilisateurs</h2>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="testimonial__slider owl-carousel">

                    <div class="col-lg-4">
                        <div class="testimonial__item">
                            <div class="testimonial__text">
                                <p>AbleLink m’a aidé à trouver mon premier emploi. La plateforme est simple et totalement accessible !</p>
                            </div>
                            <div class="testimonial__author">
                                <div class="testimonial__author__pic">
                                    <img src="img/testimonial/ta-1.jpg" alt="">
                                </div>
                                <div class="testimonial__author__text">
                                    <h5>Krista Attorn</h5>
                                    <span>Web Designer</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="testimonial__item">
                            <div class="testimonial__text">
                                <p>Grâce à AbleLink, notre entreprise a une équipe plus inclusive et diversifiée.</p>
                            </div>
                            <div class="testimonial__author">
                                <div class="testimonial__author__pic">
                                    <img src="img/testimonial/ta-2.jpg" alt="">
                                </div>
                                <div class="testimonial__author__text">
                                    <h5>Krista Attorn</h5>
                                    <span>Web Designer</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="testimonial__item">
                            <div class="testimonial__text">
                                <p>AbleLink est plus qu’une plateforme : c’est une communauté prônant l’égalité, l'accessibilité et les opportunités pour tous.</p>
                            </div>
                            <div class="testimonial__author">
                                <div class="testimonial__author__pic">
                                    <img src="img/testimonial/ta-3.jpg" alt="">
                                </div>
                                <div class="testimonial__author__text">
                                    <h5>Krista Attorn</h5>
                                    <span>Web Designer</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="testimonial__item">
                            <div class="testimonial__text">
                                <p>Le recrutement via AbleLink a été fluide. Nous avons rencontré des candidats compétents et motivés.</p>
                            </div>
                            <div class="testimonial__author">
                                <div class="testimonial__author__pic">
                                    <img src="img/testimonial/ta-1.jpg" alt="">
                                </div>
                                <div class="testimonial__author__text">
                                    <h5>Krista Attorn</h5>
                                    <span>Web Designer</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="testimonial__item">
                            <div class="testimonial__text">
                                <p>AbleLink a rendu ma recherche d’emploi simple et accessible. J’ai trouvé un environnement qui respecte mes compétences.</p>
                            </div>
                            <div class="testimonial__author">
                                <div class="testimonial__author__pic">
                                    <img src="img/testimonial/ta-2.jpg" alt="">
                                </div>
                                <div class="testimonial__author__text">
                                    <h5>Krista Attorn</h5>
                                    <span>Web Designer</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>
    <!-- Testimonial Section End -->

    <!-- Counter Section Begin -->
    <section class="counter">
        <div class="container">
            <div class="counter__content">
                <div class="row">

                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="counter__item">
                            <div class="counter__item__text">
                                <img src="img/icons/ci-1.png" alt="">
                                <h2 class="counter_num">230</h2>
                                <p>Projets accomplis</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="counter__item second__item">
                            <div class="counter__item__text">
                                <img src="img/icons/ci-2.png" alt="">
                                <h2 class="counter_num">1068</h2>
                                <p>Clients satisfaits</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="counter__item third__item">
                            <div class="counter__item__text">
                                <img src="img/icons/ci-3.png" alt="">
                                <h2 class="counter_num">230</h2>
                                <p>Clients potentiels</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="counter__item four__item">
                            <div class="counter__item__text">
                                <img src="img/icons/ci-4.png" alt="">
                                <h2 class="counter_num">230</h2>
                                <p>Projets accomplis</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- Counter Section End -->

    <!-- Footer Section Begin -->
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
                            <h5>À propos d'AbleLink</h5>
                            <p>AbleLink est une plateforme dédiée à connecter les chercheurs d’emploi en situation de handicap
                                avec des entreprises inclusives qui valorisent la diversité et l’égalité des chances.</p>
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
                            <p>Restez informé des nouvelles offres d’emploi et des événements dédiés à l’inclusion.</p>
                            <form action="#">
                                <input type="text" placeholder="Adresse email">
                                <button type="submit"><i class="fa fa-send"></i></button>
                            </form>
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
