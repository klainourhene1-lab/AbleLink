<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$viewFile = isset($viewFile) ? $viewFile : null;
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = '/projetweb/ablelink/';
$current = strpos($uri, $base) === 0 ? trim(substr($uri, strlen($base)), '/') : '';
$activeHome = ($current === '' || $current === 'index.php');
$activeStories = (strpos($current, 'success-stories') === 0) || ($current === 'success_stories.php');
$activeStats = ($current === 'stats');
$activeEvents = ($current === 'events');
$activeHistory = ($current === 'historique');
$activeAbout = ($current === 'about' || $current === 'about.php');
$activeContact = ($current === 'contact' || $current === 'contact.php');
$activeAdmin = (strpos($current, 'admin') === 0);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="AbleLink - Plateforme d'emploi inclusive pour personnes en situation de handicap">
    <meta name="keywords" content="AbleLink, Inclusion, Accessibilité, Emploi, Handicap, Diversité">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AbleLink - Empowering Inclusive Careers</title>
    <?php
        $og_title = isset($og_title) ? $og_title : 'AbleLink';
        $og_description = isset($og_description) ? $og_description : 'Histoires de réussite, inclusion et emplois accessibles.';
        $og_image = isset($og_image) ? $og_image : '/projetweb/ablelink/img/logo/logo-1.png';
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $og_url = isset($og_url) ? $og_url : ($scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    ?>
    <meta property="og:title" content="<?php echo htmlspecialchars($og_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($og_description); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($og_image); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($og_url); ?>">
    <meta property="og:type" content="article">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($og_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($og_description); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($og_image); ?>">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Css Styles -->
    <link rel="stylesheet" href="/projetweb/ablelink/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="/projetweb/ablelink/css/style.css" type="text/css">
    
    <style>
        :root {
            --primary-red: #E63946;
            --primary-blue: #457B9D;
            --primary-purple: #7209B7;
            --dark-bg: #0F172A;
            --dark-card: #1E293B;
            --text-primary: #F1FAEE;
            --text-secondary: #A8DADC;
            --accent-green: #10B981;
            --gradient-bg: linear-gradient(135deg, #0F172A 0%, #1E1B4B 100%);
            --gradient-blue-purple: linear-gradient(135deg, #457B9D 0%, #7209B7 100%);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--gradient-bg);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Header Styles */
        .header {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .header__logo {
            display: flex;
            align-items: center;
        }
        
        .site-title {
            margin: 0;
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
        
        /* Logo colors */
        .letter-a { color: #ff4f5e; text-shadow: 0 0 8px rgba(255,79,94,0.6); }
        .letter-b { color: #4c8df5; text-shadow: 0 0 8px rgba(76,141,245,0.6); }
        .letter-l { color: #b87bff; text-shadow: 0 0 8px rgba(184,123,255,0.6); }
        .letter-e { color: #ffb247; text-shadow: 0 0 8px rgba(255,178,71,0.6); }
        .letter-link { 
            color: #ffffff; 
            margin-left: 4px;
            text-shadow: 0 0 10px rgba(255,255,255,0.7);
        }
        
        .site-title a:hover {
            transform: scale(1.05);
            filter: drop-shadow(0 0 12px rgba(0, 187, 255, 0.6));
        }
        
        .site-title a span:hover {
            transform: translateY(-2px);
            display: inline-block;
            transition: 0.2s ease;
        }
        
        .header__nav {
            display: flex;
            align-items: center;
            gap: 40px;
        }
        
        .header__nav__menu {
            display: flex;
            list-style: none;
            gap: 30px;
            align-items: center;
        }
        
        .header__nav__menu li a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            transition: all 0.3s;
            position: relative;
            padding: 5px 0;
        }
        
        .header__nav__menu li a:hover,
        .header__nav__menu li a.active {
            color: var(--text-primary);
        }
        
        .header__nav__menu li a.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--gradient-blue-purple);
            border-radius: 2px;
        }
        
        .header__user {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .btn-entreprise {
            background: var(--accent-green);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .btn-entreprise:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .user-profile:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient-blue-purple);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-primary);
        }
        
        .user-type {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        /* User Dropdown Menu */
        .user-dropdown {
            position: absolute;
            top: 70px;
            right: 30px;
            background: rgba(30, 41, 59, 0.98);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            min-width: 250px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 2000;
        }
        
        .user-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s;
            font-size: 14px;
        }
        
        .dropdown-item i {
            width: 18px;
            color: var(--text-secondary);
        }
        
        .dropdown-item:hover:not(:first-child) {
            background: rgba(139, 92, 246, 0.15);
            color: var(--text-primary);
        }
        
        .dropdown-item.logout:hover {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
        }
        
        .dropdown-item.logout:hover i {
            color: #ef4444;
        }
        
        .dropdown-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 4px 0;
        }
        
        /* Login/Register Buttons */
        .btn-login,
        .btn-register {
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-login {
            background: rgba(139, 92, 246, 0.15);
            color: #a78bfa;
            border: 1px solid #8b5cf6;
        }
        
        .btn-login:hover {
            background: #8b5cf6;
            color: white;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
        }
        
        .btn-register {
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
            color: white;
            border: none;
        }
        
        .btn-register:hover {
            background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
            transform: translateY(-2px);
        }
        
        /* Main Content */
        .main-content {
            min-height: calc(100vh - 80px);
            padding: 0;
        }
        
        /* Footer */
        .footer {
            background: rgba(15, 23, 42, 0.95);
            padding: 40px 0;
            margin-top: 60px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
        }
        
        .footer__copyright {
            text-align: center;
            color: var(--text-secondary);
            font-size: 14px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 15px;
            }
            
            .header__nav__menu {
                flex-direction: column;
                gap: 15px;
            }
            
            .header__user {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Header Section Begin -->
    <header class="header">
        <div class="header-container">
            <div class="header__logo">
                <h1 class="site-title">
                    <a href="/projetweb/ablelink/">
                        <span class="letter-a">A</span>
                        <span class="letter-b">b</span>
                        <span class="letter-l">l</span>
                        <span class="letter-e">e</span>
                        <span class="letter-link">Link</span>
                    </a>
                </h1>
            </div>
            
            <nav class="header__nav">
                <ul class="header__nav__menu">
                    <li><a href="/projetweb/ablelink/" class="<?php echo $activeHome?'active':''; ?>">ACCUEIL</a></li>
                    <li><a href="/projetweb/ablelink/success-stories" class="<?php echo $activeStories?'active':''; ?>">HISTOIRES</a></li>
                    <li><a href="/projetweb/ablelink/historique" class="<?php echo $activeHistory?'active':''; ?>">HISTORIQUE</a></li>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <li><a href="/projetweb/ablelink/admin" class="<?php echo $activeAdmin?'active':''; ?>">ADMINISTRATION</a></li>
                    <?php endif; ?>
                    
                    <?php if (empty($_SESSION['user_logged_in'])): ?>
                        <li><a href="/projetweb/ablelink/login">SE CONNECTER</a></li>
                    <?php endif; ?>
                </ul>
                
                
                <div class="header__user">
                    <?php if (!empty($_SESSION['user_logged_in'])): ?>
                        <div class="user-profile" onclick="toggleUserMenu()">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?>
                            </div>
                            <div class="user-info">
                                <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur'); ?></span>
                                <span class="user-type"><?php echo $_SESSION['user_role'] === 'admin' ? 'Administrateur' : 'Utilisateur'; ?></span>
                            </div>
                            <i class="fas fa-chevron-down" style="color: var(--text-secondary); font-size: 12px;"></i>
                        </div>
                        
                        <!-- Dropdown Menu -->
                        <div class="user-dropdown" id="userDropdown">
                            <div class="dropdown-item">
                                <i class="fas fa-user"></i>
                                <span><?php echo htmlspecialchars($_SESSION['user_email']); ?></span>
                            </div>
                            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                <div class="dropdown-divider"></div>
                                <a href="/projetweb/ablelink/success-stories/history" class="dropdown-item">
                                    <i class="fas fa-shield-alt"></i>
                                    <span>Admin Panel</span>
                                </a>
                            <?php endif; ?>
                            <div class="dropdown-divider"></div>
                            <a href="/projetweb/ablelink/auth/logout" class="dropdown-item logout">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Déconnexion</span>
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="/projetweb/ablelink/auth/login" class="btn-login">
                            <i class="fas fa-sign-in-alt"></i>
                            Se connecter
                        </a>

                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>
    <!-- Header End -->

    <!-- Main Content -->
    <div class="main-content">
        <?php if ($viewFile) { include $viewFile; } ?>
    </div>

    <!-- Footer Section Begin -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer__copyright">
                <p>&copy; <script>document.write(new Date().getFullYear());</script> AbleLink | Empowering Inclusive Careers</p>
            </div>
        </div>
    </footer>
    <!-- Footer Section End -->

    <!-- Js Plugins -->
    <script src="/projetweb/ablelink/js/jquery-3.3.1.min.js"></script>
    <script src="/projetweb/ablelink/js/bootstrap.min.js"></script>
    <script src="/projetweb/ablelink/js/main.js"></script>
    
    <script>
        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const userProfile = document.querySelector('.user-profile');
            
            if (dropdown && userProfile) {
                if (!dropdown.contains(event.target) && !userProfile.contains(event.target)) {
                    dropdown.classList.remove('show');
                }
            }
        });
    </script>
</body>
</html>
