<?php
// admin_jobs_view.php - View for Job Board Moderation
// Data sources: $pendingOffers, $allOffers, $allCandidatures
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AbleLink Admin - Emplois & Candidatures</title>
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

        * { margin:0; padding:0; box-sizing:border-box; }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .app-container { display: flex; min-height: 100vh; }

        /* Sidebar Styles (From Dashboard) */
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

        .logo { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; padding: 0 10px; }
        .logo-title a {
            font-family: 'Josefin Sans', sans-serif; font-size: 26px; font-weight: 700; letter-spacing: 1px;
            display: flex; align-items: center; gap: 3px; text-decoration: none;
        }
        .letter-a { color: #FF4F5E; text-shadow: 0 0 6px rgba(255,79,94,0.7); }
        .letter-b { color: #4C8DF5; text-shadow: 0 0 6px rgba(76,141,245,0.7); }
        .letter-l { color: #B87BFF; text-shadow: 0 0 6px rgba(184,123,255,0.7); }
        .letter-e { color: #FFB247; text-shadow: 0 0 6px rgba(255,178,71,0.7); }
        .letter-link { color: #FFFFFF; margin-left: 5px; text-shadow: 0 0 10px rgba(255,255,255,0.8); }

        .nav-links { display: flex; flex-direction: column; gap: 8px; }
        .nav-link {
            display: flex; align-items: center; gap: 12px; padding: 14px 16px;
            color: var(--text-light); border-radius: 12px; transition: var(--transition);
            text-decoration: none; position: relative; overflow: hidden;
        }
        .nav-link::before {
            content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }
        .nav-link:hover::before { left: 100%; }
        .nav-link:hover, .nav-link.active {
            background: rgba(124, 58, 237, 0.1); color: white;
            box-shadow: 0 0 15px rgba(124, 58, 237, 0.2);
        }
        .nav-link i { font-size: 20px; width: 24px; text-align: center; }

        /* Main Content */
        .main-content { flex: 1; margin-left: var(--sidebar-width); padding: 25px; transition: var(--transition); }

        /* Header */
        .header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 30px; padding: 15px 0; animation: slideDown 0.5s ease-out;
        }
        .header-title h1 {
            font-size: 28px; font-weight: 700;
            background: linear-gradient(to right, white, var(--text-light));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .header-title p { color: var(--text-light); font-size: 14px; }

        .user-menu { display: flex; align-items: center; gap: 15px; position: relative; }
        .user-avatar {
            width: 45px; height: 45px; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 600; box-shadow: var(--shadow);
            cursor: pointer; transition: var(--transition);
        }
        .user-avatar:hover { transform: scale(1.05); box-shadow: var(--glow); }

        /* Floating Elements */
        .floating-elements { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: -1; }
        .floating-element {
            position: absolute; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), transparent);
            opacity: 0.1; animation: float 20s infinite linear;
        }
        .floating-element:nth-child(1) { width: 300px; height: 300px; top: 10%; left: 5%; animation-duration: 25s; }
        .floating-element:nth-child(2) { width: 200px; height: 200px; top: 60%; right: 10%; animation-duration: 20s; animation-direction: reverse; }
        .floating-element:nth-child(3) { width: 150px; height: 150px; bottom: 10%; left: 20%; animation-duration: 15s; }

        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(0, 40px) rotate(180deg); }
            100% { transform: translate(0, 0) rotate(360deg); }
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }

        /* Job Specific Styles */
        .card {
            background: var(--card-bg); backdrop-filter: blur(10px);
            border-radius: var(--border-radius); padding: 25px; margin-bottom: 25px;
            box-shadow: var(--shadow); border: 1px solid rgba(255, 255, 255, 0.05);
            animation: fadeIn 0.8s ease-out;
        }
        .section-title { font-size: 20px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
        th { color: var(--text-light); font-weight: 500; font-size: 14px; padding: 15px; text-align: left; }
        td { background: rgba(255,255,255,0.03); padding: 15px; first-child: border-radius: 10px 0 0 10px; last-child: border-radius: 0 10px 10px 0; }
        tr { transition: var(--transition); }
        tr:hover td { background: rgba(255,255,255,0.08); }
        
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
        .badge.pending { background: rgba(241, 196, 15, 0.2); color: #f1c40f; }
        .badge.published { background: rgba(46, 204, 113, 0.2); color: #2ecc71; }
        .badge.rejected { background: rgba(231, 76, 60, 0.2); color: #e74c3c; }
        
        .btn-action { width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; margin-right: 5px; transition: 0.2s; }
        .btn-approve { background: rgba(46, 204, 113, 0.2); color: #2ecc71; }
        .btn-reject { background: rgba(231, 76, 60, 0.2); color: #e74c3c; }
        .btn-delete { background: rgba(255, 255, 255, 0.1); color: #94a3b8; }
        .btn-action:hover { transform: scale(1.1); }

        .tabs { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
        .tab-btn {
            padding: 10px 20px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
            color: var(--text-light); border-radius: 8px; cursor: pointer; transition: var(--transition);
        }
        .tab-btn:hover, .tab-btn.active { background: var(--primary); color: white; border-color: var(--primary); box-shadow: 0 0 15px rgba(124, 58, 237, 0.3); }

        /* Stats Grid (reused from dashboard style) */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card {
            background: var(--card-bg); backdrop-filter: blur(10px); border-radius: var(--border-radius);
            padding: 25px; display: flex; align-items: center; gap: 20px; box-shadow: var(--shadow);
            border: 1px solid rgba(255, 255, 255, 0.05); transition: var(--transition); animation: fadeIn 0.6s ease-out;
        }
        .stat-icon {
            width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center;
            font-size: 24px; color: white;
        }
        .stat-value { font-size: 28px; font-weight: 700; margin-bottom: 5px; }
        .stat-label { color: var(--text-light); font-size: 14px; }
        
        .profile-item { display: flex; align-items: center; gap: 10px; color: #e2e8f0; padding: 10px; border-radius: 8px; text-decoration: none; transition: 0.2s; font-size: 14px; }
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
                    <span class="letter-a">A</span><span class="letter-b">B</span><span class="letter-l">L</span><span class="letter-e">E</span><span class="letter-link">LINK</span>
                </a>
            </div>
            
            <div class="nav-links">
                <a href="admin_dashboard.php" class="nav-link"><i class="fas fa-home"></i> <span>Tableau de Bord</span></a>
                <a href="../view/general/index.php" class="nav-link"><i class="fas fa-globe"></i> <span>Accueil</span></a>
                <a href="admin_events.php" class="nav-link"><i class="fas fa-calendar-alt"></i> <span>Événements</span></a>
                <a href="admin_stories.php" class="nav-link"><i class="fas fa-book-open"></i> <span>Success Stories</span></a>
                <a href="admin_jobs.php" class="nav-link active"><i class="fas fa-briefcase"></i> <span>Offres & Candidatures</span></a>
                <a href="#users" class="nav-link"><i class="fas fa-users"></i> <span>Utilisateurs</span></a>
                <a href="#moderation" class="nav-link"><i class="fas fa-shield-alt"></i> <span>Modération</span></a>
            </div>

            <div style="margin-top:auto; padding:20px 0;">
                <a href="../view/general/logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> <span>Déconnexion</span></a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="header-title">
                    <h1>Offres & Candidatures</h1>
                    <p>Gérez les offres d'emploi et suivez les candidatures</p>
                </div>
                <div class="user-menu">
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

            <!-- Tabs -->
            <div class="tabs">
                <button class="tab-btn active" onclick="showTab('stats')">Statistiques</button>
                <button class="tab-btn" onclick="showTab('pending')">Offres en attente (<?= count($pendingOffers) ?>)</button>
                <button class="tab-btn" onclick="showTab('all')">Toutes les offres</button>
                <button class="tab-btn" onclick="showTab('candidatures')">Candidatures (<?= count($allCandidatures) ?>)</button>
            </div>

            <!-- Stats Tab -->
            <div id="tab-stats" class="tab-content">
                <!-- Summary Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #60a5fa);"><i class="fas fa-briefcase"></i></div>
                        <div class="stat-info">
                            <div class="stat-value"><?= $analyticsData['jobStats']['total'] ?? 0 ?></div>
                            <div class="stat-label">Total Offres</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #34d399);"><i class="fas fa-check"></i></div>
                        <div class="stat-info">
                            <div class="stat-value"><?= $analyticsData['jobStats']['published'] ?? 0 ?></div>
                            <div class="stat-label">Publiées</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);"><i class="fas fa-clock"></i></div>
                        <div class="stat-info">
                            <div class="stat-value"><?= $analyticsData['jobStats']['pending'] ?? 0 ?></div>
                            <div class="stat-label">En Attente</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa);"><i class="fas fa-users"></i></div>
                        <div class="stat-info">
                            <div class="stat-value"><?= $analyticsData['candidatureStats']['total'] ?? 0 ?></div>
                            <div class="stat-label">Candidatures</div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px;">
                    <div class="card">
                        <div class="section-title">📈 Tendances (Derniers 6 mois)</div>
                        <div style="height: 300px;"><canvas id="trendsChart"></canvas></div>
                    </div>
                    <div class="card">
                        <div class="section-title">🥧 Distribution par Statut</div>
                        <div style="height: 300px;"><canvas id="statusChart"></canvas></div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="section-title">🏆 Top 5 Offres Populaires</div>
                    <div style="height: 250px;"><canvas id="topOffersChart"></canvas></div>
                </div>
            </div>

            <!-- Pending Offers Tab -->
            <div id="tab-pending" class="tab-content" style="display: none;">
                <div class="card">
                    <div class="section-title"><i class="fas fa-clock text-warning"></i> Modération (En attente)</div>
                    <?php if (empty($pendingOffers)): ?>
                        <div style="text-align: center; color: var(--text-light); padding: 30px;">Aucune offre en attente.</div>
                    <?php else: ?>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Entreprise</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingOffers as $offer): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600;"><?= htmlspecialchars($offer['titre']) ?></div>
                                            <small style="color: var(--text-light);"><?= htmlspecialchars(substr($offer['description'], 0, 50)) ?>...</small>
                                        </td>
                                        <td><?= htmlspecialchars($offer['entreprise']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($offer['date_publication'])) ?></td>
                                        <td>
                                            <button class="btn-action btn-approve" title="Approuver" onclick="approveOffer(<?= $offer['id'] ?>)"><i class="fas fa-check"></i></button>
                                            <button class="btn-action btn-reject" title="Rejeter" onclick="rejectOffer(<?= $offer['id'] ?>)"><i class="fas fa-times"></i></button>
                                            <button class="btn-action btn-delete" title="Supprimer" onclick="deleteOffer(<?= $offer['id'] ?>)"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- All Offers Tab -->
            <div id="tab-all" class="tab-content" style="display: none;">
                <div class="card">
                    <div class="section-title"><i class="fas fa-list"></i> Liste complète</div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Titre</th>
                                    <th>Entreprise</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allOffers as $offer): ?>
                                <tr>
                                    <td>#<?= $offer['id'] ?></td>
                                    <td><?= htmlspecialchars($offer['titre']) ?></td>
                                    <td><?= htmlspecialchars($offer['entreprise']) ?></td>
                                    <td>
                                        <?php if (($offer['statut'] ?? '') === 'published'): ?>
                                            <span class="badge published">Publié</span>
                                        <?php elseif (($offer['statut'] ?? '') === 'rejected'): ?>
                                            <span class="badge rejected">Rejeté</span>
                                        <?php else: ?>
                                            <span class="badge pending">En attente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn-action btn-delete" onclick="deleteOffer(<?= $offer['id'] ?>)"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Candidatures Tab -->
            <div id="tab-candidatures" class="tab-content" style="display: none;">
                <div class="card">
                    <div class="section-title"><i class="fas fa-users"></i> Candidatures Récentes</div>
                    <?php if (empty($allCandidatures)): ?>
                        <div style="text-align: center; color: var(--text-light); padding: 30px;">Aucune candidature reçue.</div>
                    <?php else: ?>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Candidat</th>
                                        <th>Offre</th>
                                        <th>Date</th>
                                        <th>CV</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($allCandidatures as $cand): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600;"><?= htmlspecialchars($cand['nom_candidat']) ?></div>
                                            <small style="color: var(--text-light);"><?= htmlspecialchars($cand['email']) ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($cand['offre_titre'] ?? 'Offre supprimée') ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($cand['date_candidature'])) ?></td>
                                        <td>
                                            <?php if (!empty($cand['cv'])): ?>
                                                <a href="../<?= htmlspecialchars($cand['cv']) ?>" target="_blank" class="badge published" style="text-decoration:none;">
                                                    <i class="fas fa-file-pdf"></i> Voir CV
                                                </a>
                                            <?php else: ?>
                                                <span style="color: var(--text-light);">Aucun CV</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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
        document.addEventListener("click", function(e) {
            const menu = document.getElementById("profileMenu");
            const avatar = document.querySelector(".user-avatar");
            if (menu && avatar && !menu.contains(e.target) && !avatar.contains(e.target)) {
                menu.style.display = "none";
            }
        });

        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
            document.getElementById('tab-' + tabName).style.display = 'block';
            
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            event.target.classList.add('active');
        }

        function apiCall(action, id) {
            fetch('admin_jobs.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ action: action, id: id })
            })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                } else {
                    alert('Erreur: ' + (data.message || 'Operation échouée'));
                }
            });
        }

        function approveOffer(id) {
            if(confirm('Approuver cette offre ?')) apiCall('approve_offer', id);
        }

        function rejectOffer(id) {
            if(confirm('Rejeter cette offre ?')) apiCall('reject_offer', id);
        }

        function deleteOffer(id) {
            if(confirm('Supprimer cette offre définitivement ?')) apiCall('delete_offer', id);
        }

        // Charts Initialization
        document.addEventListener('DOMContentLoaded', function() {
            // Data from PHP
            const analytics = <?= json_encode($analyticsData) ?>;
            
            // 1. Trends Chart
            const trendsCtx = document.getElementById('trendsChart').getContext('2d');
            const months = analytics.offerTrends.map(item => item.mois);
            const offerCounts = analytics.offerTrends.map(item => item.count);
            // Default to empty array if no data
            
            // Align candidature trends to same months if possible, or just use what we have. 
            // Ideally should merge data by month. For simplicity, we'll assume similar timeline or just plot what we have.
            // A better way is to create a unified label set.
            const candMonths = analytics.candidatureTrends.map(item => item.mois);
            const candCounts = analytics.candidatureTrends.map(item => item.count);

            new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: months.length > 0 ? months : candMonths, // Simple fallback
                    datasets: [
                        {
                            label: 'Offres publiées',
                            data: offerCounts,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Candidatures reçues',
                            data: candCounts,
                            borderColor: '#8b5cf6',
                            backgroundColor: 'rgba(139, 92, 246, 0.1)',
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { labels: { color: 'white' } } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.1)' }, ticks: { color: '#94a3b8' } },
                        x: { grid: { color: 'rgba(255,255,255,0.1)' }, ticks: { color: '#94a3b8' } }
                    }
                }
            });

            // 2. Status Chart
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Publiées', 'En attente', 'Rejetées'],
                    datasets: [{
                        data: [
                            analytics.jobStats.published, 
                            analytics.jobStats.pending, 
                            analytics.jobStats.rejected
                        ],
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { color: 'white' } } },
                    cutout: '70%'
                }
            });

            // 3. Top Offers Chart
            const topCtx = document.getElementById('topOffersChart').getContext('2d');
            const topLabels = analytics.topOffers.map(item => item.titre.substring(0, 20) + '...');
            const topData = analytics.topOffers.map(item => item.count);

            new Chart(topCtx, {
                type: 'bar',
                data: {
                    labels: topLabels,
                    datasets: [{
                        label: 'Nombre de candidatures',
                        data: topData,
                        backgroundColor: '#7c3aed',
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.1)' }, ticks: { color: '#94a3b8', stepSize: 1 } },
                        x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                    }
                }
            });
        });
    </script>
</body>
</html>
