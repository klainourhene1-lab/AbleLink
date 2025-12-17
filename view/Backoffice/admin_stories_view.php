<?php
// admin_stories_view.php - View for Success Stories Moderation
// Data comes from admin_stories.php controller: $storyStats, $pendingStories
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AbleLink Admin - Success Stories</title>
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

        /* Specific Styles for Stories */
        .chart-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--shadow);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: var(--transition);
            animation: fadeIn 0.8s ease-out;
            margin-bottom: 25px;
        }
        
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

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--text-light);
            font-size: 14px;
        }

        /* Admin/Moderation List */
        .admin-list {
            display: grid;
            gap: 15px;
        }

        .admin-item {
            padding: 20px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: var(--transition);
        }
        
        .admin-item:hover {
            transform: translateX(5px);
            background: rgba(255, 255, 255, 0.05);
        }

        .item-content {
            flex: 1;
        }
        
        .cta-button {
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            background: #1e293b;
            color: white;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .cta-button:hover {
            background: #374151;
            transform: translateY(-1px);
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

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge.pending { background: rgba(241, 196, 15, 0.2); color: #f1c40f; }
        
        .search-filter {
            display: flex;
            gap: 15px;
            margin: 20px 0;
            flex-wrap: wrap;
        }

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
        .letter-a { color: #FF4F5E; text-shadow: 0 0 6px rgba(255,79,94,0.7); }
        .letter-b { color: #4C8DF5; text-shadow: 0 0 6px rgba(76,141,245,0.7); }
        .letter-l { color: #B87BFF; text-shadow: 0 0 6px rgba(184,123,255,0.7); }
        .letter-e { color: #FFB247; text-shadow: 0 0 6px rgba(255,178,71,0.7); }
        .letter-link { color: #FFFFFF; margin-left: 5px; text-shadow: 0 0 10px rgba(255,255,255,0.8); }
        .site-title:hover a { transform: scale(1.05); }
        
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
            <div class="logo site-title">
                <a href="admin_dashboard.php" style="text-decoration:none;">
                    <span class="letter-a">A</span>
                    <span class="letter-b">B</span>
                    <span class="letter-l">L</span>
                    <span class="letter-e">E</span>
                    <span class="letter-link">LINK</span>
                </a>
            </div>

            <div class="nav-links">
                <a href="admin_dashboard.php" class="nav-link">
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
                <a href="admin_stories.php" class="nav-link active">
                    <i class="fas fa-book-open"></i>
                    <span>Success Stories</span>
                </a>
                <a href="#" class="nav-link" onclick="window.location.href='admin_dashboard.php#users'">
                    <i class="fas fa-users"></i>
                    <span>Utilisateurs</span>
                </a>
                <a href="#" class="nav-link" onclick="window.location.href='admin_dashboard.php#moderation'">
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
                    <h1>Modération des Success Stories</h1>
                    <p>Gérez et modérez les témoignages des utilisateurs</p>
                </div>
                
                <!-- Same User Menu as Dashboard -->
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

            <!-- Stats Grid for Stories -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $storyStats['total_stories'] ?? 0; ?></div>
                        <div class="stat-label">Total Stories</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #f87171);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $storyStats['pending_stories'] ?? 0; ?></div>
                        <div class="stat-label">En Attente</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #60a5fa);">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $storyStats['total_comments'] ?? 0; ?></div>
                        <div class="stat-label">Commentaires</div>
                    </div>
                </div>
            </div>

            <div class="chart-card">
                <div class="search-filter">
                    <input type="text" id="searchStories" class="input" placeholder="🔍 Rechercher des success stories..." style="flex: 1;">
                </div>

                <div id="storiesModerationList" class="admin-list">
                    <?php if (empty($pendingStories)): ?>
                        <div style="text-align: center; padding: 40px; color: #94a3b8;">
                            <i class="fas fa-check-circle" style="font-size: 48px; color: #10b981; margin-bottom: 20px;"></i>
                            <h3>Tout est à jour !</h3>
                            <p>Aucune story en attente de modération pour le moment.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($pendingStories as $story): ?>
                            <div class="admin-item">
                                <div class="item-content">
                                    <h4 style="margin: 0 0 8px 0; color: white; font-size: 18px;"><?php echo htmlspecialchars($story['title']); ?></h4>
                                    <p style="margin: 0 0 8px 0; color: #ccc;">
                                        <i class="fas fa-user-circle"></i> 
                                        <?php echo htmlspecialchars(($story['prenom'] ?? '') . ' ' . ($story['nom'] ?? '')); ?>
                                    </p>
                                    <div style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; margin-bottom: 12px; font-style: italic; color: #e2e8f0;">
                                        "<?php echo htmlspecialchars(truncateText($story['content'], 250)); ?>"
                                    </div>
                                    <div style="display: flex; gap: 15px; align-items: center;">
                                        <span class="badge pending">⏳ En attente</span>
                                        <span style="color: #64748b; font-size: 12px;">
                                            <i class="far fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($story['created_at'])); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <button class="cta-button success" onclick="approveStory(<?php echo $story['id']; ?>)">
                                        <i class="fas fa-check"></i> Approuver
                                    </button>
                                    <button class="cta-button danger" onclick="rejectStory(<?php echo $story['id']; ?>)">
                                        <i class="fas fa-times"></i> Rejeter
                                    </button>
                                    <button class="cta-button danger" onclick="deleteStory(<?php echo $story['id']; ?>)" title="Supprimer définitivement">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-section" style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title">📈 Stories par Mois</div>
                    </div>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="storiesMonthlyChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title">🥧 Répartition par Statut</div>
                    </div>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="storiesStatusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Contributors -->
            <div class="chart-card" style="margin-bottom: 30px;">
                <h3 style="color: white; margin-bottom: 20px;">🏆 Top Contributeurs</h3>
                
                <div class="admin-list">
                    <?php if (!empty($storyAnalytics['topContributors'])): ?>
                        <?php foreach ($storyAnalytics['topContributors'] as $contributor): ?>
                            <div class="admin-item" style="padding: 15px;">
                                <div class="item-content" style="display: flex; align-items: center; gap: 15px;">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; color: white;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <h4 style="margin: 0; color: white;"><?php echo htmlspecialchars($contributor['prenom'] . ' ' . $contributor['nom']); ?></h4>
                                        <p style="margin: 2px 0 0; color: #94a3b8; font-size: 13px;">
                                            <i class="fas fa-pen-nib"></i> <?php echo $contributor['story_count']; ?> stories publiées
                                        </p>
                                    </div>
                                </div>
                                <div class="badge badge-success">
                                    <i class="fas fa-heart"></i> <?php echo $contributor['total_likes']; ?> likes
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; color: #94a3b8; padding: 20px;">
                            Aucun contributeur actif pour le moment.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleProfileMenu() {
            const menu = document.getElementById("profileMenu");
            menu.style.display = (menu.style.display === "block") ? "none" : "block";
        }

        // Close menu when clicking outside
        document.addEventListener("click", function(e) {
            const menu = document.getElementById("profileMenu");
            const avatar = document.querySelector(".user-avatar");
            if (menu && avatar && !menu.contains(e.target) && !avatar.contains(e.target)) {
                menu.style.display = "none";
            }
        });

        function performAction(action, data, successMessage) {
            fetch('admin_stories.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: action,
                    ...data
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert(successMessage);
                    location.reload();
                } else {
                    alert('Erreur: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de l\'opération');
            });
        }

        function approveStory(id) {
            if(confirm('Approuver cette story ? Elle sera visible publiquement.')) {
                performAction('approve_story', { storyId: id }, 'Story approuvée !');
            }
        }
        function rejectStory(id) {
            if(confirm('Rejeter cette story ? Elle ne sera pas publiée.')) {
                performAction('reject_story', { storyId: id }, 'Story rejetée.');
            }
        }
        function deleteStory(id) {
            if(confirm('ATTENTION: Supprimer définitivement cette story ? cette action est irréversible.')) {
                performAction('delete_story', { storyId: id }, 'Story supprimée.');
            }
        }

        // Search functionality
        document.getElementById('searchStories').addEventListener('input', function() {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#storiesModerationList .admin-item').forEach(item => {
                item.style.display = item.innerText.toLowerCase().includes(term) ? 'flex' : 'none';
            });
        });

        // Initialize Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Data from PHP
            const monthlyData = <?php echo json_encode($storyAnalytics['monthly'] ?? array_fill(0, 12, 0)); ?>;
            const statusData = <?php echo json_encode($storyAnalytics['distribution'] ?? ['pending' => 0, 'approved' => 0, 'rejected' => 0]); ?>;

            // Monthly Chart
            const monthlyCtx = document.getElementById('storiesMonthlyChart').getContext('2d');
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                    datasets: [{
                        label: 'Stories Créées',
                        data: monthlyData,
                        borderColor: '#7c3aed',
                        backgroundColor: 'rgba(124, 58, 237, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { labels: { color: 'white' } }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(255, 255, 255, 0.1)' },
                            ticks: { color: '#94a3b8', stepSize: 1 }
                        },
                        x: {
                            grid: { color: 'rgba(255, 255, 255, 0.1)' },
                            ticks: { color: '#94a3b8' }
                        }
                    }
                }
            });

            // Status Chart
            const statusCtx = document.getElementById('storiesStatusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['En attente', 'Approuvées', 'Rejetées'],
                    datasets: [{
                        data: [
                            statusData.pending, 
                            statusData.approved, 
                            statusData.rejected
                        ],
                        backgroundColor: [
                            '#f59e0b', // pending - orange
                            '#10b981', // approved - green
                            '#ef4444'  // rejected - red
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
                            labels: { color: 'white', padding: 20 } 
                        }
                    },
                    cutout: '70%'
                }
            });
        });
    </script>
</body>
</html>
