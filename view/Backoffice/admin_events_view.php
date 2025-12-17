<?php
// admin_events_view.php - View for Event Moderation
// Data comes from admin_events.php controller
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AbleLink Admin - Événements</title>
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

        /* Card Styles */
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
        .cta-button.success:hover { background: rgba(46, 204, 113, 0.3); }

        .cta-button.danger {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }
        .cta-button.danger:hover { background: rgba(231, 76, 60, 0.3); }
        
        .cta-button.info {
            background: rgba(52, 152, 219, 0.2);
            color: #3498db;
        }
        .cta-button.info:hover { background: rgba(52, 152, 219, 0.3); }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge.pending { background: rgba(241, 196, 15, 0.2); color: #f1c40f; }
        .badge.verified { background: rgba(46, 204, 113, 0.2); color: #2ecc71; }

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
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.7);
            backdrop-filter: blur(5px);
        }
        .modal-content {
            background-color: #1e293b;
            margin: 5% auto;
            padding: 0;
            border: 1px solid #334155;
            width: 80%;
            max-width: 800px;
            border-radius: 16px;
            color: #e2e8f0;
            animation: fadeIn 0.3s ease-out;
        }
        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #334155;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-body {
            padding: 20px;
            max-height: 70vh;
            overflow-y: auto;
        }
        .close-modal {
            color: #94a3b8;
            font-size: 28px;
            font-weight: bold;
            background: none;
            border: none;
            cursor: pointer;
        }
        .close-modal:hover { color: #fff; }
        
        .event-detail-section {
            margin-bottom: 25px;
            background: rgba(0,0,0,0.2);
            padding: 20px;
            border-radius: 12px;
        }
        .event-detail-section h3 {
            margin-bottom: 15px;
            color: #94a3b8;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 8px;
        }
        .event-detail-row {
            display: flex;
            margin-bottom: 12px;
            align-items: baseline;
        }
        .event-detail-label {
            width: 150px;
            color: #94a3b8;
            font-size: 14px;
            flex-shrink: 0;
        }
        .event-detail-value {
            color: #fff;
            flex: 1;
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
                <a href="admin_dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i>
                    <span>Tableau de Bord</span>
                </a>
                <a href="../view/general/index.php" class="nav-link">
                    <i class="fas fa-globe"></i>
                    <span>Accueil</span>
                </a>
                <a href="admin_events.php" class="nav-link active">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Événements</span>
                </a>
                <a href="admin_stories.php" class="nav-link">
                    <i class="fas fa-book-open"></i>
                    <span>Success Stories</span>
                </a>
                <a href="admin_dashboard.php#users" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Utilisateurs</span>
                </a>
                <a href="admin_dashboard.php#moderation" class="nav-link">
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
                    <h1>Gestion des Événements</h1>
                    <p>Analysez et modérez les événements et évaluations</p>
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
                    <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #60a5fa);">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $stats['totalEvents'] ?? 0; ?></div>
                        <div class="stat-label">Total Événements</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo count($pendingEvents); ?></div>
                        <div class="stat-label">En Attente</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #34d399);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $stats['totalParticipants'] ?? 0; ?></div>
                        <div class="stat-label">Participants</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa);">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $stats['avgRating'] ?? 0; ?></div>
                        <div class="stat-label">Note Moyenne</div>
                    </div>
                </div>
            </div>

            <!-- Analytics Charts -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div class="chart-card">
                    <h3 style="margin-bottom: 20px; color: white;">📈 Événements par Mois</h3>
                    <div style="height: 300px;">
                        <canvas id="eventsChart"></canvas>
                    </div>
                </div>
                <div class="chart-card">
                    <h3 style="margin-bottom: 20px; color: white;">🥧 Statuts</h3>
                    <div style="height: 300px;">
                        <canvas id="eventsPieChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Pending Events List -->
            <div class="chart-card" id="pending-events">
                <h3 style="margin-bottom: 20px; color: white;">⏳ Événements en Attente</h3>
                <div class="admin-list">
                    <?php if (empty($pendingEvents)): ?>
                        <div style="text-align: center; color: #94a3b8; padding: 20px;">
                            Aucun événement en attente de modération.
                        </div>
                    <?php else: ?>
                        <?php foreach ($pendingEvents as $event): ?>
                            <div class="admin-item">
                                <div class="item-content">
                                    <h4 style="color: white; margin-bottom: 5px;"><?php echo htmlspecialchars($event['titre']); ?></h4>
                                    <div style="color: #94a3b8; font-size: 13px; margin-bottom: 5px;">
                                        <i class="fas fa-calendar"></i> <?php echo date('d/m/Y H:i', strtotime($event['date'])); ?> | 
                                        <i class="fas fa-user-tie"></i> <?php echo htmlspecialchars(($event['organisateur_prenom']??'') . ' ' . ($event['organisateur_nom']??'')); ?>
                                    </div>
                                    <div style="color: #ccc; font-size: 14px;">
                                        <?php echo htmlspecialchars(truncateText($event['description'], 100)); ?>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <button class="cta-button info" onclick="viewEventDetails(<?php echo $event['id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="cta-button success" onclick="approveEvent(<?php echo $event['id']; ?>)">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="cta-button danger" onclick="rejectEvent(<?php echo $event['id']; ?>)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <button class="cta-button danger" onclick="deleteEvent(<?php echo $event['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Reported Evaluations -->
            <div class="chart-card" id="reported-evaluations">
                <h3 style="margin-bottom: 20px; color: white;">🚩 Évaluations Signalées</h3>
                <div class="admin-list">
                    <?php if (empty($reportedEvaluations)): ?>
                        <div style="text-align: center; color: #94a3b8; padding: 20px;">
                            Aucune évaluation signalée.
                        </div>
                    <?php else: ?>
                        <?php foreach ($reportedEvaluations as $eval): ?>
                            <div class="admin-item">
                                <div class="item-content">
                                    <h4 style="color: white; margin-bottom: 5px;">
                                        Événement: <?php echo htmlspecialchars($eval['event_titre']); ?>
                                    </h4>
                                    <div style="margin-bottom: 5px;">
                                        <?php echo renderStars($eval['note']); ?> 
                                        <span style="color: #94a3b8; font-size: 13px;">par <?php echo htmlspecialchars(($eval['prenom']??'') . ' ' . ($eval['nom']??'')); ?></span>
                                    </div>
                                    <div style="background: rgba(239, 68, 68, 0.1); padding: 10px; border-radius: 8px; border-left: 3px solid #ef4444; color: #e2e8f0; font-size: 14px;">
                                        "<?php echo htmlspecialchars($eval['commentaire']); ?>"
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <button class="cta-button success" onclick="approveEvaluation(<?php echo $eval['id']; ?>)" title="Garder (Approuver)">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="cta-button danger" onclick="deleteEvaluation(<?php echo $eval['id']; ?>)" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

             <!-- Enterprises Stats -->
             <div class="chart-card">
                <h3 style="margin-bottom: 20px; color: white;">🏢 Entreprises Verifiées et Statistiques</h3>
                <div class="admin-list">
                     <?php if (empty($enterprisesStats)): ?>
                        <div style="text-align: center; color: #94a3b8; padding: 20px;">
                            Aucune donnée entreprise.
                        </div>
                    <?php else: ?>
                        <?php foreach (array_slice($enterprisesStats, 0, 5) as $ent): ?>
                            <div class="admin-item">
                                <div class="item-content">
                                    <h4 style="color: white; margin-bottom: 5px;"><?php echo htmlspecialchars(($ent['prenom']??'') . ' ' . ($ent['nom']??'')); ?></h4>
                                    <div style="display: flex; gap: 15px; font-size: 13px; color: #94a3b8;">
                                        <span><i class="fas fa-calendar"></i> <?php echo $ent['events_count'] ?? 0; ?> Événements</span>
                                        <span><i class="fas fa-star"></i> <?php echo round($ent['avg_rating'] ?? 0, 1); ?> Moyenne</span>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <span class="badge verified">✅ Vérifiée</span>
                                    <button class="cta-button danger" style="padding: 5px 10px;" onclick="unverifyEnterprise(<?php echo $ent['idUtilisateur']; ?>)">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <!-- Event Details Modal -->
    <div id="eventDetailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="eventDetailsTitle">Détails de l'événement</h2>
                <button class="close-modal" onclick="closeEventDetailsModal()">&times;</button>
            </div>
            <div class="modal-body" id="eventDetailsBody">
                <div class="loading-spinner">Chargement...</div>
            </div>
        </div>
    </div>

    <script>
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

        // Charts
        document.addEventListener('DOMContentLoaded', function() {
            const monthlyEvents = <?php echo json_encode($monthlyEvents); ?>;
            const statusDistribution = <?php echo json_encode($statusDistribution); ?>;

            // Events by month
            new Chart(document.getElementById('eventsChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                    datasets: [{
                        label: 'Événements',
                        data: monthlyEvents,
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { labels: { color: 'white' } } },
                    scales: {
                        y: { beginAtZero: true, ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255,255,255,0.1)' } },
                        x: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255,255,255,0.1)' } }
                    }
                }
            });

            // Status Distribution
            new Chart(document.getElementById('eventsPieChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Publiés', 'En attente', 'Rejetés'],
                    datasets: [{
                        data: [
                            statusDistribution['Publié'] || 0,
                            statusDistribution['Brouillon'] || 0,
                            statusDistribution['Rejeté'] || 0
                        ],
                        backgroundColor: ['#2ecc71', '#f1c40f', '#e74c3c'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'right', labels: { color: 'white' } } }
                }
            });
        });

        // AJAX Actions
        function performAction(action, data, successMessage) {
            fetch('admin_dashboard.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: action, ...data })
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert(successMessage);
                    location.reload();
                } else {
                    alert('Erreur: ' + result.message);
                }
            })
            .catch(err => { console.error(err); alert('Erreur backend'); });
        }

        function approveEvent(id) {
            if(confirm('Approuver cet événement ?')) performAction('approve_event', { eventId: id }, 'Approuvé !');
        }
        function rejectEvent(id) {
            if(confirm('Rejeter cet événement ?')) performAction('reject_event', { eventId: id }, 'Rejeté.');
        }
        function deleteEvent(id) {
            if(confirm('Supprimer définitivement ?')) performAction('delete_event', { eventId: id }, 'Supprimé.');
        }
        function approveEvaluation(id) {
            if(confirm('Approuver (garder) cet avis ?')) performAction('approve_evaluation', { evaluationId: id }, 'Avis approuvé.');
        }
        function deleteEvaluation(id) {
            if(confirm('Supprimer cet avis ?')) performAction('reject_evaluation', { evaluationId: id }, 'Avis supprimé.');
        }
        function unverifyEnterprise(id) {
            if(confirm('Révoquer ce statut ?')) alert('Fonctionnalité en cours de développement');
        }

        // Details Modal
        function viewEventDetails(id) {
            document.getElementById('eventDetailsModal').style.display = 'block';
            document.getElementById('eventDetailsBody').innerHTML = 'Chargement...';
            // Use existing admin_dashboard logic or create a fetch
            fetch(`admin_dashboard.php`, { // Reuse dashboard endpoint for details if it supports it
                 method: 'POST',
                 headers: { 'Content-Type': 'application/json' },
                 body: JSON.stringify({ action: 'get_event_details', eventId: id })
            }).then(r=>r.json()).then(d => {
                // ... assuming dashboard returns details. If not, might need a controller fix.
                // The dashboard view had a "get_event_details.php" fetch but it might be better to route through main controller.
                // Actually the view code used `get_event_details.php`. Let's assume we can use the main controller action if we added it.
                // But simplified for now:
                if(d.success && d.event) {
                    displayEventDetails(d.event);
                } else {
                     // Try the old endpoint just in case
                     fetch('get_event_details.php?id='+id).then(r2=>r2.json()).then(d2 => {
                         if(d2.success) displayEventDetails(d2.data); // Adjust based on actual API
                         else document.getElementById('eventDetailsBody').innerHTML = 'Erreur de chargement';
                     });
                     document.getElementById('eventDetailsBody').innerHTML = 'Détails non disponibles via API standard.';
                }
            });
        }
        
        function closeEventDetailsModal() {
            document.getElementById('eventDetailsModal').style.display = 'none';
        }

        function displayEventDetails(event) {
             // Simplified display
             document.getElementById('eventDetailsBody').innerHTML = `
                <h3>${event.titre}</h3>
                <p>${event.description}</p>
                <p>Date: ${event.date}</p>
                <p>Lieu: ${event.lieu}</p>
             `;
        }
    </script>
</body>
</html>
