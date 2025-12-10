<?php
// admin_dashboard_view.php - View for Admin Dashboard

// Ensure data is properly set with defaults
if (!isset($stats) || !is_array($stats)) {
    $stats = [
        'totalEvents' => 0,
        'pendingEvents' => 0,
        'totalEvaluations' => 0,
        'totalEnterprises' => 0,
        'avgRating' => 0,
        'eventsThisMonth' => 0,
        'totalParticipants' => 0,
    ];
}

if (!isset($pendingEvents) || !is_array($pendingEvents)) {
    $pendingEvents = [];
}

if (!isset($reportedEvaluations) || !is_array($reportedEvaluations)) {
    $reportedEvaluations = [];
}

if (!isset($enterprisesStats) || !is_array($enterprisesStats)) {
    $enterprisesStats = [];
}

if (!isset($analyticsData) || !is_array($analyticsData)) {
    $analyticsData = [];
}

$defaultDistribution = ['Publié' => 0, 'Brouillon' => 0, 'Rejeté' => 0];
if (!isset($statusDistribution) || !is_array($statusDistribution)) {
    $statusDistribution = $defaultDistribution;
} else {
    foreach ($defaultDistribution as $k => $v) {
        if (!isset($statusDistribution[$k])) {
            $statusDistribution[$k] = 0;
        }
    }
}

if (!isset($monthlyEvents) || !is_array($monthlyEvents)) {
    $monthlyEvents = array_fill(0, 12, 0);
}

if (!isset($monthlyParticipants) || !is_array($monthlyParticipants)) {
    $monthlyParticipants = array_fill(0, 12, 0);
}

if (!isset($recentActivities) || !is_array($recentActivities)) {
    $recentActivities = [];
}
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
            background: var(--card-bg);
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

        .badge-pending {
            background: rgba(241, 196, 15, 0.2);
            color: #f1c40f;
        }

        .badge-reported {
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
            }
            
            .menu-toggle {
                display: block;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }

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

        /* Menu Toggle (Mobile) */
        .menu-toggle {
            display: none;
            background: var(--primary);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            font-size: 20px;
            cursor: pointer;
            transition: var(--transition);
        }

        .menu-toggle:hover {
            background: var(--primary-light);
        }

        /* Role Selector */
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

        /* Stars */
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

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
            background: #111827;
            border-radius: 12px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            margin: 5% auto;
            padding: 0;
            border-radius: 16px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .modal-header {
            padding: 20px 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(0, 0, 0, 0.2);
        }

        .modal-header h2 {
            margin: 0;
            color: white;
            font-size: 24px;
        }

        .close-modal {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-modal:hover,
        .close-modal:focus {
            color: white;
        }

        .modal-body {
            padding: 30px;
            color: white;
        }

        .event-detail-section {
            margin-bottom: 25px;
        }

        .event-detail-section h3 {
            margin: 0 0 15px 0;
            color: #3498db;
            font-size: 18px;
            border-bottom: 2px solid rgba(52, 152, 219, 0.3);
            padding-bottom: 8px;
        }

        .event-detail-row {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 15px;
            margin-bottom: 12px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }

        .event-detail-label {
            font-weight: 600;
            color: #ccc;
        }

        .event-detail-value {
            color: white;
        }

        .event-description {
            padding: 15px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            line-height: 1.6;
            margin-top: 10px;
        }

        .loading-spinner {
            text-align: center;
            padding: 40px;
            color: #ccc;
        }

        .loading-spinner::after {
            content: '';
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top-color: #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
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
           <div class="logo site-title">
    <a href="dashboard.php" style="text-decoration:none; display:flex; align-items:center; gap:10px;">
        <span class="letter-a">A</span>
        <span class="letter-b">B</span>
        <span class="letter-l">L</span>
        <span class="letter-e">E</span>
        <span class="letter-link">LINK</span>
    </a>
</div>
<style>
    /* ===== AbleLink Logo (Admin Version) ===== */

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

/* Hover animation */
.site-title:hover a {
    transform: scale(1.05);
}

.site-title a span:hover {
    transform: translateY(-2px);
    transition: 0.2s ease;
}

</style>

            <div class="nav-links">
                <a href="admin_dashboard.php" class="nav-link active">
                    <i class="fas fa-home"></i>
                    <span>Tableau de Bord</span>
                </a>
                <a href="../view/FrontOffice/evaluations-evenements.php" class="nav-link">
                    <i class="fas fa-globe"></i>
                    <span>Accueil</span>
                </a>
                <a href="../view/FrontOffice/evaluations-evenements.php" class="nav-link">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Événements</span>
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
                <a href="#" class="nav-link" onclick="showSection('moderation')">
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
                    <h1>Tableau de Bord Admin</h1>
                    <p>Bienvenue dans votre espace d'administration Nexus</p>
                </div>
                <div class="user-menu" style="position: relative; display:flex; align-items:center; gap:10px;">

    <!-- Avatar Blue -->
    <div class="user-avatar" onclick="toggleProfileMenu()" 
         style="cursor:pointer; background: linear-gradient(135deg, var(--primary), var(--secondary)); 
                width:45px; height:45px; border-radius:50%; 
                display:flex; align-items:center; justify-content:center; 
                color:white; font-size:18px; box-shadow: var(--shadow);">
        <i class="fas fa-user"></i>
    </div>

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

        
       <a href="../view/Backoffice/profileadmin.php" class="profile-item">
    <i class="fas fa-user"></i> Voir le profil
</a>

        <a href="#" class="profile-item"><i class="fas fa-cog"></i> Paramètres du compte</a>
        <a href="#" class="profile-item"><i class="fas fa-bell"></i> Notifications</a>
        <a href="#" class="profile-item"><i class="fas fa-random"></i> Changer de compte</a>
        <a href="#" class="profile-item"><i class="fas fa-question-circle"></i> Centre d'aide</a>

        <a href="../view/general/logout.php" class="profile-item" style="color:#ef4444;">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>

    </div>

</div>
<style>
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
<script>
    function toggleProfileMenu() {
    const menu = document.getElementById("profileMenu");
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
}

// Close menu when clicking outside
document.addEventListener("click", function(e) {
    const menu = document.getElementById("profileMenu");
    const avatar = document.querySelector(".user-avatar");

    if (!menu.contains(e.target) && !avatar.contains(e.target)) {
        menu.style.display = "none";
    }
});

</script>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <div class="action-card" onclick="switchTab('moderation')">
                    <div class="action-icon" style="background: linear-gradient(135deg, var(--primary), var(--primary-light));">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="action-title">Modérer Événements</div>
                    <div class="action-desc">Approuvez ou rejetez les événements en attente</div>
                </div>

                <div class="action-card" onclick="switchTab('evaluations')">
                    <div class="action-icon" style="background: linear-gradient(135deg, var(--secondary), #38bdf8);">
                        <i class="fas fa-flag"></i>
                    </div>
                    <div class="action-title">Évaluations Signalées</div>
                    <div class="action-desc">Gérez les évaluations signalées par les utilisateurs</div>
                </div>

                <div class="action-card" onclick="switchTab('enterprises')">
                    <div class="action-icon" style="background: linear-gradient(135deg, var(--success), #34d399);">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="action-title">Entreprises</div>
                    <div class="action-desc">Statistiques et performances des entreprises</div>
                </div>

                <div class="action-card" onclick="switchTab('analytics')">
                    <div class="action-icon" style="background: linear-gradient(135deg, var(--warning), #fbbf24);">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="action-title">Analytiques</div>
                    <div class="action-desc">Graphiques et rapports détaillés</div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary), var(--primary-light));">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo htmlspecialchars($stats['totalEvents'] ?? 0); ?></div>
                        <div class="stat-label">Événements Totaux</div>
                        <div class="stat-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>12% ce mois</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, var(--secondary), #38bdf8);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo htmlspecialchars($stats['pendingEvents'] ?? 0); ?></div>
                        <div class="stat-label">En Attente</div>
                        <div class="stat-trend trend-down">
                            <i class="fas fa-arrow-down"></i>
                            <span>5% ce mois</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, var(--success), #34d399);">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo htmlspecialchars($stats['avgRating'] ?? 0); ?></div>
                        <div class="stat-label">Note Moyenne</div>
                        <div class="stat-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>0.3 ce mois</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, var(--warning), #fbbf24);">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo htmlspecialchars($stats['totalEnterprises'] ?? 0); ?></div>
                        <div class="stat-label">Entreprises</div>
                        <div class="stat-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            <span>8% ce mois</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-section">
                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title">Activité des Événements</div>
                        <div class="chart-actions">
                            <span class="badge badge-info">Mensuel</span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="eventsChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title">Statut des Événements</div>
                        <div class="chart-actions">
                            <span class="badge badge-info">Total</span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
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
        <?php if (empty($recentActivities)): ?>
            <div class="empty-state">📊 Aucune activité récente</div>
        <?php else: ?>
            <?php foreach ($recentActivities as $activity): ?>
                <div class="activity-item">
                    <div class="activity-icon" style="background: <?php echo $activity['color'] ?? '#3498db'; ?>;">
                        <i class="<?php echo $activity['icon'] ?? 'fas fa-info-circle'; ?>"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title"><?php echo htmlspecialchars($activity['title'] ?? 'Activité'); ?></div>
                        <div class="activity-desc"><?php echo htmlspecialchars($activity['description'] ?? 'Description'); ?></div>
                    </div>
                    <div class="activity-time"><?php echo $activity['time'] ?? 'Maintenant'; ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

            <!-- Moderation Tab -->
            <div id="moderation-tab" class="tab-content active">
                <div class="chart-card">
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
                                        <h4 style="margin: 0 0 8px 0; color: white;"><?php echo htmlspecialchars($event['titre'] ?? 'Titre non disponible'); ?></h4>
                                        <p style="margin: 0 0 8px 0; color: #ccc;">
                                            <?php echo htmlspecialchars(($event['prenom'] ?? '') . ' ' . ($event['nom'] ?? '')); ?> • 
                                            <?php echo date('d/m/Y H:i', strtotime($event['date'] ?? 'now')); ?> • 
                                            <?php echo htmlspecialchars($event['lieu'] ?? 'Lieu non spécifié'); ?>
                                        </p>
                                        <p style="margin: 0 0 8px 0; font-size: 14px; color: #ccc;">
                                            <?php echo htmlspecialchars(truncateText($event['description'] ?? '', 120)); ?>
                                        </p>
                                        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                            <span class="badge pending">⏳ En attente</span>
                                            <span style="color: #ccc; font-size: 14px;">👥 <?php echo $event['participants_count'] ?? 0; ?> participants</span>
                                            <?php if (isset($event['accessibilite']) && $event['accessibilite']): ?>
                                                <span style="color: #ccc; font-size: 14px;">♿ <?php echo htmlspecialchars($event['accessibilite']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="item-actions">
                                        <button class="cta-button success" onclick="approveEvent(<?php echo $event['id'] ?? 0; ?>)">✅ Approuver</button>
                                        <button class="cta-button danger" onclick="rejectEvent(<?php echo $event['id'] ?? 0; ?>)">❌ Rejeter</button>
                                        <button class="cta-button" onclick="viewEventDetails(<?php echo $event['id'] ?? 0; ?>)">👁️ Détails</button>
                                        <button class="cta-button danger" onclick="deleteEvent(<?php echo $event['id'] ?? 0; ?>)">🗑️ Supprimer</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Evaluations Moderation Tab -->
            <div id="evaluations-tab" class="tab-content">
                <div class="chart-card">
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
                                        <h4 style="margin: 0 0 8px 0; color: white;"><?php echo htmlspecialchars($evaluation['event_titre'] ?? 'Événement inconnu'); ?></h4>
                                        <p style="margin: 0 0 8px 0; color: #ccc;">👤 Évalué par <?php echo htmlspecialchars(($evaluation['prenom'] ?? '') . ' ' . ($evaluation['nom'] ?? '')); ?></p>
                                        <div class="rating">
                                            <span class="stars"><?php echo renderStars($evaluation['note'] ?? 0); ?></span>
                                            <span style="margin-left: 8px; color: #ccc;"><?php echo $evaluation['note'] ?? 0; ?>/5</span>
                                        </div>
                                        <p style="margin: 8px 0; font-size: 14px; color: #ccc; font-style: italic;">
                                            "<?php echo htmlspecialchars(truncateText($evaluation['commentaire'] ?? '', 150)); ?>"
                                        </p>
                                        <div style="display: flex; gap: 10px; align-items: center;">
                                            <span class="badge reported">🚩 Signalée</span>
                                            <span style="color: #ccc; font-size: 12px;">
                                                <?php echo date('d/m/Y H:i', strtotime($evaluation['dateEvaluation'] ?? 'now')); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="item-actions">
                                        <button class="cta-button success" onclick="approveEvaluation(<?php echo $evaluation['id'] ?? 0; ?>)">✅ Approuver</button>
                                        <button class="cta-button danger" onclick="deleteEvaluation(<?php echo $evaluation['id'] ?? 0; ?>)">🗑️ Supprimer</button>
                                        <button class="cta-button" onclick="viewEvaluationDetails(<?php echo $evaluation['id'] ?? 0; ?>)">👁️ Détails</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Enterprises Tab -->
            <div id="enterprises-tab" class="tab-content">
                <div class="chart-card">
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
                                            <?php echo htmlspecialchars(($enterprise['prenom'] ?? '') . ' ' . ($enterprise['nom'] ?? '')); ?>
                                        </h4>
                                        <p style="margin: 0 0 12px 0; color: #ccc;"><?php echo htmlspecialchars($enterprise['email'] ?? ''); ?></p>
                                        <div class="enterprise-stats">
                                            <div class="enterprise-stat" style="background: rgba(52, 152, 219, 0.1);">
                                                <div style="font-size: 1.5rem; font-weight: bold; color: #3498db;">
                                                    <?php echo $enterprise['events_count'] ?? 0; ?>
                                                </div>
                                                <div style="font-size: 0.9rem; color: #ccc;">Événements</div>
                                            </div>
                                            <div class="enterprise-stat" style="background: rgba(46, 204, 113, 0.1);">
                                                <div style="font-size: 1.5rem; font-weight: bold; color: #2ecc71;">
                                                    <?php echo isset($enterprise['avg_rating']) ? round($enterprise['avg_rating'], 1) : '0.0'; ?>
                                                </div>
                                                <div style="font-size: 0.9rem; color: #ccc;">Note Moyenne</div>
                                            </div>
                                            <div class="enterprise-stat" style="background: rgba(155, 89, 182, 0.1);">
                                                <div style="font-size: 1.5rem; font-weight: bold; color: #9b59b6;">
                                                    <?php echo isset($enterprise['participation_rate']) ? round($enterprise['participation_rate']) : 0; ?>%
                                                </div>
                                                <div style="font-size: 0.9rem; color: #ccc;">Participation</div>
                                            </div>
                                        </div>
                                        <div style="margin-top: 10px;">
                                            <span class="badge verified">✅ Vérifiée</span>
                                        </div>
                                    </div>
                                    <div class="item-actions">
                                        <button class="cta-button" onclick="viewEnterpriseDetails(<?php echo $enterprise['idUtilisateur'] ?? 0; ?>)">👁️ Détails</button>
                                        <button class="cta-button danger" onclick="unverifyEnterprise(<?php echo $enterprise['idUtilisateur'] ?? 0; ?>)">🚫 Révoquer</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Analytics Tab -->
            <div id="analytics-tab" class="tab-content">
                <div class="chart-card">
                    <h2 style="margin-bottom: 20px; color: white;">📊 Analytiques et Rapports</h2>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-value" id="avgRating"><?php echo $stats['avgRating'] ?? 0; ?></div>
                            <div class="stat-label">Note Moyenne</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" id="participationRate">
                                <?php echo (($stats['totalEvents'] ?? 0) > 0) ? round((($stats['totalParticipants'] ?? 0) / (($stats['totalEvents'] ?? 1) * 50)) * 100) : 0; ?>%
                            </div>
                            <div class="stat-label">Taux de Participation</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" id="topEnterprise">
                                <?php 
                                $topEnterprise = null;
                                $topRating = 0;
                                foreach ($enterprisesStats as $enterprise) {
                                    $currentRating = $enterprise['avg_rating'] ?? 0;
                                    if ($currentRating > $topRating) {
                                        $topRating = $currentRating;
                                        $topEnterprise = ($enterprise['prenom'] ?? '') . ' ' . ($enterprise['nom'] ?? '');
                                    }
                                }
                                echo $topEnterprise ? substr($topEnterprise, 0, 8) . '...' : '-';
                                ?>
                            </div>
                            <div class="stat-label">Entreprise Top</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" id="eventsThisMonth"><?php echo $stats['eventsThisMonth'] ?? 0; ?></div>
                            <div class="stat-label">Événements ce Mois</div>
                        </div>
                    </div>

                    <div class="chart-container" style="margin-top: 20px;">
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
        // Chart data from PHP
        const monthlyEvents = <?php echo json_encode($monthlyEvents); ?>;
        const monthlyParticipants = <?php echo json_encode($monthlyParticipants); ?>;
        const statusDistribution = <?php echo json_encode($statusDistribution); ?>;
        const enterprisesData = <?php echo json_encode($enterprisesStats); ?>;

        // User roles and permissions
        const USER_ROLES = {
            USER: 'user',
            COMPANY: 'company',
            INCLUSION: 'inclusion',
            ADMIN: 'admin'
        };

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

            // Status distribution chart
            const statusCtx = document.getElementById('statusChart');
            if (statusCtx) {
                new Chart(statusCtx, {
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
                        labels: topEnterprises.map(e => (e.prenom || '') + ' ' + (e.nom || '')).map(name => name.length > 10 ? name.substring(0, 10) + '...' : name),
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
        function showSection(sectionId) {
            // Handle submenu toggling
            const usersSubmenu = document.getElementById('usersSubmenu');
            if (sectionId === 'users') {
                usersSubmenu.style.display = usersSubmenu.style.display === 'none' ? 'block' : 'none';
                return; // Don't hide other sections yet
            }
            
            // Hide all sections
            document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.dashboard-section').forEach(el => el.style.display = 'none');
            
            // Show requested section
            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.style.display = 'block';
            }
            
            // Update active state in sidebar
            document.querySelectorAll('.nav-link').forEach(el => el.classList.remove('active'));
            // Find the link that called this function (approximate)
            const links = document.querySelectorAll(`[onclick="showSection('${sectionId}')"]`);
            if (links.length > 0) {
                links[0].classList.add('active');
            }
        }

        // Action functions
        function approveEvent(eventId) {
            if (confirm('Approuver cet événement ? Il sera visible par tous les utilisateurs.')) {
                performAction('approve_event', { eventId: eventId }, 'Événement approuvé avec succès !');
            }
        }

        function rejectEvent(eventId) {
            if (confirm('Rejeter cet événement ? Il ne sera pas visible par les utilisateurs.')) {
                performAction('reject_event', { eventId: eventId }, 'Événement rejeté.');
            }
        }

        function deleteEvent(eventId) {
            if (confirm('Supprimer définitivement cet événement ? Cette action est irréversible.')) {
                performAction('delete_event', { eventId: eventId }, 'Événement supprimé avec succès.');
            }
        }

        function approveEvaluation(evalId) {
            if (confirm('Approuver cette évaluation ? Elle sera visible par tous les utilisateurs.')) {
                performAction('approve_evaluation', { evaluationId: evalId }, 'Évaluation approuvée !');
            }
        }

        function deleteEvaluation(evalId) {
            if (confirm('Supprimer cette évaluation ?')) {
                performAction('reject_evaluation', { evaluationId: evalId }, 'Évaluation supprimée.');
            }
        }

        function unverifyEnterprise(enterpriseId) {
            if (confirm('Révoquer la vérification de cette entreprise ? Elle ne pourra plus créer d\'événements.')) {
                alert('Fonctionnalité à implémenter: révocation de la vérification entreprise');
            }
        }

        // AJAX helper function
        function performAction(action, data, successMessage) {
            fetch('admin_dashboard.php', {
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

        // Tab switching function
        function switchTab(tabName) {
            // Hide all tab contents
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => {
                tab.style.display = 'none';
            });
            
            // Show the selected tab
            const selectedTab = document.getElementById(tabName + '-tab');
            if (selectedTab) {
                selectedTab.style.display = 'block';
            }
            
            // Update active state on action cards
            const actionCards = document.querySelectorAll('.action-card');
            actionCards.forEach(card => {
                card.style.opacity = '0.7';
            });
            
            // Highlight the clicked action card
            if (event && event.currentTarget) {
                event.currentTarget.style.opacity = '1';
            }
        }

        // Action handlers for evaluations
        function approveEvaluation(evaluationId) {
            if (!confirm('Approuver cette évaluation ?')) return;
            
            fetch('admin_moderation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'approve_evaluation',
                    evaluationId: evaluationId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ Évaluation approuvée avec succès');
                    location.reload();
                } else {
                    alert('❌ Erreur: ' + (data.message || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Erreur lors de l\'approbation');
            });
        }

        function deleteEvaluation(evaluationId) {
            if (!confirm('Supprimer définitivement cette évaluation ?')) return;
            
            fetch('admin_moderation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'reject_evaluation',
                    evaluationId: evaluationId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ Évaluation supprimée avec succès');
                    location.reload();
                } else {
                    alert('❌ Erreur: ' + (data.message || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Erreur lors de la suppression');
            });
        }

        // Action handler for enterprises
        function unverifyEnterprise(enterpriseId) {
            if (!confirm('Révoquer la vérification de cette entreprise ?')) return;
            
            fetch('admin_moderation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'unverify_enterprise',
                    enterpriseId: enterpriseId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ Entreprise révoquée avec succès');
                    location.reload();
                } else {
                    alert('❌ Erreur: ' + (data.message || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Erreur lors de la révocation');
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

        // Event Details Modal Functions
        function viewEventDetails(eventId) {
            const modal = document.getElementById('eventDetailsModal');
            const modalBody = document.getElementById('eventDetailsBody');
            const modalTitle = document.getElementById('eventDetailsTitle');
            
            // Show modal with loading state
            modal.style.display = 'block';
            modalTitle.textContent = 'Détails de l\'événement';
            modalBody.innerHTML = '<div class="loading-spinner">Chargement...</div>';
            
            // Fetch event details from server
            fetch(`get_event_details.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'get',
                    id: eventId
                })
            })
            .then(async response => {
                if (!response.ok) {
                    let errorMessage = 'Network response was not ok';
                    try {
                        const errorData = await response.json();
                        if (errorData.message) {
                            errorMessage = errorData.message;
                        }
                    } catch (e) {
                        // Could not parse JSON, stick to default message
                    }
                    throw new Error(errorMessage);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.event) {
                    displayEventDetails(data.event);
                } else {
                    modalBody.innerHTML = `
                        <div style="text-align: center; padding: 40px; color: #e74c3c;">
                            <h3>Erreur</h3>
                            <p>Impossible de charger les détails de l'événement.</p>
                            <p style="color: #ccc; font-size: 14px;">${data.message || 'Erreur inconnue'}</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching event details:', error);
                modalBody.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #e74c3c;">
                        <h3>Erreur de chargement</h3>
                        <p>${error.message}</p>
                        <button class="cta-button" onclick="viewEventDetails(${eventId})" style="margin-top: 20px;">Réessayer</button>
                    </div>
                `;
            });
        }

        function displayEventDetails(event) {
            const modalBody = document.getElementById('eventDetailsBody');
            const modalTitle = document.getElementById('eventDetailsTitle');
            
            modalTitle.textContent = event.titre || 'Détails de l\'événement';
            
            // Format date
            const formatDate = (dateString) => {
                if (!dateString) return 'Non spécifiée';
                const date = new Date(dateString);
                return date.toLocaleDateString('fr-FR', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            };
            
            // Get status badge color
            const getStatusBadge = (statut) => {
                const badges = {
                    'Publié': '<span style="background: rgba(46, 204, 113, 0.2); color: #2ecc71; padding: 4px 12px; border-radius: 12px; font-size: 12px;">✅ Publié</span>',
                    'Brouillon': '<span style="background: rgba(241, 196, 15, 0.2); color: #f1c40f; padding: 4px 12px; border-radius: 12px; font-size: 12px;">⏳ Brouillon</span>',
                    'Rejeté': '<span style="background: rgba(231, 76, 60, 0.2); color: #e74c3c; padding: 4px 12px; border-radius: 12px; font-size: 12px;">❌ Rejeté</span>'
                };
                return badges[statut] || `<span style="background: rgba(255,255,255,0.1); padding: 4px 12px; border-radius: 12px; font-size: 12px;">${statut}</span>`;
            };
            
            const html = `
                <div class="event-detail-section">
                    <h3>📋 Informations générales</h3>
                    <div class="event-detail-row">
                        <div class="event-detail-label">Titre:</div>
                        <div class="event-detail-value"><strong>${escapeHtml(event.titre || 'N/A')}</strong></div>
                    </div>
                    <div class="event-detail-row">
                        <div class="event-detail-label">Statut:</div>
                        <div class="event-detail-value">${getStatusBadge(event.statut || 'Brouillon')}</div>
                    </div>
                    <div class="event-detail-row">
                        <div class="event-detail-label">Date:</div>
                        <div class="event-detail-value">${formatDate(event.date)}</div>
                    </div>
                    <div class="event-detail-row">
                        <div class="event-detail-label">Lieu:</div>
                        <div class="event-detail-value">${escapeHtml(event.lieu || 'N/A')}</div>
                    </div>
                    <div class="event-detail-row">
                        <div class="event-detail-label">Thème:</div>
                        <div class="event-detail-value">${escapeHtml(event.theme || 'N/A')}</div>
                    </div>
                </div>

                <div class="event-detail-section">
                    <h3>📝 Description</h3>
                    <div class="event-description">
                        ${escapeHtml(event.description || 'Aucune description disponible.')}
                    </div>
                </div>

                <div class="event-detail-section">
                    <h3>👤 Organisateur</h3>
                    <div class="event-detail-row">
                        <div class="event-detail-label">Nom:</div>
                        <div class="event-detail-value">${escapeHtml((event.prenom || '') + ' ' + (event.nom || ''))}</div>
                    </div>
                    <div class="event-detail-row">
                        <div class="event-detail-label">ID Utilisateur:</div>
                        <div class="event-detail-value">${event.idUtilisateur || 'N/A'}</div>
                    </div>
                </div>

                <div class="event-detail-section">
                    <h3>👥 Participants</h3>
                    <div class="event-detail-row">
                        <div class="event-detail-label">Inscrits:</div>
                        <div class="event-detail-value">${event.inscrits || 0}</div>
                    </div>
                    <div class="event-detail-row">
                        <div class="event-detail-label">Maximum:</div>
                        <div class="event-detail-value">${event.participants_max || 50}</div>
                    </div>
                    <div class="event-detail-row">
                        <div class="event-detail-label">Total participants:</div>
                        <div class="event-detail-value">${event.participants_count || 0}</div>
                    </div>
                </div>

                <div class="event-detail-section">
                    <h3>⭐ Évaluations</h3>
                    <div class="event-detail-row">
                        <div class="event-detail-label">Nombre d'évaluations:</div>
                        <div class="event-detail-value">${event.evaluations_count || 0}</div>
                    </div>
                </div>

                ${event.accessibilite ? `
                <div class="event-detail-section">
                    <h3>♿ Accessibilité</h3>
                    <div class="event-detail-value" style="padding: 10px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                        ${escapeHtml(event.accessibilite)}
                    </div>
                </div>
                ` : ''}

                <div class="event-detail-section">
                    <h3>🆔 Informations techniques</h3>
                    <div class="event-detail-row">
                        <div class="event-detail-label">ID Événement:</div>
                        <div class="event-detail-value" style="font-family: monospace; color: #3498db;">${event.id || 'N/A'}</div>
                    </div>
                </div>
            `;
            
            modalBody.innerHTML = html;
        }

        function closeEventDetailsModal() {
            document.getElementById('eventDetailsModal').style.display = 'none';
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('eventDetailsModal');
            if (event.target === modal) {
                closeEventDetailsModal();
            }
        }

        function formatDate(dateString) {
            if (!dateString) return 'Date inconnue';
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function viewEvaluationDetails(evalId) {
            console.log('Fetching evaluation details for ID:', evalId);
            
            // Fetch evaluation details from the server
            fetch(`admin_dashboard.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'get_evaluation_details',
                    evaluationId: evalId
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success && data.evaluation) {
                    showEvaluationDetailsModal(data.evaluation);
                } else {
                    console.error('Error from server:', data);
                    alert('❌ Erreur: ' + (data.message || 'Impossible de charger les détails'));
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('❌ Erreur lors du chargement des détails');
            });
        }

        function showEvaluationDetailsModal(evaluation) {
            // Create or get modal
            let modal = document.getElementById('evaluationDetailsModal');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'evaluationDetailsModal';
                modal.className = 'modal';
                modal.style.cssText = 'display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(5px);';
                document.body.appendChild(modal);
            }

            const modalContent = `
                <div style="background: var(--card-bg); margin: 5% auto; padding: 30px; border-radius: 16px; width: 90%; max-width: 700px; position: relative; border: 1px solid rgba(255,255,255,0.1);">
                    <button onclick="closeEvaluationDetailsModal()" style="position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.1); border: none; color: white; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; font-size: 20px; transition: all 0.2s;">✕</button>
                    
                    <h2 style="margin: 0 0 25px 0; color: white; font-size: 24px;">
                        ⭐ Détails de l'Évaluation
                    </h2>

                    <div style="display: grid; gap: 20px;">
                        <!-- Event Info -->
                        <div style="background: rgba(255,255,255,0.05); padding: 20px; border-radius: 12px; border-left: 4px solid #3498db;">
                            <h3 style="margin: 0 0 12px 0; color: white; font-size: 18px;">📅 Événement</h3>
                            <div style="color: #ccc; font-size: 16px; font-weight: 600;">${escapeHtml(evaluation.event_titre || 'Événement inconnu')}</div>
                            <div style="color: #999; font-size: 14px; margin-top: 5px;">${formatDate(evaluation.event_date || '')}</div>
                        </div>

                        <!-- User Info -->
                        <div style="background: rgba(255,255,255,0.05); padding: 20px; border-radius: 12px; border-left: 4px solid #9b59b6;">
                            <h3 style="margin: 0 0 12px 0; color: white; font-size: 18px;">👤 Évalué par</h3>
                            <div style="color: #ccc; font-size: 16px;">${escapeHtml((evaluation.prenom || '') + ' ' + (evaluation.nom || ''))}</div>
                            <div style="color: #999; font-size: 14px; margin-top: 5px;">Date: ${formatDate(evaluation.dateEvaluation || '')}</div>
                        </div>

                        <!-- Ratings -->
                        <div style="background: rgba(255,255,255,0.05); padding: 20px; border-radius: 12px; border-left: 4px solid #f1c40f;">
                            <h3 style="margin: 0 0 15px 0; color: white; font-size: 18px;">⭐ Notes</h3>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div>
                                    <div style="color: #999; font-size: 13px; margin-bottom: 5px;">Accessibilité</div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span style="color: #f1c40f; font-size: 20px;">${renderStars(evaluation.note_accessibilite || 0)}</span>
                                        <span style="color: white; font-weight: 600; font-size: 18px;">${evaluation.note_accessibilite || 0}/5</span>
                                    </div>
                                </div>
                                <div>
                                    <div style="color: #999; font-size: 13px; margin-bottom: 5px;">Inclusion</div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span style="color: #f1c40f; font-size: 20px;">${renderStars(evaluation.note_inclusion || 0)}</span>
                                        <span style="color: white; font-weight: 600; font-size: 18px;">${evaluation.note_inclusion || 0}/5</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Comment -->
                        ${evaluation.commentaire && evaluation.commentaire !== 'Not yet evaluated' ? `
                        <div style="background: rgba(255,255,255,0.05); padding: 20px; border-radius: 12px; border-left: 4px solid #2ecc71;">
                            <h3 style="margin: 0 0 12px 0; color: white; font-size: 18px;">💬 Commentaire</h3>
                            <div style="color: #ccc; font-size: 15px; line-height: 1.6; font-style: italic;">
                                "${escapeHtml(evaluation.commentaire)}"
                            </div>
                        </div>
                        ` : ''}

                        <!-- Status -->
                        <div style="background: rgba(255,255,255,0.05); padding: 20px; border-radius: 12px; border-left: 4px solid ${evaluation.signalee ? '#e74c3c' : '#2ecc71'};">
                            <h3 style="margin: 0 0 12px 0; color: white; font-size: 18px;">🏷️ Statut</h3>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <span class="badge ${evaluation.signalee ? 'badge-reported' : 'badge-success'}">
                                    ${evaluation.signalee ? '🚩 Signalée' : '✅ Approuvée'}
                                </span>
                                <span style="color: #999; font-size: 14px;">${evaluation.etat_moderation || 'Visible'}</span>
                            </div>
                        </div>

                        <!-- Technical Info -->
                        <div style="background: rgba(255,255,255,0.03); padding: 15px; border-radius: 8px;">
                            <div style="color: #666; font-size: 12px; font-family: monospace;">
                                ID Évaluation: ${evaluation.id || 'N/A'} | ID Événement: ${evaluation.idEvenement || 'N/A'} | ID Utilisateur: ${evaluation.idUtilisateur || 'N/A'}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            modal.innerHTML = modalContent;
            modal.style.display = 'block';
        }

        function closeEvaluationDetailsModal() {
            const modal = document.getElementById('evaluationDetailsModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function renderStars(rating) {
            const numRating = parseFloat(rating) || 0;
            const fullStars = Math.floor(numRating);
            const hasHalfStar = (numRating - fullStars) >= 0.5;
            let stars = '';
            
            for (let i = 0; i < fullStars; i++) {
                stars += '★';
            }
            if (hasHalfStar) {
                stars += '½';
            }
            for (let i = fullStars + (hasHalfStar ? 1 : 0); i < 5; i++) {
                stars += '☆';
            }
            
            return stars;
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