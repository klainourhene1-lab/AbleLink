<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../view/general/signin.php'); exit; }

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'] ?? 'Utilisateur';

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/UserController.php';
require_once __DIR__ . '/../Model/User.php';
require_once __DIR__ . '/../Model/Database.php';
require_once __DIR__ . '/../Model/StoryModel.php';

$controller = new UserController();
$user = $controller->showUser($user_id);
$storyModel = new StoryModel();

// Get user photo
function getPhotoUrl($photo, $prenom, $nom) {
    if ($photo && file_exists(__DIR__ . '/../../uploads/profiles/' . $photo)) {
        return '../uploads/profiles/' . $photo;
    }
    return '../view/general/img/team/team-1.jpg'; // Default photo
}
$user_photo = $user ? getPhotoUrl($user->getPhoto(), $user->getPrenom(), $user->getNom()) : '../view/general/img/team/team-1.jpg';
$user_name = $user ? $user->getPrenom() . ' ' . $user->getNom() : 'Utilisateur';


if ($user) {
    $_SESSION['user_role'] = $user->getRole();
    $user_role = $user->getRole();
}

$role_mapping = ['Admin' => 'admin', 'Entreprise' => 'company', 'Utilisateur' => 'user', 'Inclusion' => 'inclusion'];
$js_role = $role_mapping[$user_role] ?? 'user';
$role_names = ['admin' => 'Administrateur', 'company' => 'Entreprise', 'user' => 'Utilisateur', 'inclusion' => 'Responsable'];

$myStories = $storyModel->getByUserId($user_id);
$total = count($myStories);
$approved = count(array_filter($myStories, fn($s) => ($s['status'] ?? '') == 'approved'));
$pending = count(array_filter($myStories, fn($s) => ($s['status'] ?? '') == 'pending'));
$totalLikes = array_sum(array_column($myStories, 'likes'));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Stories - AbleLink</title>
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../view/general/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="../view/general/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="../view/general/css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="../view/general/css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="../view/general/css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="../view/general/css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="../view/general/css/style.css" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
         body {
            background: #100028;
            min-height: 100vh;
            color: #ffffff;
        }
        .header__nav__option { display: flex; align-items: center; justify-content: space-between; }
        .header__nav__menu { flex: 1; min-width: 0; }
        .header__nav__menu ul { display: flex; gap: 24px; align-items: center; }
        .header__nav__social { display: flex; align-items: center; gap: 12px; flex-wrap: nowrap; }
        
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
        .event-title { font-size: 18px; font-weight: bold; margin-bottom: 10px; color: white; display: block; }
        .company-info { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
        .company-logo { width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px; }
        .company-name { color: #ccc; font-size: 14px; }
        .meta { color: #ccc; font-size: 14px; margin-bottom: 10px; display: flex; gap: 15px; }
        
        /* Stats */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .stat-card { padding: 15px; text-align: center; }
        .stat-number { font-size: 24px; font-weight: bold; color: white; }
        .stat-label { font-size: 14px; color: #ccc; }

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
        .cta-button.danger { background: rgba(231, 76, 60, 0.2); color: #e74c3c; }
        .cta-button.danger:hover { background: rgba(231, 76, 60, 0.4); }

        /* Tags */
        .tag { padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .tag.approved { background: rgba(46, 204, 113, 0.2); color: #2ecc71; }
        .tag.pending { background: rgba(241, 196, 15, 0.2); color: #f1c40f; }
        .tag.rejected { background: rgba(231, 76, 60, 0.2); color: #e74c3c; }

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
            background: #1a1a2e; /* Solid background for legibility */
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
            margin-bottom: 15px;
        }
        .input:focus { outline: none; border-color: #3498db; }
        textarea.input { min-height: 150px; resize: vertical; }

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
                            <a href="../view/general/index.php">
                                <span class="letter-a">A</span><span class="letter-b">b</span><span class="letter-l">l</span><span class="letter-e">e</span><span class="letter-link">Link</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-10">
                    <div class="header__nav__option">
                        <nav class="header__nav__menu mobile-menu">
                            <ul>
                                <li><a href="../view/general/index.php">Accueil</a></li>
                                <li><a href="../view/FrontOffice/success-stories.php">Success Stories</a></li>
                                <li class="active"><a href="historique_stories.php">Mon Historique</a></li>
                                <?php if ($user_role === 'Admin'): ?>
                                    <li><a href="admin_dashboard.php">Administration</a></li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        <div class="header__nav__social">
                            <div class="role-badge <?php echo $js_role; ?>">
                                <?php echo $role_names[$js_role] ?? 'Utilisateur'; ?>
                            </div>
                            <div class="user-profile-dropdown" id="userProfileDropdown">
                                <button class="user-profile-button" onclick="toggleUserMenu()">
                                    <img src="<?php echo htmlspecialchars($user_photo); ?>" alt="Profile" class="user-avatar">
                                    <div class="user-info">
                                        <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
                                        <span class="user-role-text"><?php echo htmlspecialchars($role_names[$js_role] ?? 'Utilisateur'); ?></span>
                                    </div>
                                    <i class="fa fa-chevron-down" style="margin-left: 5px; font-size: 12px;"></i>
                                </button>
                                <div class="user-dropdown-menu" id="userDropdownMenu">
                                    <a href="../view/general/profile.php" class="user-dropdown-item"><i class="fa fa-user"></i> Mon Profil</a>
                                     <a href="../view/general/logout.php" class="user-dropdown-item" style="color: #e74c3c;"><i class="fa fa-sign-out"></i> Déconnexion</a>
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
                     <h1 class="title-bubble">Mes Success Stories</h1>
                     <p style="margin-top: 15px; color: #ccc;">Gérez vos témoignages et partagez vos réussites</p>
                </section>

                <div class="stats-grid">
                    <div class="stat-card glass"><div class="stat-number"><?php echo $total; ?></div><div class="stat-label">Total Stories</div></div>
                    <div class="stat-card glass"><div class="stat-number"><?php echo $approved; ?></div><div class="stat-label">Approuvées</div></div>
                    <div class="stat-card glass"><div class="stat-number"><?php echo $pending; ?></div><div class="stat-label">En attente</div></div>
                    <div class="stat-card glass"><div class="stat-number"><?php echo $totalLikes; ?></div><div class="stat-label">Total Likes</div></div>
                </div>

                <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
                    <h2 style="color: white; font-size: 24px;">Mes publications</h2>
                    <button onclick="openCreateModal()" class="cta-button primary" style="font-size: 16px; padding: 12px 24px;">
                        <i class="fa fa-plus-circle"></i> Nouvelle Story
                    </button>
                </div>

                <?php if (empty($myStories)): ?>
                    <div class="glass" style="text-align: center; padding: 60px;">
                        <i class="fa fa-book" style="font-size: 64px; color: rgba(255,255,255,0.3); margin-bottom: 20px;"></i>
                        <h2>Aucune story pour le moment</h2>
                        <p style="color: #ccc; margin: 20px 0;">Commencez à partager vos expériences avec la communauté !</p>
                        <button onclick="openCreateModal()" class="cta-button primary">Créer ma première story</button>
                    </div>
                <?php else: ?>
                    <div class="grid">
                        <?php foreach ($myStories as $story): ?>
                        <div class="event-card glass">
                            <div class="company-info">
                                <div class="company-logo"><?php echo strtoupper(substr($story['author'] ?? 'M', 0, 1)); ?></div>
                                <div class="company-name">Moi</div>
                                <span class="tag <?php echo $story['status'] ?? 'pending'; ?>" style="margin-left: auto;">
                                    <?php 
                                        $statusLabels = ['pending'=>'En attente', 'approved'=>'Approuvée', 'rejected'=>'Rejetée'];
                                        echo $statusLabels[$story['status'] ?? 'pending'] ?? ucfirst($story['status']); 
                                    ?>
                                </span>
                            </div>
                            
                            <h3 class="event-title"><?php echo htmlspecialchars($story['title']); ?></h3>
                            
                            <div class="meta">
                                <span><i class="fa fa-calendar"></i> <?php echo date('d/m/Y', strtotime($story['created_at'])); ?></span>
                                
                            </div>
                            <div style="color: #bbb; margin-bottom: 15px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                <?php echo nl2br(htmlspecialchars($story['content'])); ?>
                            </div>
                            
                            <div style="display: flex; gap: 15px; margin-bottom: 15px; font-size: 14px; color: #ccc;">
                                <span><i class="fa fa-heart" style="color: #ff4f5e;"></i> <?php echo $story['likes'] ?? 0; ?></span>
                                <span><i class="fa fa-share"></i> <?php echo $story['shares'] ?? 0; ?></span>
                            </div>

                            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                <button onclick="editStory(<?php echo $story['id']; ?>)" class="cta-button"><i class="fa fa-edit"></i> Modifier</button>
                                <button onclick="deleteStory(<?php echo $story['id']; ?>)" class="cta-button danger"><i class="fa fa-trash"></i> Supprimer</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Modal Création -->
    <div id="createModal" class="modal">
        <div class="panel">
            <button class="cta-button" style="float: right; padding: 5px 10px; background: transparent;" onclick="closeModal('createModal')">✕</button>
            <h2 style="color: white; margin-bottom: 20px;">Partager une Success Story</h2>
            <form method="POST" action="story_actions.php">
                <input type="hidden" name="action" value="create">
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #ccc;">Titre de votre histoire</label>
                    <input type="text" name="title" class="input" required placeholder="Ex: Mon parcours vers l'emploi...">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #ccc;">Nom à afficher</label>
                    <input type="text" name="author_name" class="input" required value="<?php echo htmlspecialchars($user_name); ?>">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #ccc;">Votre témoignage</label>
                    <textarea name="content" class="input" required placeholder="Racontez votre expérience, vos défis et vos réussites..." style="height: 200px;"></textarea>
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" onclick="closeModal('createModal')" class="cta-button">Annuler</button>
                    <button type="submit" class="cta-button primary">Publier</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Modification -->
    <div id="editModal" class="modal">
        <div class="panel">
            <button class="cta-button" style="float: right; padding: 5px 10px; background: transparent;" onclick="closeModal('editModal')">✕</button>
            <h2 style="color: white; margin-bottom: 20px;">Modifier la story</h2>
            <form method="POST" action="story_actions.php">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="editId">
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #ccc;">Titre</label>
                    <input type="text" name="title" id="editTitle" class="input" required>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #ccc;">Auteur</label>
                    <input type="text" name="author_name" id="editAuthor" class="input" required>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; color: #ccc;">Contenu</label>
                    <textarea name="content" id="editContent" class="input" required style="height: 200px;"></textarea>
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" onclick="closeModal('editModal')" class="cta-button">Annuler</button>
                    <button type="submit" class="cta-button primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleUserMenu() {
            document.getElementById('userDropdownMenu').classList.toggle('show');
        }
        
        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.user-profile-button') && !event.target.closest('.user-profile-button')) {
                var dropdowns = document.getElementsByClassName("user-dropdown-menu");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        function editStory(id) {
            fetch('../view/FrontOffice/get_story.php?id=' + id)
                .then(r => r.json())
                .then(story => {
                    document.getElementById('editId').value = story.id;
                    document.getElementById('editTitle').value = story.title;
                    document.getElementById('editAuthor').value = story.author;
                    document.getElementById('editContent').value = story.content;
                    document.getElementById('editModal').style.display = 'block';
                });
        }

        function deleteStory(id) {
            if (confirm('Voulez-vous vraiment supprimer cette story ?')) {
                window.location.href = 'story_actions.php?action=delete&id=' + id;
            }
        }

        function openCreateModal() { document.getElementById('createModal').style.display = 'block'; }
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    </script>
</body>
</html>
