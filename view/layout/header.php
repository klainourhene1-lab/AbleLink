<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="AbleLink – Plateforme d'emploi inclusive">
    <meta name="keywords" content="AbleLink, Inclusion, Accessibilité, Emploi, Handicap">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $pageTitle ?? 'AbleLink' ?></title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="/projetttwebbbbbbbbb/assets/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="/projetttwebbbbbbbbb/assets/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="/projetttwebbbbbbbbb/assets/css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="/projetttwebbbbbbbbb/assets/css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="/projetttwebbbbbbbbb/assets/css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="/projetttwebbbbbbbbb/assets/css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="/projetttwebbbbbbbbb/assets/css/style.css" type="text/css">
    <link rel="stylesheet" href="/projetttwebbbbbbbbb/assets/css/custom.css?v=<?= time() ?>" type="text/css">
    <link rel="stylesheet" href="/projetttwebbbbbbbbb/assets/css/frontend.css?v=<?= time() ?>" type="text/css">

</head>
<body>
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
        <a href="/projetttwebbbbbbbbb/index.php?controller=offre&action=liste">
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
       margin-top: -30px;
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
   .letter-a  { color: #ff4f5e; text-shadow: 0 0 8px rgba(255,79,94,0.6); }
   .letter-b  { color: #4c8df5; text-shadow: 0 0 8px rgba(76,141,245,0.6); }
   .letter-l  { color: #b87bff; text-shadow: 0 0 8px rgba(184,123,255,0.6); }
   .letter-e  { color: #ffb247; text-shadow: 0 0 8px rgba(255,178,71,0.6); }
   .letter-link { 
       color: #ffffff; 
       margin-left: 4px;
       text-shadow: 0 0 10px rgba(255,255,255,0.7);
   }
   .site-title a:hover {
       transform: scale(1.05);
       filter: drop-shadow(0 0 12px rgba(0, 187, 255, 0.6));
   }
</style>
</div>
                <div class="col-lg-10">

                    <div class="header__nav__option">
                        <nav class="header__nav__menu mobile-menu">
                            <ul class="main-nav">
                                <li class="active"><a href="/projetttwebbbbbbbbb/index.php?controller=offre&action=liste">Accueil</a></li>
                                <li><a href="/projetttwebbbbbbbbb/index.php?controller=dashboard&action=index" class="btn-dashboard" style="padding: 10px 20px; background: linear-gradient(135deg, #7c3aed, #8b5cf6); color: white; border-radius: 8px; font-weight: 600;">
                                    <i class="fa fa-tachometer-alt"></i> Dashboard
                                </a></li>
                                <li class="user-profile-menu">
                                    <a href="#" class="user-profile-link">
                                        <div class="user-avatar">
                                            <img src="/projetttwebbbbbbbbb/assets/img/avatar-default.png" alt="User Avatar" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Ccircle cx=%2250%22 cy=%2250%22 r=%2250%22 fill=%22%233a4ced%22/%3E%3Ctext x=%2250%22 y=%2260%22 font-size=%2240%22 fill=%22white%22 text-anchor=%22middle%22%3EU%3C/text%3E%3C/svg%3E';">
                                        </div>
                                        <div class="user-info">
                                            <span class="user-name">Utilisateur</span>
                                            <span class="user-role">Administrateur</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>
    <!-- Header End -->