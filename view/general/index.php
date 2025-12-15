<?php
require_once __DIR__ . "/../../config/ai_translate.php";

$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

if (!in_array($lang, ['fr', 'ar', 'en', 'es', 'de', 'it', 'tr', 'ru', 'zh'])) {
    $lang = "en"; // لغة افتراضية
}


?>
<!DOCTYPE html>
<html lang="fr">

<head>
    
    <meta charset="UTF-8">
    <meta name="description" content="AbleLink – Plateforme d’emploi inclusive">
    <meta name="keywords" content="AbleLink, Inclusion, Accessibilité, Emploi, Handicap">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AbleLink</title>

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
    <!-- Chatbase Chatbot -->
<script>
(function(){
    if(!window.chatbase || window.chatbase("getState") !== "initialized") {
        window.chatbase = (...arguments) => {
            if(!window.chatbase.q) {
                window.chatbase.q = []
            }
            window.chatbase.q.push(arguments)
        };
        window.chatbase = new Proxy(window.chatbase, {
            get(target, prop) {
                if(prop === "q") {
                    return target.q
                }
                return (...args) => target(prop, ...args)
            }
        })
    }
    const onLoad = function() {
        const script = document.createElement("script")
        script.src = "https://www.chatbase.co/embed.min.js"
        script.id = "WCv-2DIoQrUgV_egwStrP"   // <-- هذا هو chatbotId
        script.domain = "www.chatbase.co"
        document.body.appendChild(script)
    };
    if(document.readyState === "complete") {
        onLoad()
    } else {
        window.addEventListener("load", onLoad)
    }
})();
</script>

</body>
</html>

    <!-- Page Preloader -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    <!-- Header Begin -->
    <header class="header">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                   <div class="header__logo">
                    
    <h1 class="site-title">
        <a href="./index.php">
            <span class="letter-a">A</span>
            <span class="letter-b">b</span>
            <span class="letter-l">l</span>
            <span class="letter-e">e</span>
            <span class="letter-link">Link</span>
        </a>
    </h1>
</div>
<style>
   .site-title {
       margin-top: -30px; /* Monte titre */

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
                <div class="col-lg-10">
                    <div class="header__nav__option">
                        <nav class="header__nav__menu mobile-menu">
                            <ul class="main-nav">
    <li class="active"><a href="./index.php">Accueil</a></li>
    <li><a href="./about.php">À propos</a></li>
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
        /* Hero Section - Style clair et élégant comme l'image */
.hero__item {
    height: 75vh;
    min-height: 600px;
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

/* Overlay clair et subtil */
.hero__item:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, 
        rgba(18, 1, 43, 0.85) 0%, 
        rgba(1, 4, 18, 0.9) 50%, 
        rgba(0, 0, 0, 0.95) 100%);
}

.hero__text {
    position: relative;
    z-index: 2;
    max-width: 700px;
    padding: 40px;
    text-align: center;
   
}

/* Badge élégant comme l'image */
.hero__text span {
    display: inline-block;
    background: linear-gradient(135deg, #3a4ced 0%, #4c8df5 100%);
    color: white;
    padding: 8px 24px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 25px;
    letter-spacing: 1px;
    text-transform: uppercase;
    font-family: 'Poppins', sans-serif;
    box-shadow: 0 4px 12px rgba(128, 136, 203, 0.2);
}

/* Titre principal */
.hero__text h2 {
    font-size: 3.2rem;
    font-weight: 800;
    color: #a3a9e6ff;
    line-height: 1.1;
    margin-bottom: 20px;
    font-family: 'Josefin Sans', sans-serif;
    letter-spacing: -0.5px;
}

/* Description */
.hero__text p {
    font-size: 1.2rem;
    color: #bba3c1ff;
    margin-bottom: 35px;
    line-height: 1.7;
    font-family: 'Poppins', sans-serif;
    font-weight: 400;
    max-width: 550px;
    margin-left: auto;
    margin-right: auto;
}

/* Bouton élégant */
.hero__btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 15px 38px;
    background: linear-gradient(135deg, #3a4ced 0%, #4c8df5 100%);
    border: none;
    color: white;
    font-size: 16px;
    font-weight: 600;
    border-radius: 12px;
    transition: all 0.3s ease;
    text-decoration: none;
    font-family: 'Poppins', sans-serif;
    letter-spacing: 0.5px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(58, 76, 237, 0.25);
}

.hero__btn:before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, 
        transparent, 
        rgba(255, 255, 255, 0.2), 
        transparent);
    transition: 0.5s;
}
.hero__text {
    animation: gentleDrop 1s ease-out;
}

.hero__text span {
    animation: slideInRight 0.7s ease-out 0.2s both;
}

.hero__text h2 {
    animation: waveIn 0.9s ease-out 0.3s both;
}

.hero__text p {
    animation: softFade 0.8s ease-out 0.5s both;
}

.hero__btn {
    animation: pulseIn 0.7s ease-out 0.8s both;
}

@keyframes gentleDrop {
    0% {
        opacity: 0;
        transform: translateY(-30px);
    }
    50% {
        opacity: 1;
        transform: translateY(10px);
    }
    100% {
        transform: translateY(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(40px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes waveIn {
    0% {
        opacity: 0;
        transform: translateY(30px);
    }
    60% {
        opacity: 1;
        transform: translateY(-5px);
    }
    100% {
        transform: translateY(0);
    }
}

@keyframes softFade {
    from {
        opacity: 0;
        transform: translateY(15px) rotate(1deg);
    }
    to {
        opacity: 1;
        transform: translateY(0) rotate(0);
    }
}

@keyframes pulseIn {
    0% {
        opacity: 0;
        transform: scale(0.7);
    }
    70% {
        opacity: 1;
        transform: scale(1.05);
    }
    85% {
        transform: scale(0.95);
    }
    100% {
        transform: scale(1);
    }
}

/* Effet de bordure subtile */
.hero__text:after {
    content: '';
    position: absolute;
    top: 10px;
    left: 10px;
    right: 10px;
    bottom: 10px;
    border: 1px solid rgba(58, 76, 237, 0.05);
    border-radius: 16px;
    pointer-events: none;
}

/* Points décoratifs subtils */
.hero__item:after {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background-image: 
        radial-gradient(circle at 20% 30%, rgba(58, 76, 237, 0.03) 2px, transparent 2px),
        radial-gradient(circle at 80% 70%, rgba(76, 141, 245, 0.03) 2px, transparent 2px),
        radial-gradient(circle at 40% 60%, rgba(184, 123, 255, 0.02) 1px, transparent 1px);
    background-size: 50px 50px, 70px 70px, 30px 30px;
    animation: float 25s infinite linear;
}

@keyframes float {
    0% {
        background-position: 0 0, 0 0, 0 0;
    }
    100% {
        background-position: 100px 150px, 150px 100px, 50px 50px;
    }
}

/* Responsive design */
@media (max-width: 992px) {
    .hero__text {
        max-width: 600px;
        padding: 35px;
    }
    
    .hero__text h2 {
        font-size: 2.6rem;
    }
    
    .hero__text p {
        font-size: 1.1rem;
    }
}

@media (max-width: 768px) {
    .hero__item {
        height: 65vh;
        min-height: 500px;
    }
    
    .hero__text {
        max-width: 85%;
        padding: 30px 25px;
        margin: 20px;
    }
    
    .hero__text h2 {
        font-size: 2.2rem;
    }
    
    .hero__text span {
        font-size: 12px;
        padding: 7px 20px;
    }
    
    .hero__btn {
        padding: 14px 32px;
        font-size: 15px;
    }
}

@media (max-width: 576px) {
    .hero__item {
        height: 60vh;
        min-height: 450px;
    }
    
    .hero__text {
        padding: 25px 20px;
        background: rgba(255, 255, 255, 0.98);
    }
    
    .hero__text h2 {
        font-size: 1.9rem;
    }
    
    .hero__text p {
        font-size: 1rem;
        line-height: 1.6;
    }
    
    .hero__btn {
        padding: 12px 28px;
        font-size: 14px;
    }
}

/* Effet de surbrillance sur le badge */
.hero__text span {
    position: relative;
    overflow: hidden;
}

.hero__text span:before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, 
        transparent, 
        rgba(255, 255, 255, 0.2), 
        transparent);
    animation: slideShine 3s infinite;
}

@keyframes slideShine {
    0% {
        left: -100%;
    }
    100% {
        left: 100%;
    }
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

    <!-- Hero Section Begin -->
<section class="hero">
        <div class="hero__slider owl-carousel">

            <div class="hero__item" style="background-image: url('img/hero/new-hero.jpg');">
                <div class="hero__text">
                    <span>Bienvenue chez AbleLink</span>
                    <h2>Votre avenir commence ici</h2>
                    <p>Découvrez les meilleurs services pour développer votre carrière et rejoignez une communauté inclusive dédiée à votre réussite professionnelle.</p>
                    <a href="signup.php" class="hero__btn">Commencer l'aventure</a>
                </div>
            </div>

        </div>
    </section>
    <!-- Hero Section End -->

    <!-- Services Section Begin -->
    <section class="services spad">
        <div class="container">
            <div class="row">
                
                <div class="col-lg-4">
                    <div class="services__title">
                        <div class="section-title">
                            <span>Notre Mission</span>
                            <h2>Ce que nous faisons</h2>
                        </div>
                        <p>
                            AbleLink est une plateforme inclusive créée par l’association
                            <strong>“Paix et Inclusion”</strong>.  
                            Nous connectons les demandeurs d’emploi en situation de handicap aux entreprises
                            engagées dans l’accessibilité, l’égalité et le recrutement équitable.
                        </p>
                        <a href="#" class="primary-btn">Voir tous les services</a>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="row">

                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="services__item">
                                <div class="services__item__icon">
                                    <img src="img/icons/si-1.png" alt="">
                                </div>
                                <h4>Recrutement Accessible</h4>
                                <p>Nous connectons les candidats aux employeurs certifiés inclusifs.</p>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="services__item">
                                <div class="services__item__icon">
                                    <img src="img/icons/si-2.png" alt="">
                                </div>
                                <h4>Orientation Professionnelle</h4>
                                <p>Nous accompagnons les candidats dans la rédaction de CV, profils et entretiens.</p>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="services__item">
                                <div class="services__item__icon">
                                    <img src="img/icons/si-3.png" alt="">
                                </div>
                                <h4>Formation des Employeurs</h4>
                                <p>Nous aidons les entreprises à devenir plus inclusives.</p>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="services__item">
                                <div class="services__item__icon">
                                    <img src="img/icons/si-4.png" alt="">
                                </div>
                                <h4>Soutien Communautaire</h4>
                                <p>Ateliers, ressources et événements pour candidats et employeurs.</p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- Services Section End -->

    <!-- Counter Section Begin -->
    <section class="counter">
        <div class="container">
            <div class="counter__content">
                <div class="row">

                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="counter__item">
                            <div class="counter__item__text">
                                <img src="img/icons/ci-1.png" alt="">
                                <h2 class="counter_num">480</h2>
                                <p>Correspondances d'emploi</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="counter__item second__item">
                            <div class="counter__item__text">
                                <img src="img/icons/ci-2.png" alt="">
                                <h2 class="counter_num">1500+</h2>
                                <p>Candidats inscrits</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="counter__item third__item">
                            <div class="counter__item__text">
                                <img src="img/icons/ci-3.png" alt="">
                                <h2 class="counter_num">320</h2>
                                <p>Partenaires inclusifs</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="counter__item four__item">
                            <div class="counter__item__text">
                                <img src="img/icons/ci-4.png" alt="">
                                <h2 class="counter_num">98%</h2>
                                <p>Satisfaction utilisateur</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- Counter Section End -->
<style>
    /* CONTAINER */
.team__circle {
    text-align: center;
    cursor: pointer;
    margin-bottom: 25px;

    opacity: 0;
    animation: fadeUp 0.9s forwards ease-out;
}

/* STAGGER ANIMATION */
.team__circle:nth-child(1) { animation-delay: .1s; }
.team__circle:nth-child(2) { animation-delay: .2s; }
.team__circle:nth-child(3) { animation-delay: .3s; }
.team__circle:nth-child(4) { animation-delay: .4s; }
.team__circle:nth-child(5) { animation-delay: .5s; }

/* CIRCLE IMAGE BIG SIZE */
.team__circle img {
    width: 160px !important;
    height: 160px !important;
    border-radius: 50%;
    object-fit: cover;

    border: 5px solid #ffffff;
    box-shadow: 0 0 0 3px #467cff;  /* cercle bleu  */
    transition: transform .4s ease, box-shadow .4s ease;
}

/* TEXT */
.team__circle h4 {
    font-size: 20px;
    margin-top: 15px;
    font-weight: 700;
    color: #111;
}

.team__circle p {
    font-size: 15px;
    color: #777;
    margin: 0;
}

/* HOVER SUPER CLEAN */
.team__circle:hover img {
    transform: scale(1.12);
    box-shadow: 0 0 20px rgba(70, 124, 255, 0.35);
}

.team__circle:hover h4 {
    color: #467cff;
}

/* FADE ANIMATION */
@keyframes fadeUp {
    0% {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

</style>
    <!-- Team Section Begin -->
<section class="team spad set-bg" data-setbg="img/breadcrumbbg.jpg">
    <div class="container">

        <div class="row">
            <div class="col-lg-12">
                <div class="section-title team__title">
                    <span>Rencontrez notre équipe</span>
                    <h2>Équipe AbleLink</h2>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">

            <div class="col-lg-2 col-md-4 col-sm-6 mb-5">
                <div class="team__circle">
                    <img src="img/team/team-1.jpg" alt="">
                    <h4>Nourhene Klai</h4>
                    <p>Cheffe de Projet</p>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 mb-5">
                <div class="team__circle">
                    <img src="img/team/team-2.jpg" alt="">
                    <h4>Challouf Mohammed Amine</h4>
                    <p>Spécialiste Emploi</p>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 mb-5">
                <div class="team__circle">
                    <img src="img/team/team-3.jpg" alt="">
                    <h4>Iness Missaoui</h4>
                    <p>Consultante Accessibilité</p>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 mb-5">
                <div class="team__circle">
                    <img src="img/team/team-4.jpg" alt="">
                    <h4>Adem Friaa</h4>
                    <p>Support Technique</p>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 mb-5">
                <div class="team__circle">
                    <img src="img/team/team-5.jpg" alt="">
                    <h4>Malek Jafrar</h4>
                    <p>Support Technique</p>
                </div>
            </div>

        </div>

    </div>
</section>
<!-- Team Section End -->

    <!-- Latest Blog Section Begin -->
    <section class="latest spad">
        <div class="container">

            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title center-title">
                        <span>Blog AbleLink</span>
                        <h2>Dernières actualités</h2>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="latest__slider owl-carousel">

                    <div class="col-lg-4">
                        <div class="blog__item latest__item">
                            <h4>Comment améliorer l’accessibilité en entreprise ?</h4>
                            <ul>
                                <li>03 Jan 2025</li>
                                <li>12 Commentaires</li>
                            </ul>
                            <p>Des solutions simples pour rendre les environnements de travail plus accessibles.</p>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="blog__item latest__item">
                            <h4>Compétences recherchées par les employeurs en 2025</h4>
                            <ul>
                                <li>10 Jan 2025</li>
                                <li>4 Commentaires</li>
                            </ul>
                            <p>Les compétences clés qui augmentent les chances d'être recruté via AbleLink.</p>
                            
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="blog__item latest__item">
                            <h4>Success Story : Du stage à l’emploi</h4>
                            <ul>
                                <li>12 Jan 2025</li>
                                <li>8 Commentaires</li>
                            </ul>
                            <p>Découvrez comment AbleLink a aidé un jeune talent à décrocher son premier emploi.</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>
    <!-- Latest Blog Section End -->
<style>
    .callto__text a {
  color: #ffffff;
   background: #3a4ced;
  font-size: 15px;
  font-weight: 700;
  font-family: "Play", sans-serif;
  letter-spacing: 2px;
  text-transform: uppercase;
  display: inline-block;
  padding: 14px 32px 12px;
}
</style>
    <!-- Call To Action Begin -->
    <section class="callto spad set-bg" data-setbg="img/teeaaaam.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="callto__text">
                        <h2>Rejoignez AbleLink et construisez votre avenir</h2>
                        <p>Ensemble, construisons un monde professionnel plus inclusif.</p>
                        <a href="signup.php">Inscrivez-vous maintenant</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Call To Action End -->

    <!-- Footer Begin -->
    <footer class="footer">
        <div class="container">

            <div class="footer__top">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
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
                </div>
            </div>

            <div class="footer__option">
                <div class="row">

                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="footer__option__item">
                            <h5>À propos d’AbleLink</h5>
                            <p>AbleLink connecte les chercheurs d’emploi en situation de handicap aux entreprises inclusives.</p>
                            <a href="#" class="read__more">En savoir plus <span class="arrow_right"></span></a>
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
                            <p>Restez informé des nouvelles opportunités et actualités.</p>
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
                            AbleLink | Vers un avenir professionnel inclusif
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </footer>
    <!-- Footer End -->

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
