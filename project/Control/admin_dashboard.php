<?php
// admin_dashboard.php - Full database-connected admin dashboard

// Start session and check admin permissions
session_start();

// Database configuration
$host = '127.0.0.1';
$dbname = 'projet';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if user is admin (in a real app, you'd check session)
$isAdmin = true; // For demo purposes

// Get statistics from database
function getDashboardStats($pdo) {
    $stats = [];
    
    // Total events
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM evenement");
    $stats['totalEvents'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Pending events (Brouillon status)
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM evenement WHERE statut = 'Brouillon'");
    $stats['pendingEvents'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total evaluations
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM evaluation");
    $stats['totalEvaluations'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total enterprises (users who are companies)
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM utilisateur WHERE isAdmin = 0");
    $stats['totalEnterprises'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Average rating
    $stmt = $pdo->query("SELECT AVG(note) as avg FROM evaluation");
    $stats['avgRating'] = round($stmt->fetch(PDO::FETCH_ASSOC)['avg'], 1);
    
    // Events this month
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM evenement WHERE MONTH(date) = MONTH(CURRENT_DATE()) AND YEAR(date) = YEAR(CURRENT_DATE())");
    $stats['eventsThisMonth'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total participants
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM participation");
    $stats['totalParticipants'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    return $stats;
}

// Get pending events for moderation
function getPendingEvents($pdo) {
    $sql = "SELECT e.*, u.nom, u.prenom, u.email,
            (SELECT COUNT(*) FROM participation p WHERE p.idEvenement = e.id) as participants_count
            FROM evenement e 
            JOIN utilisateur u ON e.idUtilisateur = u.idUtilisateur 
            WHERE e.statut = 'Brouillon'
            ORDER BY e.date DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get reported evaluations
function getReportedEvaluations($pdo) {
    $sql = "SELECT e.*, ev.titre as event_titre, u.nom, u.prenom, u.email
            FROM evaluation e 
            JOIN evenement ev ON e.idEvenement = ev.id 
            JOIN utilisateur u ON e.idUtilisateur = u.idUtilisateur 
            WHERE e.signalee = 1 
            ORDER BY e.dateEvaluation DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get enterprises statistics
function getEnterprisesStats($pdo) {
    $sql = "SELECT u.idUtilisateur, u.nom, u.prenom, u.email,
            COUNT(DISTINCT ev.id) as events_count,
            AVG(eval.note) as avg_rating,
            COUNT(DISTINCT p.id) as total_participations,
            (COUNT(DISTINCT p.id) * 100.0 / GREATEST(COUNT(DISTINCT ev.id) * 50, 1)) as participation_rate
            FROM utilisateur u
            LEFT JOIN evenement ev ON u.idUtilisateur = ev.idUtilisateur
            LEFT JOIN evaluation eval ON ev.id = eval.idEvenement
            LEFT JOIN participation p ON ev.id = p.idEvenement
            WHERE u.isAdmin = 0
            GROUP BY u.idUtilisateur, u.nom, u.prenom, u.email
            ORDER BY avg_rating DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get events for analytics
function getEventsForAnalytics($pdo) {
    $sql = "SELECT 
            MONTH(date) as month,
            COUNT(*) as event_count,
            AVG(participants_count) as avg_participants
            FROM evenement e
            LEFT JOIN (
                SELECT idEvenement, COUNT(*) as participants_count 
                FROM participation 
                GROUP BY idEvenement
            ) p ON e.id = p.idEvenement
            WHERE YEAR(date) = YEAR(CURRENT_DATE())
            GROUP BY MONTH(date)
            ORDER BY month";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            case 'approve_event':
                $stmt = $pdo->prepare("UPDATE evenement SET statut = 'Publié' WHERE id = ?");
                $stmt->execute([$_POST['event_id']]);
                echo json_encode(['success' => true, 'message' => 'Événement approuvé']);
                break;
                
            case 'reject_event':
                $stmt = $pdo->prepare("UPDATE evenement SET statut = 'Rejeté' WHERE id = ?");
                $stmt->execute([$_POST['event_id']]);
                echo json_encode(['success' => true, 'message' => 'Événement rejeté']);
                break;
                
            case 'delete_event':
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("DELETE FROM participation WHERE idEvenement = ?");
                $stmt->execute([$_POST['event_id']]);
                
                $stmt = $pdo->prepare("DELETE FROM evaluation WHERE idEvenement = ?");
                $stmt->execute([$_POST['event_id']]);
                
                $stmt = $pdo->prepare("DELETE FROM evenement WHERE id = ?");
                $stmt->execute([$_POST['event_id']]);
                $pdo->commit();
                
                echo json_encode(['success' => true, 'message' => 'Événement supprimé']);
                break;
                
            case 'approve_evaluation':
                $stmt = $pdo->prepare("UPDATE evaluation SET signalee = 0 WHERE id = ?");
                $stmt->execute([$_POST['evaluation_id']]);
                echo json_encode(['success' => true, 'message' => 'Évaluation approuvée']);
                break;
                
            case 'delete_evaluation':
                $stmt = $pdo->prepare("DELETE FROM evaluation WHERE id = ?");
                $stmt->execute([$_POST['evaluation_id']]);
                echo json_encode(['success' => true, 'message' => 'Évaluation supprimée']);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
        }
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        exit;
    }
}

// Load data
$stats = getDashboardStats($pdo);
$pendingEvents = getPendingEvents($pdo);
$reportedEvaluations = getReportedEvaluations($pdo);
$enterprisesStats = getEnterprisesStats($pdo);
$analyticsData = getEventsForAnalytics($pdo);

// Prepare chart data
$monthlyEvents = array_fill(0, 12, 0);
$monthlyParticipants = array_fill(0, 12, 0);
foreach ($analyticsData as $data) {
    $monthlyEvents[$data['month'] - 1] = (int)$data['event_count'];
    $monthlyParticipants[$data['month'] - 1] = (int)$data['avg_participants'];
}

$statusDistribution = [
    'Publié' => 0,
    'Brouillon' => 0,
    'Rejeté' => 0
];
$stmt = $pdo->query("SELECT statut, COUNT(*) as count FROM evenement GROUP BY statut");
$statusData = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($statusData as $data) {
    $statusDistribution[$data['statut']] = (int)$data['count'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord & Modération - AbeLink</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* GENERAL */
        body  {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: #0d1117;
            color: #d1d5db;
        }

        a { text-decoration: none; }

        /* APP WRAPPER */
        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 280px;
            background: #0f1624;
            padding: 30px 20px;
            color: white;
        }
        .sidebar .logo {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar .logo span { color: #4f9cff; }
        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 15px;
            color: #d1d5db;
            border-radius: 8px;
            margin-bottom: 10px;
            font-size: 16px;
            transition: 0.2s;
        }
        .sidebar a.active { background: #7c3aed; color: #fff; }
        .sidebar a:hover { background: #4f9cff; color: #fff; }

        /* MAIN CONTENT */
        .main-content { 
            flex: 1; 
            padding: 40px;
        }

        /* CARD */
        .card {
            background: #111827;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 0 20px rgba(0, 150, 255, 0.15);
            border: 1px solid rgba(0, 150, 255, 0.2);
            margin-bottom: 20px;
        }

        /* ACTION BUTTONS */
        .action-btn {
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            display: inline-block;
        }
        .action-btn.edit { background: #facc15; color: #000; }
        .action-btn.delete { background: #dc2626; color: #fff; }

        /* FORM */
        .form-group { margin-bottom: 20px; }
        .form-group label { 
            display: block; 
            color: #cbd5e1; 
            margin-bottom: 8px;
            font-weight: 500;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: none;
            background: #1e293b;
            color: white;
            box-sizing: border-box;
        }
        input::placeholder { color: #9ca3af; }
        .btn-submit {
            background: #4f9cff;
            border: none;
            padding: 12px 18px;
            border-radius: 6px;
            color: white;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-submit:hover { background: #3b82f6; }
        .success { 
            color: #4ade80; 
            margin-bottom: 15px;
            padding: 10px;
            background: rgba(74, 222, 128, 0.1);
            border-radius: 6px;
        }
        .error { 
            color: #f87171; 
            margin-bottom: 15px;
            padding: 10px;
            background: rgba(248, 113, 113, 0.1);
            border-radius: 6px;
        }

        /* Admin Dashboard Specific Styles */
        .nav-links {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .nav-links a {
            color: #d1d5db;
            padding: 10px 15px;
            border-radius: 8px;
            transition: 0.2s;
        }

        .nav-links a:hover, .nav-links a.active {
            background: #7c3aed;
            color: #fff;
        }

        .role-selector {
            display: flex;
            align-items: center;
            margin-left: 20px;
        }

        .role-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }

        .role-badge.user {
            background: rgba(52, 152, 219, 0.2);
            color: #3498db;
        }

        .role-badge.company {
            background: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
        }

        .role-badge.inclusion {
            background: rgba(155, 89, 182, 0.2);
            color: #9b59b6;
        }

        .role-badge.admin {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
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

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .stat-card {
            padding: 25px;
            text-align: center;
            border-radius: 12px;
            background: #111827;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: white;
        }

        .stat-label {
            font-size: 1rem;
            color: #ccc;
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 10px;
            margin: 30px 0 20px 0;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            padding-bottom: 10px;
            flex-wrap: wrap;
        }

        .tab {
            padding: 12px 24px;
            background: #1e293b;
            border: none;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .tab.active {
            background: #4f9cff;
        }

        .tab:hover {
            background: #3b82f6;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .action-card {
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #111827;
        }

        .action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(0, 150, 255, 0.3);
        }

        .action-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        /* Admin List */
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
            background: #111827;
        }

        .item-content {
            flex: 1;
        }

        .item-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge.pending {
            background: rgba(241, 196, 15, 0.2);
            color: #f1c40f;
        }

        .badge.approved {
            background: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
        }

        .badge.rejected {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }

        .badge.verified {
            background: rgba(52, 152, 219, 0.2);
            color: #3498db;
        }

        .badge.reported {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }

        /* Search Filter */
        .search-filter {
            display: flex;
            gap: 15px;
            margin: 20px 0;
            flex-wrap: wrap;
        }

        /* CTA Buttons */
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

        .cta-button.danger {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }

        .cta-button.danger:hover {
            background: rgba(231, 76, 60, 0.3);
        }

        .cta-button.success {
            background: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
        }

        .cta-button.success:hover {
            background: rgba(46, 204, 113, 0.3);
        }

        /* Chart Container */
        .chart-container {
            background: #111827;
            padding: 25px;
            border-radius: 12px;
            margin: 20px 0;
            height: 300px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
            background: #111827;
            border-radius: 12px;
        }

        /* Loading */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Header */
        header {
            background: #0f1624;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Footer */
        #footer {
            margin-top: 40px;
            background: #0f1624;
            padding: 20px 0;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .footer-links {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: #d1d5db;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: #4f9cff;
        }

        .copyright {
            color: #9ca3af;
            font-size: 14px;
        }

        /* Rating Stars */
        .stars {
            color: #f1c40f;
            font-size: 14px;
        }

        .avg {
            color: #ccc;
            font-size: 12px;
            margin-left: 5px;
        }

        .rating {
            display: flex;
            align-items: center;
            gap: 5px;
            margin: 5px 0;
        }

        /* Enterprise Stats */
        .enterprise-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }

        .enterprise-stat {
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            background: rgba(255,255,255,0.05);
        }

        /* Error State */
        .error-state {
            text-align: center;
            padding: 40px 20px;
            color: #e74c3c;
            background: rgba(231, 76, 60, 0.1);
            border-radius: 12px;
        }

        @media (max-width: 768px) {
            .admin-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .item-actions {
                width: 100%;
                justify-content: flex-start;
            }
            
            .tabs {
                flex-direction: column;
            }
            
            .search-filter {
                flex-direction: column;
            }
            
            .footer-content {
                flex-direction: column;
                text-align: center;
            }

            .app-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                padding: 20px;
            }
            
            .main-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="app-container">
    <div class="sidebar">
        <div class="logo">ABLE<span>LINK</span></div>
        <a href="#" class="active">Tableau de Bord</a>
        <a href="../FrontOffice/index.html">Accueil</a>
        <a href="../FrontOffice/evaluations-evenements.php">Évaluations & Événements</a>
    </div>

    <div class="main-content">
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 20px;">
            <div>
                <h1 style="margin: 0; color: white;">Tableau de Bord & Modération</h1>
                <p style="margin: 5px 0 0 0; color: #ccc;">Gérez les événements, modérez les évaluations et consultez les statistiques</p>
            </div>
            <div class="role-selector">
                <select id="roleSelect" class="input" style="width: 150px;" onchange="changeUserRole()">
                    <option value="user">Utilisateur</option>
                    <option value="company">Entreprise</option>
                    <option value="inclusion">Responsable Inclusion</option>
                    <option value="admin" selected>Administrateur</option>
                </select>
                <div id="roleBadge" class="role-badge admin">Administrateur</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <div class="action-card" onclick="switchTab('moderation')">
                <div class="action-icon">📋</div>
                <h3>Modérer Événements</h3>
                <p>Approuvez ou rejetez les événements en attente</p>
            </div>
            <div class="action-card" onclick="switchTab('evaluations')">
                <div class="action-icon">⭐</div>
                <h3>Évaluations</h3>
                <p>Gérez les évaluations signalées</p>
            </div>
            <div class="action-card" onclick="switchTab('enterprises')">
                <div class="action-icon">🏢</div>
                <h3>Entreprises</h3>
                <p>Statistiques et performances</p>
            </div>
            <div class="action-card" onclick="switchTab('analytics')">
                <div class="action-icon">📊</div>
                <h3>Analytiques</h3>
                <p>Graphiques et rapports détaillés</p>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="card">
            <h2 style="margin-bottom: 25px; color: white;">Aperçu Général</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number" id="totalEvents"><?php echo $stats['totalEvents']; ?></div>
                    <div class="stat-label">Événements Totaux</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="pendingEvents"><?php echo $stats['pendingEvents']; ?></div>
                    <div class="stat-label">Événements en Attente</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="totalEvaluations"><?php echo $stats['totalEvaluations']; ?></div>
                    <div class="stat-label">Évaluations</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="totalEnterprises"><?php echo $stats['totalEnterprises']; ?></div>
                    <div class="stat-label">Entreprises</div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="tabs">
            <button class="tab active" onclick="switchTab('moderation')">📋 Modération Événements</button>
            <button class="tab" onclick="switchTab('evaluations')">⭐ Modération Évaluations</button>
            <button class="tab" onclick="switchTab('enterprises')">🏢 Statistiques Entreprises</button>
            <button class="tab" onclick="switchTab('analytics')">📊 Analytiques</button>
        </div>

        <!-- Moderation Tab -->
        <div id="moderation-tab" class="tab-content active">
            <div class="card">
                <h2 style="margin-bottom: 20px; color: white;">📋 Modération des Événements</h2>
                
                <div class="search-filter">
                    <input type="text" id="searchEvents" class="input" placeholder="🔍 Rechercher des événements..." style="flex: 1;">
                    <select id="filterStatus" class="input" style="width: 200px;">
                        <option value="all">Tous les statuts</option>
                        <option value="Brouillon">⏳ En attente</option>
                        <option value="Publié">✅ Approuvés</option>
                        <option value="Rejeté">❌ Rejetés</option>
                    </select>
                </div>

                <div id="eventsModerationList" class="admin-list">
                    <?php if (empty($pendingEvents)): ?>
                        <div class="empty-state">📭 Aucun événement en attente de modération</div>
                    <?php else: ?>
                        <?php foreach ($pendingEvents as $event): ?>
                            <div class="admin-item">
                                <div class="item-content">
                                    <h4 style="margin: 0 0 8px 0; color: white;"><?php echo htmlspecialchars($event['titre']); ?></h4>
                                    <p style="margin: 0 0 8px 0; color: #ccc;">
                                        <?php echo htmlspecialchars($event['prenom'] . ' ' . $event['nom']); ?> • 
                                        <?php echo date('d/m/Y H:i', strtotime($event['date'])); ?> • 
                                        <?php echo htmlspecialchars($event['lieu']); ?>
                                    </p>
                                    <p style="margin: 0 0 8px 0; font-size: 14px; color: #ccc;">
                                        <?php echo htmlspecialchars(truncateText($event['description'], 120)); ?>
                                    </p>
                                    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                        <span class="badge pending">⏳ En attente</span>
                                        <span style="color: #ccc; font-size: 14px;">👥 <?php echo $event['participants_count']; ?> participants</span>
                                        <?php if ($event['accessibilite']): ?>
                                            <span style="color: #ccc; font-size: 14px;">♿ <?php echo htmlspecialchars($event['accessibilite']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <button class="cta-button success" onclick="approveEvent(<?php echo $event['id']; ?>)">✅ Approuver</button>
                                    <button class="cta-button danger" onclick="rejectEvent(<?php echo $event['id']; ?>)">❌ Rejeter</button>
                                    <button class="cta-button" onclick="viewEventDetails(<?php echo $event['id']; ?>)">👁️ Détails</button>
                                    <button class="cta-button danger" onclick="deleteEvent(<?php echo $event['id']; ?>)">🗑️ Supprimer</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Evaluations Moderation Tab -->
        <div id="evaluations-tab" class="tab-content">
            <div class="card">
                <h2 style="margin-bottom: 20px; color: white;">⭐ Modération des Évaluations</h2>
                
                <div class="search-filter">
                    <input type="text" id="searchEvaluations" class="input" placeholder="🔍 Rechercher des évaluations..." style="flex: 1;">
                </div>

                <div id="evaluationsModerationList" class="admin-list">
                    <?php if (empty($reportedEvaluations)): ?>
                        <div class="empty-state">📭 Aucune évaluation signalée</div>
                    <?php else: ?>
                        <?php foreach ($reportedEvaluations as $evaluation): ?>
                            <div class="admin-item">
                                <div class="item-content">
                                    <h4 style="margin: 0 0 8px 0; color: white;"><?php echo htmlspecialchars($evaluation['event_titre']); ?></h4>
                                    <p style="margin: 0 0 8px 0; color: #ccc;">👤 Évalué par <?php echo htmlspecialchars($evaluation['prenom'] . ' ' . $evaluation['nom']); ?></p>
                                    <div class="rating">
                                        <span class="stars"><?php echo renderStars($evaluation['note']); ?></span>
                                        <span style="margin-left: 8px; color: #ccc;"><?php echo $evaluation['note']; ?>/5</span>
                                    </div>
                                    <p style="margin: 8px 0; font-size: 14px; color: #ccc; font-style: italic;">
                                        "<?php echo htmlspecialchars(truncateText($evaluation['commentaire'], 150)); ?>"
                                    </p>
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <span class="badge reported">🚩 Signalée</span>
                                        <span style="color: #ccc; font-size: 12px;">
                                            <?php echo date('d/m/Y H:i', strtotime($evaluation['dateEvaluation'])); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <button class="cta-button success" onclick="approveEvaluation(<?php echo $evaluation['id']; ?>)">✅ Approuver</button>
                                    <button class="cta-button danger" onclick="deleteEvaluation(<?php echo $evaluation['id']; ?>)">🗑️ Supprimer</button>
                                    <button class="cta-button" onclick="viewEvaluationDetails(<?php echo $evaluation['id']; ?>)">👁️ Détails</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Enterprises Tab -->
        <div id="enterprises-tab" class="tab-content">
            <div class="card">
                <h2 style="margin-bottom: 20px; color: white;">🏢 Statistiques des Entreprises</h2>
                
                <div class="search-filter">
                    <input type="text" id="searchEnterprises" class="input" placeholder="🔍 Rechercher des entreprises..." style="flex: 1;">
                </div>

                <div id="enterprisesStatsList" class="admin-list">
                    <?php if (empty($enterprisesStats)): ?>
                        <div class="empty-state">🏢 Aucune entreprise trouvée</div>
                    <?php else: ?>
                        <?php foreach ($enterprisesStats as $enterprise): ?>
                            <div class="admin-item">
                                <div class="item-content">
                                    <h4 style="margin: 0 0 12px 0; color: white;">
                                        <?php echo htmlspecialchars($enterprise['prenom'] . ' ' . $enterprise['nom']); ?>
                                    </h4>
                                    <p style="margin: 0 0 12px 0; color: #ccc;"><?php echo htmlspecialchars($enterprise['email']); ?></p>
                                    <div class="enterprise-stats">
                                        <div class="enterprise-stat" style="background: rgba(52, 152, 219, 0.1);">
                                            <div style="font-size: 1.5rem; font-weight: bold; color: #3498db;">
                                                <?php echo $enterprise['events_count'] ?: 0; ?>
                                            </div>
                                            <div style="font-size: 0.9rem; color: #ccc;">Événements</div>
                                        </div>
                                        <div class="enterprise-stat" style="background: rgba(46, 204, 113, 0.1);">
                                            <div style="font-size: 1.5rem; font-weight: bold; color: #2ecc71;">
                                                <?php echo $enterprise['avg_rating'] ? round($enterprise['avg_rating'], 1) : '0.0'; ?>
                                            </div>
                                            <div style="font-size: 0.9rem; color: #ccc;">Note Moyenne</div>
                                        </div>
                                        <div class="enterprise-stat" style="background: rgba(155, 89, 182, 0.1);">
                                            <div style="font-size: 1.5rem; font-weight: bold; color: #9b59b6;">
                                                <?php echo $enterprise['participation_rate'] ? round($enterprise['participation_rate']) : 0; ?>%
                                            </div>
                                            <div style="font-size: 0.9rem; color: #ccc;">Participation</div>
                                        </div>
                                    </div>
                                    <div style="margin-top: 10px;">
                                        <span class="badge verified">✅ Vérifiée</span>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <button class="cta-button" onclick="viewEnterpriseDetails(<?php echo $enterprise['idUtilisateur']; ?>)">👁️ Détails</button>
                                    <button class="cta-button danger" onclick="unverifyEnterprise(<?php echo $enterprise['idUtilisateur']; ?>)">🚫 Révoquer</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Analytics Tab -->
        <div id="analytics-tab" class="tab-content">
            <div class="card">
                <h2 style="margin-bottom: 20px; color: white;">📊 Analytiques et Rapports</h2>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number" id="avgRating"><?php echo $stats['avgRating']; ?></div>
                        <div class="stat-label">Note Moyenne</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="participationRate">
                            <?php echo $stats['totalEvents'] > 0 ? round(($stats['totalParticipants'] / ($stats['totalEvents'] * 50)) * 100) : 0; ?>%
                        </div>
                        <div class="stat-label">Taux de Participation</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="topEnterprise">
                            <?php 
                            $topEnterprise = null;
                            $topRating = 0;
                            foreach ($enterprisesStats as $enterprise) {
                                if ($enterprise['avg_rating'] > $topRating) {
                                    $topRating = $enterprise['avg_rating'];
                                    $topEnterprise = $enterprise['prenom'] . ' ' . $enterprise['nom'];
                                }
                            }
                            echo $topEnterprise ? substr($topEnterprise, 0, 8) . '...' : '-';
                            ?>
                        </div>
                        <div class="stat-label">Entreprise Top</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="eventsThisMonth"><?php echo $stats['eventsThisMonth']; ?></div>
                        <div class="stat-label">Événements ce Mois</div>
                    </div>
                </div>

                <div class="chart-container">
                    <h3 style="margin-bottom: 15px;">📈 Événements par Mois</h3>
                    <canvas id="eventsChart"></canvas>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                    <div class="chart-container">
                        <h3 style="margin-bottom: 15px;">🥧 Répartition des Événements</h3>
                        <canvas id="eventsPieChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h3 style="margin-bottom: 15px;">🏆 Notes par Entreprise</h3>
                        <canvas id="ratingsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-links">
                <a href="../FrontOffice/index.php">Accueil</a>
                <a href="#">Politique de confidentialité</a>
                <a href="#">Conditions d'utilisation</a>
                <a href="admin_dashboard.php">Administration</a>
            </div>
            <div class="copyright">
                &copy; 2025 AbeLink. Tous droits réservés. Tableau de Bord Administratif.
            </div>
        </div>
    </div>
</div>

<script>
    // User roles and permissions
    const USER_ROLES = {
        USER: 'user',
        COMPANY: 'company',
        INCLUSION: 'inclusion',
        ADMIN: 'admin'
    };

    // Chart data from PHP
    const monthlyEvents = <?php echo json_encode($monthlyEvents); ?>;
    const monthlyParticipants = <?php echo json_encode($monthlyParticipants); ?>;
    const statusDistribution = <?php echo json_encode($statusDistribution); ?>;
    const enterprisesData = <?php echo json_encode($enterprisesStats); ?>;

    // Initialize the dashboard
    document.addEventListener('DOMContentLoaded', function() {
        // Set admin role for dashboard
        window.CURRENT_USER_ROLE = USER_ROLES.ADMIN;
        updateUIForRole();
        
        // Setup charts
        setupCharts();
        
        // Setup event listeners for search/filter
        setupEventListeners();
    });

    // Setup charts
    function setupCharts() {
        // Events by month chart
        const eventsCtx = document.getElementById('eventsChart');
        if (eventsCtx) {
            new Chart(eventsCtx, {
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
                    }, {
                        label: 'Participants moyens',
                        data: monthlyParticipants,
                        borderColor: '#2ecc71',
                        backgroundColor: 'rgba(46, 204, 113, 0.1)',
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

        // Events pie chart
        const pieCtx = document.getElementById('eventsPieChart');
        if (pieCtx) {
            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Publiés', 'En attente', 'Rejetés'],
                    datasets: [{
                        data: [
                            statusDistribution['Publié'] || 0,
                            statusDistribution['Brouillon'] || 0,
                            statusDistribution['Rejeté'] || 0
                        ],
                        backgroundColor: [
                            'rgba(46, 204, 113, 0.8)',
                            'rgba(241, 196, 15, 0.8)',
                            'rgba(231, 76, 60, 0.8)'
                        ]
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

        // Ratings by enterprise
        const ratingsCtx = document.getElementById('ratingsChart');
        if (ratingsCtx && enterprisesData.length > 0) {
            // Get top 5 enterprises by rating
            const topEnterprises = [...enterprisesData]
                .sort((a, b) => (b.avg_rating || 0) - (a.avg_rating || 0))
                .slice(0, 5);
            
            new Chart(ratingsCtx, {
                type: 'bar',
                data: {
                    labels: topEnterprises.map(e => e.prenom + ' ' + e.nom),
                    datasets: [{
                        label: 'Note Moyenne',
                        data: topEnterprises.map(e => parseFloat(e.avg_rating) || 0),
                        backgroundColor: 'rgba(155, 89, 182, 0.8)'
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
                            max: 5,
                            ticks: { color: 'white' },
                            grid: { color: 'rgba(255,255,255,0.1)' }
                        },
                        x: {
                            ticks: { 
                                color: 'white',
                                maxRotation: 45,
                                minRotation: 45
                            },
                            grid: { color: 'rgba(255,255,255,0.1)' }
                        }
                    }
                }
            });
        }
    }

    // User role management
    function loadUserRole() {
        return localStorage.getItem('abelink_user_role') || USER_ROLES.USER;
    }

    function saveUserRole(role) {
        localStorage.setItem('abelink_user_role', role);
    }

    function changeUserRole() {
        const roleSelect = document.getElementById('roleSelect');
        const newRole = roleSelect.value;
        
        if (newRole && Object.values(USER_ROLES).includes(newRole)) {
            window.CURRENT_USER_ROLE = newRole;
            saveUserRole(newRole);
            updateUIForRole();
        }
    }

    function updateUIForRole() {
        const role = window.CURRENT_USER_ROLE || USER_ROLES.USER;
        const roleBadge = document.getElementById('roleBadge');
        
        // Update role badge
        if (roleBadge) {
            roleBadge.textContent = getRoleDisplayName(role);
            roleBadge.className = `role-badge ${role}`;
        }
        
        // Update role selector value
        const roleSelect = document.getElementById('roleSelect');
        if (roleSelect) {
            roleSelect.value = role;
        }
    }

    function getRoleDisplayName(role) {
        const displayNames = {
            [USER_ROLES.USER]: 'Utilisateur',
            [USER_ROLES.COMPANY]: 'Entreprise',
            [USER_ROLES.INCLUSION]: 'Responsable Inclusion',
            [USER_ROLES.ADMIN]: 'Administrateur'
        };
        return displayNames[role] || 'Utilisateur';
    }

    // Tab management
    function switchTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelectorAll('.tab').forEach(tab => {
            tab.classList.remove('active');
        });

        // Show selected tab
        document.getElementById(tabName + '-tab').classList.add('active');
        event.target.classList.add('active');
    }

    // Action functions
    function approveEvent(eventId) {
        if (confirm('Approuver cet événement ? Il sera visible par tous les utilisateurs.')) {
            performAction('approve_event', { event_id: eventId }, 'Événement approuvé avec succès !');
        }
    }

    function rejectEvent(eventId) {
        if (confirm('Rejeter cet événement ? Il ne sera pas visible par les utilisateurs.')) {
            performAction('reject_event', { event_id: eventId }, 'Événement rejeté.');
        }
    }

    function deleteEvent(eventId) {
        if (confirm('Supprimer définitivement cet événement ? Cette action est irréversible.')) {
            performAction('delete_event', { event_id: eventId }, 'Événement supprimé avec succès.');
        }
    }

    function approveEvaluation(evalId) {
        if (confirm('Approuver cette évaluation ? Elle sera visible par tous les utilisateurs.')) {
            performAction('approve_evaluation', { evaluation_id: evalId }, 'Évaluation approuvée !');
        }
    }

    function deleteEvaluation(evalId) {
        if (confirm('Supprimer cette évaluation ?')) {
            performAction('delete_evaluation', { evaluation_id: evalId }, 'Évaluation supprimée.');
        }
    }

    function unverifyEnterprise(enterpriseId) {
        if (confirm('Révoquer la vérification de cette entreprise ? Elle ne pourra plus créer d\'événements.')) {
            alert('Fonctionnalité à implémenter: révocation de la vérification entreprise');
        }
    }

    // AJAX helper function
    function performAction(action, data, successMessage) {
        const formData = new FormData();
        formData.append('action', action);
        
        for (const key in data) {
            formData.append(key, data[key]);
        }

        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(successMessage);
                location.reload(); // Refresh to show updated data
            } else {
                alert('Erreur: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de l\'opération');
        });
    }

    // Utility functions
    function renderStars(rating) {
        const numRating = parseFloat(rating) || 0;
        const fullStars = Math.floor(numRating);
        const hasHalfStar = numRating % 1 >= 0.5;
        const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
        
        let stars = '';
        for (let i = 0; i < fullStars; i++) {
            stars += '★';
        }
        if (hasHalfStar) {
            stars += '½';
        }
        for (let i = 0; i < emptyStars; i++) {
            stars += '☆';
        }
        
        return stars;
    }

    function truncateText(text, length) {
        if (!text) return '';
        return text.length > length ? text.substring(0, length) + '...' : text;
    }

    // Setup event listeners for search/filter
    function setupEventListeners() {
        // Add search/filter functionality here
        const searchEvents = document.getElementById('searchEvents');
        const searchEvaluations = document.getElementById('searchEvaluations');
        const searchEnterprises = document.getElementById('searchEnterprises');
        
        if (searchEvents) {
            searchEvents.addEventListener('input', filterEvents);
        }
        if (searchEvaluations) {
            searchEvaluations.addEventListener('input', filterEvaluations);
        }
        if (searchEnterprises) {
            searchEnterprises.addEventListener('input', filterEnterprises);
        }
    }

    function filterEvents() {
        const searchTerm = this.value.toLowerCase();
        const items = document.querySelectorAll('#eventsModerationList .admin-item');
        
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(searchTerm) ? 'flex' : 'none';
        });
    }

    function filterEvaluations() {
        const searchTerm = this.value.toLowerCase();
        const items = document.querySelectorAll('#evaluationsModerationList .admin-item');
        
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(searchTerm) ? 'flex' : 'none';
        });
    }

    function filterEnterprises() {
        const searchTerm = this.value.toLowerCase();
        const items = document.querySelectorAll('#enterprisesStatsList .admin-item');
        
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(searchTerm) ? 'flex' : 'none';
        });
    }

    // Placeholder functions for future implementation
    function viewEventDetails(eventId) {
        alert(`📋 Détails de l'événement ${eventId} - Redirection vers la page des événements`);
    }

    function viewEvaluationDetails(evalId) {
        alert(`⭐ Détails de l'évaluation ${evalId}`);
    }

    function viewEnterpriseDetails(enterpriseId) {
        alert(`🏢 Détails de l'entreprise ${enterpriseId}`);
    }

    // Make functions globally available
    window.switchTab = switchTab;
    window.changeUserRole = changeUserRole;
    window.approveEvent = approveEvent;
    window.rejectEvent = rejectEvent;
    window.deleteEvent = deleteEvent;
    window.approveEvaluation = approveEvaluation;
    window.deleteEvaluation = deleteEvaluation;
    window.unverifyEnterprise = unverifyEnterprise;
    window.viewEventDetails = viewEventDetails;
    window.viewEvaluationDetails = viewEvaluationDetails;
    window.viewEnterpriseDetails = viewEnterpriseDetails;
</script>
</body>
</html>
<?php
// Helper functions
function truncateText($text, $length) {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}

function renderStars($rating) {
    $numRating = floatval($rating);
    $fullStars = floor($numRating);
    $hasHalfStar = ($numRating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
    
    $stars = '';
    for ($i = 0; $i < $fullStars; $i++) {
        $stars .= '★';
    }
    if ($hasHalfStar) {
        $stars .= '½';
    }
    for ($i = 0; $i < $emptyStars; $i++) {
        $stars .= '☆';
    }
    
    return $stars;
}
?>