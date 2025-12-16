<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AbleLink Admin - Tableau de Bord</title>
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
            justify-content: center;
            margin-bottom: 40px;
            padding: 0 10px;
            cursor: pointer;
            transition: var(--transition);
        }
        .logo:hover {
            transform: scale(1.05);
        }
        .logo-icon {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .site-title { margin: 0; padding: 0; }
        .site-title a {
            font-family: 'Josefin Sans', sans-serif;
            font-size: 28px;
            font-weight: 700;
            text-decoration: none;
            line-height: 1.2;
            display: inline-flex;
            gap: 2px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: 0.3s ease;
        }
        .letter-a { color: #ff4f5e; text-shadow: 0 0 8px rgba(255,79,94,0.6); }
        .letter-b { color: #4c8df5; text-shadow: 0 0 8px rgba(76,141,245,0.6); }
        .letter-l { color: #b87bff; text-shadow: 0 0 8px rgba(184,123,255,0.6); }
        .letter-e { color: #ffb247; text-shadow: 0 0 8px rgba(255,178,71,0.6); }
        .letter-link { color: #ffffff; margin-left: 4px; text-shadow: 0 0 10px rgba(255,255,255,0.7); }
        .site-title a:hover { transform: scale(1.05); filter: drop-shadow(0 0 12px rgba(0, 187, 255, 0.6)); }
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
            text-decoration: none;
            color: inherit;
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
            color: var(--text-light);
        }
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        /* Pending Stories */
        .pending-stories-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .pending-story-card {
            background: rgba(30, 41, 59, 0.6);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: var(--transition);
        }
        
        .pending-story-card:hover {
            border-color: rgba(245, 158, 11, 0.5);
            transform: translateX(5px);
        }
        
        .story-header-pending {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .story-header-pending h4 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 5px;
        }
        
        .story-author-pending {
            font-size: 13px;
            color: var(--text-light);
            margin: 5px 0;
        }
        
        .story-date-pending {
            font-size: 12px;
            color: var(--text-light);
        }
        
        .story-content-preview {
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .story-actions-pending {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn-approve {
            padding: 8px 16px;
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid var(--success);
            border-radius: 8px;
            color: var(--success);
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-approve:hover {
            background: var(--success);
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-reject {
            padding: 8px 16px;
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid var(--danger);
            border-radius: 8px;
            color: var(--danger);
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-reject:hover {
            background: var(--danger);
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-view {
            padding: 8px 16px;
            background: rgba(14, 165, 233, 0.2);
            border: 1px solid var(--secondary);
            border-radius: 8px;
            color: var(--secondary);
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-view:hover {
            background: var(--secondary);
            color: white;
            transform: translateY(-2px);
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
            <div class="logo" onclick="window.location.href='/projetweb/ablelink/admin'">
                <h1 class="site-title">
                    <a href="/projetweb/ablelink/admin">
                        <span class="letter-a">A</span>
                        <span class="letter-b">b</span>
                        <span class="letter-l">l</span>
                        <span class="letter-e">e</span>
                        <span class="letter-link">Link</span>
                    </a>
                </h1>
            </div>
            <div class="nav-links">
                <a href="/projetweb/ablelink/admin" class="nav-link active">
                    <i class="fas fa-home"></i>
                    <span>Tableau de Bord</span>
                </a>
                <a href="/projetweb/ablelink/admin/all-stories" class="nav-link">
                    <i class="fas fa-book"></i>
                    <span>Toutes les Stories</span>
                </a>
                <a href="/projetweb/ablelink/success-stories/create" class="nav-link">
                    <i class="fas fa-plus-circle"></i>
                    <span>Nouvelle Story</span>
                </a>
                <a href="/projetweb/ablelink/admin/comments?reported=all" class="nav-link">
                    <i class="fas fa-comments"></i>
                    <span>Commentaires</span>
                </a>
                <a href="/projetweb/ablelink/" class="nav-link">
                    <i class="fas fa-globe"></i>
                    <span>Voir le Site</span>
                </a>
            </div>
            <div style="margin-top: auto; padding: 20px 0;">
                <a href="/projetweb/ablelink/" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Retour au Site</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-title">
                    <h1>Tableau de Bord Admin</h1>
                    <p>Gestion des Success Stories et Commentaires</p>
                </div>
                <div class="user-menu">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="/projetweb/ablelink/admin/all-stories" class="action-card">
                    <div class="action-icon" style="background: linear-gradient(135deg, var(--primary), var(--primary-light));">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="action-title">Toutes les Stories</div>
                    <div class="action-desc">Gérer les témoignages</div>
                </a>
                <a href="/projetweb/ablelink/success-stories/create" class="action-card">
                    <div class="action-icon" style="background: linear-gradient(135deg, var(--secondary), #38bdf8);">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="action-title">Nouvelle Story</div>
                    <div class="action-desc">Créer un nouveau témoignage</div>
                </a>
                <a href="/projetweb/ablelink/admin/stats" class="action-card">
                    <div class="action-icon" style="background: linear-gradient(135deg, var(--success), #34d399);">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="action-title">Statistiques</div>
                    <div class="action-desc">Voir les statistiques</div>
                </a>
                <a href="/projetweb/ablelink/admin/all-stories?status=pending" class="action-card">
                    <div class="action-icon" style="background: linear-gradient(135deg, var(--warning), #f59e0b);">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="action-title">En Attente</div>
                    <div class="action-desc">Valider les nouvelles stories</div>
                </a>
                <a href="/projetweb/ablelink/" class="action-card">
                    <div class="action-icon" style="background: linear-gradient(135deg, var(--warning), #fbbf24);">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="action-title">Site Web</div>
                    <div class="action-desc">Retour au site public</div>
                </a>
                <?php 
                    $start = date('Y-m-d', strtotime('-30 days'));
                    $href = '/projetweb/ablelink/admin/export-stories?status=approved&sort=recent&start=' . urlencode($start) . '&end=' . urlencode(date('Y-m-d'));
                ?>
                <a href="<?php echo $href; ?>" class="action-card">
                    <div class="action-icon" style="background: linear-gradient(135deg, var(--success), #10b981);">
                        <i class="fas fa-file-csv"></i>
                    </div>
                    <div class="action-title">Exporter CSV</div>
                    <div class="action-desc">30 derniers jours, approuvées</div>
                </a>
            </div>

            <!-- Stats Grid -->
        <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary), var(--primary-light));">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value" id="totalStories"><?php echo isset($stats['stories']) ? number_format($stats['stories']) : '0'; ?></div>
                        <div class="stat-label">Success Stories</div>
                        <div class="stat-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>Actives</span>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, var(--secondary), #38bdf8);">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value" id="totalComments"><?php echo isset($stats['comments']) ? number_format($stats['comments']) : '0'; ?></div>
                        <div class="stat-label">Commentaires</div>
                        <div class="stat-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>Total</span>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, var(--success), #34d399);">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value" id="totalLikes"><?php echo isset($stats['likes']) ? number_format($stats['likes']) : '0'; ?></div>
                        <div class="stat-label">Likes Totaux</div>
                        <div class="stat-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>Engagement</span>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, var(--warning), #fbbf24);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value" id="pendingStories"><?php echo isset($stats['pending']) ? number_format($stats['pending']) : '0'; ?></div>
                        <div class="stat-label">En Attente</div>
                        <div class="stat-trend trend-up">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>À modérer</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stories en Attente de Modération -->
            <?php if (isset($pendingStories) && count($pendingStories) > 0): ?>
            <div class="content-section">
                <div class="section-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Stories en Attente de Modération (<?php echo count($pendingStories); ?>)</span>
                    <a href="/projetweb/ablelink/admin/all-stories" class="btn-create-story" style="margin-left: auto; padding: 8px 20px; font-size: 13px;">
                        <i class="fas fa-list"></i> Voir Toutes les Stories
                    </a>
                </div>
                <div class="pending-stories-list">
                    <?php foreach ($pendingStories as $story): ?>
                    <div class="pending-story-card">
                        <div class="story-header-pending">
                            <div>
                                <h4><?php echo htmlspecialchars($story['title'] ?? ''); ?></h4>
                                <p class="story-author-pending">Par: <?php echo htmlspecialchars($story['author'] ?? ''); ?></p>
                                <p class="story-date-pending"><?php echo date('d/m/Y H:i', strtotime($story['created_at'] ?? 'now')); ?></p>
                            </div>
                            <span class="badge badge-warning">En Attente</span>
                        </div>
                        <p class="story-content-preview"><?php $txt = $story['content'] ?? ($story['description'] ?? ''); echo htmlspecialchars(mb_substr($txt, 0, 150)) . '...'; ?></p>
                        <div class="story-actions-pending">
                            <a href="/projetweb/ablelink/admin/approve?id=<?php echo (int)$story['id']; ?>" class="btn-approve">
                                <i class="fas fa-check"></i> Approuver
                            </a>
                            <a href="/projetweb/ablelink/admin/reject?id=<?php echo (int)$story['id']; ?>" class="btn-reject">
                                <i class="fas fa-times"></i> Rejeter
                            </a>
                            <a href="/projetweb/ablelink/success-stories/comments?id=<?php echo (int)$story['id']; ?>" class="btn-view">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="content-section">
                <div class="section-title">
                    <i class="fas fa-check-circle"></i>
                    <span>Toutes les Stories</span>
                    <a href="/projetweb/ablelink/admin/all-stories" class="btn-create-story" style="margin-left: auto; padding: 8px 20px; font-size: 13px;">
                        <i class="fas fa-list"></i> Voir Toutes les Stories
                    </a>
                </div>
                <div class="empty-state">
                    <i class="fas fa-check-circle" style="font-size: 48px; margin-bottom: 15px; color: var(--success);"></i>
                    <h3>Aucune story en attente</h3>
                    <p>Toutes les stories ont été modérées</p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Charts Section -->
            <div class="charts-section">
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title">Activité Mensuelle</div>
                        <div class="chart-actions">
                            <span class="badge badge-info"><?php echo date('Y'); ?></span>
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
                    <div class="activity-item">
                        <div class="activity-icon" style="background: linear-gradient(135deg, var(--primary), var(--primary-light));">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Nouvelles Success Stories</div>
                            <div class="activity-desc">Gérez et modérez les témoignages partagés</div>
                        </div>
                        <div class="activity-time">Maintenant</div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon" style="background: linear-gradient(135deg, var(--secondary), #38bdf8);">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Commentaires en attente</div>
                            <div class="activity-desc">Modérez les commentaires des utilisateurs</div>
                        </div>
                        <div class="activity-time">Récent</div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon" style="background: linear-gradient(135deg, var(--success), #34d399);">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Engagement communautaire</div>
                            <div class="activity-desc">Suivez les likes et interactions</div>
                        </div>
                        <div class="activity-time">Aujourd'hui</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<script>
        // Initialize the dashboard
        document.addEventListener('DOMContentLoaded', function() {
            // Setup charts
            setupCharts();
        });

        // Setup charts with sample data
        function setupCharts() {
            // Performance Chart
            const performanceCtx = document.getElementById('performanceChart');
            if (performanceCtx) {
                new Chart(performanceCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                        datasets: [{
                            label: 'Stories',
                            data: [<?php echo isset($stats['stories']) ? $stats['stories'] : 0; ?>, 8, 12, 15, 18, 20, 22, 25, 28, 30, 32, <?php echo isset($stats['stories']) ? $stats['stories'] : 0; ?>],
                            borderColor: '#7c3aed',
                            backgroundColor: 'rgba(124, 58, 237, 0.1)',
                            tension: 0.4,
                            fill: true
                        }, {
                            label: 'Commentaires',
                            data: [<?php echo isset($stats['comments']) ? round($stats['comments']/12) : 0; ?>, 5, 8, 10, 12, 15, 18, 20, 22, 25, 28, <?php echo isset($stats['comments']) ? round($stats['comments']/12) : 0; ?>],
                            borderColor: '#0ea5e9',
                            backgroundColor: 'rgba(14, 165, 233, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: { color: 'white' }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { color: 'white' },
                                grid: { color: 'rgba(255,255,255,0.1)' }
                            },
                            x: {
                                ticks: { color: 'white' },
                                grid: { color: 'rgba(255,255,255,0.1)' }
                            }
                        }
                    }
                });
            }

            // Distribution Chart
            const distributionCtx = document.getElementById('distributionChart');
            if (distributionCtx) {
                const stories = <?php echo isset($stats['stories']) ? $stats['stories'] : 0; ?>;
                const comments = <?php echo isset($stats['comments']) ? $stats['comments'] : 0; ?>;
                const likes = <?php echo isset($stats['likes']) ? $stats['likes'] : 0; ?>;
                
                new Chart(distributionCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Stories', 'Commentaires', 'Likes'],
                        datasets: [{
                            data: [stories || 1, comments || 1, likes || 1],
                            backgroundColor: [
                                'rgba(124, 58, 237, 0.8)',
                                'rgba(14, 165, 233, 0.8)',
                                'rgba(16, 185, 129, 0.8)'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: 'white' }
                            }
                        }
                    }
                });
            }
        }
</script>
</body>
</html>
