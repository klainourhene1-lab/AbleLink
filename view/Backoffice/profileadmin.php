<?php
session_start();
require_once __DIR__ . '/../../Controller/config.php';
require_once __DIR__ . '/../../Controller/UserController.php';
require_once __DIR__ . '/../../Model/User.php';

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté.");
}

$controller = new UserController();
$user = $controller->showUser($_SESSION['user_id']);

if (!$user) {
    die("Utilisateur introuvable.");
}

// === UPLOAD PHOTO ===
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["photo"]) && $_FILES["photo"]["error"] === 0) {
    
    // Create directory if it doesn't exist
    $uploadDir = __DIR__ . "/../../uploads/profiles/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = $_FILES["photo"]["name"];
    $fileTmp = $_FILES["photo"]["tmp_name"];
    $fileSize = $_FILES["photo"]["size"];

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($fileTmp);
    
    if (!in_array($fileType, $allowedTypes)) {
        die("Type de fichier non autorisé.");
    }

    // Validate file size (max 5MB)
    if ($fileSize > 5 * 1024 * 1024) {
        die("Fichier trop volumineux (max 5MB).");
    }

    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
    $newName = "profile_" . $user->getId() . "_" . time() . "." . $extension;
    $uploadPath = $uploadDir . $newName;

    if (move_uploaded_file($fileTmp, $uploadPath)) {
        // Save in DB
        if ($controller->updatePhoto($user->getId(), $newName)) {
            $user->setPhoto($newName);
            // Refresh the page to show new photo
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            die("Erreur lors de la mise à jour en base de données.");
        }
    } else {
        die("Erreur lors du téléchargement.");
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AbleLink Admin - Mon Profil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #7c3aed;
            --primary-light: #8b5cf6;
            --secondary: #0ea5e9;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --dark-light: #1e293b;
            --text: #e2e8f0;
            --text-light: #94a3b8;
            --card-bg: rgba(30, 41, 59, 0.7);
            --sidebar-width: 260px;
            --header-height: 70px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --border-radius: 16px;
            --shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            --glow: 0 0 20px rgba(124, 58, 237, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            padding: 25px 20px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            transition: var(--transition);
            z-index: 100;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
            padding: 0 10px;
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            color: var(--text-light);
            border-radius: 12px;
            transition: var(--transition);
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }

        .nav-link:hover::before {
            left: 100%;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(124, 58, 237, 0.1);
            color: white;
            box-shadow: 0 0 15px rgba(124, 58, 237, 0.2);
        }

        .nav-link i {
            font-size: 20px;
            width: 24px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 25px;
            transition: var(--transition);
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 15px 0;
            animation: slideDown 0.5s ease-out;
        }

        .header-title h1 {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(to right, white, var(--text-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-title p {
            color: var(--text-light);
            font-size: 14px;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            box-shadow: var(--shadow);
            cursor: pointer;
            transition: var(--transition);
        }

        .user-avatar:hover {
            transform: scale(1.05);
            box-shadow: var(--glow);
        }

        /* Profile Card */
        .profile-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: var(--shadow);
            border: 1px solid rgba(255, 255, 255, 0.05);
            margin-bottom: 25px;
            animation: fadeIn 0.6s ease-out;
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 25px;
            margin-bottom: 30px;
            padding-bottom: 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .profile-photo-wrapper {
            position: relative;
        }

        .profile-photo {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid var(--primary);
            box-shadow: var(--glow);
        }

        .photo-upload-btn {
            position: absolute;
            bottom: 0;
            right: 0;
            background: var(--primary);
            border: none;
            padding: 8px 12px;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            font-size: 12px;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .photo-upload-btn:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
        }

        .profile-info h2 {
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .profile-info .role {
            color: var(--text-light);
            font-size: 14px;
            margin-bottom: 5px;
        }

        .profile-info .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: rgba(124, 58, 237, 0.2);
            color: var(--primary-light);
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .edit-btn {
            padding: 10px 20px;
            background: var(--warning);
            border-radius: 8px;
            color: white;
            font-size: 14px;
            cursor: pointer;
            border: none;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .edit-btn:hover {
            background: #d97706;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(245, 158, 11, 0.3);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .info-box {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 12px;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .info-box:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-2px);
        }

        .info-label {
            font-size: 13px;
            color: var(--text-light);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-label i {
            color: var(--primary);
        }

        .info-value {
            font-size: 16px;
            font-weight: 500;
            color: var(--text);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(124, 58, 237, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(124, 58, 237, 0); }
            100% { box-shadow: 0 0 0 0 rgba(124, 58, 237, 0); }
        }

        /* Logo Styles */
        .site-title a {
            font-family: 'Josefin Sans', sans-serif;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 3px;
            transition: 0.3s ease;
        }

        .letter-a  { color: #FF4F5E; text-shadow: 0 0 6px rgba(255,79,94,0.7); }
        .letter-b  { color: #4C8DF5; text-shadow: 0 0 6px rgba(76,141,245,0.7); }
        .letter-l  { color: #B87BFF; text-shadow: 0 0 6px rgba(184,123,255,0.7); }
        .letter-e  { color: #FFB247; text-shadow: 0 0 6px rgba(255,178,71,0.7); }

        .letter-link {
            color: #FFFFFF;
            margin-left: 5px;
            text-shadow: 0 0 10px rgba(255,255,255,0.8);
        }

        .site-title:hover a {
            transform: scale(1.05);
        }

        .site-title a span:hover {
            transform: translateY(-2px);
            transition: 0.2s ease;
        }

        /* Profile Menu Dropdown */
        .profile-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #e2e8f0;
            padding: 10px;
            border-radius: 8px;
            text-decoration: none;
            transition: 0.2s;
            font-size: 14px;
        }

        .profile-item:hover {
            background: rgba(255,255,255,0.08);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo site-title">
                <a href="../../Controller/admin_dashboard.php" style="text-decoration:none; display:flex; align-items:center; gap:10px;">
                    <span class="letter-a">A</span>
                    <span class="letter-b">B</span>
                    <span class="letter-l">L</span>
                    <span class="letter-e">E</span>
                    <span class="letter-link">LINK</span>
                </a>
            </div>

            <div class="nav-links">
                <a href="../../Controller/admin_dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i>
                    <span>Tableau de Bord</span>
                </a>
                <a href="../FrontOffice/evaluations-evenements.php" class="nav-link">
                    <i class="fas fa-globe"></i>
                    <span>Accueil</span>
                </a>
                <a href="../FrontOffice/evaluations-evenements.php" class="nav-link">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Événements</span>
                </a>
                <a href="#" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Utilisateurs</span>
                </a>
                <a href="#" class="nav-link">
                    <i class="fas fa-shield-alt"></i>
                    <span>Modération</span>
                </a>
                <a href="profileadmin.php" class="nav-link active">
                    <i class="fas fa-user-circle"></i>
                    <span>Mon Profil</span>
                </a>
            </div>

            <div style="margin-top: auto; padding: 20px 0;">
                <div class="nav-link">
                    <i class="fas fa-question-circle"></i>
                    <span>Aide & Support</span>
                </div>
                <a href="../general/logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Déconnexion</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-title">
                    <h1>Mon Profil</h1>
                    <p>Gérez vos informations personnelles</p>
                </div>
                <div class="user-menu" style="position: relative; display:flex; align-items:center; gap:10px;">
                    <div class="user-avatar" onclick="toggleProfileMenu()" 
                         style="cursor:pointer;">
                        <i class="fas fa-user"></i>
                    </div>

                    <div id="profileMenu"
                         style="
                            display:none;
                            position:absolute;
                            top:60px;
                            right:0;
                            width:230px;
                            background:#1e293b;
                            border-radius:12px;
                            padding:15px;
                            box-shadow:0 10px 25px rgba(0,0,0,0.4);
                            z-index:999;
                         ">
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;">
                            <div style="
                                width:40px; height:40px; border-radius:50%; 
                                background: linear-gradient(135deg, var(--primary), var(--secondary));
                                display:flex; justify-content:center; align-items:center;
                                color:white; font-size:16px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <div style="font-weight:600;">Administrateur</div>
                            </div>
                        </div>

                        <hr style="border-color:#334155; margin:10px 0;">

                        <a href="profileadmin.php" class="profile-item">
                            <i class="fas fa-user"></i> Voir le profil
                        </a>
                        <a href="#" class="profile-item"><i class="fas fa-cog"></i> Paramètres du compte</a>
                        <a href="#" class="profile-item"><i class="fas fa-bell"></i> Notifications</a>
                        <a href="../general/logout.php" class="profile-item" style="color:#ef4444;">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                    </div>
                </div>
            </div>

            <!-- Profile Card -->
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-photo-wrapper">
                        <img id="profileImg" class="profile-photo"
                            src="<?= $user->getPhoto() ? '../../uploads/profiles/' . $user->getPhoto() : '../general/img/team/team-1.jpg'; ?>"
                            alt="Profile Photo">
                        
                        <button class="photo-upload-btn" onclick="document.getElementById('photoInput').click()">
                            <i class="fas fa-camera"></i>
                        </button>

                        <form method="POST" enctype="multipart/form-data" id="photoForm" style="display:none;">
                            <input type="file" name="photo" id="photoInput" accept="image/*" onchange="uploadPhoto()">
                        </form>
                    </div>

                    <div class="profile-info">
                        <h2><?= htmlspecialchars($user->getPrenom() . " " . $user->getNom()); ?></h2>
                        <p class="role"><?= $user->getRole(); ?></p>
                        <span class="badge">Profil Administrateur</span>
                    </div>
                </div>

                <div class="section-title">
                    Informations personnelles
                    <a href="../general/backoffice/edit_user.php?id=<?= $user->getId(); ?>" class="edit-btn">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                </div>

                <div class="info-grid">
                    <div class="info-box">
                        <div class="info-label">
                            <i class="fas fa-user"></i>
                            Prénom
                        </div>
                        <div class="info-value"><?= htmlspecialchars($user->getPrenom()); ?></div>
                    </div>

                    <div class="info-box">
                        <div class="info-label">
                            <i class="fas fa-user"></i>
                            Nom
                        </div>
                        <div class="info-value"><?= htmlspecialchars($user->getNom()); ?></div>
                    </div>

                    <div class="info-box">
                        <div class="info-label">
                            <i class="fas fa-envelope"></i>
                            Adresse Email
                        </div>
                        <div class="info-value"><?= htmlspecialchars($user->getEmail()); ?></div>
                    </div>

                    <div class="info-box">
                        <div class="info-label">
                            <i class="fas fa-phone"></i>
                            Téléphone
                        </div>
                        <div class="info-value"><?= htmlspecialchars($user->getTelephone()); ?></div>
                    </div>

                    <div class="info-box">
                        <div class="info-label">
                            <i class="fas fa-user-tag"></i>
                            Rôle
                        </div>
                        <div class="info-value"><?= htmlspecialchars($user->getRole()); ?></div>
                    </div>

                    <div class="info-box">
                        <div class="info-label">
                            <i class="fas fa-check-circle"></i>
                            Statut
                        </div>
                        <div class="info-value"><?= htmlspecialchars($user->getStatut()); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function uploadPhoto() {
            document.getElementById("photoForm").submit();
        }

        function toggleProfileMenu() {
            const menu = document.getElementById("profileMenu");
            menu.style.display = menu.style.display === "none" ? "block" : "none";
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('profileMenu');
            const avatar = document.querySelector('.user-avatar');
            
            if (!menu.contains(event.target) && !avatar.contains(event.target)) {
                menu.style.display = 'none';
            }
        });
    </script>
</body>
</html>
