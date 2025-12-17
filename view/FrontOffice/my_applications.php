<?php
// my_applications.php - View for Candidates to see their history
session_start();
// Check if user is logged in (Candidate or any user)
if (!isset($_SESSION['user_id'])) {
    header('Location: ../general/signin.php');
    exit;
}

require_once __DIR__ . '/../../Controller/config.php';
require_once __DIR__ . '/../../Controller/UserController.php';
require_once __DIR__ . '/../../Controller/CandidatureController.php';

$candCtrl = new CandidatureController();
$myApps = $candCtrl->getMyApplications();

// Initialize UserController for header
$userController = new UserController();
$user_id = $_SESSION['user_id'];
$user = $userController->showUser($user_id);
$user_role = $user ? $user->getRole() : 'guest';

// Role mappings
$role_mapping = ['Admin' => 'admin', 'Entreprise' => 'company', 'Utilisateur' => 'user', 'Inclusion' => 'inclusion', 'guest' => 'guest'];
$js_role = $role_mapping[$user_role] ?? 'user';
$role_display_names = ['admin' => 'Administrateur', 'company' => 'Entreprise', 'user' => 'Utilisateur', 'inclusion' => 'Responsable', 'guest' => 'Visiteur'];

// Helper for User Photo
function getPhotoUrl($user) {
    if ($user && $user->getPhoto() && file_exists(__DIR__ . '/../../uploads/profiles/' . $user->getPhoto())) {
        return '../../uploads/profiles/' . $user->getPhoto();
    }
    return '../general/img/team/team-1.jpg'; // Default photo
}

$user_photo = getPhotoUrl($user);
$user_name = $user ? $user->getPrenom() . ' ' . $user->getNo m() : 'Visiteur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Candidatures - AbleLink</title>
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../view/general/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="../../view/general/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="../../view/general/css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="../../view/general/css/style.css" type="text/css">
    <style>
         body {
            background: #100028;
            min-height: 100vh;
            color: #ffffff;
        }
        .header__nav__option { display: flex; align-items: center; justify-content: space-between; }
        .header__nav__menu { flex: 1; min-width: 0; }
        .header__nav__menu ul { display: flex; gap: 24px; align-items: center; }
        
        /* Role badges */
        .role-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }
        .role-badge.user { background: rgba(52, 152, 219, 0.2); color: #3498db; }
        .role-badge.company { background: rgba(46, 204, 113, 0.2); color: #2ecc71; }
        .role-badge.inclusion { background: rgba(155, 89, 182, 0.2); color: #9b59b6; }
        .role-badge.admin { background: rgba(231, 76, 60, 0.2); color: #e74c3c; }

        /* Title Bubble */
        .title-bubble {
            display: inline-block;
            background: linear-gradient(135deg, #10b981, #059669);
            padding: 20px 40px;
            border-radius: 50px;
            box-shadow: 0 8px 32px rgba(16, 185, 129, 0.4);
            color: white;
            font-weight: 700;
            text-align: center;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        /* Glass Cards */
        .glass {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
        }

        .content-wrapper { margin-top: 120px; }
        @media (max-width: 991px) {
            .content-wrapper { margin-top: 100px; }
        }
        
        /* Application Card */
        .app-card {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .app-card:hover {
            transform: translateX(8px);
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
            border-color: rgba(16, 185, 129, 0.6);
        }

        .status-badge {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: inline-block;
        }
        .accepted { background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid #10b981; }
        .rejected { background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid #ef4444; }
        .pending { background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid #f59e0b; }

         /* User profile dropdown */
        .user-profile-dropdown { position: relative; display: inline-block; }
        .user-profile-button {
            display: flex; align-items: center; gap: 10px; padding: 6px 12px;
            background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);
            border-radius: 25px; cursor: pointer; transition: all 0.3s ease;
        }
        .user-profile-button:hover { background: rgba(255,255,255,0.2); }
        .user-avatar { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; }
        .user-info { display: flex; flex-direction: column; align-items: flex-start; }
        .user-name { font-size: 14px; font-weight: 600; color: white; line-height: 1.2; }
        .user-role-text { font-size: 12px; color: rgba(255,255,255,0.7); }
        .user-dropdown-menu {
            display: none; position: absolute; top: calc(100% + 10px); right: 0; min-width: 200px;
            background: rgba(15,23,42,0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.2);
            border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.5); z-index: 1000; overflow: hidden;
        }
        .user-dropdown-menu.show { display: block; }
        .user-dropdown-item {
            display: block; padding: 12px 16px; color: white; text-decoration: none;
            transition: background 0.2s ease; border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .user-dropdown-item:hover { background: rgba(255,255,255,0.1); color: white; }
    </style>
</head>
<body>
    <div class="bg-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <!-- HEADER -->
    <header class="header">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="header__logo">
                        <div class="site-title">
                            <a href="../../view/general/index.php">
                                <span class="letter-a">A</span><span class="letter-b">b</span><span class="letter-l">l</span><span class="letter-e">e</span><span class="letter-link">Link</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-10">
                    <div class="header__nav__option">
                        <nav class="header__nav__menu mobile-menu">
                            <ul>
                                <li><a href="../../view/general/index.php">Accueil</a></li>
                                <li><a href="liste_offres.php">Offres d'Emploi</a></li>
                                <li class="active"><a href="my_applications.php">Mes Candidatures</a></li>
                                <li><a href="mes_favoris.php">Mes Favoris</a></li>
                                <?php if ($user_role === 'Admin'): ?>
                                    <li><a href="../../Controller/admin_dashboard.php">Administration</a></li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        <div class="header__nav__social">
                            <div class="role-badge <?php echo $js_role; ?>">
                                <?php echo $role_display_names[$js_role] ?? 'Utilisateur'; ?>
                            </div>
                            <div class="user-profile-dropdown" id="userProfileDropdown">
                                <button class="user-profile-button" onclick="toggleUserMenu()">
                                    <img src="<?php echo htmlspecialchars($user_photo); ?>" alt="Profile" class="user-avatar">
                                    <div class="user-info">
                                        <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
                                        <span class="user-role-text"><?php echo htmlspecialchars($role_display_names[$js_role] ?? 'Utilisateur'); ?></span>
                                    </div>
                                    <i class="fa fa-chevron-down" style="margin-left: 5px; font-size: 12px;"></i>
                                </button>
                                <div class="user-dropdown-menu" id="userDropdownMenu">
                                   <a href="../../view/general/profile.php" class="user-dropdown-item"><i class="fa fa-user"></i> Mon Profil</a>
                                   <a href="../../view/general/logout.php" class="user-dropdown-item" style="color: #e74c3c;"><i class="fa fa-sign-out"></i> Déconnexion</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>

     <style>
    .site-title { margin-top: -0px; padding: 0; }
    .site-title a { font-family: 'Josefin Sans', sans-serif; font-size: 32px; font-weight: 700; text-decoration: none; line-height: 1.2; display: inline-flex; gap: 2px; letter-spacing: 1px; text-transform: uppercase; transition: 0.3s ease; }
    .letter-a  { color: #ff4f5e; text-shadow: 0 0 8px rgba(255,79,94,0.6); }
    .letter-b  { color: #4c8df5; text-shadow: 0 0 8px rgba(76,141,245,0.6); }
    .letter-l  { color: #b87bff; text-shadow: 0 0 8px rgba(184,123,255,0.6); }
    .letter-e  { color: #ffb247; text-shadow: 0 0 8px rgba(255,178,71,0.6); }
    .letter-link { color: #ffffff; margin-left: 4px; text-shadow: 0 0 10px rgba(255,255,255,0.7); }
    </style>

    <section class="services spad">
        <div class="container">
            <div class="content-wrapper">
                <section class="page-header" style="text-align: center; margin-bottom: 40px;">
                     <h1 class="title-bubble">Mes Candidatures</h1>
                     <p style="margin-top: 15px; color: #ccc;">Suivez l'état de vos candidatures envoyées.</p>
                </section>

                <div class="row">
                    <div class="col-12">
                        <?php if(empty($myApps)): ?>
                            <div class="glass" style="padding: 60px 40px; text-align: center;">
                                <i class="fa fa-folder-open" style="font-size: 60px; color: #64748b; margin-bottom:20px;"></i>
                                <h3 style="color: #94a3b8; margin-bottom: 15px;">Aucune candidature pour le moment</h3>
                                <p style="color: #64748b; margin-bottom: 25px;">Vous n'avez envoyé aucune candidature.</p>
                                <a href="liste_offres.php" style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; text-decoration: none; border-radius: 10px; font-weight: 600; transition: 0.3s;">
                                    <i class="fa fa-search"></i> Parcourir les offres
                                </a>
                            </div>
                        <?php else: ?>
                            <?php foreach($myApps as $app): ?>
                            <div class="app-card">
                                <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 15px;">
                                    <div style="flex: 1; min-width: 250px;">
                                        <h3 style=" color: #ffffff; font-size: 22px; font-weight: 700; margin-bottom: 12px;">
                                            <?= htmlspecialchars($app['offre_titre']) ?>
                                        </h3>
                                        <div style="color: #a78bfa; font-size: 14px; margin-bottom: 8px;">
                                            <i class="fa fa-building"></i> <?= htmlspecialchars($app['entreprise']) ?>
                                        </div>
                                        <div style="color: #94a3b8; font-size: 14px; margin-bottom: 8px;">
                                            <i class="fa fa-map-marker"></i> <?= htmlspecialchars($app['localisation']) ?>
                                        </div>
                                        <div style="color: #64748b; font-size: 13px; margin-top: 12px;">
                                            <i class="fa fa-calendar"></i> Envoyée le <?= date('d/m/Y', strtotime($app['date_candidature'])) ?>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <?php
                                            $st = $app['statut'] ?? 'pending';
                                            $cls = 'pending'; $lbl = 'En attente';
                                            if($st === 'accepted' || $st === 'accepte') { $cls = 'accepted'; $lbl = 'Acceptée'; }
                                            if($st === 'rejected' || $st === 'refuse') { $cls = 'rejected'; $lbl = 'Refusée'; }
                                            if($st === 'en_attente') { $cls = 'pending'; $lbl = 'En attente'; }
                                        ?>
                                        <span class="status-badge <?= $cls ?>"><?= $lbl ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="../../view/general/js/jquery-3.3.1.min.js"></script>
    <script>
        function toggleUserMenu() {
            document.getElementById('userDropdownMenu').classList.toggle('show');
        }
        window.onclick = function(event) {
            if (!event.target.matches('.user-profile-button') && !event.target.closest('.user-profile-button')) {
                var dropdowns = document.getElementsByClassName("user-dropdown-menu");
                for (var i = 0; i < dropdowns.length; i++) {
                     if (dropdowns[i].classList.contains('show')) dropdowns[i].classList.remove('show');
                }
            }
        }
    </script>
</body>
</html>
