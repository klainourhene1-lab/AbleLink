<?php
session_start();

// 1) Auth Check
if (!isset($_SESSION['user_id'])) {
    header('Location: signin.php');
    exit;
}

// 2) Load Dependencies
require_once __DIR__ . '/../../Control/config.php';
require_once __DIR__ . '/../../Control/UserController.php';
require_once __DIR__ . '/../../Control/SocialController.php';
require_once __DIR__ . '/../../Model/User.php';

$userController = new UserController();
$socialController = new SocialController();

// 3) Determine Profile Target
$currentUserId = $_SESSION['user_id'];
$profileId = $_GET['id'] ?? $currentUserId;

// Fetch Data
$profileData = $socialController->getProfileData($profileId, $currentUserId);

if (!$profileData || !$profileData['user']) {
    die("Utilisateur non trouvé.");
}

$user = $profileData['user']; // The user being viewed
$currentUser = $userController->showUser($currentUserId); // The viewer
$user_role = $_SESSION['user_role'] ?? 'Utilisateur';

// Data for View
$isOwnProfile = $profileData['isOwnProfile'];
$friendship = $profileData['friendship'];
$friends = $profileData['friends'];
$posts = $profileData['posts'];
$pendingRequests = $profileData['pendingRequests'];

// Role logic for header
$role_mapping = ['Admin' => 'admin', 'Entreprise' => 'company', 'Utilisateur' => 'user', 'Inclusion' => 'inclusion', 'guest' => 'guest'];
$js_role = $role_mapping[$user_role] ?? 'user';
$role_display_names = ['admin' => 'Administrateur', 'company' => 'Entreprise', 'user' => 'Utilisateur', 'inclusion' => 'Responsable', 'guest' => 'Visiteur'];

// --- Handle Profile Update (Legacy Logic) ---
$refresh = false;
$updateMessage = "";
if ($isOwnProfile && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    
    // Photo Logic
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = 'user_' . $currentUserId . '_' . time() . '.' . $ext;
        $path = __DIR__ . '/../../uploads/profiles/' . $filename;
        if (!is_dir(dirname($path))) mkdir(dirname($path), 0777, true);
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $path)) {
            $userController->updatePhoto($currentUserId, $filename);
            $user->setPhoto($filename);
            $_SESSION['user_photo'] = $filename; // Update session
        }
    }
    
    $user->setNom($nom);
    $user->setPrenom($prenom);
    $user->setEmail($email);
    $user->setTelephone($telephone);
    
    if ($userController->updateUser($user, $currentUserId)) {
        $updateMessage = "Profil mis à jour !";
        $refresh = true;
    }
}

if ($refresh) {
    header("Location: profile.php");
    exit;
}

// Helpers
function getPhoto($u) {
    if ($u->getPhoto() && file_exists(__DIR__ . '/../../uploads/profiles/' . $u->getPhoto())) {
        return '../../uploads/profiles/' . $u->getPhoto();
    }
    return 'img/team/team-1.jpg'; // Path adapted for view/general
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="AbleLink - My Profile">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil | AbleLink</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Standard CSS (Relative to view/general) -->
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
    
    <style>
        body {
            background: #100028; /* Matches shared style */
            min-height: 100vh;
            color: #ffffff;
            display: flex;
            flex-direction: column;
        }
        
        /* Ensure the main content pushes the footer down */
        .social-container {
            flex: 1;
        }
        
        /* HEADER STYLES COPIED FROM LISTE_OFFRES */
        .header__nav__option { display: flex; align-items: center; justify-content: space-between; }
        .header__nav__menu { flex: 1; min-width: 0; }
        .header__nav__menu ul { display: flex; gap: 24px; align-items: center; justify-content: flex-end; }
        
        .role-badge {
            padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; margin-left: 10px;
        }
        .role-badge.user { background: rgba(52, 152, 219, 0.2); color: #3498db; }
        .role-badge.company { background: rgba(46, 204, 113, 0.2); color: #2ecc71; }
        .role-badge.inclusion { background: rgba(155, 89, 182, 0.2); color: #9b59b6; }
        .role-badge.admin { background: rgba(231, 76, 60, 0.2); color: #e74c3c; }

        /* Site Title */
        .site-title { margin-top: 0; padding: 0; }
        .site-title a { font-family: 'Josefin Sans', sans-serif; font-size: 32px; font-weight: 700; text-decoration: none; line-height: 1.2; display: inline-flex; gap: 2px; letter-spacing: 1px; text-transform: uppercase; transition: 0.3s ease; }
        .letter-a  { color: #ff4f5e; text-shadow: 0 0 8px rgba(255,79,94,0.6); }
        .letter-b  { color: #4c8df5; text-shadow: 0 0 8px rgba(76,141,245,0.6); }
        .letter-l  { color: #b87bff; text-shadow: 0 0 8px rgba(184,123,255,0.6); }
        .letter-e  { color: #ffb247; text-shadow: 0 0 8px rgba(255,178,71,0.6); }
        .letter-link { color: #ffffff; margin-left: 4px; text-shadow: 0 0 10px rgba(255,255,255,0.7); }

        /* User Profile Dropdown */
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
            display: none; position: absolute; top: calc(100% + 15px); right: 0; min-width: 280px;
            background: #0f172a; border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.6); z-index: 1000; overflow: hidden;
            padding-bottom: 8px;
        }
        .user-dropdown-menu.show { display: block; animation: fadeIn 0.2s ease-out; }
        
        .dropdown-user-header {
            padding: 20px; display: flex; align-items: center; gap: 15px;
            background: rgba(255,255,255,0.02);
        }
        .dropdown-avatar-large { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #00bfe7; }
        .dropdown-user-details { display: flex; flex-direction: column; }
        .d-name { color: #fff; font-weight: 700; font-size: 15px; }
        .d-email { color: #94a3b8; font-size: 12px; }
        
        .dropdown-divider { height: 1px; background: rgba(255,255,255,0.08); margin: 8px 0; }
        
        .user-dropdown-item {
            display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #cbd5e1;
            text-decoration: none; transition: all 0.2s; font-size: 14px; font-weight: 500;
        }
        .user-dropdown-item i { width: 20px; text-align: center; color: #94a3b8; transition: 0.2s; }
        .user-dropdown-item:hover { background: rgba(255,255,255,0.05); color: #fff; text-decoration: none; }
        .user-dropdown-item:hover i { color: #00bfe7; }
        
        .user-dropdown-item.item-logout { color: #ef4444; margin-top: 5px; }
        .user-dropdown-item.item-logout i { color: #ef4444; }
        .user-dropdown-item.item-logout:hover { background: rgba(239, 68, 68, 0.1); }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

        /* --- Social Page Styles --- */
        .social-container {
            max-width: 1200px; margin: 180px auto 40px; padding: 0 15px;
            display: grid; grid-template-columns: 280px 1fr 300px; gap: 25px;
        }
        @media(max-width: 991px) { .social-container { grid-template-columns: 1fr; margin-top: 150px; } }

        .s-card {
            background: #1a083d; border-radius: 12px; padding: 20px;
            margin-bottom: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.05);
        }

        .p-avatar {
            width: 100px; height: 100px; border-radius: 50%; object-fit: cover;
            border: 3px solid #00bfe7; margin-bottom: 15px;
        }
        .p-name { font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 5px; text-align: center; }
        .p-role { font-size: 14px; color: #a9a9a9; text-transform: uppercase; letter-spacing: 1px; text-align: center; }
        .profile-mini-header { text-align: center; }

        .p-stats {
            display: flex; justify-content: space-around; margin-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;
        }
        .stat-item { text-align: center; }
        .stat-val { display: block; font-size: 18px; font-weight: 700; color: #fff; }
        .stat-label { font-size: 12px; color: #888; }

        .create-post { display: flex; gap: 15px; }
        .cp-input {
            flex: 1; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px; padding: 12px 20px; color: #fff; resize: none; overflow: hidden; height: 48px;
        }
        .cp-input:focus { outline: none; border-color: #00bfe7; background: rgba(255,255,255,0.1); }
        .cp-btn {
            background: #00bfe7; border: none; width: 48px; height: 48px; border-radius: 50%;
            color: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer;
            transition: 0.3s;
        }
        .cp-btn:hover { background: #0099b8; transform: scale(1.1); }

        .feed-post { padding: 0; overflow: hidden; }
        .post-header {
            padding: 15px 20px; display: flex; align-items: center; gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .ph-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
        .ph-info h5 { margin: 0; font-size: 16px; color: #fff; }
        .ph-info span { font-size: 12px; color: #888; }
        .post-content { padding: 20px; font-size: 15px; line-height: 1.6; color: #e0e0e0; }
        
        .post-actions {
            padding: 10px 20px; background: rgba(0,0,0,0.2);
            display: flex; gap: 20px;
        }
        .action-btn {
            background: none; border: none; color: #888; font-size: 14px;
            display: flex; align-items: center; gap: 6px; cursor: pointer; transition: 0.2s;
        }
        .action-btn:hover, .action-btn.active { color: #00bfe7; }
        
        .comments-section {
            padding: 15px 20px; background: rgba(0,0,0,0.3); border-top: 1px solid rgba(255,255,255,0.05);
            display: none;
        }
        .comment { display: flex; gap: 10px; margin-bottom: 15px; }
        .c-avatar { width: 30px; height: 30px; border-radius: 50%; }
        .c-bubble {
            background: rgba(255,255,255,0.05); padding: 8px 12px; border-radius: 12px;
            flex: 1;
        }
        .c-name { font-size: 13px; font-weight: 700; color: #ddd; margin-bottom: 2px; }
        .c-text { font-size: 13px; color: #bbb; margin: 0; }

        .friend-item {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 15px;
        }
        .fi-info { display: flex; align-items: center; gap: 10px; }
        .fi-avatar { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; }
        .fi-name { font-size: 14px; color: #fff; text-decoration: none; font-weight: 600; }
        .fi-name:hover { color: #00bfe7; }
        
        .btn-small { padding: 4px 10px; font-size: 11px; border-radius: 4px; border: none; cursor: pointer; }
        .btn-add { background: rgba(0, 191, 231, 0.2); color: #00bfe7; }
        .btn-add:hover { background: #00bfe7; color: #fff; }
        
        /* Modal */
        .modal { background: rgba(0,0,0,0.8); z-index: 10000; }
        .modal-content { background: #1a083d; color: #fff; border: 1px solid rgba(255,255,255,0.1); }
        .close { color: #fff; }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="header__logo">
                        <div class="site-title">
                            <a href="index.php">
                                <span class="letter-a">A</span><span class="letter-b">b</span><span class="letter-l">l</span><span class="letter-e">e</span><span class="letter-link">Link</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-10">
                    <div class="header__nav__option">
                        <nav class="header__nav__menu mobile-menu">
                            <ul>
                                <li><a href="index.php">Accueil</a></li>
                                <!-- Links removed as requested -->
                                <?php if ($user_role === 'Admin'): ?>
                                    <li><a href="../../Control/admin_dashboard.php">Administration</a></li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        <div class="header__nav__social">
                            <?php if ($currentUser): ?>
                                <div class="role-badge <?php echo $js_role; ?>">
                                    <?php echo $role_display_names[$js_role] ?? 'Utilisateur'; ?>
                                </div>
                                <div class="user-profile-dropdown" id="userProfileDropdown">
                                    <button class="user-profile-button" onclick="toggleUserMenu()">
                                        <img src="<?php echo htmlspecialchars(getPhoto($currentUser)); ?>" alt="Profile" class="user-avatar">
                                        <div class="user-info">
                                            <span class="user-name"><?php echo htmlspecialchars($currentUser->getPrenom()); ?></span>
                                            <span class="user-role-text"><?php echo htmlspecialchars($role_display_names[$js_role] ?? 'Utilisateur'); ?></span>
                                        </div>
                                        <i class="fa fa-chevron-down" style="margin-left: 5px; font-size: 12px;"></i>
                                    </button>
                                    <div class="user-dropdown-menu" id="userDropdownMenu">
                                       <div class="dropdown-user-header">
                                           <img src="<?php echo htmlspecialchars(getPhoto($currentUser)); ?>" class="dropdown-avatar-large">
                                           <div class="dropdown-user-details">
                                               <span class="d-name"><?php echo htmlspecialchars($currentUser->getPrenom() . ' ' . $currentUser->getNom()); ?></span>
                                               <span class="d-email"><?php echo htmlspecialchars($currentUser->getEmail()); ?></span>
                                           </div>
                                       </div>
                                       <div class="dropdown-divider"></div>
                                       <a href="profile.php" class="user-dropdown-item"><i class="fa fa-user"></i> Voir le profil</a>
                                       <a href="edit_profile.php" class="user-dropdown-item"><i class="fa fa-cog"></i> Paramètres du compte</a>
                                       <a href="#" class="user-dropdown-item" onclick="toggleNotifications(event)"><i class="fa fa-bell"></i> Notifications <span id="notif-badge" class="badge bg-danger" style="margin-left:auto; display:none;">0</span></a>
                                <div id="notification-list" style="display:none; background: rgba(0,0,0,0.2);">
                                    <!-- Notifications will be loaded here -->
                                </div>
                                <a href="#" class="user-dropdown-item"><i class="fa fa-exchange"></i> Changer de compte</a>
                                       <a href="#" class="user-dropdown-item"><i class="fa fa-question-circle"></i> Centre d'aide</a>
                                       <a href="logout.php" class="user-dropdown-item item-logout"><i class="fa fa-sign-out"></i> Déconnexion</a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <a href="signin.php" class="btn btn-primary">Connexion</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="social-container">
        
        <!-- LEFT COLUMN: Profile Card -->
        <div class="left-col">
            <div class="s-card profile-mini-header">
                <img src="<?php echo getPhoto($user); ?>" class="p-avatar">
                <div class="p-name"><?php echo htmlspecialchars($user->getPrenom() . ' ' . $user->getNom()); ?></div>
                <div class="p-role"><?php echo htmlspecialchars($user->getRole()); ?></div>
                
                <?php if ($isOwnProfile): ?>
                    <a href="edit_profile.php" class="btn btn-sm btn-outline-light mt-3">
                        <i class="fa fa-pencil"></i> Modifier
                    </a>
                <?php else: ?>
                    <div class="mt-3">
                        <?php if ($friendship && $friendship['statut'] == 'accepte'): ?>
                            <span class="badge badge-success">Amis</span>
                        <?php elseif ($friendship && $friendship['statut'] == 'attente'): ?>
                            <span class="badge badge-warning">Demande envoyée</span>
                        <?php else: ?>
                            <button class="btn btn-sm btn-primary" onclick="sendRequest(<?php echo $user->getId(); ?>)">
                                <i class="fa fa-user-plus"></i> Ajouter
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="p-stats">
                    <div class="stat-item">
                        <span class="stat-val"><?php echo count($friends); ?></span>
                        <span class="stat-label">Amis</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-val"><?php echo count($posts); ?></span>
                        <span class="stat-label">Posts</span>
                    </div>
                </div>
            </div>

             <?php if ($isOwnProfile && !empty($pendingRequests)): ?>
                <div class="s-card">
                    <h5 style="font-size: 16px; margin-bottom: 15px;">Demandes</h5>
                    <?php foreach ($pendingRequests as $req): ?>
                        <div class="friend-item" id="req-<?php echo $req['friendship_id']; ?>">
                            <div class="fi-info">
                                <img src="<?php echo getPhoto(new User(null, '', '', '', '', '', '', '', null, null, $req['photo'])); ?>" class="fi-avatar">
                                <a href="profile.php?id=<?php echo $req['id']; ?>" class="fi-name">
                                    <?php echo htmlspecialchars($req['prenom'] . ' ' . $req['nom']); ?>
                                </a>
                            </div>
                            <button class="btn-small btn-add" onclick="acceptRequest(<?php echo $req['friendship_id']; ?>)">
                                <i class="fa fa-check"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- CENTER COLUMN: Feed -->
        <div class="center-col">
            <?php if ($isOwnProfile): ?>
                <div class="s-card">
                    <!-- Search Bar -->
        <div class="s-card search-card">
            <h5 style="color: #fff; margin-bottom: 15px; font-weight: 600;">Rechercher des membres</h5>
            <div style="display: flex; gap: 10px; position: relative;">
                <input type="text" id="userSearchInput" class="cp-input" placeholder="Chercher un ami..." style="width: 100%;" onkeypress="handleSearchKey(event)">
                <button onclick="searchUsers()" class="cp-btn" style="border-radius: 12px; width: auto; padding: 0 20px;"><i class="fa fa-search"></i></button>
                
                <div id="searchResults" class="search-dropdown" style="display: none; position: absolute; top: 100%; left: 0; width: 100%; background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; z-index: 100; margin-top: 5px; max-height: 300px; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                    <!-- Results go here -->
                </div>
            </div>
        </div>

        <div class="create-post">
                        <img src="<?php echo getPhoto($user); ?>" style="width: 48px; height: 48px; border-radius: 50%;">
                        <input type="text" id="postContent" class="cp-input" placeholder="Quoi de neuf, <?php echo htmlspecialchars($user->getPrenom()); ?> ?">
                        <button class="cp-btn" onclick="submitPost()"><i class="fa fa-paper-plane"></i></button>
                    </div>
                </div>
            <?php endif; ?>

            <div id="feed-container">
                <?php foreach ($posts as $post): ?>
                    <div class="s-card feed-post">
                        <div class="post-header">
                            <img src="<?php echo $post['photo'] ? '../../uploads/profiles/'.$post['photo'] : 'img/team/team-1.jpg'; ?>" class="ph-avatar">
                            <div class="ph-info">
                                <h5><?php echo htmlspecialchars($post['prenom'] . ' ' . $post['nom']); ?></h5>
                                <span><?php echo date('d M H:i', strtotime($post['datePublication'])); ?></span>
                            </div>
                        </div>
                        <div class="post-content">
                            <?php echo nl2br(htmlspecialchars($post['contenu'])); ?>
                        </div>
                        <div class="post-actions">
                            <button class="action-btn" onclick="toggleLike(<?php echo $post['id']; ?>, this)">
                                <i class="fa fa-heart-o"></i> Like <span class="like-count"><?php echo $post['popularite']; ?></span>
                            </button>
                            <button class="action-btn" onclick="toggleComments(<?php echo $post['id']; ?>)">
                                <i class="fa fa-comment-o"></i> Commenter
                            </button>
                        </div>
                        <div class="comments-section" id="comments-<?php echo $post['id']; ?>">
                            <div class="create-post mb-3">
                                <input type="text" class="cp-input" placeholder="Écrire un commentaire..." id="comment-input-<?php echo $post['id']; ?>">
                                <button class="cp-btn" style="width: 36px; height: 36px;" onclick="submitComment(<?php echo $post['id']; ?>)">
                                    <i class="fa fa-paper-plane" style="font-size: 12px;"></i>
                                </button>
                            </div>
                            <div id="comments-list-<?php echo $post['id']; ?>">
                                <!-- Dynamic comments -->
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- RIGHT COLUMN: Friends -->
        <div class="right-col">
            <div class="s-card">
                <h5 style="font-size: 16px; margin-bottom: 15px;">Vos Amis</h5>
                <?php if (empty($friends)): ?>
                    <p style="color: #777; font-size: 13px;">Aucun ami.</p>
                <?php else: ?>
                    <?php foreach ($friends as $friend): ?>
                        <div class="friend-item">
                            <div class="fi-info">
                                <img src="<?php echo getPhoto(new User(null, '', '', '', '', '', '', '', null, null, $friend['photo'])); ?>" class="fi-avatar">
                                <a href="profile.php?id=<?php echo $friend['id']; ?>" class="fi-name">
                                    <?php echo htmlspecialchars($friend['prenom'] . ' ' . $friend['nom']); ?>
                                </a>
                            </div>
                            <a href="profile.php?id=<?php echo $friend['id']; ?>" class="btn-small" style="background: rgba(255,255,255,0.1); color: #fff;">
                                Voir
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier mon profil</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_profile">
                        <div class="form-group">
                            <label>Photo</label>
                            <input type="file" name="photo" class="form-control-file">
                        </div>
                         <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Prénom</label>
                                    <input type="text" name="prenom" class="form-control" value="<?php echo htmlspecialchars($user->getPrenom()); ?>">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Nom</label>
                                    <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($user->getNom()); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user->getEmail()); ?>">
                        </div>
                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="text" name="telephone" class="form-control" value="<?php echo htmlspecialchars($user->getTelephone()); ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer__top">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="site-title" style="font-size: 32px; font-weight: 700;">
                            <a href="index.php">
                                <span class="letter-a">A</span><span class="letter-b">b</span><span class="letter-l">l</span><span class="letter-e">e</span><span class="letter-link">Link</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer__option">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="footer__option__item">
                            <h5>À propos d’AbleLink</h5>
                            <p>AbleLink connecte les chercheurs d’emploi en situation de handicap aux entreprises inclusives.</p>
                        </div>
                    </div>
                     <div class="col-lg-2 col-md-3 col-sm-3">
                        <div class="footer__option__item">
                            <h5>Ressources</h5>
                            <ul>
                                <li><a href="#">Équipe</a></li>
                                <li><a href="#">Carrières</a></li>
                                <li><a href="#">Contact</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-3">
                        <div class="footer__option__item">
                            <h5>Explorer</h5>
                            <ul>
                                <li><a href="../FrontOffice/liste_offres.php">Offres d’emploi</a></li>
                                <li><a href="#">Communauté</a></li>
                            </ul>
                        </div>
                    </div>
                     <div class="col-lg-4 col-md-12">
                        <div class="footer__option__item">
                            <h5>Newsletter</h5>
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
                        <p>Copyright © <?php echo date('Y'); ?> AbleLink | Vers un avenir professionnel inclusif</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- JS -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    
    <script>
    // User Menu Toggle (Exact match from liste_offres.php)
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

    // --- API HANDLER ---
    async function apiCall(action, data) {
        const response = await fetch('../../Control/social_handler.php?action=' + action, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        return await response.json();
    }

    async function submitPost() {
        const content = document.getElementById('postContent').value.trim();
        if (!content) return;
        const res = await apiCall('post', { content: content });
        if (res.success) location.reload();
    }

    async function toggleLike(postId, btn) {
        const res = await apiCall('like', { postId: postId });
        if (res.success) {
            const countSpan = btn.querySelector('.like-count');
            let count = parseInt(countSpan.innerText);
            if (res.status === 'liked') {
                btn.classList.add('active');
                btn.querySelector('i').classList.remove('fa-heart-o');
                btn.querySelector('i').classList.add('fa-heart');
                countSpan.innerText = count + 1;
            } else {
                btn.classList.remove('active');
                 btn.querySelector('i').classList.remove('fa-heart');
                btn.querySelector('i').classList.add('fa-heart-o');
                countSpan.innerText = count - 1;
            }
        }
    }
    
    function toggleComments(postId) {
        const section = document.getElementById('comments-' + postId);
        section.style.display = section.style.display === 'none' || section.style.display === '' ? 'block' : 'none';
    }

    async function submitComment(postId) {
        const input = document.getElementById('comment-input-' + postId);
        const content = input.value.trim();
        if (!content) return;

        const res = await apiCall('comment', { postId: postId, content: content });
        if (res.success) {
            const list = document.getElementById('comments-list-' + postId);
            const html = `
                <div class="comment">
                    <img src="<?php echo getPhoto($currentUser); ?>" class="c-avatar">
                    <div class="c-bubble">
                        <div class="c-name">Vous</div>
                        <p class="c-text">${content}</p>
                    </div>
                </div>
            `;
            list.insertAdjacentHTML('beforeend', html);
            input.value = '';
        }
    }

    async function sendRequest(targetId) {
        const res = await apiCall('friend_request', { targetId: targetId });
        if (res.success) { alert('Demande envoyée !'); location.reload(); }
        else alert('Erreur');
    }

    async function acceptRequest(requestId) {
        const res = await apiCall('accept_friend', { requestId: requestId });
        if (res.success) { alert('Ami accepté !'); loadNotifications(); location.reload(); }
    }

    async function rejectRequest(requestId) {
        const res = await apiCall('reject_request', { requestId: requestId });
        if (res.success) { alert('Demande rejetée.'); loadNotifications(); }
    }

    // SEARCH & NOTIFICATIONS
    function handleSearchKey(e) {
        if (e.key === 'Enter') searchUsers();
    }

    async function searchUsers() {
        const query = document.getElementById('userSearchInput').value.trim();
        const resultsDiv = document.getElementById('searchResults');
        
        if (query.length === 0) {
            resultsDiv.style.display = 'none';
            return;
        }

        const res = await apiCall('search_users', { query: query });
        if (res.success && res.users.length > 0) {
            let html = '';
            res.users.forEach(u => {
                let actionBtn = `<button onclick="sendRequest(${u.id})" class="btn-sm btn-primary">Ajouter</button>`;
                let profileLink = `profile.php?id=${u.id}`; // Always link to profile
                
                if (u.friendship) {
                    if (u.friendship.statut === 'attente') actionBtn = '<span class="text-warning" style="font-size:12px;">En attente</span>';
                    else if (u.friendship.statut === 'accepte') actionBtn = '<span class="text-success" style="font-size:12px;"><i class="fa fa-check"></i> Amis</span>';
                }
                
                // Fix image path if needed
                let photo = u.photo ? '../../uploads/profiles/' + u.photo : 'img/team/team-1.jpg';

                html += `
                    <div style="display: flex; align-items: center; padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <a href="${profileLink}" style="text-decoration: none;">
                            <img src="${photo}" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 15px; object-fit:cover; border: 1px solid #00bfe7;">
                        </a>
                        <div style="flex: 1;">
                            <a href="${profileLink}" style="color: #fff; font-size: 15px; font-weight: 600; text-decoration: none; display: block;">
                                ${u.prenom} ${u.nom}
                            </a>
                            <div style="color: #888; font-size: 11px; margin-top:2px;">${u.role}</div>
                        </div>
                        ${actionBtn}
                    </div>
                `;
            });
            resultsDiv.innerHTML = html;
            resultsDiv.style.display = 'block';
        } else {
             resultsDiv.innerHTML = '<div style="padding:20px; color:#a9a9a9; text-align:center; font-style:italic;">Utilisateur introuvable</div>';
             resultsDiv.style.display = 'block';
        }
    }

    async function loadNotifications() {
        const res = await apiCall('get_notifications', {});
        const badge = document.getElementById('notif-badge');
        const list = document.getElementById('notification-list');
        
        if (res.success && res.notifications.length > 0) {
            badge.innerText = res.notifications.length;
            badge.style.display = 'inline-block';
            
            let html = '';
            res.notifications.forEach(n => {
                 let photo = n.photo ? '../../uploads/profiles/' + n.photo : 'img/team/team-1.jpg';
                 html += `
                    <div style="padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; align-items: center; gap: 10px;">
                        <img src="${photo}" style="width: 30px; height: 30px; border-radius: 50%;">
                        <div style="flex: 1; color: #ccc; font-size: 12px;">
                            <span style="color: #fff; font-weight: bold;">${n.prenom} ${n.nom}</span> veut vous ajouter.
                        </div>
                        <div style="display: flex; gap: 5px;">
                            <button onclick="acceptRequest(${n.friendship_id})" style="background:none; border:none; color: #2ecc71; cursor: pointer;"><i class="fa fa-check"></i></button>
                            <button onclick="rejectRequest(${n.friendship_id})" style="background:none; border:none; color: #e74c3c; cursor: pointer;"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                 `;
            });
            list.innerHTML = html;
        } else {
            badge.style.display = 'none';
            list.innerHTML = '<div style="padding:10px; color:#888; text-align:center; font-size: 12px;">Aucune notification</div>';
        }
    }

    function toggleNotifications(e) {
        e.preventDefault();
        e.stopPropagation();
        const list = document.getElementById('notification-list');
        list.style.display = list.style.display === 'none' ? 'block' : 'none';
        // Reload notifications when opening
        if(list.style.display === 'block') loadNotifications();
    }
    
    // Initial Load
    // loadNotifications(); // Call this immediately if we want badge to show up 
    document.addEventListener('DOMContentLoaded', loadNotifications);


    </script>
</body>
</html>