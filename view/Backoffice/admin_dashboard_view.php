<?php
// admin_dashboard_view.php - General Overview
// Load admin model to fetch users for the dashboard quick-select
require_once __DIR__ . '/../../Model/AdminModel.php';
$adminModel = new AdminModel();
$users = [];
try {
    $users = $adminModel->getAllUsers();
} catch (Exception $e) {
    $users = [];
}
?>
<?php
$currentPage = 'dashboard';
$currentSubPage = '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AbleLink Admin - Tableau de Bord</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@700&display=swap" rel="stylesheet">
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

        .logo-title a {
            font-family: 'Josefin Sans', sans-serif;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 3px;
        }
        .letter-a { color: #FF4F5E; text-shadow: 0 0 6px rgba(255,79,94,0.7); }
        .letter-b { color: #4C8DF5; text-shadow: 0 0 6px rgba(76,141,245,0.7); }
        .letter-l { color: #B87BFF; text-shadow: 0 0 6px rgba(184,123,255,0.7); }
        .letter-e { color: #FFB247; text-shadow: 0 0 6px rgba(255,178,71,0.7); }
        .letter-link { color: #FFFFFF; margin-left: 5px; text-shadow: 0 0 10px rgba(255,255,255,0.8); }

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
        .nav-link:hover::before { left: 100%; }

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

        .header-title p { color: var(--text-light); font-size: 14px; }

        .user-menu { display: flex; align-items: center; gap: 15px; }

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

        .user-avatar:hover { transform: scale(1.05); box-shadow: var(--glow); }

        /* Floating Elements */
        .floating-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }
        .floating-element {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), transparent);
            opacity: 0.1;
            animation: float 20s infinite linear;
        }
        .floating-element:nth-child(1) { width: 300px; height: 300px; top: 10%; left: 5%; animation-duration: 25s; }
        .floating-element:nth-child(2) { width: 200px; height: 200px; top: 60%; right: 10%; animation-duration: 20s; animation-direction: reverse; }
        .floating-element:nth-child(3) { width: 150px; height: 150px; bottom: 10%; left: 20%; animation-duration: 15s; }

        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(0, 40px) rotate(180deg); }
            100% { transform: translate(0, 0) rotate(360deg); }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: var(--shadow);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: var(--transition);
            animation: fadeIn 0.6s ease-out;
            position: relative;
            overflow: hidden;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        .stat-value { font-size: 28px; font-weight: 700; margin-bottom: 5px; }
        .stat-label { color: var(--text-light); font-size: 14px; }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .action-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 25px;
            text-align: center;
            box-shadow: var(--shadow);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: var(--transition);
            cursor: pointer;
            animation: fadeIn 1.2s ease-out;
        }
        .action-card:hover { transform: translateY(-5px) scale(1.02); box-shadow: var(--glow); }
        .action-icon {
            width: 60px; height: 60px;
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; color: white;
            margin: 0 auto 15px;
        }
        .action-title { font-weight: 600; margin-bottom: 8px; }
        .action-desc { color: var(--text-light); font-size: 13px; }

        /* Recent Activity */
        .activity-section {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--shadow);
            border: 1px solid rgba(255, 255, 255, 0.05);
            margin-bottom: 30px;
            animation: fadeIn 1s ease-out;
        }
        .section-title { font-size: 20px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .activity-list { display: flex; flex-direction: column; gap: 15px; }
        .activity-item {
            display: flex; align-items: center; gap: 15px;
            padding: 15px; border-radius: 12px;
            background: rgba(255, 255, 255, 0.03);
            transition: var(--transition);
        }
        .activity-item:hover { background: rgba(255, 255, 255, 0.07); transform: translateX(5px); }
        .activity-icon {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: white;
        }
        .activity-content { flex: 1; }
        .activity-title { font-weight: 500; margin-bottom: 5px; }
        .activity-desc { color: var(--text-light); font-size: 13px; }
        .activity-time { color: var(--text-light); font-size: 12px; }

         /* Profile Menu Styles */
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
        .profile-item:hover { background: rgba(255,255,255,0.08); }
    </style>
</head>
<body>
    <div class="floating-elements">
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
    </div>

    <div class="app-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo logo-title">
                <a href="admin_dashboard.php" style="text-decoration:none;">
                    <span class="letter-a">A</span>
                    <span class="letter-b">B</span>
                    <span class="letter-l">L</span>
                    <span class="letter-e">E</span>
                    <span class="letter-link">LINK</span>
                </a>
            </div>

            <div class="nav-links">
                <a href="admin_dashboard.php" class="nav-link active">
                    <i class="fas fa-home"></i>
                    <span>Tableau de Bord</span>
                </a>
                <a href="../view/general/index.php" class="nav-link">
                    <i class="fas fa-globe"></i>
                    <span>Accueil</span>
                </a>
                <a href="admin_events.php" class="nav-link">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Événements</span>
                </a>
                <a href="admin_stories.php" class="nav-link">
                    <i class="fas fa-book-open"></i>
                    <span>Success Stories</span>
                </a>
                <a href="admin_jobs.php" class="nav-link">
                    <i class="fas fa-briefcase"></i>
                    <span>Offres & Candidatures</span>
                </a>
                <a href="#" class="nav-link" onclick="showSection('users')">
                    <i class="fas fa-users"></i>
                    <span>Utilisateurs</span>
                </a>
                <div id="usersSubmenu" style="display: none; padding-left: 20px; margin-bottom: 10px;">
                    <a href="../view/general/backoffice/user_list.php" class="nav-link" style="font-size: 14px; padding: 8px 16px;">
                        <i class="fas fa-list-ul" style="font-size: 14px;"></i>
                        <span>Liste des utilisateurs</span>
                    </a>
                    <a href="../view/general/backoffice/add_user.php" class="nav-link" style="font-size: 14px; padding: 8px 16px;">
                        <i class="fas fa-user-plus" style="font-size: 14px;"></i>
                        <span>Ajouter utilisateur</span>
                    </a>
                </div>
                <a href="#moderation" class="nav-link">
                    <i class="fas fa-shield-alt"></i>
                    <span>Modération</span>
                </a>
            </div>

            <div style="margin-top: auto; padding: 20px 0;">
                <div class="nav-link">
                    <i class="fas fa-question-circle"></i>
                    <span>Aide & Support</span>
                </div>
                <a href="../view/general/logout.php" class="nav-link" style="text-decoration: none;">
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
                    <h1>Tableau de Bord</h1>
                    <p>Bienvenue dans votre espace d'administration</p>
                </div>
                
                 <div class="user-menu" style="position: relative; display:flex; align-items:center; gap:10px;">
                    <div class="user-avatar" onclick="toggleProfileMenu()">
                        <i class="fas fa-user"></i>
                    </div>
                    
                    <div id="profileMenu" style="display:none; position:absolute; top:60px; right:0; width:230px; background:#1e293b; border-radius:12px; padding:15px; box-shadow:0 10px 25px rgba(0,0,0,0.4); z-index:999;">
                         <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;">
                            <div style="width:40px; height:40px; border-radius:50%; background: linear-gradient(135deg, var(--primary), var(--secondary)); display:flex; justify-content:center; align-items:center; color:white; font-size:16px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <div style="font-weight:600;">Administrateur</div>
                            </div>
                        </div>
                        <hr style="border-color:#334155; margin:10px 0;">
                        <a href="../view/Backoffice/profileadmin.php" class="profile-item"><i class="fas fa-user"></i> Voir le profil</a>
                        <a href="../view/general/logout.php" class="profile-item" style="color:#ef4444;"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #7c3aed, #8b5cf6);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <!-- Placeholder for total users if not available in stats, assuming stats holds it or we skip -->
                        <div class="stat-value"><?php echo $stats['activeUsers'] ?? 0; ?></div>
                        <div class="stat-label">Utilisateurs Actifs</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #0ea5e9, #38bdf8);">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $stats['totalEvents'] ?? 0; ?></div>
                        <div class="stat-label">Événements Total</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #34d399);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $stats['totalEnterprises'] ?? 0; ?></div>
                        <div class="stat-label">Entreprises</div>
                    </div>
                </div>
                 <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php // Hard to get correct story count without controller change, but we have access to AdminController data if passed
                            // We might need to ensure admin_dashboard.php controller passes storyStats 
                             echo isset($storyStats['total_stories']) ? $storyStats['total_stories'] : '-'; 
                        ?></div>
                        <div class="stat-label">Success Stories</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <h2 class="section-title">✨ Actions Rapides</h2>
            <div class="quick-actions">
                <div class="action-card" onclick="window.location.href='admin_events.php'">
                    <div class="action-icon" style="background: linear-gradient(135deg, #3b82f6, #60a5fa);">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <div class="action-title">Gérer Événements</div>
                    <div class="action-desc">Modérer et valider</div>
                </div>
                
                <div class="action-card" onclick="window.location.href='admin_stories.php'">
                    <div class="action-icon" style="background: linear-gradient(135deg, #ec4899, #f472b6);">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <div class="action-title">Gérer Stories</div>
                    <div class="action-desc">Modérer les témoignages</div>
                </div>

                <div class="action-card" onclick="alert('Module Utilisateurs en maintenance')">
                    <div class="action-icon" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa);">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="action-title">Gérer Utilisateurs</div>
                    <div class="action-desc">Ajouter ou modifier</div>
                </div>

                <div class="action-card" onclick="alert('Exportation des rapports...')">
                    <div class="action-icon" style="background: linear-gradient(135deg, #10b981, #34d399);">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <div class="action-title">Rapports</div>
                    <div class="action-desc">Télécharger les statistiques</div>
                </div>
            </div>

            <!-- Users Quick-Select Section (hidden by default) -->
            <div id="usersSection" class="activity-section" style="display:none;">
                <div class="section-title"><i class="fas fa-users"></i> Utilisateurs</div>
                <div style="margin-bottom:15px; display:flex; gap:10px; align-items:center;">
                    <label for="usersSelect" style="min-width:140px; color:var(--text-light);">Sélection rapide :</label>
                    <select id="usersSelect" style="flex:1; padding:10px; border-radius:8px; background:rgba(255,255,255,0.03); color:var(--text); border:1px solid rgba(255,255,255,0.04);">
                        <option value="">-- Choisir un utilisateur --</option>
                        <option value="add">➕ Ajouter un utilisateur</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?php echo htmlspecialchars($u['id']); ?>"><?php echo htmlspecialchars($u['prenom'] . ' ' . $u['nom'] . ' (' . $u['email'] . ')'); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="action-card" style="padding:10px 14px; min-width:140px; cursor:pointer;" onclick="openUserList()">
                        Voir la liste complète
                    </button>
                </div>
                <div id="userQuickInfo" style="color:var(--text-light);"></div>
            </div>

             <!-- Recent Activity -->
            <div class="activity-section">
                <div class="section-title">
                    <i class="fas fa-history text-primary"></i> Activité Récente
                </div>
                <div class="activity-list">
                    <?php if (isset($recentActivities) && !empty($recentActivities)): ?>
                        <?php foreach ($recentActivities as $activity): ?>
                            <div class="activity-item">
                                <div class="activity-icon" style="background: <?php echo $activity['color'] ?? 'rgba(124, 58, 237, 0.2)'; ?>; color: <?php echo $activity['iconColor'] ?? '#7c3aed'; ?>;">
                                    <i class="<?php echo $activity['icon'] ?? 'fas fa-bell'; ?>"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title"><?php echo htmlspecialchars($activity['title']); ?></div>
                                    <div class="activity-desc"><?php echo htmlspecialchars($activity['description']); ?></div>
                                    <div class="activity-time"><?php echo htmlspecialchars($activity['time']); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align:center; color:#94a3b8; padding:20px;">Aucune activité récente</div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <script>
        function showSection(name) {
            // Hide users submenu in sidebar
            const usersSub = document.getElementById('usersSubmenu');
            if (usersSub) usersSub.style.display = (usersSub.style.display === 'block') ? 'none' : 'block';

            // Toggle main users section
            const usersSection = document.getElementById('usersSection');
            if (!usersSection) return;
            usersSection.style.display = (usersSection.style.display === 'block') ? 'none' : 'block';
            // Scroll into view
            if (usersSection.style.display === 'block') {
                usersSection.scrollIntoView({behavior: 'smooth'});
            }
        }

        function openUserList() {
            window.location.href = '../view/general/backoffice/user_list.php';
        }

        document.getElementById('usersSelect')?.addEventListener('change', function(e) {
            const val = e.target.value;
            if (!val) return;
            if (val === 'add') {
                window.location.href = '../view/general/backoffice/add_user.php';
                return;
            }
            // Open user detail or list filtered by user id
            window.location.href = '../view/general/backoffice/user_list.php?user_id=' + encodeURIComponent(val);
        });

        function toggleProfileMenu() {
            const menu = document.getElementById("profileMenu");
            menu.style.display = (menu.style.display === "block") ? "none" : "block";
        }
        document.addEventListener("click", function(e) {
            const menu = document.getElementById("profileMenu");
            const avatar = document.querySelector(".user-avatar");
            if (menu && avatar && !menu.contains(e.target) && !avatar.contains(e.target)) {
                menu.style.display = "none";
            }
        });
    </script>
</body>
</html>