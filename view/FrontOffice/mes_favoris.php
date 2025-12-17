<?php
session_start();
require_once __DIR__ . '/../../Controller/config.php';
require_once __DIR__ . '/../../Controller/UserController.php';
require_once __DIR__ . '/../../Controller/FavorisController.php';

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header('Location: ../../view/general/signin.php');
    exit;
}

$favCtrl = new FavorisController();
// Fetch raw favorites
$allFavorites = $favCtrl->getMyFavorites($userId);

// Initialize UserController for header
$userController = new UserController();
$user = $userController->showUser($userId);
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
$user_name = $user ? $user->getPrenom() . ' ' . $user->getNom() : 'Visiteur';
$total = count($allFavorites);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Favoris - AbleLink</title>
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
            background: linear-gradient(135deg, #ec4899, #db2777);
            padding: 20px 40px;
            border-radius: 50px;
            box-shadow: 0 8px 32px rgba(236, 72, 153, 0.4);
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

        /* Grid & Cards */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        
        /* Modern Job Card Styles from Source */
        .modern-job-card {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            border: 1px solid rgba(139, 92, 246, 0.3);
            border-radius: 20px;
            padding: 30px;
            position: relative;
            overflow: hidden;
            min-height: 480px;
            transition: all 0.4s ease;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        .modern-job-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(124, 58, 237, 0.5);
            border-color: rgba(139, 92, 246, 0.6);
        }
        
        /* Buttons */
        .cta-button {
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            background: rgba(255,255,255,0.1);
            color: white;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .cta-button:hover { background: rgba(255,255,255,0.2); color: white; transform: translateY(-1px); }
        .cta-button.primary { background: #3498db; }
        
        .btn-apply-modern {
             display: flex; align-items: center; justify-content: center; gap: 8px;
             padding: 14px 24px; background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
             color: #ffffff; text-decoration: none; border: none; border-radius: 12px;
             transition: all 0.3s; font-weight: 700; font-size: 14px; text-transform: uppercase;
             letter-spacing: 0.5px; box-shadow: 0 4px 15px rgba(124, 58, 237, 0.4);
        }
        .btn-apply-modern:hover {
            transform: scale(1.05); box-shadow: 0 6px 20px rgba(124, 58, 237, 0.6);
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white;
        }
        
        .content-wrapper { margin-top: 120px; }
        @media (max-width: 991px) {
            .content-wrapper { margin-top: 100px; }
        }
        
        /* Inputs */
        .input {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.05);
            color: white;
            font-family: inherit;
        }
        
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

    <!-- HEADER EXACTEMENT COMME LISTE_OFFRES.PHP -->
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
                                <li class="active"><a href="mes_favoris.php">Mes Favoris</a></li>
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
                     <h1 class="title-bubble">Mes Favoris</h1>
                     <p style="margin-top: 15px; color: #ccc;">Retrouvez ici toutes vos offres sauvegardées.</p>
                </section>

                <div class="row">
                    <?php 
                    if ($total == 0) {
                        echo '<div class="col-12 text-center"><p style="color: #fff; font-size: 18px;">Vous n\'avez aucune offre en favoris pour le moment.</p><br><a href="liste_offres.php" class="cta-button primary">Parcourir les offres</a></div>';
                    } else {
                        $icons = ['fa-cog', 'fa-link', 'fa-desktop', 'fa-code', 'fa-database', 'fa-mobile'];
                        $iconColors = ['#8b5cf6', '#06b6d4', '#ec4899', '#f59e0b', '#10b981', '#3b82f6'];
                        $index = 0;
                        
                        foreach ($allFavorites as $offre): 
                            $iconIndex = $index % count($icons);
                    ?>
                        <div class="col-lg-4 col-md-6 col-sm-12" style="margin-bottom: 30px;">
                            <div class="modern-job-card">
                                <!-- Decorative Icon -->
                                <div style="position: absolute; top: 25px; right: 25px; width: 50px; height: 50px; background: rgba(139, 92, 246, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa <?= $icons[$iconIndex] ?>" style="color: <?= $iconColors[$iconIndex] ?>; font-size: 24px;"></i>
                                </div>
                                
                                <h3 style="color: #ffffff; font-size: 24px; font-weight: 700; margin-bottom: 20px; padding-right: 60px;">
                                    <?= htmlspecialchars($offre['titre']) ?>
                                </h3>
                                
                                <div style="margin-bottom: 15px;">
                                    <span style="color: #a78bfa; font-size: 13px; opacity: 0.9;"><?= htmlspecialchars($offre['entreprise']) ?></span>
                                </div>
                                
                                <div style="margin-bottom: 20px;">
                                    <p style="color: #e0e7ff; margin-bottom: 8px; font-size: 14px;">
                                        <i class="fa fa-map-marker" style="color: #8b5cf6; margin-right: 8px;"></i>
                                        <?= htmlspecialchars($offre['localisation']) ?>
                                    </p>
                                    <p style="color: #e0e7ff; margin-bottom: 8px; font-size: 14px;">
                                        <i class="fa fa-briefcase" style="color: #8b5cf6; margin-right: 8px;"></i>
                                        <?= htmlspecialchars($offre['type_contrat']) ?? 'CDI' ?>
                                    </p>
                                    <?php if (!empty($offre['salaire'])): ?>
                                        <p style="color: #34d399; font-weight: 600; margin-bottom: 8px; font-size: 16px;">
                                            <i class="fa fa-money" style="margin-right: 8px;"></i>
                                            <?= htmlspecialchars($offre['salaire']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                
                                <p style="color: rgba(224, 231, 255, 0.7); font-size: 13px; margin-bottom: 25px; line-height: 1.6;">
                                    <?= htmlspecialchars(substr($offre['description'], 0, 100)) ?>...
                                </p>
                                
                                <div style="display: flex; flex-direction: column; gap: 12px;">
                                    <a href="details_offre.php?id=<?= $offre['id'] ?>" class="cta-button" style="justify-content: center; width: 100%; border: 1px solid #7c3aed;">
                                        <i class="fa fa-info-circle"></i> Voir détails
                                    </a>
                                    
                                    <div style="display: flex; gap: 10px;">
                                        <button onclick="removeFavorite(<?= $offre['id'] ?>)" 
                                                class="cta-button" 
                                                style="flex: 1; justify-content: center; background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); color: #fff; border: none;">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        
                                        <a href="postuler.php?id_offre=<?= $offre['id'] ?>" class="btn-apply-modern" style="flex: 2;">
                                            POSTULER
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php 
                            $index++;
                        endforeach; 
                    }
                    ?>
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
        
        function removeFavorite(id_offre) {
            if(!confirm('Retirer cette offre de vos favoris ?')) return;

            fetch('../../Controller/job_handler.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ action: 'toggle_favorite', offre_id: id_offre })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload(); 
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(e => console.error(e));
        }
    </script>
</body>
</html>
