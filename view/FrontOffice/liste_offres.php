<?php
session_start();

// Load necessary files
require_once __DIR__ . '/../../Control/config.php';
require_once __DIR__ . '/../../Control/UserController.php';
require_once __DIR__ . '/../../Control/OffreController.php';
require_once __DIR__ . '/../../Model/User.php';

// Initialize Controllers
$userController = new UserController();
$offreController = new OffreController();

// Handle User Authentication State
$user_id = $_SESSION['user_id'] ?? null;
$user = null;
$user_role = 'guest';

if ($user_id) {
    // Logged in user logic
    $user = $userController->showUser($user_id);
    if ($user) {
        $db_role = $user->getRole();
        $_SESSION['user_role'] = $db_role; // Refresh role in session
        $user_role = $db_role;
    }
}

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

// --- Fetch Offres ---
$filters = [];
if (isset($_GET['q'])) $filters['start_search'] = $_GET['q']; // Using q as search param
if (isset($_GET['cat'])) $filters['localisation'] = $_GET['cat']; // Mapping cat to localisation or other

$allOffres = $offreController->index();
$offres = $offreController->filter($allOffres, $filters);
$total = count($offres);

$q = $_GET['q'] ?? '';
$cat = $_GET['cat'] ?? '';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offres d'Emploi - AbleLink</title>
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
            background: linear-gradient(135deg, #5b6ff5, #8b5cf6);
            padding: 20px 40px;
            border-radius: 50px;
            box-shadow: 0 8px 32px rgba(139, 92, 246, 0.4);
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
                                <li class="active"><a href="liste_offres.php">Offres d'Emploi</a></li>
                                <?php if ($user_id): ?>
                                    <li><a href="mes_favoris.php">Mes Favoris</a></li>
                                    <?php if ($user_role === 'Admin'): ?>
                                        <li><a href="../../Control/admin_dashboard.php">Administration</a></li>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        <div class="header__nav__social">
                            <?php if ($user_id): ?>
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
                            <?php else: ?>
                                <a href="../../view/general/signin.php" class="cta-button primary">Se connecter</a>
                            <?php endif; ?>
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
                     <h1 class="title-bubble">Offres d'Emploi Inclusives</h1>
                     <p style="margin-top: 15px; color: #ccc;">Découvrez des opportunités professionnelles adaptées à tous les profils.</p>
                </section>

                <div class="glass" style="padding: 20px; margin-bottom: 30px;">
                    <form method="GET" action="liste_offres.php" style="display: flex; gap: 15px; flex-wrap: wrap;">
                         <input type="text" name="q" class="input" placeholder="Rechercher par titre, entreprise..." value="<?php echo htmlspecialchars($q); ?>" style="flex: 1; min-width: 200px; margin: 0;">
                         <input type="text" name="cat" class="input" placeholder="Localisation (ex: Paris)" value="<?php echo htmlspecialchars($cat); ?>" style="width: auto; min-width: 150px; margin: 0;">
                         <button type="submit" class="cta-button">Rechercher</button>
                    </form>
                    <?php if ($user_role === 'Admin' || $user_role === 'Entreprise'): ?>
                        <div style="margin-top: 15px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
                            <a href="create_offre.php" class="cta-button primary" style="width: 100%; justify-content: center;">
                                <i class="fa fa-plus-circle"></i> Déposer une offre
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <?php 
                    if ($total == 0) {
                        echo '<div class="col-12 text-center"><p style="color: #fff; font-size: 18px;">Aucune offre trouvée.</p></div>';
                    } else {
                        $icons = ['fa-cog', 'fa-link', 'fa-desktop', 'fa-code', 'fa-database', 'fa-mobile'];
                        $iconColors = ['#8b5cf6', '#06b6d4', '#ec4899', '#f59e0b', '#10b981', '#3b82f6'];
                        $index = 0;
                        
                        foreach ($offres as $offre): 
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
                                        <?= htmlspecialchars($offre['type_contrat']) ?>
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
                                        <button onclick="toggleFavoris(<?= $offre['id'] ?>)" 
                                                id="fav-btn-<?= $offre['id'] ?>"
                                                class="cta-button" 
                                                style="flex: 1; justify-content: center; background: rgba(236, 72, 153, 0.1); color: #ec4899; border: 1px solid #ec4899;">
                                            <i class="fa fa-heart-o" id="fav-icon-<?= $offre['id'] ?>"></i>
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
        
        // Favorites Logic
        function toggleFavoris(id_offre) {
            const btn = document.getElementById('fav-btn-' + id_offre);
            const icon = document.getElementById('fav-icon-' + id_offre);
            
            // Check current backend status via icon class is simplified, better to trust API response
            // We just call toggle endpoint
            
            fetch('../../Control/job_handler.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ action: 'toggle_favorite', offre_id: id_offre })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    if (data.action === 'added') {
                         icon.classList.remove('fa-heart-o');
                         icon.classList.add('fa-heart');
                         btn.style.background = 'linear-gradient(135deg, #ec4899 0%, #db2777 100%)';
                         btn.style.color = '#fff';
                    } else {
                         icon.classList.remove('fa-heart');
                         icon.classList.add('fa-heart-o');
                        btn.style.background = 'rgba(236, 72, 153, 0.1)';
                        btn.style.color = '#ec4899';
                    }
                } else {
                    if(data.message === 'Not logged in') alert('Veuillez vous connecter pour ajouter aux favoris.');
                    else alert('Erreur: ' + data.message);
                }
            })
            .catch(e => console.error(e));
        }

        // Check favorites on load
        document.addEventListener('DOMContentLoaded', function() {
            const favButtons = document.querySelectorAll('[id^="fav-btn-"]');
            favButtons.forEach(btn => {
                const id_offre = btn.id.split('-')[2];
                fetch(`../../Control/job_handler.php?action=check_favorite&offre_id=${id_offre}`)
                .then(r => r.json())
                .then(data => {
                    if (data.isFavorite) {
                        const icon = document.getElementById('fav-icon-' + id_offre);
                        const btn = document.getElementById('fav-btn-' + id_offre);
                        icon.classList.remove('fa-heart-o');
                        icon.classList.add('fa-heart');
                        btn.style.background = 'linear-gradient(135deg, #ec4899 0%, #db2777 100%)';
                        btn.style.color = '#fff';
                    }
                });
            });
        });
    </script>
</body>
</html>
