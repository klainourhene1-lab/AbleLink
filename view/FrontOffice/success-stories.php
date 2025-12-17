<?php
session_start();

// Load necessary files
require_once __DIR__ . '/../../Controller/config.php';
require_once __DIR__ . '/../../Controller/UserController.php';
require_once __DIR__ . '/../../Controller/StoryController.php';
require_once __DIR__ . '/../../Model/User.php';

// Initialize Controllers
$userController = new UserController();
$storyController = new StoryController();

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

// --- Handle Story Creation ---
$error_message = $storyController->create(); 

// --- Fetch Stories ---
$stories = $storyController->index();
$total = count($stories);

class Helpers {
    public static function estimateReadingMinutes($text) {
        return max(1, ceil(str_word_count(strip_tags($text)) / 200));
    }
}

$q = $_GET['q'] ?? '';
$sort = $_GET['sort'] ?? 'recent';
$cat = $_GET['cat'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success Stories - AbleLink</title>
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

        /* Glass Cards */
        .glass {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 12px;
        }

        /* Grid & Cards */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        .event-card {
            padding: 20px;
            transition: transform 0.2s ease;
        }
        .event-card:hover { transform: translateY(-2px); }
        .company-logo { width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px; margin-right: 10px; }
        
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
        .cta-button.primary:hover { background: #2980b9; }

        /* Tags */
        .tag { padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; background: rgba(255,255,255,0.1); color: #ccc;}
        .tag.domain { background: rgba(52, 152, 219, 0.2); color: #3498db; }

        /* Modals */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            backdrop-filter: blur(5px);
            overflow-y: auto;
        }
        .panel {
            background: #1a1a2e;
            margin: 5% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 700px;
            position: relative;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .input {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.05);
            color: white;
            font-family: inherit;
            margin-bottom: 10px;
        }
        .input:focus { outline: none; border-color: #3498db; }
        
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
        
        .content-wrapper { margin-top: 120px; }
        @media (max-width: 991px) {
            .content-wrapper { margin-top: 100px; }
        }
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
                                <li class="active"><a href="success-stories.php">Success Stories</a></li>
                                <?php if ($user_id): ?>
                                    <li><a href="../../Controller/historique_stories.php">Mon Historique</a></li>
                                    <?php if ($user_role === 'Admin'): ?>
                                        <li><a href="../../Controller/admin_dashboard.php">Administration</a></li>
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
                     <h1 class="title-bubble">Success Stories</h1>
                     <p style="margin-top: 15px; color: #ccc;">Témoignages et parcours inspirants de notre communauté</p>
                </section>

                <div class="glass" style="padding: 20px; margin-bottom: 30px;">
                    <form method="GET" action="success-stories.php" style="display: flex; gap: 15px; flex-wrap: wrap;">
                         <input type="text" name="q" class="input" placeholder="Rechercher..." value="<?php echo htmlspecialchars($q); ?>" style="flex: 1; min-width: 200px; margin: 0;">
                         <select name="cat" class="input" style="width: auto; min-width: 150px; margin: 0;">
                            <option value="">Tous les domaines</option>
                            <option value="Web" <?php echo $cat === 'Web' ? 'selected' : ''; ?>>Développement Web</option>
                            <option value="Marketing" <?php echo $cat === 'Marketing' ? 'selected' : ''; ?>>Marketing</option>
                         </select>
                         <button type="submit" class="cta-button">Rechercher</button>
                         
                         <?php if ($user_id): ?>
                            <button type="button" class="cta-button primary" onclick="openStoryModal()" style="margin-left: auto;">+ Partager une histoire</button>
                         <?php else: ?>
                             <a href="../../view/general/signin.php" class="cta-button primary" style="margin-left: auto;">+ Se connecter pour partager</a>
                         <?php endif; ?>
                    </form>
                </div>

                <div class="grid">
                    <?php if ($total > 0): ?>
                        <?php foreach ($stories as $story): ?>
                        <div class="event-card glass">
                            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                <div class="company-logo"><?php echo strtoupper(substr($story['author'] ?? 'A', 0, 1)); ?></div>
                                <div style="color: #ccc; font-weight: 600;"><?php echo htmlspecialchars($story['author'] ?? 'Anonyme'); ?></div>
                            </div>
                            
                            <h3 style="color: white; font-size: 20px; margin-bottom: 10px;"><?php echo htmlspecialchars($story['title']); ?></h3>
                            
                            <div style="color: #888; font-size: 14px; margin-bottom: 15px;">
                                <i class="fa fa-calendar"></i> <?php echo date('d/m/Y', strtotime($story['created_at'])); ?> &bull; 
                                <i class="fa fa-clock-o"></i> <?php echo Helpers::estimateReadingMinutes($story['content']); ?> min
                            </div>
                            
                            <div style="color: #bbb; line-height: 1.6; margin-bottom: 20px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                <?php echo nl2br(htmlspecialchars($story['content'])); ?>
                            </div>

                            <div style="display: flex; gap: 10px;">
                                <button onclick="openReadModal(`<?php echo addslashes($story['title']); ?>`, `<?php echo addslashes($story['author'] ?? 'Anonyme'); ?>`, `<?php echo addslashes($story['content']); ?>`)" class="cta-button primary" style="flex: 1;"><i class="fa fa-book"></i> Lire</button>
                                <button onclick="likeStory(<?php echo $story['id']; ?>)" class="cta-button"><i class="fa fa-heart"></i></button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="grid-column: 1/-1; text-align: center; color: #ccc; padding: 40px;">
                            <p>Aucune histoire trouvée.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Create Modal -->
    <div id="storyModal" class="modal">
        <div class="panel">
            <button class="cta-button" style="float: right; background: transparent; padding: 5px;" onclick="closeStoryModal()">✕</button>
            <h2 style="color: white; margin-bottom: 20px;">Partager votre histoire</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="create_story">
                <input type="text" name="title" class="input" placeholder="Titre" required>
                <input type="text" name="author_name" class="input" placeholder="Votre nom" required value="<?php echo htmlspecialchars($user_name); ?>">
                <textarea name="content" class="input" placeholder="Votre témoignage..." required style="height: 150px;"></textarea>
                <div style="text-align: right; margin-top: 15px;">
                    <button type="button" onclick="closeStoryModal()" class="cta-button">Annuler</button>
                    <button type="submit" class="cta-button primary">Publier</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Read Modal -->
    <div id="readModal" class="modal">
        <div class="panel">
            <button class="cta-button" style="float: right; background: transparent; padding: 5px;" onclick="closeReadModal()">✕</button>
            <h2 id="readTitle" style="color: #3498db; margin-bottom: 10px;"></h2>
            <div style="color: #888; margin-bottom: 20px;" id="readAuthor"></div>
            <div id="readContent" style="color: #ddd; line-height: 1.8; white-space: pre-wrap;"></div>
        </div>
    </div>

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
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
        function openStoryModal() { document.getElementById('storyModal').style.display = 'block'; }
        function closeStoryModal() { document.getElementById('storyModal').style.display = 'none'; }
        
        function openReadModal(title, author, content) {
            document.getElementById('readTitle').textContent = title;
            document.getElementById('readAuthor').textContent = 'Par ' + author;
            document.getElementById('readContent').textContent = content;
            document.getElementById('readModal').style.display = 'block';
        }
        function closeReadModal() { document.getElementById('readModal').style.display = 'none'; }
        
        function likeStory(id) {
            fetch('../../Controller/stories_handler.php?action=like_story', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ story_id: id })
            }).then(r => r.json()).then(res => {
                if(res.success) alert('Merci pour votre like !');
                else alert('Erreur');
            });
        }
    </script>
</body>
</html>
