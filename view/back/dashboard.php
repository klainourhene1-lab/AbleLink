<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - AbleLink</title>
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
            background-clip: text;
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
            background-clip: text;
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

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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
            display: block;
        }

        .action-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: var(--glow);
        }

        .action-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
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
            color: var(--text);
        }

        .action-desc {
            color: var(--text-light);
            font-size: 13px;
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
            border-radius: 50%;
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

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-info {
            background: rgba(14, 165, 233, 0.2);
            color: var(--secondary);
        }

        /* Activity Section */
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

        .activity-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.03);
            transition: var(--transition);
            margin-bottom: 10px;
        }

        .activity-item:hover {
            background: rgba(255, 255, 255, 0.07);
            transform: translateX(5px);
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
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
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="logo-text">AbleLink</div>
            </div>

            <div class="nav-links">
                <a href="/projetttwebbbbbbbbb/index.php?controller=dashboard&action=index" class="nav-link active">
                    <i class="fas fa-home"></i>
                    <span>Tableau de Bord</span>
                </a>
                <a href="/projetttwebbbbbbbbb/index.php?controller=offre&action=gestion" class="nav-link">
                    <i class="fas fa-briefcase"></i>
                    <span>Gestion Offres</span>
                </a>
                <a href="/projetttwebbbbbbbbb/index.php?controller=candidature&action=gestion" class="nav-link">
                    <i class="fas fa-file-alt"></i>
                    <span>Candidatures</span>
                </a>
                <a href="#" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Utilisateurs</span>
                </a>
                <a href="/projetttwebbbbbbbbb/index.php?controller=offre&action=liste" class="nav-link">
                    <i class="fas fa-globe"></i>
                    <span>Front Office</span>
                </a>
            </div>

            <div style="margin-top: auto; padding: 20px 0;">
                <div class="nav-link">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </div>
                <a href="/projetttwebbbbbbbbb/index.php?controller=auth&action=logout" class="nav-link">
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
                    <h1>Tableau de Bord Admin</h1>
                    <p>Gestion des offres d'emploi et des candidatures</p>
                </div>
                <div class="user-menu">
                    <div class="user-avatar">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="/projetttwebbbbbbbbb/index.php?controller=offre&action=gestion" class="action-card">
                    <div class="action-icon" style="background: linear-gradient(135deg, #7c3aed, #8b5cf6);">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="action-title">Toutes les Offres</div>
                    <div class="action-desc">Gérer toutes les publications</div>
                </a>

                <a href="/projetttwebbbbbbbbb/index.php?controller=offre&action=add" class="action-card">
                    <div class="action-icon" style="background: linear-gradient(135deg, #0ea5e9, #38bdf8);">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="action-title">Nouvelle Offre</div>
                    <div class="action-desc">Créer une nouvelle offre d'emploi</div>
                </a>

                <a href="#charts-section" class="action-card">
                    <div class="action-icon" style="background: linear-gradient(135deg, #10b981, #34d399);">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="action-title">Statistiques</div>
                    <div class="action-desc">Voir les statistiques</div>
                </a>

                <a href="/projetttwebbbbbbbbb/index.php?controller=offre&action=liste" class="action-card">
                    <div class="action-icon" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="action-title">En Attente</div>
                    <div class="action-desc">Voir les nouvelles stories</div>
                </a>

                <a href="#" class="action-card">
                    <div class="action-icon" style="background: linear-gradient(135deg, #6366f1, #818cf8);">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="action-title">Site Web</div>
                    <div class="action-desc">Retour sur site public</div>
                </a>

                <a href="#" class="action-card">
                    <div class="action-icon" style="background: linear-gradient(135deg, #10b981, #34d399);">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <div class="action-title">Exporter CSV</div>
                    <div class="action-desc">30 derniers jours répertoriés</div>
                </a>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #7c3aed, #8b5cf6);">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?= $stats['totalOffres'] ?? 0 ?></div>
                        <div class="stat-label">Offres d'emploi</div>
                        <div class="stat-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>Actives</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #0ea5e9, #38bdf8);">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?= $stats['totalCandidatures'] ?? 0 ?></div>
                        <div class="stat-label">Candidatures</div>
                        <div class="stat-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>Reçues</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #34d399);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?= $stats['totalUsers'] ?? 0 ?></div>
                        <div class="stat-label">Utilisateurs</div>
                        <div class="stat-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>Inscrits</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?= number_format($stats['totalVisiteurs'] ?? 0) ?></div>
                        <div class="stat-label">Visiteurs</div>
                        <div class="stat-trend trend-down">
                            <i class="fas fa-arrow-down"></i>
                            <span>Ce mois</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-section" id="charts-section">
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
                
                <div class="activity-item">
                    <div class="activity-icon" style="background: linear-gradient(135deg, #10b981, #34d399);">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Nouvelle offre ajoutée</div>
                        <div class="activity-desc">Une nouvelle offre d'emploi a été créée</div>
                    </div>
                    <div class="activity-time">Il y a 2 heures</div>
                </div>
                
                <div class="activity-item">
                    <div class="activity-icon" style="background: linear-gradient(135deg, #0ea5e9, #38bdf8);">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Nouvel utilisateur inscrit</div>
                        <div class="activity-desc">Un nouveau compte a été créé</div>
                    </div>
                    <div class="activity-time">Il y a 5 heures</div>
                </div>

                <div class="activity-item">
                    <div class="activity-icon" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);">
                        <i class="fas fa-file"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Nouvelle candidature</div>
                        <div class="activity-desc">Une candidature a été soumise</div>
                    </div>
                    <div class="activity-time">Il y a 1 jour</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Smooth scroll pour les liens d'ancrage
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Setup charts
        document.addEventListener('DOMContentLoaded', function() {
            // Performance Chart
            const performanceCtx = document.getElementById('performanceChart');
            if (performanceCtx) {
                new Chart(performanceCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
                        datasets: [{
                            label: 'Offres',
                            data: [12, 19, 15, 25, 22, 30],
                            borderColor: '#7c3aed',
                            backgroundColor: 'rgba(124, 58, 237, 0.1)',
                            tension: 0.4,
                            fill: true
                        }, {
                            label: 'Candidatures',
                            data: [8, 15, 12, 20, 18, 25],
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
                new Chart(distributionCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['En cours', 'Acceptées', 'Refusées'],
                        datasets: [{
                            data: [<?= $stats['totalCandidatures'] ?? 15 ?>, 8, 5],
                            backgroundColor: [
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(239, 68, 68, 0.8)'
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
        });
    </script>
</body>
</html>
