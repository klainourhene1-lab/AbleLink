<?php
session_start();

// Check if user is logged in - redirect to login if not
if (!isset($_SESSION['user_id'])) {
    header('Location: ../general/signin.php');
    exit;
}

// Get user information from session
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'] ?? 'Utilisateur';

// Load user data from database
require_once __DIR__ . '/../../Control/config.php';
require_once __DIR__ . '/../../Control/UserController.php';
require_once __DIR__ . '/../../Model/User.php';

$controller = new UserController();
$user = $controller->showUser($user_id);

// Get user photo URL
function getPhotoUrl($photo, $prenom, $nom) {
    if ($photo && file_exists(__DIR__ . '/../../uploads/profiles/' . $photo)) {
        return '../../uploads/profiles/' . $photo;
    }
    return '../general/img/team/team-1.jpg'; // Default photo
}

$user_photo = $user ? getPhotoUrl($user->getPhoto(), $user->getPrenom(), $user->getNom()) : '../general/img/team/team-1.jpg';
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
    <title>Évaluations & Événements - AbeLink</title>
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../videograph-master/videograph-master/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="../../videograph-master/videograph-master/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="../../videograph-master/videograph-master/css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="../../videograph-master/videograph-master/css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="../../videograph-master/videograph-master/css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="../../videograph-master/videograph-master/css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="../../videograph-master/videograph-master/css/style.css" type="text/css">
    <link rel="stylesheet" href="css/theme-toggle.css" type="text/css">
  <style>
    .header__nav__option { 
        display: flex; 
        align-items: center; 
        justify-content: space-between; 
    }
    .header__nav__menu { 
        flex: 1; 
        min-width: 0;
        display: flex;
        justify-content: flex-end; /* Aligne le menu à droite */
    }
    .header__nav__menu ul { 
        display: flex; 
        gap: 12px; /* Réduit de 24px à 12px */
        align-items: center; 
        margin: 0;
        padding: 0;
        justify-content: flex-end; /* Aligne les items du menu à droite */
    }
    .header__nav__menu ul li {
        margin: 0;
        padding: 0;
    }
    .header__nav__menu ul li a {
        padding: 8px 12px; /* Réduit le padding */
        font-size: 14px; /* Optionnel: taille de police légèrement réduite */
        white-space: nowrap;
    }
    .header__nav__social { 
        display: flex; 
        align-items: center; 
        gap: 12px; 
        flex-wrap: nowrap;
        margin-left: 20px; /* Ajoute un espace entre le menu et les éléments sociaux */
    }
    .header__nav__social .input { height: 36px; padding: 6px 10px; }
    .role-badge { display: inline-flex; align-items: center; height: 36px; }
    .admin-combined-btn { display: inline-flex; align-items: center; height: 36px; }
    .content-wrapper { margin-top: 24px; }
    .page-header { padding: 30px 0; }
    .page-header h1 { margin: 0 0 8px; color: #fff; }
    .page-header p { margin: 0; color: #ccc; }
    
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

    /* Title Bubble */
    .title-bubble {
        display: inline-block;
        background: linear-gradient(135deg, rgba(58, 76, 237, 0.9), rgba(123, 31, 162, 0.9));
        padding: 20px 40px;
        border-radius: 50px;
        box-shadow: 0 8px 32px rgba(58, 76, 237, 0.4);
        color: white;
        font-weight: 700;
        text-align: center;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    /* Role selector */
    .role-selector {
        display: flex;
        align-items: center;
        margin-left: 20px;
    }

    /* Modal styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        backdrop-filter: blur(5px);
    }

    .panel {
        background: rgba(255,255,255,0.1);
        margin: 5% auto;
        padding: 20px;
        border-radius: 12px;
        width: 90%;
        max-width: 600px;
        position: relative;
    }

    /* Form styles */
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .checkbox-group {
        display: grid;
        gap: 10px;
        margin-top: 8px;
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    textarea {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.2);
        background: rgba(255,255,255,0.1);
        color: white;
        font-family: inherit;
        resize: vertical;
        min-height: 100px;
    }

    textarea:focus {
        outline: none;
        border-color: #3498db;
    }

    .small {
        font-size: 12px;
        color: #ccc;
    }

    /* Event card styles */
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
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

    .card-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 15px;
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

    /* Text overflow fixes */
    .event-card {
        overflow: hidden;
        word-wrap: break-word;
    }

    .event-title,
    .event-description,
    .event-detail {
        overflow: hidden;
        text-overflow: ellipsis;
        word-wrap: break-word;
        word-break: break-word;
    }

    .event-description {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Ensure ALL text in cards wraps properly */
    .event-card,
    .event-card *,
    .glass,
    .glass * {
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-word;
        hyphens: auto;
    }

    /* Specific elements that need wrapping */
    .meta,
    .company-name,
    .tag,
    .small,
    .event-detail,
    p,
    span,
    div {
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
</style>
    </head>
<body>
    <header class="header">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="header__logo">
                        <div class="site-title">
        <a href="../general/index.php">
            <span class="letter-a">A</span>
            <span class="letter-b">b</span>
            <span class="letter-l">l</span>
            <span class="letter-e">e</span>
            <span class="letter-link">Link</span>
        </a>
    </div>
    <style> .site-title {
       margin-top: -0px;
       margin-left: -15px; /* Move logo more to the left */
    padding: 0;
}

.site-title a {
    font-family: 'Josefin Sans', sans-serif;
    font-size: 26px; /* Reduced from 32px */
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
                                <li><a href="../general/index.php">Accueil</a></li>
                                <li class="active"><a href="evaluations-evenements.php">Évaluations & Événements</a></li>
                                <li><a href="../../Control/historique.php">Historique</a></li>
                                <?php if ($user_role === 'Admin'): ?>
                                <li><a href="../../Control/admin_dashboard.php">Administration</a></li>
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
                                    <a href="../general/profile.php" class="user-dropdown-item">
                                        <i class="fa fa-user"></i> Mon Profil
                                    </a>
                                    <a href="../general/profile.php" class="user-dropdown-item">
                                        <i class="fa fa-cog"></i> Paramètres
                                    </a>
                                    <?php if ($user_role === 'Admin'): ?>
                                    <a href="../../Control/admin_dashboard.php" class="user-dropdown-item">
                                        <i class="fa fa-dashboard"></i> Administration
                                    </a>
                                    <?php endif; ?>
                                    <a href="../../Control/historique.php" class="user-dropdown-item">
                                        <i class="fa fa-history"></i> Historique
                                    </a>
                                    <a href="../general/logout.php" class="user-dropdown-item" style="color: #e74c3c;">
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

    <section class="services spad">
    <div class="container">
        <div class="content-wrapper">
            <section class="page-header">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <h1 class="title-bubble">Évaluations & Événements Inclusifs</h1>
                            <p>Découvrez, participez et évaluez les événements inclusifs.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="services spad" style="margin-top: 0; padding-top: 40px;">
                <h2 style="margin: 6px 0 12px; color: white;">Événements disponibles</h2>
                
                <div class="filters glass" style="padding: 20px; margin-bottom: 20px;">
                    <div style="display: grid; gap: 10px;">
                        <input id="searchInput" class="input" type="search" placeholder="Rechercher par titre, lieu..." aria-label="Rechercher événements"/>
                        <select id="filterAccess" class="input" aria-label="Filtrer par accessibilité">
                            <option value="">Tous les types d'accessibilité</option>
                            <option>Langue des signes</option>
                            <option>Accès PMR</option>
                            <option>Sous-titrage</option>
                            <option>Interprète LSF</option>
                            <option>Visio (en ligne)</option>
                        </select>
                        <select id="filterCompany" class="input" aria-label="Filtrer par entreprise">
                            <option value="">Toutes les entreprises</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 8px; margin-top: 12px; flex-wrap: wrap;">
                        <button class="cta-button" onclick="filterByDate('upcoming')">À venir</button>
                        <button class="cta-button primary" onclick="openEventModal()" id="createEventBtn" style="margin-left: auto; display: none;">+ Créer un événement</button>
                    </div>
                </div>
                
                <div id="eventsGrid" class="grid" role="list" aria-live="polite"></div>
                <div id="eventsPagination" class="pagination-controls" style="display: none; margin-top: 20px; text-align: center;"></div>
                <div id="noEvents" class="muted" style="display: none; margin-top: 12px; text-align: center;">Aucun événement trouvé.</div>
            </section>

            <?php if ($user_role !== 'Admin'): ?>
            <section class="services spad" style="margin-top: 0; padding-top: 40px; display: none;">
                <h2 style="margin: 6px 0 12px; color: white;">Événements à évaluer</h2>
                <p class="muted" style="margin-bottom: 15px;">Évaluez les événements passés auxquels vous avez participé.</p>
                <div id="eventsToEvaluateGrid" class="grid" role="list" aria-live="polite"></div>
                <div id="eventsToEvaluatePagination" class="pagination-controls" style="display: none; margin-top: 20px; text-align: center;"></div>
                <div id="noEventsToEvaluate" class="muted" style="display: none; margin-top: 12px; text-align: center;">Aucun événement à évaluer pour le moment.</div>
            </section>
            <?php endif; ?>

            <?php if ($user_role !== 'Admin'): ?>
            <section class="services spad" style="margin-top: 0; padding-top: 40px; display: none;">
                <h2 style="margin: 6px 0 12px; color: white;">Mes évaluations</h2>
                <p class="muted" style="margin-bottom: 15px;">Consultez les évaluations que vous avez déjà soumises.</p>
                <div id="myEvaluationsGrid" class="grid" role="list" aria-live="polite"></div>
                <div id="myEvaluationsPagination" class="pagination-controls" style="display: none; margin-top: 20px; text-align: center;"></div>
                <div id="noMyEvaluations" class="muted" style="display: none; margin-top: 12px; text-align: center;">Vous n'avez pas encore évalué d'événements.</div>
            </section>
            <?php endif; ?>
        </div>
    </div>
    </section>

    <!-- Rest of your modals remain the same -->
    <div id="eventModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="eventModalTitle">
        <div class="panel glass" role="document">
            <button class="cta-button" aria-label="Fermer" style="float: right; padding: 8px 12px;" onclick="closeEventModal()">✕</button>
            <h3 id="eventModalTitle">Créer un événement inclusif</h3>
            <form id="eventForm">
                <input type="hidden" id="eventId">
                <div style="display: grid; gap: 15px;">
                    <div>
                        <label for="titre" style="color: white;">Titre de l'événement *</label>
                        <input id="titre" class="input" required placeholder="Ex: Atelier Accessibilité Numérique">
                    </div>
                    <div>
                        <label for="description" style="color: white;">Description détaillée *</label>
                        <textarea id="description" required placeholder="Décrivez votre événement, son objectif, son public cible..."></textarea>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <label for="date_evenement" style="color: white;">Date de l'événement *</label>
                            <input id="date_evenement" type="datetime-local" class="input" required>
                        </div>
                        <div class="col">
                            <label for="participants_max" style="color: white;">Nombre maximum de participants</label>
                            <input id="participants_max" type="number" class="input" min="1" value="50">
                        </div>
                    </div>
                    <div>
                        <label for="lieu" style="color: white;">Lieu ou lien de participation *</label>
                        <input id="lieu" class="input" required placeholder="Adresse physique ou lien de visioconférence">
                    </div>
                    <div>
                        <label style="color: white;">Mesures d'accessibilité proposées *</label>
                        <div class="checkbox-group">
                            <div class="checkbox-item">
                                <input type="checkbox" id="access_lsf" value="Langue des signes">
                                <label for="access_lsf" style="color: white;">Langue des signes (LSF)</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="access_pmr" value="Accès PMR">
                                <label for="access_pmr" style="color: white;">Accès PMR</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="access_sous_titres" value="Sous-titrage">
                                <label for="access_sous_titres" style="color: white;">Sous-titrage</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="access_visio" value="Visio (en ligne)">
                                <label for="access_visio" style="color: white;">Participation en ligne</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="access_autre" value="Autre">
                                <label for="access_autre" style="color: white;">Autre mesure d'accessibilité</label>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="statut" style="color: white;">Statut de publication</label>
                        <select id="statut" class="input">
                            <option value="Brouillon">Brouillon (non visible)</option>
                            <option value="Publié">Publié (visible par tous)</option>
                        </select>
                        <small class="small">Les événements des entreprises doivent être validés par un modérateur avant publication.</small>
                    </div>

                    <div style="display: flex; gap: 8px; justify-content: flex-end; margin-top: 6px;">
                        <button type="button" class="cta-button" onclick="closeEventModal()">Annuler</button>
                        <button type="submit" class="cta-button primary" id="submitEventBtn">Enregistrer l'événement</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="evaluationModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="evaluationModalTitle">
        <div class="panel glass" role="document">
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
                        <label for="commentaire" style="color: white;">Commentaire détaillé</label>
                        <textarea id="commentaire" placeholder="Partagez votre expérience, vos suggestions d'amélioration..."></textarea>
                    </div>

                    <div style="display: flex; gap: 8px; justify-content: flex-end; margin-top: 6px;">
                        <button type="button" class="cta-button" onclick="closeEvaluationModal()">Annuler</button>
                        <button type="submit" class="cta-button primary">Soumettre l'évaluation</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="detailsModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="detailsTitle">
        <div class="panel glass" role="document" style="max-width: 900px;">
            <button class="cta-button" aria-label="Fermer" style="float: right; padding: 8px 12px;" onclick="closeDetailsModal()">✕</button>
            <h3 id="detailsTitle">Détails de l'événement</h3>
            <div id="detailsContent"></div>
        </div>
    </div>

    <script src="../../videograph-master/videograph-master/js/jquery-3.3.1.min.js"></script>
    <script src="../../videograph-master/videograph-master/js/bootstrap.min.js"></script>
    <script src="../../videograph-master/videograph-master/js/jquery.slicknav.js"></script>
    <script src="../../videograph-master/videograph-master/js/owl.carousel.min.js"></script>
    <script src="../../videograph-master/videograph-master/js/jquery.magnific-popup.min.js"></script>
    <script src="../../videograph-master/videograph-master/js/mixitup.min.js"></script>
    <script src="../../videograph-master/videograph-master/js/masonry.pkgd.min.js"></script>
    <script src="../../videograph-master/videograph-master/js/main.js"></script>
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

        // Store management disabled: always use database
        function loadStore() { return { events: [], companies: [], users: [], evaluations: [] }; }
        function saveStore() { /* no-op: use database only */ }

        // User role management - uses session-based role from PHP
        function loadUserRole() {
            // Use session role from PHP, fallback to default
            return typeof SESSION_USER_ROLE !== 'undefined' ? SESSION_USER_ROLE : USER_ROLES.USER;
        }
        
        function updateUIForRole() {
            const role = window.CURRENT_USER_ROLE || USER_ROLES.USER;
            const roleBadge = document.getElementById('roleBadge');
            const createEventBtn = document.getElementById('createEventBtn');
            
            // Update role badge (already set by PHP, ensure it's correct)
            if (roleBadge) {
                roleBadge.textContent = getRoleDisplayName(role);
                roleBadge.className = `role-badge ${role}`;
            }
            
            // Update create event button visibility
            if (createEventBtn) {
                createEventBtn.style.display = canCreateEvents() ? 'block' : 'none';
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

        function canCreateEvents() {
            const role = window.CURRENT_USER_ROLE || USER_ROLES.USER;
            return role === USER_ROLES.COMPANY || role === USER_ROLES.ADMIN || role === USER_ROLES.INCLUSION;
        }

        // Utility functions
        function makeId() {
            return 'id_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now().toString(36);
        }

        function todayISO() {
            return new Date().toISOString().split('T')[0];
        }

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

        function calculateEventRating(event) {
            if (!event.evaluations || event.evaluations.length === 0) {
                return { access: 0, inclusion: 0, count: 0 };
            }
            
            const accessSum = event.evaluations.reduce((sum, evaluation) => sum + (evaluation.note || 0), 0);
            const inclusionSum = event.evaluations.reduce((sum, evaluation) => sum + (evaluation.note || 0), 0);
            const count = event.evaluations.length;
            
            return {
                access: (accessSum / count).toFixed(1),
                inclusion: (inclusionSum / count).toFixed(1),
                count: count
            };
        }

        function renderStars(rating) {
            const numRating = parseFloat(rating) || 0;
            const fullStars = Math.floor(numRating);
            const hasHalfStar = numRating % 1 >= 0.5;
            const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
            
            let stars = '';
            for (let i = 0; i < fullStars; i++) {
                stars += '★';
            }
            if (hasHalfStar) {
                stars += '½';
            }
            for (let i = 0; i < emptyStars; i++) {
                stars += '☆';
            }
            
            return stars;
        }

        function getCompanyById(companyId) {
            return (window.COMPANIES || []).find(company => company.id === companyId);
        }

        function getCompanyName(companyId) {
            const company = getCompanyById(companyId);
            return company ? company.nom : 'AbeLink';
        }

        // Filter functions
        function setupFilters() {
            const searchInput = document.getElementById('searchInput');
            const filterAccess = document.getElementById('filterAccess');
            const filterCompany = document.getElementById('filterCompany');
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    window.CURRENT_FILTER.search = this.value;
                    if (typeof renderEvents === 'function') renderEvents();
                });
            }
            
            if (filterAccess) {
                filterAccess.addEventListener('change', function() {
                    window.CURRENT_FILTER.access = this.value;
                    if (typeof renderEvents === 'function') renderEvents();
                });
            }
            
            if (filterCompany) {
                filterCompany.addEventListener('change', function() {
                    window.CURRENT_FILTER.company = this.value;
                    if (typeof renderEvents === 'function') renderEvents();
                });
                
                // Populate company filter
                populateCompanyFilter();
            }
        }

        function populateCompanyFilter() {
            const filterCompany = document.getElementById('filterCompany');
            if (!filterCompany) return;
            
            // Clear existing options except the first one
            while (filterCompany.options.length > 1) {
                filterCompany.remove(1);
            }
            
            // Add companies
            (window.COMPANIES || []).forEach(company => {
                const option = document.createElement('option');
                option.value = company.id;
                option.textContent = company.nom;
                filterCompany.appendChild(option);
            });
        }

        function filterByDate() {
            window.CURRENT_FILTER.date = 'upcoming';
            if (typeof renderEvents === 'function') renderEvents();
        }

        // Modal functions
        function openEventModal(eventId = null) {
            const modal = document.getElementById('eventModal');
            const title = document.getElementById('eventModalTitle');
            const form = document.getElementById('eventForm');
            
            if (eventId) {
                // Edit mode
                title.textContent = 'Modifier l\'événement';
                const event = window.EVENTS.find(e => e.id === eventId);
                if (event) {
                    document.getElementById('eventId').value = event.id;
                    document.getElementById('titre').value = event.titre || '';
                    document.getElementById('description').value = event.description || '';
                    document.getElementById('date_evenement').value = event.date ? event.date.replace(' ', 'T') : '';
                    document.getElementById('lieu').value = event.lieu || '';
                    document.getElementById('participants_max').value = event.participants_max || 50;
                    document.getElementById('statut').value = event.statut || 'Brouillon';
                    
                    // Reset checkboxes
                    document.querySelectorAll('.checkbox-item input').forEach(cb => {
                        cb.checked = false;
                    });
                    
                    // Set accessibility checkboxes
                    (event.accessibilite || []).forEach(acc => {
                        const checkbox = document.querySelector(`input[value="${acc}"]`);
                        if (checkbox) checkbox.checked = true;
                    });
                }
            } else {
                // Create mode
                title.textContent = 'Créer un événement inclusif';
                form.reset();
                document.getElementById('eventId').value = '';
                document.getElementById('statut').value = 'Brouillon';
                // Set default date to tomorrow
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                document.getElementById('date_evenement').value = tomorrow.toISOString().slice(0, 16);
            }
            
            modal.style.display = 'block';
        }

        function closeEventModal() {
            document.getElementById('eventModal').style.display = 'none';
        }

        function openEvaluationModal(eventId) {
            const event = window.EVENTS.find(e => e.id === eventId);
            if (!event) return;
            
            // Check if user is admin or company and if they created this event
            const isAdminOrCompany = window.CURRENT_USER_ROLE === 'admin' || window.CURRENT_USER_ROLE === 'company';
            const isEventCreator = event.idUtilisateur === window.CURRENT_USER_ID;
            
            if (isAdminOrCompany && isEventCreator) {
                alert('Vous ne pouvez pas évaluer un événement que vous avez créé.');
                return;
            }
            
            const modal = document.getElementById('evaluationModal');
            document.getElementById('evaluationEventId').value = eventId;
            
            if (event) {
                document.getElementById('evaluationModalTitle').textContent = `Évaluer: ${event.titre}`;
            }
            
            modal.style.display = 'block';
        }

        function closeEvaluationModal() {
            document.getElementById('evaluationModal').style.display = 'none';
            document.getElementById('evaluationForm').reset();
        }

   function openEventDetails(eventId) {
    const modal = document.getElementById('detailsModal');
    const content = document.getElementById('detailsContent');
    const title = document.getElementById('detailsTitle');
    
    const event = window.EVENTS.find(e => e.id == eventId);
    if (event) {
        title.textContent = event.titre;
        
        const company = getCompanyById(event.idUtilisateur);
        const rating = calculateEventRating(event);
        const isPastEvent = isEventPassed(event);
        const isAdmin = window.CURRENT_USER_ROLE === 'admin';
        const isInclusion = window.CURRENT_USER_ROLE === 'inclusion';
        const isCompany = window.CURRENT_USER_ROLE === 'company';
        const isEventCreator = event.idUtilisateur === window.CURRENT_USER_ID;
        // Companies and admins cannot join any events
        const canJoinOrEvaluate = !isCompany && !isAdmin;
        
        // Base event info
        let eventHTML = `
            <div style="display: grid; gap: 20px;">
                <div>
                    <h4 style="margin: 0 0 10px 0; color: white;">Description</h4>
                    <p style="color: #ccc;">${escapeHtml(event.description || 'Aucune description disponible.')}</p>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <h4 style="margin: 0 0 10px 0; color: white;">Informations</h4>
                        <p style="color: #ccc;"><strong>Date:</strong> ${formatDate(event.date)}</p>
                        <p style="color: #ccc;"><strong>Lieu:</strong> ${escapeHtml(event.lieu)}</p>
                        <p style="color: #ccc;"><strong>Organisateur:</strong> ${company ? company.nom : 'AbeLink'}</p>
                        <p style="color: #ccc;"><strong>Participants:</strong> ${event.inscrits || 0}/${event.participants_max || 50}</p>
                        <p style="color: #ccc;"><strong>Statut:</strong> ${event.statut}</p>
                    </div>
                    
                    <div>
                        <h4 style="margin: 0 0 10px 0; color: white;">Accessibilité</h4>
                        <div class="tags">
                            ${(typeof event.accessibilite === 'string' ? event.accessibilite.split(', ') : event.accessibilite || []).map(acc => `<span class="tag">${escapeHtml(acc)}</span>`).join('')}
                            ${event.accessibilite && event.accessibilite.length === 0 ? '<span class="muted">Aucune mesure spécifiée</span>' : ''}
                        </div>
                    </div>
                </div>
                
                ${event.evaluations && event.evaluations.length > 0 ? `
                    <div>
                        <h4 style="margin: 0 0 10px 0; color: white;">Notes moyennes</h4>
                        <div style="display: flex; gap: 30px; margin-bottom: 15px;">
                            <div>
                                <div class="small">Accessibilité</div>
                                <div class="stars">${renderStars(rating.access)} <span class="avg">${rating.access}/5</span></div>
                            </div>
                            <div>
                                <div class="small">Inclusion</div>
                                <div class="stars">${renderStars(rating.inclusion)} <span class="avg">${rating.inclusion}/5</span></div>
                            </div>
                        </div>
                    </div>
                ` : ''}
                
                <div id="evaluationsContainer">
                    <div style="text-align: center; padding: 20px;">
                        <div class="loading"></div>
                        <p>Chargement des évaluations...</p>
                    </div>
                </div>
                
                <div class="card-actions">
                    ${!isPastEvent && event.statut === 'Publié' && !isFull(event) && canJoinOrEvaluate ? 
                        `<button class="cta-button primary" onclick="registerForEvent('${event.id}'); closeDetailsModal();">S'inscrire</button>` : ''}
                        
                    ${isPastEvent && canJoinOrEvaluate ? 
                        `<button class="cta-button primary" onclick="openEvaluationModal('${event.id}'); closeDetailsModal();">Évaluer cet événement</button>` : ''}
                        
                    ${(event.idUtilisateur === window.CURRENT_USER_ID) || isAdmin || isInclusion ? 
                        `<button class="cta-button" onclick="editEvent('${event.id}'); closeDetailsModal();">Modifier l'événement</button>` : ''}
                        
                    ${isAdmin ? 
                        `<button class="cta-button danger" onclick="deleteEventFromDetails('${event.id}')" style="background: rgba(255, 107, 107, 0.2); color: var(--danger);">Supprimer l'événement</button>` : ''}
                </div>
            </div>
        `;
        
        content.innerHTML = eventHTML;
        
        // Load evaluations from database
        loadEventEvaluations(eventId, isPastEvent, isAdmin || isInclusion);
        
        modal.style.display = 'block';
    }
}

function closeDetailsModal() {
    document.getElementById('detailsModal').style.display = 'none';
}

function loadEventEvaluations(eventId, showReportButtons = false, showModerationButtons = false) {
    fetch(`../../Control/admin_moderation.php?action=get_event_evaluations&eventId=${eventId}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('evaluationsContainer');
            
            if (data.success && data.evaluations && data.evaluations.length > 0) {
                container.innerHTML = `
                    <h4 style="margin: 20px 0 10px 0; color: white;">Évaluations (${data.evaluations.length})</h4>
                    <div style="max-height: 400px; overflow-y: auto;">
                        ${data.evaluations.map(evaluation => `
                            <div class="glass" style="padding: 15px; margin-bottom: 10px; border-left: 4px solid ${evaluation.signalee ? '#e74c3c' : '#2ecc71'}">
                                <div style="display: flex; justify-content: space-between; align-items: start;">
                                    <div>
                                        <strong>${evaluation.prenom} ${evaluation.nom}</strong>
                                        <span class="small muted"> • ${formatDate(evaluation.dateEvaluation)}</span>
                                        ${evaluation.signalee ? '<span style="color: #e74c3c; margin-left: 10px;">🚩 Signalée</span>' : ''}
                                    </div>
                                    <div style="text-align: right;">
                                        <div class="small">Accès: ${evaluation.note_accessibilite}/5</div>
                                        <div class="small">Inclusion: ${evaluation.note_inclusion}/5</div>
                                    </div>
                                </div>
                                <div style="margin-top: 8px; color: #ccc;">${escapeHtml(evaluation.commentaire || 'Aucun commentaire')}</div>
                                <div style="margin-top: 10px; display: flex; gap: 10px;">
                                    ${showReportButtons && !evaluation.signalee ? `
                                        <button class="cta-button" onclick="reportEvaluation(${evaluation.id})" style="padding: 6px 12px; font-size: 12px; background: rgba(231, 76, 60, 0.2); color: #e74c3c;">
                                            🚩 Signaler
                                        </button>
                                    ` : ''}
                                    
                                    ${showModerationButtons && evaluation.signalee ? `
                                        <button class="cta-button primary" onclick="approveEvaluationFromDetails(${evaluation.id}, '${eventId}')" style="padding: 6px 12px; font-size: 12px;">
                                            ✅ Approuver
                                        </button>
                                        <button class="cta-button" onclick="rejectEvaluationFromDetails(${evaluation.id}, '${eventId}')" style="padding: 6px 12px; font-size: 12px; background: rgba(231, 76, 60, 0.2); color: #e74c3c;">
                                            ❌ Supprimer
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
            } else {
                container.innerHTML = '<p class="muted">Aucune évaluation pour le moment.</p>';
            }
        })
        .catch(error => {
            console.error('Error loading evaluations:', error);
            document.getElementById('evaluationsContainer').innerHTML = '<p class="muted">Erreur lors du chargement des évaluations.</p>';
        });
}

// New functions for evaluation reporting and moderation
function reportEvaluation(evaluationId) {
    if (confirm('Signaler cette évaluation pour modération ?')) {
        fetch('../Control/manage_evaluation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'report',
                evaluationId: evaluationId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Évaluation signalée pour modération.');
                // Reload the evaluations
                const eventId = document.getElementById('evaluationEventId')?.value;
                if (eventId) {
                    loadEventEvaluations(eventId, true, false);
                }
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors du signalement');
        });
    }
}

function approveEvaluationFromDetails(evaluationId, eventId) {
    if (confirm('Approuver cette évaluation ?')) {
        fetch('../Control/admin_moderation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'approve_evaluation',
                evaluationId: evaluationId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Évaluation approuvée.');
                loadEventEvaluations(eventId, false, true);
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de l\'approbation');
        });
    }
}

function rejectEvaluationFromDetails(evaluationId, eventId) {
    if (confirm('Supprimer cette évaluation ?')) {
        fetch('../Control/admin_moderation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'reject_evaluation',
                evaluationId: evaluationId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Évaluation supprimée.');
                loadEventEvaluations(eventId, false, true);
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

function deleteEventFromDetails(eventId) {
    if (confirm('Supprimer définitivement cet événement ? Toutes les évaluations et participations associées seront également supprimées.')) {
        fetch('../Control/admin_moderation.php', {
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
                closeDetailsModal();
                // Refresh events list
                if (typeof renderEvents === 'function') renderEvents();
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

// Add this helper function
function isFull(event) {
    return (event.inscrits || 0) >= (event.participants_max || 50);
}

        function closeDetailsModal() {
            document.getElementById('detailsModal').style.display = 'none';
        }

        // Event participation
        function registerForEvent(eventId) {
            console.log('registerForEvent called with eventId:', eventId, 'type:', typeof eventId);
            console.log('Current user ID:', window.CURRENT_USER_ID);
            console.log('Current user role:', window.CURRENT_USER_ROLE);
            console.log('Available events:', window.EVENTS);
            console.log('Event IDs in EVENTS:', window.EVENTS ? window.EVENTS.map(e => ({id: e.id, type: typeof e.id})) : 'No events');
            
            // Use loose equality (==) to handle type mismatch between string and number
            const event = window.EVENTS.find(e => e.id == eventId);
            if (!event) {
                console.error('Event not found:', eventId);
                alert('Événement introuvable. Veuillez rafraîchir la page.');
                return;
            }
            
            console.log('Found event:', event);
            
            // Prevent companies and admins from registering for any events
            if (window.CURRENT_USER_ROLE === 'company') {
                alert('Les entreprises ne peuvent pas s\'inscrire à des événements.');
                return;
            }
            
            if (window.CURRENT_USER_ROLE === 'admin') {
                alert('Les administrateurs ne peuvent pas s\'inscrire à des événements.');
                return;
            }
            
            // Check if event is full
            if (event.inscrits >= event.participants_max) {
                alert('Désolé, cet événement est complet.');
                return;
            }
            
            console.log('Sending registration request...');
            
            // Register user via backend
            fetch('../../Control/manage_participation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'register',
                    idUtilisateur: window.CURRENT_USER_ID,
                    idEvenement: eventId
                })
            })
            .then(response => {
                console.log('Response received:', response);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Registration response data:', data);
                
                if (data.success) {
                    alert('Inscription confirmée ! Nous avons hâte de vous voir.');
                    // Reload canonical data from DB and refresh lists
                    loadEventsFromDatabase().then(() => {
                        renderEvents();
                        renderEventsToEvaluate();
                        renderMyEvaluations();
                    });
                } else {
                    alert('Erreur lors de l\'inscription: ' + (data.message || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Error during registration:', error);
                alert('Erreur lors de l\'enregistrement: ' + error.message);
            });
        }


        function editEvent(eventId) {
            openEventModal(eventId);
        }

        // Database export functions
        function exportEvents(format) {
            const btn = document.querySelector(`.export-btn[onclick*="${format}"]`);
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Export...';
            
            // Get current filters
            const params = new URLSearchParams();
            params.append('format', format);
            
            if (window.CURRENT_FILTER.search) params.append('search', window.CURRENT_FILTER.search);
            if (window.CURRENT_FILTER.access) params.append('access', window.CURRENT_FILTER.access);
            if (window.CURRENT_FILTER.company) params.append('company', window.CURRENT_FILTER.company);
            
            // Prepare the data
            const exportData = {
                action: 'export_events',
                events: window.EVENTS || []
            };
            
            console.log('Exporting events:', window.EVENTS);
            
            // Send the data to the server via AJAX
            fetch(`../../Control/export_events.php?${params.toString()}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(exportData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Export response:', data);
                if (data.success) {
                    resultDiv.innerHTML = `<p style="color: #2ecc71;">${data.message}</p>`;
                    // Show statistics
                    if (data.stats) {
                        document.getElementById('exportStats').style.display = 'block';
                        document.getElementById('eventsCount').textContent = data.stats.events;
                        document.getElementById('evaluationsCount').textContent = data.stats.evaluations;
                        document.getElementById('participationsCount').textContent = data.stats.participations;
                    }
                } else {
                    resultDiv.innerHTML = `<p style="color: #e74c3c;">Erreur: ${data.message}</p>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultDiv.innerHTML = `<p style="color: #e74c3c;">Erreur lors de l'export: ${error.message}</p>`;
            });
        }

        function checkDBConnection() {
            const resultDiv = document.getElementById('exportResult');
            resultDiv.innerHTML = '<p style="color: #3498db;">Test de connexion en cours...</p>';
            
            fetch('../Control/export_events.php?action=test_connection')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Connection test response:', data);
                if (data.success) {
                    resultDiv.innerHTML = `<p style="color: #2ecc71;">${data.message}</p>`;
                } else {
                    resultDiv.innerHTML = `<p style="color: #e74c3c;">Erreur: ${data.message}</p>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultDiv.innerHTML = `<p style="color: #e74c3c;">Erreur lors du test: ${error.message}</p>`;
            });
        }

        function viewExportedEvents() {
            // This would typically redirect to a page showing events from the database
            alert('Cette fonctionnalité afficherait les événements exportés depuis la base de données.');
        }

        function updateDBConfig() {
            const host = document.getElementById('dbHost').value;
            const dbName = document.getElementById('dbName').value;
            const user = document.getElementById('dbUser').value;
            const password = document.getElementById('dbPass').value;
            
            // In a real application, you'd send this to a configuration file
            // For this demo, we'll just show a confirmation
            alert(`Configuration mise à jour:\nHôte: ${host}\nBase: ${dbName}\nUtilisateur: ${user}`);
        }

        // Event form handling
        function handleEventFormSubmit(e) {
            e.preventDefault();
            console.log('Event form submitted');
            
            if (!canCreateEvents()) {
                alert('Seules les entreprises vérifiées et l\'équipe AbeLink peuvent créer des événements.');
                return;
            }
            
            const eventId = document.getElementById('eventId').value;
            const titre = document.getElementById('titre').value.trim();
            const description = document.getElementById('description').value.trim();
            const date_evenement = document.getElementById('date_evenement').value;
            const lieu = document.getElementById('lieu').value.trim();
            const participants_max = parseInt(document.getElementById('participants_max').value) || 50;
            const statut = document.getElementById('statut').value;
            
            const accessibilite = [];
            document.querySelectorAll('.checkbox-item input:checked').forEach(cb => {
                accessibilite.push(cb.value);
            });
            
            console.log('Form data:', { titre, date_evenement, lieu, description, accessibilite });
            
            // ===== COMPREHENSIVE INPUT VALIDATION (Contrôle de saisie) =====
            
            // Validate title
            if (!titre) {
                alert('❌ Le titre est obligatoire.');
                document.getElementById('titre').focus();
                return;
            }
            if (titre.length < 5) {
                alert('❌ Le titre doit contenir au moins 5 caractères.');
                document.getElementById('titre').focus();
                return;
            }
            if (titre.length > 200) {
                alert('❌ Le titre ne peut pas dépasser 200 caractères.');
                document.getElementById('titre').focus();
                return;
            }
            
            // Validate description
            if (!description) {
                alert('❌ La description est obligatoire.');
                document.getElementById('description').focus();
                return;
            }
            if (description.length < 20) {
                alert('❌ La description doit contenir au moins 20 caractères pour être informative.');
                document.getElementById('description').focus();
                return;
            }
            if (description.length > 2000) {
                alert('❌ La description ne peut pas dépasser 2000 caractères.');
                document.getElementById('description').focus();
                return;
            }
            
            // Validate date
            if (!date_evenement) {
                alert('❌ La date de l\'événement est obligatoire.');
                document.getElementById('date_evenement').focus();
                return;
            }
            const eventDate = new Date(date_evenement);
            const now = new Date();
            if (eventDate < now) {
                alert('❌ La date de l\'événement doit être dans le futur.');
                document.getElementById('date_evenement').focus();
                return;
            }
            
            // Validate location
            if (!lieu) {
                alert('❌ Le lieu ou lien de participation est obligatoire.');
                document.getElementById('lieu').focus();
                return;
            }
            if (lieu.length < 3) {
                alert('❌ Le lieu doit contenir au moins 3 caractères.');
                document.getElementById('lieu').focus();
                return;
            }
            
            // Validate participants max
            if (participants_max < 1) {
                alert('❌ Le nombre maximum de participants doit être au moins 1.');
                document.getElementById('participants_max').focus();
                return;
            }
            if (participants_max > 10000) {
                alert('❌ Le nombre maximum de participants ne peut pas dépasser 10000.');
                document.getElementById('participants_max').focus();
                return;
            }
            
            // Validate accessibility (at least one option)
            if (accessibilite.length === 0) {
                alert('❌ Veuillez sélectionner au moins une mesure d\'accessibilité.');
                return;
            }
            
            // Show loading state
            const submitBtn = document.getElementById('submitEventBtn');
            const originalText = submitBtn.textContent;
            submitBtn.innerHTML = '<span class="loading"></span> Enregistrement...';
            submitBtn.disabled = true;
            
            // Prepare data for database
            const eventData = {
                titre,
                description,
                date: date_evenement.replace('T', ' '),
                lieu,
                participants_max,
                accessibilite: accessibilite.join(', '),
                statut,
                idUtilisateur: window.CURRENT_USER_ID || SESSION_USER_ID, // Use session user ID
                theme: 'Inclusion' // Default theme
            };
            
            // If editing, add the event ID
            if (eventId) {
                eventData.id = eventId;
            }
            
            // Send to server
            fetch('../../Control/save_event.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(eventData)
            })
            .then(async response => {
                if (!response.ok) {
                    const text = await response.text();
                    console.error('Server Error:', response.status, text);
                    try {
                        const json = JSON.parse(text);
                        throw new Error(json.message || 'Network response was not ok');
                    } catch (e) {
                        throw new Error(`Erreur serveur (${response.status}): ${text.substring(0, 100)}`);
                    }
                }
                return response.json();
            })
            .then(data => {
                console.log('Save event response:', data);
                
                if (data.success) {
                    // Reload from database to reflect canonical state
                    closeEventModal();
                    loadEventsFromDatabase().then(() => {
                        renderEvents();
                        renderEventsToEvaluate();
                        renderMyEvaluations();
                    });
                    
                    alert('Événement enregistré avec succès !');
                } else {
                    alert('Erreur lors de l\'enregistrement: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de l\'enregistrement: ' + error.message);
            })
            .finally(() => {
                // Reset button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        }

        function handleEvaluationFormSubmit(e) {
            e.preventDefault();
            
            const eventId = document.getElementById('evaluationEventId').value;
            const event = window.EVENTS.find(e => e.id === eventId);
            
            if (event) {
                // Check if user is admin or company and if they created this event
                const isAdminOrCompany = window.CURRENT_USER_ROLE === 'admin' || window.CURRENT_USER_ROLE === 'company';
                const isEventCreator = event.idUtilisateur === window.CURRENT_USER_ID;
                
                if (isAdminOrCompany && isEventCreator) {
                    alert('Vous ne pouvez pas évaluer un événement que vous avez créé.');
                    return;
                }
            }
            
            const note_accessibilite = parseInt(document.getElementById('note_accessibilite').value);
            const note_inclusion = parseInt(document.getElementById('note_inclusion').value);
            const commentaire = document.getElementById('commentaire').value.trim();
            
            // ===== COMPREHENSIVE INPUT VALIDATION (Contrôle de saisie) =====
            
            // Validate accessibility rating
            if (!note_accessibilite || isNaN(note_accessibilite)) {
                alert('❌ La note d\'accessibilité est obligatoire.');
                document.getElementById('note_accessibilite').focus();
                return;
            }
            if (note_accessibilite < 1 || note_accessibilite > 5) {
                alert('❌ La note d\'accessibilité doit être entre 1 et 5.');
                document.getElementById('note_accessibilite').focus();
                return;
            }
            
            // Validate inclusion rating
            if (!note_inclusion || isNaN(note_inclusion)) {
                alert('❌ La note d\'inclusion est obligatoire.');
                document.getElementById('note_inclusion').focus();
                return;
            }
            if (note_inclusion < 1 || note_inclusion > 5) {
                alert('❌ La note d\'inclusion doit être entre 1 et 5.');
                document.getElementById('note_inclusion').focus();
                return;
            }
            
            // Validate comment
            if (!commentaire) {
                alert('❌ Le commentaire est obligatoire.');
                document.getElementById('commentaire').focus();
                return;
            }
            if (commentaire.length < 10) {
                alert('❌ Le commentaire doit contenir au moins 10 caractères pour être utile.');
                document.getElementById('commentaire').focus();
                return;
            }
            if (commentaire.length > 1000) {
                alert('❌ Le commentaire ne peut pas dépasser 1000 caractères.');
                document.getElementById('commentaire').focus();
                return;
            }
            
            // Use session user ID from PHP
            const validUserId = window.CURRENT_USER_ID || SESSION_USER_ID;
            
            // Extract numeric event ID if it has a prefix
            let numericEventId = eventId;
            if (eventId.includes('_')) {
                numericEventId = eventId.split('_')[1];
            }
            
            // Prepare data for database submission
            const evaluationData = {
                action: 'submit',
                idUtilisateur: validUserId,
                idEvenement: parseInt(numericEventId),
                note_accessibilite: note_accessibilite,
                note_inclusion: note_inclusion,
                commentaire: commentaire
            };
            
            console.log('Submitting evaluation:', evaluationData);
            
            // Send to server
            fetch('../Control/manage_evaluation.php', {
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
                    // Reload canonical data from DB
                    closeEvaluationModal();
                    loadEventsFromDatabase().then(() => {
                        renderEventsToEvaluate();
                        renderMyEvaluations();
                        renderEvents();
                        alert('Merci pour votre évaluation ! Votre retour aide à améliorer l\'inclusion.');
                    });
                } else {
                    alert('Erreur lors de l\'envoi de l\'évaluation: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de l\'envoi de l\'évaluation: ' + error.message);
            });
        }

        function renderEvents() {
            const eventsGrid = document.getElementById('eventsGrid');
            const noEvents = document.getElementById('noEvents');
            
            const filteredEvents = window.EVENTS.filter(event => {
                const searchTerm = window.CURRENT_FILTER.search.toLowerCase();
                if (searchTerm && 
                    !event.titre.toLowerCase().includes(searchTerm) && 
                    !event.lieu.toLowerCase().includes(searchTerm) &&
                    !event.description.toLowerCase().includes(searchTerm)) {
                    return false;
                }
                
                if (window.CURRENT_FILTER.access && !event.accessibilite.includes(window.CURRENT_FILTER.access)) {
                    return false;
                }
                
                if (window.CURRENT_FILTER.company && event.idUtilisateur !== window.CURRENT_FILTER.company) {
                    return false;
                }
                
                if (window.CURRENT_FILTER.date === 'upcoming' && isEventPassed(event)) {
                    return false;
                }
                if (window.CURRENT_FILTER.date === 'past' && !isEventPassed(event)) {
                    return false;
                }
                
                // Users can see published events and draft events they created
                if (window.CURRENT_USER_ROLE === 'user' && event.statut !== 'Publié' && event.idUtilisateur !== window.CURRENT_USER_ID) {
                    return false;
                }
                
                if (window.CURRENT_USER_ROLE === 'company' && event.statut === 'Brouillon' && event.idUtilisateur !== window.CURRENT_USER_ID) {
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
                const rating = calculateEventRating(event);
                const isUserParticipating = event.participations && 
                    event.participations.some(p => p.idUtilisateur === window.CURRENT_USER_ID && p.statut === 'Confirmée');
                const isUserCreator = event.idUtilisateur === window.CURRENT_USER_ID || 
                    (window.CURRENT_USER_ROLE === 'admin') ||
                    (window.CURRENT_USER_ROLE === 'inclusion');
        const isCompany = window.CURRENT_USER_ROLE === 'company';
        const isAdmin = window.CURRENT_USER_ROLE === 'admin';
        // Companies and admins cannot join any events
        const canJoinOrEvaluate = !isCompany && !isAdmin;
                const company = getCompanyById(event.idUtilisateur);
                const isFull = event.inscrits >= event.participants_max;
                
                const div = document.createElement('div');
                div.className = 'event-card glass';
                div.setAttribute('role', 'listitem');
                
                div.innerHTML = `
                    <div class="event-title">${escapeHtml(event.titre)}</div>
                    <div class="company-info">
                        <div class="company-logo">${company ? company.nom.charAt(0) : 'E'}</div>
                        <div class="company-name">${company ? company.nom : 'AbeLink'}</div>
                    </div>
                    <div class="meta">${formatDate(event.date)} • ${escapeHtml(event.lieu)}</div>
                    <div class="small">${truncate(escapeHtml(event.description || ''), 120)}</div>
                    
                    <div class="tags">
                        ${(typeof event.accessibilite === 'string' ? event.accessibilite.split(', ') : event.accessibilite || []).map(acc => `<span class="tag">${escapeHtml(acc)}</span>`).join('')}
                        <span class="tag status ${event.statut.toLowerCase()}">${event.statut}</span>
                        ${isFull ? '<span class="tag" style="background: rgba(255, 107, 107, 0.2); color: var(--danger);">Complet</span>' : ''}
                    </div>
                    
                    <div style="margin-top: 10px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div class="stars">${renderStars(rating.access)} <span class="avg">${rating.access || '—'}</span></div>
                            <div class="small">${event.inscrits}/${event.participants_max} participants</div>
                        </div>
                    </div>
                    
                    <div class="card-actions">
                        ${isUserParticipating ? 
                            '<span class="tag" style="background: rgba(46, 204, 113, 0.2); color: #2ecc71;">Inscrit</span>' : 
                            (event.statut === 'Publié' && !isEventPassed(event) && !isFull && canJoinOrEvaluate ? 
                                `<button class="cta-button" onclick="registerForEvent('${event.id}')">S'inscrire</button>` : '')
                        }
                        
                        ${!isUserParticipating && isEventPassed(event) && event.statut === 'Publié' && canJoinOrEvaluate ? 
                            `<button class="cta-button" onclick="openEvaluationModal('${event.id}')">Évaluer</button>` : ''}
                        
                        <button class="cta-button" onclick="openEventDetails('${event.id}')">Détails</button>
                        
                        ${isUserCreator ? 
                            `<button class="cta-button" onclick="editEvent('${event.id}')">Modifier</button>` : ''}
                    </div>
                `;
                
                eventsGrid.appendChild(div);
            });
        }

        function renderEventsToEvaluate() {
            const eventsToEvaluateGrid = document.getElementById('eventsToEvaluateGrid');
            const noEventsToEvaluate = document.getElementById('noEventsToEvaluate');
            
            const eventsToEvaluate = window.EVENTS.filter(event => {
                const userParticipated = event.participations && 
                    event.participations.some(p => p.idUtilisateur === window.CURRENT_USER_ID);
                
                const isPast = isEventPassed(event);
                
                const userEvaluated = event.evaluations && 
                    event.evaluations.some(e => e.idUtilisateur === window.CURRENT_USER_ID);
                
                // Check if user is admin/company and created this event
                const isAdminOrCompany = window.CURRENT_USER_ROLE === 'admin' || window.CURRENT_USER_ROLE === 'company';
                const isEventCreator = event.idUtilisateur === window.CURRENT_USER_ID;
                const canEvaluate = !(isAdminOrCompany && isEventCreator);
                
                return userParticipated && isPast && !userEvaluated && canEvaluate;
            });
            
            eventsToEvaluateGrid.innerHTML = '';
            
            if (eventsToEvaluate.length === 0) {
                noEventsToEvaluate.style.display = 'block';
            } else {
                noEventsToEvaluate.style.display = 'none';
                
                eventsToEvaluate.forEach(event => {
                    const company = getCompanyById(event.idUtilisateur);
                    
                    // Double check: prevent admin/company from evaluating events they created
                    const isAdminOrCompany = window.CURRENT_USER_ROLE === 'admin' || window.CURRENT_USER_ROLE === 'company';
                    const isEventCreator = event.idUtilisateur === window.CURRENT_USER_ID;
                    const canEvaluate = !(isAdminOrCompany && isEventCreator);
                    
                    if (!canEvaluate) return; // Skip this event
                    
                    const div = document.createElement('div');
                    div.className = 'event-card glass';
                    
                    div.innerHTML = `
                        <div class="event-title">${escapeHtml(event.titre)}</div>
                        <div class="company-info">
                            <div class="company-logo">${company ? company.nom.charAt(0) : 'E'}</div>
                            <div class="company-name">${company ? company.nom : 'AbeLink'}</div>
                        </div>
                        <div class="meta">${formatDate(event.date)} • ${escapeHtml(event.lieu)}</div>
                        <div class="small">${truncate(escapeHtml(event.description || ''), 120)}</div>
                        
                        <div class="card-actions">
                            <button class="cta-button primary" onclick="openEvaluationModal('${event.id}')">Évaluer cet événement</button>
                        </div>
                    `;
                    
                    eventsToEvaluateGrid.appendChild(div);
                });
            }
        }

        function renderMyEvaluations() {
            const myEvaluationsGrid = document.getElementById('myEvaluationsGrid');
            const noMyEvaluations = document.getElementById('noMyEvaluations');
            
            const myEvaluations = [];
            window.EVENTS.forEach(event => {
                if (event.evaluations) {
                    event.evaluations.filter(e => e.idUtilisateur === window.CURRENT_USER_ID)
                        .forEach(evaluationObj => {
                            myEvaluations.push({
                                ...evaluationObj,
                                eventTitre: event.titre,
                                eventDate: event.date,
                                companyName: getCompanyName(event.idUtilisateur)
                            });
                        });
                }
            });
            
            myEvaluationsGrid.innerHTML = '';
            
            if (myEvaluations.length === 0) {
                noMyEvaluations.style.display = 'block';
            } else {
                noMyEvaluations.style.display = 'none';
                
                myEvaluations.forEach(evaluationObj => {
                    const div = document.createElement('div');
                    div.className = 'event-card glass';
                    
                    const evalDate = evaluationObj.dateEvaluation ? new Date(evaluationObj.dateEvaluation) : new Date();
                    const formattedEvalDate = evalDate.toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
                    
                    const accessNote = evaluationObj.note_accessibilite || 0;
                    const inclusionNote = evaluationObj.note_inclusion || 0;
                    
                    div.innerHTML = `
                        <div class="event-title">${escapeHtml(evaluationObj.eventTitre)}</div>
                        <div class="company-info">
                            <div class="company-logo">${evaluationObj.companyName ? evaluationObj.companyName.charAt(0) : 'E'}</div>
                            <div class="company-name">${escapeHtml(evaluationObj.companyName || 'AbeLink')}</div>
                        </div>
                        <div class="meta">${formatDate(evaluationObj.eventDate)} • Évalué le ${formattedEvalDate}</div>
                        
                        <div class="glass" style="margin: 15px 0; padding: 15px; border-radius: 8px;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 10px;">
                                <div>
                                    <div class="small" style="color: #aaa;">Accessibilité</div>
                                    <div class="stars">${renderStars(accessNote)} <span class="avg" style="color: white;">${accessNote}/5</span></div>
                                </div>
                                <div>
                                    <div class="small" style="color: #aaa;">Inclusion</div>
                                    <div class="stars">${renderStars(inclusionNote)} <span class="avg" style="color: white;">${inclusionNote}/5</span></div>
                                </div>
                            </div>
                            <div>
                                <div class="small" style="color: #aaa; margin-bottom: 5px;">Votre commentaire</div>
                                <div style="color: #ccc; font-style: italic; background: rgba(255,255,255,0.03); padding: 10px; border-radius: 6px;">"${escapeHtml(evaluationObj.commentaire || 'Pas de commentaire')}"</div>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="cta-button" onclick="openEventDetails('${evaluationObj.idEvenement}')">Détails</button>
                        </div>
                    `;
                    
                    myEvaluationsGrid.appendChild(div);
                });
            }
        }

        // Always use database; no sample data

        // Close modals when clicking outside
        document.addEventListener('click', function(event) {
            const modals = ['eventModal', 'evaluationModal', 'detailsModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal && event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing...');
            
            // Use session-based role and user ID from PHP
            window.CURRENT_USER_ROLE = typeof SESSION_USER_ROLE !== 'undefined' ? SESSION_USER_ROLE : USER_ROLES.USER;
            window.CURRENT_USER_ID = typeof SESSION_USER_ID !== 'undefined' ? SESSION_USER_ID : null;
            window.CURRENT_FILTER = { search: '', access: '', date: 'upcoming', company: '' };
            
            updateUIForRole();
            setupFilters();
            
            // Load events from database, then render
            loadEventsFromDatabase().then(() => {
                renderEvents();
                renderEventsToEvaluate();
                renderMyEvaluations();
            });
            
            // Connect event listeners
            const eventForm = document.getElementById('eventForm');
            const evaluationForm = document.getElementById('evaluationForm');
            
            if (eventForm) {
                eventForm.removeEventListener('submit', handleEventFormSubmit);
                eventForm.addEventListener('submit', handleEventFormSubmit);
            }
            if (evaluationForm) {
                evaluationForm.removeEventListener('submit', handleEvaluationFormSubmit);
                evaluationForm.addEventListener('submit', handleEvaluationFormSubmit);
            }
        });

        // Load events from database
        function loadEventsFromDatabase() {
            console.log('Loading events from database...');
            return fetch('../../Control/get_events_for_evaluation.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        window.EVENTS = data.events || [];
                        // Extract companies from events
                        const companiesMap = new Map();
                        (window.EVENTS || []).forEach(event => {
                            if (event.idUtilisateur && !companiesMap.has(event.idUtilisateur)) {
                                companiesMap.set(event.idUtilisateur, {
                                    id: event.idUtilisateur,
                                    nom: event.nom_entreprise || 'AbeLink',
                                    prenom: event.prenom_entreprise || '',
                                    role: event.organizer_role || 'Entreprise'
                                });
                            }
                        });
                        window.COMPANIES = Array.from(companiesMap.values());
                    } else {
                        throw new Error(data.message || 'Failed to load events');
                    }
                })
                .catch(error => {
                    console.error('Error loading events:', error);
                    window.EVENTS = [];
                    window.COMPANIES = [];
                });
        }

        // Make functions globally available
        window.openEventModal = openEventModal;
        window.editEvent = editEvent;
        window.openEventDetails = openEventDetails;
        window.closeDetailsModal = closeDetailsModal;
        window.registerForEvent = registerForEvent;
        window.openEvaluationModal = openEvaluationModal;
        window.closeEvaluationModal = closeEvaluationModal;
        window.filterByDate = filterByDate;
        window.exportEventsToDB = exportEventsToDB;
        window.checkDBConnection = checkDBConnection;
        
        // User profile dropdown toggle
        function toggleUserMenu() {
            const menu = document.getElementById('userDropdownMenu');
            if (menu) {
                menu.classList.toggle('show');
            }
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userProfileDropdown');
            const menu = document.getElementById('userDropdownMenu');
            if (dropdown && menu && !dropdown.contains(event.target)) {
                menu.classList.remove('show');
            }
        });
        
        window.toggleUserMenu = toggleUserMenu;
    </script>
    
    <!-- Theme Toggle Script -->
    <script src="js/theme-toggle.js"></script>
    
    <!-- Pagination Script -->
    <script src="js/pagination.js"></script>
    <script src="js/pagination-helper.js"></script>
</body>
</html>
