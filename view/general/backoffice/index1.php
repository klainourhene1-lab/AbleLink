<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Admin - Tableau de Bord</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            box-shadow: var(--glow);
            animation: pulse 2s infinite;
        }

        .logo-text {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.1), transparent);
            opacity: 0;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--glow);
        }

        .stat-card:hover::after {
            opacity: 1;
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

        .stat-info {
            flex: 1;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--text-light);
            font-size: 14px;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            margin-top: 5px;
        }

        .trend-up {
            color: var(--success);
        }

        .trend-down {
            color: var(--danger);
        }

        /* Charts Section */
        .charts-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--shadow);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: var(--transition);
            animation: fadeIn 0.8s ease-out;
        }

        .chart-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--glow);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-title {
            font-size: 18px;
            font-weight: 600;
        }

        .chart-container {
            height: 300px;
            position: relative;
        }

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

        .action-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: var(--glow);
        }

        .action-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin: 0 auto 15px;
        }

        .action-title {
            font-weight: 600;
            margin-bottom: 8px;
        }

        .action-desc {
            color: var(--text-light);
            font-size: 13px;
        }

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

        .section-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.03);
            transition: var(--transition);
        }

        .activity-item:hover {
            background: rgba(255, 255, 255, 0.07);
            transform: translateX(5px);
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: white;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 500;
            margin-bottom: 5px;
        }

        .activity-desc {
            color: var(--text-light);
            font-size: 13px;
        }

        .activity-time {
            color: var(--text-light);
            font-size: 12px;
        }

        /* Content Sections */
        .content-section {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--shadow);
            border: 1px solid rgba(255, 255, 255, 0.05);
            margin-bottom: 30px;
            animation: fadeIn 1s ease-out;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success);
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.2);
            color: var(--warning);
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.2);
            color: var(--danger);
        }

        .badge-info {
            background: rgba(14, 165, 233, 0.2);
            color: var(--secondary);
        }

        /* Input Styles */
        .input {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.2);
            background: #1e293b;
            color: white;
            font-family: inherit;
        }

        .input:focus {
            outline: none;
            border-color: #3498db;
        }

        /* Button Styles */
        .cta-button {
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            background: #1e293b;
            color: white;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .cta-button:hover {
            background: #374151;
            transform: translateY(-1px);
        }

        .cta-button.primary {
            background: #4f9cff;
        }

        .cta-button.primary:hover {
            background: #3b82f6;
        }

        .cta-button.success {
            background: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
        }

        .cta-button.success:hover {
            background: rgba(46, 204, 113, 0.3);
        }

        .cta-button.danger {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }

        .cta-button.danger:hover {
            background: rgba(231, 76, 60, 0.3);
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

        .floating-element:nth-child(1) {
            width: 300px;
            height: 300px;
            top: 10%;
            left: 5%;
            animation-duration: 25s;
        }

        .floating-element:nth-child(2) {
            width: 200px;
            height: 200px;
            top: 60%;
            right: 10%;
            animation-duration: 20s;
            animation-direction: reverse;
        }

        .floating-element:nth-child(3) {
            width: 150px;
            height: 150px;
            bottom: 10%;
            left: 20%;
            animation-duration: 15s;
        }

        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(20px, 20px) rotate(90deg); }
            50% { transform: translate(0, 40px) rotate(180deg); }
            75% { transform: translate(-20px, 20px) rotate(270deg); }
            100% { transform: translate(0, 0) rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .charts-section {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
            }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
            background: #111827;
            border-radius: 12px;
        }
        .profile-item {
    display:flex;
    align-items:center;
    gap:10px;
    color:#e2e8f0;
    padding:10px;
    border-radius:8px;
    text-decoration:none;
    transition:0.2s;
    font-size:14px;
}

.profile-item:hover {
    background:rgba(255,255,255,0.08);
}

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
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-cube"></i>
                </div>
                <div class="logo-text">AbleLink</div>
            </div>

            <div class="nav-links">
                <a href="#" class="nav-link active">
                    <i class="fas fa-home"></i>
                    <span>Tableau de Bord</span>
                </a>
                <script>
function toggleUserMenu() {
    const menu = document.getElementById("userSubMenu");
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
}



</script>

<div class="nav-link" onclick="toggleUserMenu()" style="cursor:pointer;">
    <i class="fas fa-users"></i>
    <span>Utilisateurs</span>
    <i class="fas fa-chevron-down" style="margin-left:auto; font-size:14px;"></i>
</div>

<div id="userSubMenu" style="display:none; margin-left:35px;">
    <a href="user_list.php" class="nav-link" style="padding-left:40px;">
    <i class="fas fa-list"></i> Liste des utilisateurs
</a>
<a href="add_user.php" class="nav-link" style="padding-left:40px;">
    <i class="fas fa-user-plus"></i> Ajouter un utilisateur
</a>
</div>


                <a href="#" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>Analytiques</span>
                </a>
                <a href="#" class="nav-link">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </a>
            </div>

            <div style="margin-top: auto; padding: 20px 0;">
                <div class="nav-link">
                    <i class="fas fa-question-circle"></i>
                    <span>Aide & Support</span>
                </div>
                <div class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Déconnexion</span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
          <!-- Header -->
<div class="header">
    <div class="header-title">
        <h1>Tableau de Bord Admin</h1>
        <p>Bienvenue dans votre espace d'administration</p>
    </div>

    <div class="user-menu" style="position: relative; display:flex; align-items:center; gap:10px;">

        <!-- Admin Image -->
       <img src="/webb/view/general/img/team/team-1.jpg"
     onclick="toggleProfileMenu()"
     style="
        width:45px;
        height:45px;
        border-radius:50%;
        cursor:pointer;
        object-fit:cover;
        border:2px solid var(--primary);
     ">


        <!-- Admin Name -->
        <span onclick="toggleProfileMenu()"
      style="cursor:pointer; font-weight:600; font-size:14px; color:white;">
    Administrateur
</span>

        <!-- Dropdown Menu -->
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

            <!-- User Info -->
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;">
                <img src="/webb/view/general/img/team/team-1.jpg"
                     style="width:45px; height:45px; border-radius:50%; object-fit:cover;">
                <div>
    <div style="font-weight:600;">Administrateur</div>
    <div style="font-size:12px; color:#94a3b8;">admin@ablelink.com</div>
</div>
</div>

<hr style="border-color:#334155; margin:10px 0;">

<a href="#" onclick="loadProfile()" class="profile-item">
    <i class="fas fa-user"></i> Voir le profil
</a>


<a href="#" class="profile-item"><i class="fas fa-cog"></i> Paramètres du compte</a>
<a href="#" class="profile-item"><i class="fas fa-bell"></i> Notifications</a>
<a href="#" class="profile-item"><i class="fas fa-random"></i> Changer de compte</a>
<a href="#" class="profile-item"><i class="fas fa-question-circle"></i> Centre d'aide</a>
<a href="logout.php" class="profile-item" style="color:#ef4444;">
    <i class="fas fa-sign-out-alt"></i> Déconnexion
</a>
</div>

    </div>
</div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <div class="action-card" onclick="switchSection('dashboard')">
                    <div class="action-icon" style="background: linear-gradient(135deg, var(--primary), var(--primary-light));">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="action-title">Tableau de Bord</div>
                    <div class="action-desc">Vue d'ensemble de votre application</div>
                </div>

                <div class="action-card" onclick="switchSection('users')">
                    <div class="action-icon" style="background: linear-gradient(135deg, var(--secondary), #38bdf8);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="action-title">Gestion Utilisateurs</div>
                    <div class="action-desc">Gérez les utilisateurs et permissions</div>
                </div>

                <div class="action-card" onclick="switchSection('analytics')">
                    <div class="action-icon" style="background: linear-gradient(135deg, var(--success), #34d399);">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="action-title">Analytiques</div>
                    <div class="action-desc">Statistiques et rapports détaillés</div>
                </div>

                <div class="action-card" onclick="switchSection('settings')">
                    <div class="action-icon" style="background: linear-gradient(135deg, var(--warning), #fbbf24);">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div class="action-title">Paramètres</div>
                    <div class="action-desc">Configurez votre application</div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary), var(--primary-light));">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value" id="totalUsers">0</div>
                        <div class="stat-label">Utilisateurs Totaux</div>
                        <div class="stat-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>12% ce mois</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, var(--secondary), #38bdf8);">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value" id="totalOrders">0</div>
                        <div class="stat-label">Commandes</div>
                        <div class="stat-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>8% ce mois</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, var(--success), #34d399);">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value" id="revenue">0€</div>
                        <div class="stat-label">Revenu Total</div>
                        <div class="stat-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>15% ce mois</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, var(--warning), #fbbf24);">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value" id="visitors">0</div>
                        <div class="stat-label">Visiteurs</div>
                        <div class="stat-trend trend-down">
                            <i class="fas fa-arrow-down"></i>
                            <span>3% ce mois</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-section">
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title">Performance Mensuelle</div>
                        <div class="chart-actions">
                            <span class="badge badge-info">2024</span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title">Répartition</div>
                        <div class="chart-actions">
                            <span class="badge badge-info">Total</span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="distributionChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="activity-section">
                <div class="section-title">
                    <i class="fas fa-history"></i>
                    <span>Activité Récente</span>
                </div>
                <div class="activity-list">
                    <div class="empty-state">
                        <i class="fas fa-database" style="font-size: 48px; margin-bottom: 15px;"></i>
                        <h3>Aucune donnée disponible</h3>
                        <p>Connectez votre base de données pour afficher les activités récentes</p>
                    </div>
                </div>
            </div>

            <!-- Content Sections -->
            <div class="content-section">
                <div class="section-title">
                    <i class="fas fa-table"></i>
                    <span>Données Récentes</span>
                </div>
                <div class="empty-state">
                    <i class="fas fa-table" style="font-size: 48px; margin-bottom: 15px;"></i>
                    <h3>Section de contenu</h3>
                    <p>Ajoutez vos tables, formulaires et autres éléments ici</p>
                </div>
            </div>
        </div>
    </div>

   <script>

// Initialize the dashboard
document.addEventListener("DOMContentLoaded", function () {
    setupCharts();
    loadSampleData();
});

// 📌 Load Fake Data (Just for demonstration)
function loadSampleData() {
    document.getElementById("totalUsers").innerText = 128;
    document.getElementById("totalOrders").innerText = 54;
    document.getElementById("revenue").innerText = "3,420€";
    document.getElementById("visitors").innerText = 980;
}

// 📌 Setup Charts
function setupCharts() {

    /* -------- PERFORMANCE CHART (LINE) ---------- */
    const performanceCtx = document.getElementById("performanceChart").getContext("2d");

    new Chart(performanceCtx, {
        type: "line",
        data: {
            labels: ["Jan", "Fév", "Mar", "Avr", "Mai", "Jun", "Jul", "Aoû", "Sep", "Oct", "Nov", "Déc"],
            datasets: [{
                label: "Performance",
                data: [20, 32, 25, 48, 50, 65, 70, 68, 80, 90, 110, 125],
                borderColor: "#7c3aed",
                backgroundColor: "rgba(124, 58, 237, 0.3)",
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointBackgroundColor: "#7c3aed"
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { ticks: { color: "#e2e8f0" }, grid: { color: "rgba(255,255,255,0.07)" }},
                x: { ticks: { color: "#e2e8f0" }, grid: { display: false }}
            }
        }
    });

    /* -------- DISTRIBUTION CHART (DOUGHNUT) ---------- */
    const distributionCtx = document.getElementById("distributionChart").getContext("2d");

    new Chart(distributionCtx, {
        type: "doughnut",
        data: {
            labels: ["Utilisateurs", "Commandes", "Visiteurs"],
            datasets: [{
                data: [128, 54, 980],
                backgroundColor: [
                    "rgba(124,58,237,0.7)",
                    "rgba(14,165,233,0.7)",
                    "rgba(245,158,11,0.7)"
                ],
                borderWidth: 2,
                borderColor: "#0f172a"
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { labels: { color: "#e2e8f0" } } }
        }
    });
}

function loadProfile() {
    fetch('profileadmin.php')
        .then(response => response.text())
        .then(html => {
            document.querySelector('.main-content').innerHTML = html;
        })
        .catch(err => {
            console.error("Error loading profile page:", err);
        });

    // غلق المنيو بعد الضغط
    document.getElementById("profileMenu").style.display = "none";
}


 function toggleProfileMenu() {
    const menu = document.getElementById("profileMenu");
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
}


// Close menu when clicking outside
document.addEventListener("click", function(e) {
    const menu = document.getElementById("profileMenu");
    const avatar = document.querySelector(".user-menu img");
    const name = document.querySelector(".user-menu span");

    if (!menu.contains(e.target) && 
        !avatar.contains(e.target) &&
        !name.contains(e.target)) 
    {
        menu.style.display = "none";
    }
});
/* -------- SWITCH SECTIONS ---------- */
function switchSection(section) {
    alert("Switching to: " + section);
    // Here you can hide/show your real sections later
}

</script>

</body>
</html>