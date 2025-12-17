<?php
session_start();

// Check if user is logged in - redirect to login if not
if (!isset($_SESSION['user_id'])) {
    header('Location: ../view/general/signin.php');
    exit;
}

// Get user information from session
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'] ?? 'Utilisateur';

// Load user data from database
require_once __DIR__ . '/../Control/config.php';
require_once __DIR__ . '/../Control/UserController.php';
require_once __DIR__ . '/../Model/User.php';

$controller = new UserController();
$user = $controller->showUser($user_id);

// Get user photo URL
function getPhotoUrl($photo, $prenom, $nom) {
    if ($photo && file_exists(__DIR__ . '/../uploads/profiles/' . $photo)) {
        return '../uploads/profiles/' . $photo;
    }
    return '../view/general/img/team/team-1.jpg'; // Default photo
}

$user_photo = $user ? getPhotoUrl($user->getPhoto(), $user->getPrenom(), $user->getNom()) : '../view/general/img/team/team-1.jpg';
$user_name = $user ? $user->getPrenom() . ' ' . $user->getNom() : 'Utilisateur';

// Map PHP session roles to JavaScript roles
$role_mapping = [
    'Admin' => 'admin',
    'Entreprise' => 'company', 
    'Utilisateur' => 'user',
    'Inclusion' => 'inclusion'
];

// Always sync session role with database role (to reflect real-time changes)
if ($user) {
    $db_role = $user->getRole();
    // Update session to match database
    $_SESSION['user_role'] = $db_role;
    $current_role = $db_role;
} else {
    $current_role = $_SESSION['user_role'] ?? 'Utilisateur';
}

// Update user_role for HTML checks
$user_role = $current_role;

$js_role = $role_mapping[$current_role] ?? 'user';

// Role display names
$role_display_names = [
    'admin' => 'Administrateur',
    'company' => 'Entreprise',
    'user' => 'Utilisateur',
    'inclusion' => 'Responsable Inclusion'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Événements - AbeLink</title>
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../videograph-master/videograph-master/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="../videograph-master/videograph-master/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="../videograph-master/videograph-master/css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="../videograph-master/videograph-master/css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="../videograph-master/videograph-master/css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="../videograph-master/videograph-master/css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="../videograph-master/videograph-master/css/style.css" type="text/css">
    <link rel="stylesheet" href="../view/FrontOffice/css/theme-toggle.css" type="text/css">
    <style>
        .header__nav__option { display: flex; align-items: center; justify-content: space-between; }
        .header__nav__menu { flex: 1; min-width: 0; }
        .header__nav__menu ul { display: flex; gap: 24px; align-items: center; }
        .header__nav__social { display: flex; align-items: center; gap: 12px; flex-wrap: nowrap; }
        .header__nav__social .input { height: 36px; padding: 6px 10px; }
        .role-badge { display: inline-flex; align-items: center; height: 36px; }
        .admin-combined-btn { display: inline-flex; align-items: center; height: 36px; }
        .content-wrapper { margin-top: 24px; }
        .hero.glass h1 { margin: 0 0 8px; }
        #pageDescription { margin: 0; }
        /* Role badges */
        .role-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }

        .role-badge.user {
            background: rgba(52, 152, 219, 0.2);
            color: #3498db;
        }

        .role-badge.company {
            background: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
        }

        .role-badge.inclusion {
            background: rgba(155, 89, 182, 0.2);
            color: #9b59b6;
        }

        .role-badge.admin {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }

        /* Role selector */
        .role-selector {
            display: flex;
            align-items: center;
            margin-left: 20px;
        }

        /* Event card styles */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        /* In the style section of historique.php, add: */
.card-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 15px;
}

.card-actions .cta-button {
    padding: 8px 12px;
    font-size: 13px;
}

.card-actions .cta-button.primary {
    background: #3498db;
    border: none;
}

.card-actions .cta-button.danger {
    background: rgba(255, 107, 107, 0.2);
    color: #ff6b6b;
    border: 1px solid rgba(255, 107, 107, 0.3);
}

.card-actions .cta-button.danger:hover {
    background: rgba(255, 107, 107, 0.3);
}

        .event-card {
            padding: 20px;
            border-radius: 12px;
            transition: transform 0.2s ease;
        }

        .event-card:hover {
            transform: translateY(-2px);
        }

        .event-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: white;
        }

        .company-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .company-logo {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #3498db;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }

        .company-name {
            color: #ccc;
            font-size: 14px;
        }

        .meta {
            color: #ccc;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin: 10px 0;
        }

        .tag {
            padding: 4px 8px;
            background: rgba(255,255,255,0.1);
            border-radius: 12px;
            font-size: 12px;
            color: #ccc;
        }

        .tag.status {
            background: rgba(52, 152, 219, 0.2);
            color: #3498db;
        }

        .tag.status.publie {
            background: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
        }

        .tag.status.brouillon {
            background: rgba(241, 196, 15, 0.2);
            color: #f1c40f;
        }

        .stars {
            color: #f1c40f;
            font-size: 14px;
        }

        .avg {
            color: #ccc;
            font-size: 12px;
            margin-left: 5px;
        }

        /* Button styles */
        .cta-button {
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            background: rgba(255,255,255,0.1);
            color: white;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .cta-button:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-1px);
        }

        .cta-button.primary {
            background: #3498db;
        }

        .cta-button.primary:hover {
            background: #2980b9;
        }

        /* Input styles */
        .input {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.1);
            color: white;
            font-family: inherit;
        }

        .input:focus {
            outline: none;
            border-color: #3498db;
        }

        /* Utility classes */
        .muted {
            color: #666;
            font-style: italic;
        }

        .glass {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        /* Add to existing style section */
.evaluation-card {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.evaluation-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.evaluation-event-title {
    font-size: 18px;
    font-weight: bold;
    color: white;
    margin: 0;
}

.evaluation-date {
    color: #ccc;
    font-size: 14px;
}

.evaluation-ratings {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 15px;
}

.rating-item {
    background: rgba(255, 255, 255, 0.05);
    padding: 15px;
    border-radius: 8px;
}

.rating-label {
    color: #aaa;
    font-size: 14px;
    margin-bottom: 5px;
}

.rating-stars {
    color: #f1c40f;
    font-size: 18px;
    margin-bottom: 5px;
}

.rating-value {
    color: white;
    font-weight: bold;
    font-size: 16px;
}

.evaluation-comment {
    background: rgba(255, 255, 255, 0.05);
    padding: 15px;
    border-radius: 8px;
    margin-top: 15px;
}

.comment-label {
    color: #aaa;
    font-size: 14px;
    margin-bottom: 8px;
}

.comment-text {
    color: #ccc;
    font-style: italic;
    line-height: 1.5;
}
        
        /* Loading indicator */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Combined admin button */
        .admin-combined-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .admin-combined-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        /* Stats cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: white;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 14px;
            color: #ccc;
        }

        /* User profile dropdown */
        .user-profile-dropdown {
            position: relative;
            display: inline-block;
        }

        .user-profile-button {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-profile-button:hover {
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.3);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,0.3);
        }

        .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: white;
            line-height: 1.2;
        }

        .user-role-text {
            font-size: 12px;
            color: rgba(255,255,255,0.7);
        }

        .user-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            min-width: 200px;
            background: rgba(15,23,42,0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            z-index: 1000;
            overflow: hidden;
        }

        .user-dropdown-menu.show {
            display: block;
        }

        .user-dropdown-item {
            display: block;
            padding: 12px 16px;
            color: white;
            text-decoration: none;
            transition: background 0.2s ease;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .user-dropdown-item:last-child {
            border-bottom: none;
        }

        .user-dropdown-item:hover {
            background: rgba(255,255,255,0.1);
        }

        .user-dropdown-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Title Bubble */
        .title-bubble {
            display: inline-block;
            background: linear-gradient(135deg, rgba(58, 76, 237, 0.9), rgba(123, 31, 162, 0.9));
            padding: 15px 40px;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border: 2px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            margin-bottom: 20px;
            color: white;
            text-transform: uppercase;
            letter-spacing: 1px;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        /* Pagination styles */
        .pagination-controls {
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }

        .pagination-btn {
            padding: 8px 16px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 6px;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
        }

        .pagination-btn:hover:not(:disabled) {
            background: rgba(255,255,255,0.2);
            transform: translateY(-1px);
        }

        .pagination-btn.active {
            background: #3498db;
            border-color: #3498db;
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-info {
            color: #ccc;
            font-size: 14px;
            padding: 0 12px;
        }

        /* Text wrapping and overflow fixes */
        .event-card {
            overflow: hidden;
            word-wrap: break-word;
        }

        .event-title {
            overflow: hidden;
            text-overflow: ellipsis;
            word-wrap: break-word;
            word-break: break-word;
            hyphens: auto;
        }

        .event-description,
        .meta,
        .company-name {
            overflow: hidden;
            word-wrap: break-word;
            word-break: break-word;
            hyphens: auto;
        }

        .event-description {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Ensure all text in cards wraps properly */
        .event-card * {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
    </style>
</head>
<body>
    <div class="bg-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <header class="header">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="header__logo">
                        <div class="site-title">
        <a href="../view/general/index.php">
            <span class="letter-a">A</span>
            <span class="letter-b">b</span>
            <span class="letter-l">l</span>
            <span class="letter-e">e</span>
            <span class="letter-link">Link</span>
        </a>
    </div>
    <style> .site-title {
       margin-top: -0px;
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
                            <ul>
                                <li><a href="../view/general/index.php">Accueil</a></li>
                                <li><a href="../view/FrontOffice/evaluations-evenements.php">Évaluations & Événements</a></li>
                                <li class="active"><a href="historique.php">Historique</a></li>
                                <?php if ($user_role === 'Admin'): ?>
                                <li><a href="admin_dashboard.php">Administration</a></li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        <div class="header__nav__social">
                            <div id="roleBadge" class="role-badge <?php echo $js_role; ?>">
                                <?php echo $role_display_names[$js_role] ?? 'Utilisateur'; ?>
                            </div>
                            <div class="user-profile-dropdown" id="userProfileDropdown">
                                <button class="user-profile-button" onclick="toggleUserMenu()">
                                    <img src="<?php echo htmlspecialchars($user_photo); ?>" alt="<?php echo htmlspecialchars($user_name); ?>" class="user-avatar">
                                    <div class="user-info">
                                        <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
                                        <span class="user-role-text"><?php echo htmlspecialchars($role_display_names[$js_role] ?? 'Utilisateur'); ?></span>
                                    </div>
                                    <i class="fa fa-chevron-down" style="margin-left: 5px; font-size: 12px;"></i>
                                </button>
                                <div class="user-dropdown-menu" id="userDropdownMenu">
                                    <a href="../view/general/profile.php" class="user-dropdown-item">
                                        <i class="fa fa-user"></i> Mon Profil
                                    </a>
                                    <a href="../view/general/profile.php" class="user-dropdown-item">
                                        <i class="fa fa-cog"></i> Paramètres
                                    </a>
                                    <?php if ($user_role === 'Admin'): ?>
                                    <a href="admin_dashboard.php" class="user-dropdown-item">
                                        <i class="fa fa-dashboard"></i> Administration
                                    </a>
                                    <?php endif; ?>
                                    <a href="historique.php" class="user-dropdown-item">
                                        <i class="fa fa-history"></i> Historique
                                    </a>
                                    <a href="../view/general/logout.php" class="user-dropdown-item" style="color: #e74c3c;">
                                        <i class="fa fa-sign-out"></i> Déconnexion
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>

    <!-- Theme Toggle Button -->
    <button id="themeToggle" class="theme-toggle-btn" onclick="toggleTheme()" aria-label="Passer en mode clair">
        <i class="fa fa-sun-o"></i>
    </button>

    <style>
        body {
            background: #100028;
            min-height: 100vh;
            color: #ffffff;
        }
        /* Add to the existing style section */
#eventDetailsContent .detail-section {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

#eventDetailsContent .detail-section:last-child {
    border-bottom: none;
}

#eventDetailsContent .detail-label {
    color: #aaa;
    font-size: 14px;
    margin-bottom: 5px;
}

#eventDetailsContent .detail-value {
    color: white;
    font-size: 16px;
}

#eventDetailsContent .evaluation-item {
    background: rgba(255,255,255,0.05);
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 10px;
}

#eventDetailsContent .evaluation-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

#eventDetailsContent .evaluation-user {
    font-weight: bold;
    color: white;
}

#eventDetailsContent .evaluation-date {
    color: #aaa;
    font-size: 12px;
}

#eventDetailsContent .evaluation-rating {
    display: flex;
    gap: 20px;
    margin-bottom: 10px;
}

#eventDetailsContent .evaluation-comment {
    color: #ccc;
    font-style: italic;
}
    </style>
    <section class="services spad">
    <div class="container">
        <div class="content-wrapper">
            <section class="page-header" style="text-align: center;">
                <h1 class="title-bubble">Historique des Événements</h1>
                <p id="pageDescription">Consultez votre historique d'événements</p>
            </section>

            <!-- Statistics Section -->
            <section style="margin-top: 20px;">
                <div class="stats-grid">
                    <div class="stat-card glass">
                        <div class="stat-number" id="totalEvents">0</div>
                        <div class="stat-label">Événements au total</div>
                    </div>
                    <div class="stat-card glass">
                        <div class="stat-number" id="publishedEvents">0</div>
                        <div class="stat-label">Événements publiés</div>
                    </div>
                    <div class="stat-card glass">
                        <div class="stat-number" id="upcomingEvents">0</div>
                        <div class="stat-label">Événements à venir</div>
                    </div>
                    <div class="stat-card glass">
                        <div class="stat-number" id="pastEvents">0</div>
                        <div class="stat-label">Événements passés</div>
                    </div>
                </div>
            </section>

            <!-- Events Section -->
            <section style="margin-top: 20px;">
                <div class="filters glass" style="padding: 20px; margin-bottom: 20px;">
                    <div style="display: grid; gap: 10px;">
                        <input id="searchInput" class="input" type="search" placeholder="Rechercher par titre, lieu..." aria-label="Rechercher événements"/>
                        <select id="filterStatus" class="input" aria-label="Filtrer par statut">
                            <option value="">Tous les statuts</option>
                            <option value="Publié">Publié</option>
                            <option value="Brouillon">Brouillon</option>
                            <option value="Rejeté" id="rejectedOption" style="display: none;">Rejeté</option>
                        </select>
                        <select id="filterDate" class="input" aria-label="Filtrer par date">
                            <option value="all">Toutes les dates</option>
                            <option value="upcoming">À venir</option>
                            <option value="past">Passés</option>
                        </select>
                        <?php if ($user_role !== 'Admin'): ?>
                        <!-- Mes évaluations section - hidden for admins -->
<section style="margin-top: 40px;">
    <h2 style="margin: 6px 0 12px; color: white;">Mes évaluations</h2>
    <p class="muted" style="margin-bottom: 15px;">Consultez les évaluations que vous avez déjà soumises.</p>
    <div id="myEvaluationsGrid" class="grid" role="list" aria-live="polite"></div>
    <div id="myEvaluationsPagination" class="pagination-controls" style="display: none; margin-top: 20px; text-align: center;"></div>
    <div id="noMyEvaluations" class="muted" style="display: none; margin-top: 12px; text-align: center;">Vous n'avez pas encore évalué d'événements.</div>
</section>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div id="eventsGrid" class="grid" role="list" aria-live="polite"></div>
                <div id="eventsPagination" class="pagination-controls" style="display: none; margin-top: 20px; text-align: center;"></div>
                <div id="noEvents" class="muted" style="display: none; margin-top: 12px; text-align: center;">Aucun événement trouvé.</div>
                <div id="loadingEvents" class="muted" style="display: none; margin-top: 12px; text-align: center;">
                    <div class="loading"></div> Chargement des événements...
                </div>
            </section>
        </div>
    </div>
    </section>

    <script src="../videograph-master/videograph-master/js/jquery-3.3.1.min.js"></script>
    <script src="../videograph-master/videograph-master/js/bootstrap.min.js"></script>
    <script src="../videograph-master/videograph-master/js/jquery.slicknav.js"></script>
    <script src="../videograph-master/videograph-master/js/owl.carousel.min.js"></script>
    <script src="../videograph-master/videograph-master/js/jquery.magnific-popup.min.js"></script>
    <script src="../videograph-master/videograph-master/js/mixitup.min.js"></script>
    <script src="../videograph-master/videograph-master/js/masonry.pkgd.min.js"></script>
    <script src="../videograph-master/videograph-master/js/main.js"></script>
    <script>
        // Session-based user data (from PHP)
        const SESSION_USER_ID = <?php echo json_encode($user_id); ?>;
        const SESSION_USER_ROLE = <?php echo json_encode($js_role); ?>;
        const SESSION_USER_ROLE_NAME = <?php echo json_encode($user_role); ?>;
    </script>
    <script>
        
        // User roles and permissions
        const USER_ROLES = {
            USER: 'user',
            COMPANY: 'company',
            INCLUSION: 'inclusion',
            ADMIN: 'admin'
        };

        // User role management - uses session-based role from PHP
        function loadUserRole() {
            return typeof SESSION_USER_ROLE !== 'undefined' ? SESSION_USER_ROLE : USER_ROLES.USER;
        }
        // Add this function to load user evaluations
function loadUserEvaluations() {
    if (!window.CURRENT_USER_ID) return;
    
    fetch('get_historique_events.php?action=get_user_evaluations&userId=' + window.CURRENT_USER_ID)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.CURRENT_USER_EVALUATIONS = data.evaluations || [];
                console.log('Loaded user evaluations:', window.CURRENT_USER_EVALUATIONS.length);
            }
        })
        .catch(error => {
            console.error('Error loading user evaluations:', error);
            window.CURRENT_USER_EVALUATIONS = [];
        });
}

// Update DOMContentLoaded to load evaluations:

document.addEventListener('DOMContentLoaded', function() {
    console.log('Historique page loaded, initializing...');
    
    // Use session-based role and user ID from PHP
    window.CURRENT_USER_ROLE = typeof SESSION_USER_ROLE !== 'undefined' ? SESSION_USER_ROLE : USER_ROLES.USER;
    window.CURRENT_USER_ID = typeof SESSION_USER_ID !== 'undefined' ? SESSION_USER_ID : null;
    window.CURRENT_FILTER = { search: '', status: '', date: 'all' };
    window.EVENTS = [];
    
    console.log('Initial state:', {
        role: window.CURRENT_USER_ROLE,
        userId: window.CURRENT_USER_ID,
        sessionRole: typeof SESSION_USER_ROLE !== 'undefined' ? SESSION_USER_ROLE : 'not set'
    });
    
    updateUIForRole();
    setupFilters();
    loadEvents();
    
    // Load user evaluations
    loadUserEvaluations();
    
    // Connect evaluation form submit handler
    const evaluationForm = document.getElementById('evaluationForm');
    if (evaluationForm) {
        evaluationForm.addEventListener('submit', handleEvaluationFormSubmit);
        console.log('Evaluation form connected');
    }
    
    console.log('Historique page initialized with role:', window.CURRENT_USER_ROLE);
});

    function updateUIForRole() {
    const role = window.CURRENT_USER_ROLE || USER_ROLES.USER;
    const roleBadge = document.getElementById('roleBadge');
    const adminNavItem = document.getElementById('adminNavItem');
    const pageDescription = document.getElementById('pageDescription');
    
    // Update role badge
    if (roleBadge) {
        roleBadge.textContent = getRoleDisplayName(role);
        roleBadge.className = `role-badge ${role}`;
    }
    
    // Update admin nav visibility
    if (adminNavItem) {
        adminNavItem.style.display = (role === USER_ROLES.ADMIN) ? 'inline-block' : 'none';
    }
    
    // Update page description based on role
    if (pageDescription) {
        const descriptions = {
            [USER_ROLES.USER]: 'Consultez les événements auxquels vous avez participé.',
            [USER_ROLES.COMPANY]: 'Consultez les événements que vous avez créés.',
            [USER_ROLES.INCLUSION]: 'Consultez tous les événements de la plateforme.',
            [USER_ROLES.ADMIN]: 'Consultez tous les événements de la plateforme.'
        };
        pageDescription.textContent = descriptions[role] || 'Consultez votre historique d\'événements';
    }
    
    // Update role selector value
    const roleSelect = document.getElementById('roleSelect');
    if (roleSelect) {
        roleSelect.value = role;
    }
    
    // Update status filter based on role
    const rejectedOption = document.getElementById('rejectedOption');
    if (rejectedOption) {
        // Show "Rejeté" option only for company and admin roles
        if (role === USER_ROLES.COMPANY || role === USER_ROLES.ADMIN || role === USER_ROLES.INCLUSION) {
            rejectedOption.style.display = 'block';
        } else {
            rejectedOption.style.display = 'none';
            // Clear the filter if it was set to "Rejeté"
            const filterStatus = document.getElementById('filterStatus');
            if (filterStatus && filterStatus.value === 'Rejeté') {
                filterStatus.value = '';
                window.CURRENT_FILTER.status = '';
                renderEvents();
            }
        }
    }
}

        function getRoleDisplayName(role) {
            const displayNames = {
                [USER_ROLES.USER]: 'Utilisateur',
                [USER_ROLES.COMPANY]: 'Entreprise',
                [USER_ROLES.INCLUSION]: 'Responsable Inclusion',
                [USER_ROLES.ADMIN]: 'Administrateur'
            };
            return displayNames[role] || 'Utilisateur';
        }

        // Utility functions
        function formatDate(dateString) {
            if (!dateString) return '';
            const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            return new Date(dateString).toLocaleDateString('fr-FR', options);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function truncate(text, length) {
            return text.length > length ? text.substring(0, length) + '...' : text;
        }

        function isEventPassed(event) {
            if (!event.date) return false;
            return new Date(event.date) < new Date();
        }

        // Event loading and filtering
        function setupFilters() {
            const searchInput = document.getElementById('searchInput');
            const filterStatus = document.getElementById('filterStatus');
            const filterDate = document.getElementById('filterDate');
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    window.CURRENT_FILTER.search = this.value;
                    renderEvents();
                });
            }
            
            if (filterStatus) {
                filterStatus.addEventListener('change', function() {
                    window.CURRENT_FILTER.status = this.value;
                    renderEvents();
                });
            }
            
            if (filterDate) {
                filterDate.addEventListener('change', function() {
                    window.CURRENT_FILTER.date = this.value;
                    renderEvents();
                });
            }
        }

       function loadEvents() {
    const loadingElement = document.getElementById('loadingEvents');
    const eventsGrid = document.getElementById('eventsGrid');
    const noEvents = document.getElementById('noEvents');
    
    loadingElement.style.display = 'block';
    eventsGrid.innerHTML = '';
    noEvents.style.display = 'none';
    
    // Determine which endpoint to call based on user role
    let url = 'get_historique_events.php';
    const params = new URLSearchParams({
        role: window.CURRENT_USER_ROLE,
        userId: SESSION_USER_ID // Use actual session user ID
    });
    
    console.log('DEBUG: Loading events from:', url);
    console.log('DEBUG: Parameters:', {
        role: window.CURRENT_USER_ROLE,
        userId: SESSION_USER_ID,
        fullUrl: `${url}?${params}`
    });
    
    fetch(`${url}?${params}`)
        .then(response => {
            console.log('DEBUG: Response status:', response.status, response.statusText);
            
            // First, check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    console.error('DEBUG: Non-JSON response:', text.substring(0, 500));
                    throw new Error(`Expected JSON but got: ${text.substring(0, 100)}...`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('DEBUG: API Response:', data);
            loadingElement.style.display = 'none';
            
            if (data.success) {
                window.EVENTS = data.events || [];
                updateStatistics();
                renderEvents();
                
                console.log('DEBUG: Loaded events:', {
                    count: window.EVENTS.length,
                    role: window.CURRENT_USER_ROLE,
                    userId: SESSION_USER_ID,
                    debug: data.debug,
                    sample: window.EVENTS[0]
                });
                
                // Show debug info on page for testing
                if (data.debug) {
                    console.log('DEBUG Info:', data.debug);
                    if (window.EVENTS.length === 0 && data.debug.user_participations_count > 0) {
                        console.warn('DEBUG: User has participations but no events returned!', data.debug);
                    }
                }
            } else {
                console.error('DEBUG: API returned error:', data.message);
                throw new Error(data.message || 'Failed to load events');
            }
        })
        .catch(error => {
            console.error('DEBUG: Error loading events:', error);
            loadingElement.style.display = 'none';
            eventsGrid.innerHTML = `
                <div class="glass" style="padding: 20px; text-align: center; grid-column: 1 / -1;">
                    <h3 style="color: #e74c3c; margin-bottom: 10px;">Erreur de chargement</h3>
                    <p style="color: #ccc; margin-bottom: 15px;">${error.message}</p>
                    <p style="color: #999; font-size: 12px;">User ID: ${SESSION_USER_ID}, Role: ${window.CURRENT_USER_ROLE}</p>
                    <button class="cta-button primary" onclick="loadEvents()">Réessayer</button>
                </div>
            `;
        });
}

        function updateStatistics() {
            const events = window.EVENTS || [];
            const totalEvents = events.length;
            const publishedEvents = events.filter(event => event.statut === 'Publié').length;
            const upcomingEvents = events.filter(event => !isEventPassed(event)).length;
            const pastEvents = events.filter(event => isEventPassed(event)).length;
            
            document.getElementById('totalEvents').textContent = totalEvents;
            document.getElementById('publishedEvents').textContent = publishedEvents;
            document.getElementById('upcomingEvents').textContent = upcomingEvents;
            document.getElementById('pastEvents').textContent = pastEvents;
        }

       function renderEvents() {
    const eventsGrid = document.getElementById('eventsGrid');
    const noEvents = document.getElementById('noEvents');
    
    const filteredEvents = (window.EVENTS || []).filter(event => {
        // Prevent regular users from seeing rejected events
        const isUser = window.CURRENT_USER_ROLE === USER_ROLES.USER;
        if (isUser && event.statut === 'Rejeté') {
            return false;
        }
        
        // For companies, only show their own rejected events
        const isCompany = window.CURRENT_USER_ROLE === USER_ROLES.COMPANY;
        if (isCompany && event.statut === 'Rejeté') {
            // Only show rejected events if they created them
            // This is already handled server-side, but adding client-side check for safety
            const isEventCreator = event.idUtilisateur === window.CURRENT_USER_ID;
            if (!isEventCreator) {
                return false;
            }
        }
        
        const searchTerm = window.CURRENT_FILTER.search.toLowerCase();
        if (searchTerm && 
            !event.titre.toLowerCase().includes(searchTerm) && 
            !event.lieu.toLowerCase().includes(searchTerm) &&
            !event.description.toLowerCase().includes(searchTerm)) {
            return false;
        }
        
        // Prevent users from filtering by "Rejeté" status
        if (window.CURRENT_FILTER.status) {
            if (isUser && window.CURRENT_FILTER.status === 'Rejeté') {
                // Ignore rejected filter for users
                return true; // Continue with other filters
            }
            if (event.statut !== window.CURRENT_FILTER.status) {
                return false;
            }
        }
        
        if (window.CURRENT_FILTER.date === 'upcoming' && isEventPassed(event)) {
            return false;
        }
        if (window.CURRENT_FILTER.date === 'past' && !isEventPassed(event)) {
            return false;
        }
        
        return true;
    });
    
    eventsGrid.innerHTML = '';
    
    if (filteredEvents.length === 0) {
        noEvents.style.display = 'block';
        return;
    }
    
    noEvents.style.display = 'none';
    
    filteredEvents.forEach(event => {
        const isPast = isEventPassed(event);
        const companyName = event.nom_entreprise || 'AbeLink';
        const isUser = window.CURRENT_USER_ROLE === USER_ROLES.USER;
        const isAdmin = window.CURRENT_USER_ROLE === USER_ROLES.ADMIN;
        const isCompany = window.CURRENT_USER_ROLE === USER_ROLES.COMPANY;
        const isInclusion = window.CURRENT_USER_ROLE === USER_ROLES.INCLUSION;
        
        // Check if user is the event creator (for companies/admins)
        const isEventCreator = event.idUtilisateur == window.CURRENT_USER_ID;
        
        // For users, check if they participated in this event
        let userParticipated = false;
        if (isUser) {
            // Since we're loading only events user participated in (from query),
            // we can assume they participated
            userParticipated = true;
        }
        
        // Check if user has already evaluated this event
        let userEvaluated = false;
        if (window.CURRENT_USER_EVALUATIONS) {
            userEvaluated = window.CURRENT_USER_EVALUATIONS.some(eval => 
                eval.idEvenement == event.id && eval.idUtilisateur == window.CURRENT_USER_ID
            );
        }
        
        const div = document.createElement('div');
        div.className = 'event-card glass';
        div.setAttribute('role', 'listitem');
        
        div.innerHTML = `
            <div class="event-title">${escapeHtml(event.titre)}</div>
            <div class="company-info">
                <div class="company-logo">${companyName.charAt(0)}</div>
                <div class="company-name">${companyName}</div>
            </div>
            <div class="meta">${formatDate(event.date)} • ${escapeHtml(event.lieu)}</div>
            <div class="small">${truncate(escapeHtml(event.description || ''), 120)}</div>
            
            <div class="tags">
                ${(typeof event.accessibilite === 'string' ? event.accessibilite.split(', ') : event.accessibilite || []).map(acc => `<span class="tag">${escapeHtml(acc)}</span>`).join('')}
                <span class="tag status ${event.statut.toLowerCase()}">${event.statut}</span>
                ${isPast ? '<span class="tag" style="background: rgba(52, 152, 219, 0.2); color: #3498db;">Passé</span>' : '<span class="tag" style="background: rgba(46, 204, 113, 0.2); color: #2ecc71;">À venir</span>'}
            </div>
            
            <div style="margin-top: 10px; display: flex; justify-content: space-between; align-items: center;">
                <div class="small">
                    ${event.participants_max ? `${event.inscrits || 0}/${event.participants_max} participants` : ''}
                </div>
                <div class="small">
                    Créé le ${formatDate(event.date_creation)}
                </div>
            </div>
            
          
<div class="card-actions" style="margin-top: 15px; display: flex; gap: 8px; flex-wrap: wrap;">
    <!-- Details button - always visible -->
    <button class="cta-button" onclick="openEventDetails('${event.id}')">Détails</button>
    
    <!-- Testimonials button - only for past events -->
    ${isPast ? 
        `<button class="cta-button" onclick="viewTestimonials('${event.id}')" style="background: rgba(52, 152, 219, 0.2); color: #3498db;">
            <i class="fa fa-comments"></i> Témoignages
        </button>` : ''}
    
    <!-- Evaluate button - only for past events that user participated in and hasn't evaluated yet -->
    ${isUser && isPast && userParticipated && !userEvaluated ? 
        `<button class="cta-button primary" onclick="openEvaluationModal('${event.id}')">Évaluer</button>` : ''}
    
    <!-- Edit button - for event creators, admins, and inclusion managers -->
    ${(isEventCreator || isAdmin || isInclusion) ? 
        `<button class="cta-button" onclick="editEvent('${event.id}')">Modifier</button>` : ''}
        
    <!-- Admin delete button -->
    ${isAdmin ? 
        `<button class="cta-button danger" onclick="deleteEvent('${event.id}')" style="background: rgba(255, 107, 107, 0.2); color: #ff6b6b;">Supprimer</button>` : ''}
</div>
        `;
        
        eventsGrid.appendChild(div);
    });
}

// Add these new functions to historique.php JavaScript:

function editEvent(eventId) {
    // Redirect to event creation/edit page
    window.location.href = `evaluations-evenements.php?edit=${eventId}`;
}

function deleteEvent(eventId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet événement ? Cette action est irréversible.')) {
        fetch('admin_moderation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'delete_event',
                eventId: eventId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Événement supprimé avec succès.');
                loadEvents(); // Reload the events
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la suppression');
        });
    }
}

function viewTestimonials(eventId) {
    // Redirect to testimonials page
    window.location.href = `../view/FrontOffice/temoignages.php?eventId=${eventId}`;
}

// Evaluation functions
function openEvaluationModal(eventId) {
    const event = window.EVENTS.find(e => e.id == eventId);
    if (!event) {
        alert('Événement non trouvé');
        return;
    }
    
    // Check if user already evaluated this event
    if (window.CURRENT_USER_EVALUATIONS && window.CURRENT_USER_EVALUATIONS.some(e => e.idEvenement == eventId)) {
        alert('Vous avez déjà évalué cet événement.');
        return;
    }
    
    document.getElementById('evaluationEventId').value = eventId;
    document.getElementById('evaluationModalTitle').textContent = `Évaluer: ${event.titre}`;
    document.getElementById('evaluationForm').reset();
    document.getElementById('evaluationModal').style.display = 'block';
}

function closeEvaluationModal() {
    document.getElementById('evaluationModal').style.display = 'none';
}

function handleEvaluationFormSubmit(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitEvaluationBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="loading"></span> Envoi...';
    submitBtn.disabled = true;
    
    const eventId = document.getElementById('evaluationEventId').value;
    const note_accessibilite = parseInt(document.getElementById('note_accessibilite').value);
    const note_inclusion = parseInt(document.getElementById('note_inclusion').value);
    const commentaire = document.getElementById('commentaire').value.trim();
    
    // Validation
    if (!note_accessibilite || note_accessibilite < 1 || note_accessibilite > 5) {
        alert('❌ La note d\'accessibilité doit être entre 1 et 5.');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        return;
    }
    
    if (!note_inclusion || note_inclusion < 1 || note_inclusion > 5) {
        alert('❌ La note d\'inclusion doit être entre 1 et 5.');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        return;
    }
    
    if (!commentaire || commentaire.length < 10) {
        alert('❌ Le commentaire doit contenir au moins 10 caractères.');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        return;
    }
    
    // Prepare data for database
    const evaluationData = {
        action: 'submit',
        idUtilisateur: window.CURRENT_USER_ID,
        idEvenement: parseInt(eventId),
        note_accessibilite: note_accessibilite,
        note_inclusion: note_inclusion,
        commentaire: commentaire
    };
    
    console.log('Submitting evaluation:', evaluationData);
    
    // Send to database via AJAX
    fetch('manage_evaluation.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify(evaluationData)
})
        
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Evaluation response:', data);
        
        if (data.success) {
            alert('✅ Merci pour votre évaluation ! Votre retour est précieux.');
            closeEvaluationModal();
            
            // Refresh the evaluations list
            loadUserEvaluations();
            
            // Update the event card to remove the evaluate button
            const eventCards = document.querySelectorAll('.event-card');
            eventCards.forEach(card => {
                if (card.querySelector(`[onclick*="evaluateEvent('${eventId}')"]`)) {
                    const evaluateBtn = card.querySelector(`[onclick*="evaluateEvent('${eventId}')"]`);
                    if (evaluateBtn) {
                        evaluateBtn.remove();
                    }
                }
            });
        } else {
            alert('❌ Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Erreur lors de l\'envoi de l\'évaluation: ' + error.message);
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function openEventDetails(eventId) {
    document.getElementById('eventDetailsModalTitle').textContent = 'Chargement...';
    document.getElementById('eventDetailsContent').innerHTML = `
        <div style="text-align: center; padding: 40px;">
            <div class="loading"></div>
            <p>Chargement des détails...</p>
        </div>
    `;
    document.getElementById('eventDetailsModal').style.display = 'block';
    
    console.log('Fetching event details for ID:', eventId);
    
    // Fetch event details from database
    fetch(`../Control/get_event_details.php?id=${eventId}`)
        .then(async response => {
            const contentType = response.headers.get('content-type');
            console.log('Response content-type:', contentType);
            
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Non-JSON response (first 200 chars):', text.substring(0, 200));
                throw new Error(`Expected JSON but got: ${contentType}. Check server errors.`);
            }
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Event details received:', data);
            
            if (data.success && data.event) {
                displayEventDetails(data.event);
            } else {
                throw new Error(data.message || 'Événement non trouvé dans la réponse');
            }
        })
        .catch(error => {
            console.error('Error loading event details:', error);
            document.getElementById('eventDetailsContent').innerHTML = `
                <div style="text-align: center; padding: 40px; color: #ff6b6b;">
                    <h4>Erreur de chargement</h4>
                    <p>${escapeHtml(error.message)}</p>
                    <p style="font-size: 12px; color: #999;">Event ID: ${eventId}</p>
                    <button class="cta-button" onclick="openEventDetails(${eventId})" style="margin-top: 15px;">Réessayer</button>
                </div>
            `;
        });
}

function displayEventDetails(event) {
    const isPast = isEventPassed(event);
    const companyName = event.nom_entreprise || event.organisateur || 'AbeLink';
    
    // Format accessibility tags
    const accessibilityTags = (typeof event.accessibilite === 'string' ? 
        event.accessibilite.split(', ') : 
        (event.accessibilite || [])).map(acc => 
            `<span class="tag">${escapeHtml(acc)}</span>`
        ).join('');
    
    // Get evaluations for this event
    let evaluationsHtml = '<p class="muted">Aucune évaluation pour le moment.</p>';
    if (event.evaluations && event.evaluations.length > 0) {
        evaluationsHtml = event.evaluations.map(eval => `
            <div class="evaluation-item">
                <div class="evaluation-header">
                    <div class="evaluation-user">${escapeHtml(eval.prenom || 'Utilisateur')} ${escapeHtml(eval.nom || '')}</div>
                    <div class="evaluation-date">${formatDate(eval.dateEvaluation)}</div>
                </div>
                <div class="evaluation-rating">
                    <div>
                        <small>Accessibilité:</small>
                        <div class="stars">${renderStars(eval.note_accessibilite)} <span class="avg">${eval.note_accessibilite}/5</span></div>
                    </div>
                    <div>
                        <small>Inclusion:</small>
                        <div class="stars">${renderStars(eval.note_inclusion)} <span class="avg">${eval.note_inclusion}/5</span></div>
                    </div>
                </div>
                <div class="evaluation-comment">${escapeHtml(eval.commentaire || 'Pas de commentaire')}</div>
            </div>
        `).join('');
    }
    
    document.getElementById('eventDetailsModalTitle').textContent = event.titre;
    
    document.getElementById('eventDetailsContent').innerHTML = `
        <div class="detail-section">
            <div class="detail-label">Description</div>
            <div class="detail-value">${escapeHtml(event.description || 'Aucune description disponible')}</div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="detail-section">
                <div class="detail-label">Date et heure</div>
                <div class="detail-value">${formatDate(event.date)}</div>
            </div>
            
            <div class="detail-section">
                <div class="detail-label">Lieu</div>
                <div class="detail-value">${escapeHtml(event.lieu)}</div>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="detail-section">
                <div class="detail-label">Organisateur</div>
                <div class="detail-value">${escapeHtml(companyName)}</div>
            </div>
            
            <div class="detail-section">
                <div class="detail-label">Statut</div>
                <div class="detail-value">
                    <span class="tag status ${event.statut.toLowerCase()}">${event.statut}</span>
                </div>
            </div>
        </div>
        
        <div class="detail-section">
            <div class="detail-label">Mesures d'accessibilité</div>
            <div class="tags" style="margin-top: 8px;">
                ${accessibilityTags || '<span class="muted">Aucune mesure spécifiée</span>'}
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="detail-section">
                <div class="detail-label">Participants</div>
                <div class="detail-value">${event.inscrits || 0}/${event.participants_max || 50} inscrits</div>
            </div>
            
            <div class="detail-section">
                <div class="detail-label">Thème</div>
                <div class="detail-value">${escapeHtml(event.theme || 'Inclusion')}</div>
            </div>
        </div>
        
        <div class="detail-section">
            <div class="detail-label">Évaluations (${event.evaluations ? event.evaluations.length : 0})</div>
            <div style="max-height: 300px; overflow-y: auto; margin-top: 10px;">
                ${evaluationsHtml}
            </div>
        </div>
    `;
}

function closeEventDetailsModal() {
    document.getElementById('eventDetailsModal').style.display = 'none';
}

// Add helper function to render stars
function renderStars(rating) {
    const numRating = parseFloat(rating) || 0;
    const fullStars = Math.floor(numRating);
    const hasHalfStar = numRating % 1 >= 0.5;
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
    
    let stars = '';
    for (let i = 0; i < fullStars; i++) stars += '★';
    if (hasHalfStar) stars += '½';
    for (let i = 0; i < emptyStars; i++) stars += '☆';
    
    return stars;
}

        // Handle admin button click
        function handleAdminButtonClick() {
            window.location.href = 'admin_dashboard.php';
        }

        // Initialize when DOM is loaded
        // Initialize when DOM is loaded
        
document.addEventListener('DOMContentLoaded', function() {
    const evaluationForm = document.getElementById('evaluationForm');
    if (evaluationForm) {
        evaluationForm.addEventListener('submit', handleEvaluationFormSubmit);
        console.log('Evaluation form connected');
    }
    console.log('DEBUG: Historique page loaded, initializing...');
    console.log('DEBUG: Session data from PHP:', {
        SESSION_USER_ID: typeof SESSION_USER_ID !== 'undefined' ? SESSION_USER_ID : 'undefined',
        SESSION_USER_ROLE: typeof SESSION_USER_ROLE !== 'undefined' ? SESSION_USER_ROLE : 'undefined',
        SESSION_USER_ROLE_NAME: typeof SESSION_USER_ROLE_NAME !== 'undefined' ? SESSION_USER_ROLE_NAME : 'undefined'
    });
    
    // Use session-based role and user ID from PHP
    window.CURRENT_USER_ROLE = typeof SESSION_USER_ROLE !== 'undefined' ? SESSION_USER_ROLE : USER_ROLES.USER;
    window.CURRENT_USER_ID = typeof SESSION_USER_ID !== 'undefined' ? SESSION_USER_ID : null;
    window.CURRENT_FILTER = { search: '', status: '', date: 'all' };
    window.EVENTS = [];
    
    console.log('DEBUG: Initial state:', {
        role: window.CURRENT_USER_ROLE,
        userId: window.CURRENT_USER_ID,
        sessionRole: typeof SESSION_USER_ROLE !== 'undefined' ? SESSION_USER_ROLE : 'not set'
    });
    
    updateUIForRole();
    
    setupFilters();
    loadEvents();
    
    console.log('DEBUG: Historique page initialized with role:', window.CURRENT_USER_ROLE);
});
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Historique page loaded, initializing...');
            
            // Use session-based role and user ID from PHP
            window.CURRENT_USER_ROLE = typeof SESSION_USER_ROLE !== 'undefined' ? SESSION_USER_ROLE : USER_ROLES.USER;
            window.CURRENT_USER_ID = typeof SESSION_USER_ID !== 'undefined' ? SESSION_USER_ID : null;
            window.CURRENT_FILTER = { search: '', status: '', date: 'all' };
            window.EVENTS = [];
            
            console.log('Initial state:', {
                role: window.CURRENT_USER_ROLE,
                userId: window.CURRENT_USER_ID,
                sessionRole: typeof SESSION_USER_ROLE !== 'undefined' ? SESSION_USER_ROLE : 'not set'
            });
            
            updateUIForRole();
            
            setupFilters();
            loadEvents();
            
            console.log('Historique page initialized with role:', window.CURRENT_USER_ROLE);
        });
        
        // User profile dropdown toggle
        function toggleUserMenu() {
            const menu = document.getElementById('userDropdownMenu');
            if (menu) {
                menu.classList.toggle('show');
            }
        }
        // Close modals when clicking outside
document.addEventListener('click', function(event) {
    const modals = ['evaluationModal', 'eventDetailsModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal && event.target === modal) {
            if (modalId === 'evaluationModal') closeEvaluationModal();
            if (modalId === 'eventDetailsModal') closeEventDetailsModal();
        }
    });
});
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userProfileDropdown');
            const menu = document.getElementById('userDropdownMenu');
            if (dropdown && menu && !dropdown.contains(event.target)) {
                menu.classList.remove('show');
            }
        });
        
        window.toggleUserMenu = toggleUserMenu;
        // Add this function to load user evaluations
function loadUserEvaluations() {
    if (!window.CURRENT_USER_ID) {
        console.error('No user ID found');
        return;
    }
    
    console.log('Loading evaluations for user:', window.CURRENT_USER_ID);
    
    // Show loading
    const evaluationsGrid = document.getElementById('myEvaluationsGrid');
    const noEvaluations = document.getElementById('noMyEvaluations');
    
    if (evaluationsGrid) {
        evaluationsGrid.innerHTML = `
            <div class="glass" style="padding: 20px; text-align: center; grid-column: 1 / -1;">
                <div class="loading"></div>
                <p>Chargement des évaluations...</p>
            </div>
        `;
    }
    
    // Fetch user evaluations from database
    fetch(`manage_evaluation.php?action=get_user_evaluations&userId=${window.CURRENT_USER_ID}`)
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('User evaluations response:', data);
            
            if (evaluationsGrid) {
                evaluationsGrid.innerHTML = '';
            }
            
            if (!data.success || !data.evaluations || data.evaluations.length === 0) {
                if (noEvaluations) {
                    noEvaluations.style.display = 'block';
                    noEvaluations.textContent = 'Vous n\'avez pas encore évalué d\'événements.';
                }
                return;
            }
            
            if (noEvaluations) {
                noEvaluations.style.display = 'none';
            }
            
            // Display each evaluation
            data.evaluations.forEach(evaluation => {
                const div = document.createElement('div');
                div.className = 'event-card glass';
                div.setAttribute('role', 'listitem');
                
                // Calculate average rating
                const avgRating = ((evaluation.note_accessibilite + evaluation.note_inclusion) / 2).toFixed(1);
                
                div.innerHTML = `
                    <div class="event-title">${escapeHtml(evaluation.event_titre || 'Événement')}</div>
                    
                    <div class="company-info">
                        <div class="company-logo">${evaluation.organizer_prenom ? evaluation.organizer_prenom.charAt(0) : 'E'}</div>
                        <div class="company-name">${escapeHtml(evaluation.organizer_prenom || '')} ${escapeHtml(evaluation.organizer_nom || 'Organisateur')}</div>
                    </div>
                    
                    <div class="meta">
                        ${formatDate(evaluation.event_date)} • ${escapeHtml(evaluation.event_lieu || '')}
                    </div>
                    
                    <div style="margin: 15px 0; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <div>
                                <div class="small" style="color: #aaa;">Évalué le:</div>
                                <div style="color: white; font-size: 14px;">${formatDate(evaluation.dateEvaluation)}</div>
                            </div>
                            ${evaluation.signalee ? 
                                '<span style="color: #e74c3c; font-size: 12px; background: rgba(231, 76, 60, 0.2); padding: 4px 8px; border-radius: 12px;">🚩 Signalée</span>' : ''}
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 10px;">
                            <div>
                                <div class="small" style="color: #aaa;">Note d'accessibilité</div>
                                <div class="stars">${renderStars(evaluation.note_accessibilite)} 
                                    <span class="avg" style="color: white;">${evaluation.note_accessibilite}/5</span>
                                </div>
                            </div>
                            <div>
                                <div class="small" style="color: #aaa;">Note d'inclusion</div>
                                <div class="stars">${renderStars(evaluation.note_inclusion)} 
                                    <span class="avg" style="color: white;">${evaluation.note_inclusion}/5</span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="small" style="color: #aaa; margin-bottom: 5px;">Votre commentaire:</div>
                            <div style="color: #ccc; font-style: italic; background: rgba(255,255,255,0.03); padding: 10px; border-radius: 6px;">
                                "${escapeHtml(evaluation.commentaire || 'Pas de commentaire')}"
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-actions">
                        <button class="cta-button" onclick="openEventDetails(${evaluation.idEvenement})">Voir l'événement</button>
                        <button class="cta-button primary" onclick="editEvaluation(${evaluation.id})">Modifier</button>
                        <button class="cta-button danger" onclick="deleteEvaluation(${evaluation.id})" 
                            style="background: rgba(255, 107, 107, 0.1); color: #ff6b6b; border-color: rgba(255,107,107,0.3);">
                            Supprimer
                        </button>
                    </div>
                `;
                
                if (evaluationsGrid) {
                    evaluationsGrid.appendChild(div);
                }
            });
        })
        .catch(error => {
            console.error('Error loading user evaluations:', error);
            if (evaluationsGrid) {
                evaluationsGrid.innerHTML = `
                    <div class="glass" style="padding: 20px; text-align: center; grid-column: 1 / -1;">
                        <h3 style="color: #e74c3c; margin-bottom: 10px;">Erreur de chargement</h3>
                        <p style="color: #ccc; margin-bottom: 15px;">${error.message}</p>
                        <button class="cta-button primary" onclick="loadUserEvaluations()">Réessayer</button>
                    </div>
                `;
            }
        });
}

// Add function to edit evaluation
function editEvaluation(evaluationId) {
    // First get the evaluation details
    fetch(`manage_evaluation.php?action=get&evaluationId=${evaluationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.evaluation) {
                const eval = data.evaluation;
                
                // Open evaluation modal with existing data
                document.getElementById('evaluationEventId').value = eval.idEvenement;
                
                // Get event title for modal
                fetch(`get_event_details.php?id=${eval.idEvenement}`)
                    .then(response => response.json())
                    .then(eventData => {
                        if (eventData.success && eventData.event) {
                            document.getElementById('evaluationModalTitle').textContent = `Modifier évaluation: ${eventData.event.titre}`;
                        }
                    });
                
                // Fill form with existing data
                document.getElementById('note_accessibilite').value = eval.note_accessibilite;
                document.getElementById('note_inclusion').value = eval.note_inclusion;
                document.getElementById('commentaire').value = eval.commentaire || '';
                
                // Store evaluation ID for update
                document.getElementById('evaluationEventId').setAttribute('data-evaluation-id', evaluationId);
                
                // Open modal
                document.getElementById('evaluationModal').style.display = 'block';
            } else {
                alert('Erreur lors du chargement de l\'évaluation');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors du chargement de l\'évaluation');
        });
}

// Add function to delete evaluation
function deleteEvaluation(evaluationId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette évaluation ? Cette action est irréversible.')) {
        return;
    }
    
    fetch('manage_evaluation.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'delete',
            evaluationId: evaluationId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Évaluation supprimée avec succès.');
            loadUserEvaluations(); // Refresh the list
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de la suppression');
    });
}

// Update the evaluation form submission to handle both create and update
function handleEvaluationFormSubmit(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitEvaluationBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="loading"></span> Envoi...';
    submitBtn.disabled = true;
    
    const eventId = document.getElementById('evaluationEventId').value;
    const evaluationId = document.getElementById('evaluationEventId').getAttribute('data-evaluation-id');
    const note_accessibilite = parseInt(document.getElementById('note_accessibilite').value);
    const note_inclusion = parseInt(document.getElementById('note_inclusion').value);
    const commentaire = document.getElementById('commentaire').value.trim();
    
    // Validation
    if (!note_accessibilite || note_accessibilite < 1 || note_accessibilite > 5) {
        alert('❌ La note d\'accessibilité doit être entre 1 et 5.');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        return;
    }
    
    if (!note_inclusion || note_inclusion < 1 || note_inclusion > 5) {
        alert('❌ La note d\'inclusion doit être entre 1 et 5.');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        return;
    }
    
    if (!commentaire || commentaire.length < 10) {
        alert('❌ Le commentaire doit contenir au moins 10 caractères.');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        return;
    }
    
    // Prepare data
    const evaluationData = {
        idUtilisateur: window.CURRENT_USER_ID,
        idEvenement: parseInt(eventId),
        note_accessibilite: note_accessibilite,
        note_inclusion: note_inclusion,
        commentaire: commentaire
    };
    
    // Determine if it's an update or create
    if (evaluationId) {
        evaluationData.action = 'update';
        evaluationData.evaluationId = evaluationId;
    } else {
        evaluationData.action = 'submit';
    }
    
    console.log('Submitting evaluation:', evaluationData);
    
    // Send to database
    fetch('manage_evaluation.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(evaluationData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Evaluation response:', data);
        
        if (data.success) {
            const message = evaluationId ? 'Évaluation modifiée avec succès !' : '✅ Merci pour votre évaluation ! Votre retour est précieux.';
            alert(message);
            closeEvaluationModal();
            
            // Refresh the evaluations list
            loadUserEvaluations();
            
            // Clear stored evaluation ID
            document.getElementById('evaluationEventId').removeAttribute('data-evaluation-id');
        } else {
            alert('❌ Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Erreur lors de l\'envoi de l\'évaluation: ' + error.message);
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}
        
    </script>
    <!-- Evaluation Modal -->
<div id="evaluationModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="evaluationModalTitle">
    <div class="panel glass" role="document" style="max-width: 500px;">
        <button class="cta-button" aria-label="Fermer" style="float: right; padding: 8px 12px;" onclick="closeEvaluationModal()">✕</button>
        <h3 id="evaluationModalTitle">Évaluer un événement</h3>
        <form id="evaluationForm">
            <input type="hidden" id="evaluationEventId">
            <div style="display: grid; gap: 15px;">
                <div>
                    <label for="note_accessibilite" style="color: white;">Note d'accessibilité (1-5) *</label>
                    <select id="note_accessibilite" class="input" required>
                        <option value="">Sélectionnez une note</option>
                        <option value="1">1 - Très mauvais</option>
                        <option value="2">2 - Mauvais</option>
                        <option value="3">3 - Moyen</option>
                        <option value="4">4 - Bon</option>
                        <option value="5">5 - Excellent</option>
                    </select>
                    <small class="small">Évaluez les mesures d'accessibilité (LSF, PMR, sous-titrage...)</small>
                </div>
                <div>
                    <label for="note_inclusion" style="color: white;">Note d'inclusion (1-5) *</label>
                    <select id="note_inclusion" class="input" required>
                        <option value="">Sélectionnez une note</option>
                        <option value="1">1 - Très mauvais</option>
                        <option value="2">2 - Mauvais</option>
                        <option value="3">3 - Moyen</option>
                        <option value="4">4 - Bon</option>
                        <option value="5">5 - Excellent</option>
                    </select>
                    <small class="small">Évaluez l'ambiance inclusive et l'accueil des diversités</small>
                </div>
                <div>
                    <label for="commentaire" style="color: white;">Commentaire détaillé *</label>
                    <textarea id="commentaire" placeholder="Partagez votre expérience, vos suggestions d'amélioration..." required></textarea>
                </div>

                <div style="display: flex; gap: 8px; justify-content: flex-end; margin-top: 6px;">
                    <button type="button" class="cta-button" onclick="closeEvaluationModal()">Annuler</button>
                    <button type="submit" class="cta-button primary" id="submitEvaluationBtn">Soumettre l'évaluation</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Event Details Modal -->
<div id="eventDetailsModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="eventDetailsModalTitle">
    <div class="panel glass" role="document" style="max-width: 800px; max-height: 80vh; overflow-y: auto;">
        <button class="cta-button" aria-label="Fermer" style="float: right; padding: 8px 12px;" onclick="closeEventDetailsModal()">✕</button>
        <h3 id="eventDetailsModalTitle">Détails de l'événement</h3>
        <div id="eventDetailsContent" style="margin-top: 20px;">
            <div style="text-align: center; padding: 40px;">
                <div class="loading"></div>
                <p>Chargement des détails...</p>
            </div>
        </div>
    </div>
</div>

<!-- Theme Toggle Script -->
<script src="../view/FrontOffice/js/theme-toggle.js"></script>

<!-- Pagination Scripts -->
<script src="../view/FrontOffice/js/pagination.js"></script>
<script src="../view/FrontOffice/js/pagination-helper.js"></script>
</body>
</html>