<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../general/signin.php');
    exit;
}

$eventId = $_GET['eventId'] ?? null;

if (!$eventId) {
    header('Location: ../../Control/historique.php');
    exit;
}

// Get user information
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'] ?? 'Utilisateur';

require_once __DIR__ . '/../../Control/config.php';
require_once __DIR__ . '/../../Control/UserController.php';
require_once __DIR__ . '/../../Model/User.php';

$controller = new UserController();
$user = $controller->showUser($user_id);

function getPhotoUrl($photo, $prenom, $nom) {
    if ($photo && file_exists(__DIR__ . '/../../uploads/profiles/' . $photo)) {
        return '../../uploads/profiles/' . $photo;
    }
    return '../general/img/team/team-1.jpg';
}

$user_photo = $user ? getPhotoUrl($user->getPhoto(), $user->getPrenom(), $user->getNom()) : '../general/img/team/team-1.jpg';
$user_name = $user ? $user->getPrenom() . ' ' . $user->getNom() : 'Utilisateur';

$role_mapping = [
    'Admin' => 'admin',
    'Entreprise' => 'company', 
    'Utilisateur' => 'user',
    'Inclusion' => 'inclusion'
];

if ($user) {
    $db_role = $user->getRole();
    $_SESSION['user_role'] = $db_role;
    $current_role = $db_role;
} else {
    $current_role = $_SESSION['user_role'] ?? 'Utilisateur';
}

$user_role = $current_role;
$js_role = $role_mapping[$current_role] ?? 'user';

$role_display_names = [
    'admin' => 'Administrateur',
    'company' => 'Entreprise',
    'user' => 'Utilisateur',
    'inclusion' => 'Responsable Inclusion'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Témoignages - AbeLink</title>
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../videograph-master/videograph-master/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="../../videograph-master/videograph-master/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="../../videograph-master/videograph-master/css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="../../videograph-master/videograph-master/css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="../../videograph-master/videograph-master/css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="../../videograph-master/videograph-master/css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="../../videograph-master/videograph-master/css/style.css" type="text/css">
    <link rel="stylesheet" href="css/theme-toggle.css" type="text/css">
    <style>
        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
        }

        .page-header {
            text-align: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .page-header h1 {
            color: white;
            margin: 0 0 10px;
            font-size: 32px;
        }

        .page-header p {
            color: rgba(255,255,255,0.9);
            margin: 0;
        }

        .event-info-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .event-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-item i {
            color: #667eea;
            font-size: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 14px;
        }

        .evaluations-container {
            display: grid;
            gap: 20px;
        }

        .evaluation-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 25px;
            transition: transform 0.2s;
        }

        .evaluation-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px var(--shadow);
        }

        .evaluation-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-details h4 {
            margin: 0;
            color: var(--text-primary);
            font-size: 16px;
        }

        .evaluation-date {
            color: var(--text-muted);
            font-size: 13px;
        }

        .report-btn {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.3);
            color: #e74c3c;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s;
        }

        .report-btn:hover:not(:disabled) {
            background: rgba(231, 76, 60, 0.2);
        }

        .report-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .ratings-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .rating-item {
            background: rgba(255,255,255,0.05);
            padding: 12px;
            border-radius: 8px;
        }

        .rating-label {
            color: var(--text-muted);
            font-size: 13px;
            margin-bottom: 5px;
        }

        .stars {
            color: #f1c40f;
            font-size: 16px;
        }

        .comment-box {
            background: rgba(255,255,255,0.05);
            padding: 15px;
            border-radius: 8px;
            border-left: 3px solid #667eea;
        }

        .comment-text {
            color: var(--text-secondary);
            font-style: italic;
            line-height: 1.6;
        }

        .no-evaluations {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .no-evaluations i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .loading {
            text-align: center;
            padding: 40px;
        }

        .loading-spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 4px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #667eea;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.2s;
            margin-bottom: 20px;
        }

        .back-btn:hover {
            background: var(--bg-hover);
            transform: translateX(-3px);
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: var(--bg-card);
            margin: 15% auto;
            padding: 30px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
        }

        .modal-header {
            margin-bottom: 20px;
        }

        .modal-header h3 {
            margin: 0;
            color: var(--text-primary);
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
        }

        .btn-cancel {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .btn-confirm {
            background: #e74c3c;
            color: white;
        }

        .btn:hover {
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <!-- Theme Toggle Button -->
    <button id="themeToggle" class="theme-toggle-btn" onclick="toggleTheme()" aria-label="Passer en mode clair">
        <i class="fa fa-sun-o"></i>
    </button>

    <section class="services spad">
        <div class="container">
            <a href="../../Control/historique.php" class="back-btn">
                <i class="fa fa-arrow-left"></i>
                Retour à l'historique
            </a>

            <div id="loadingContainer" class="loading">
                <div class="loading-spinner"></div>
                <p>Chargement des témoignages...</p>
            </div>

            <div id="contentContainer" style="display: none;">
                <div class="page-header" id="eventHeader">
                    <h1 id="eventTitle">Témoignages</h1>
                    <p id="eventSubtitle"></p>
                </div>

                <div class="event-info-card" id="eventInfo" style="display: none;">
                    <!-- Event details will be loaded here -->
                </div>

                <div class="stats-grid" id="statsGrid" style="display: none;">
                    <!-- Statistics will be loaded here -->
                </div>

                <h2 style="margin-bottom: 20px; color: var(--text-primary);">
                    <i class="fa fa-comments"></i> Évaluations des participants
                </h2>

                <div class="evaluations-container" id="evaluationsContainer">
                    <!-- Evaluations will be loaded here -->
                </div>

                <div id="noEvaluations" class="no-evaluations" style="display: none;">
                    <i class="fa fa-comment-o"></i>
                    <h3>Aucune évaluation pour le moment</h3>
                    <p>Soyez le premier à partager votre expérience !</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Report Confirmation Modal -->
    <div id="reportModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>🚩 Signaler cette évaluation</h3>
            </div>
            <p>Êtes-vous sûr de vouloir signaler cette évaluation comme inappropriée ?</p>
            <p style="color: var(--text-muted); font-size: 13px;">
                L'évaluation sera masquée en attendant la vérification par un administrateur.
            </p>
            <div class="modal-buttons">
                <button class="btn btn-cancel" onclick="closeReportModal()">Annuler</button>
                <button class="btn btn-confirm" onclick="confirmReport()">Signaler</button>
            </div>
        </div>
    </div>

    <script>
        const eventId = <?php echo json_encode($eventId); ?>;
        let currentEvaluationId = null;

        // Load event and evaluations on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadEventDetails();
            loadEvaluations();
        });

        function loadEventDetails() {
            fetch(`../../Control/temoignages_api.php?action=get_event&eventId=${eventId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const event = data.event;
                        document.getElementById('eventTitle').textContent = event.titre;
                        document.getElementById('eventSubtitle').textContent = 
                            `${formatDate(event.date)} • ${event.lieu}`;
                        
                        // Show event info
                        const eventInfo = document.getElementById('eventInfo');
                        eventInfo.innerHTML = `
                            <h3 style="margin: 0 0 15px; color: var(--text-primary);">
                                <i class="fa fa-info-circle"></i> Détails de l'événement
                            </h3>
                            <div class="event-info-grid">
                                <div class="info-item">
                                    <i class="fa fa-calendar"></i>
                                    <div>
                                        <div style="font-size: 12px; color: var(--text-muted);">Date</div>
                                        <div style="color: var(--text-primary);">${formatDate(event.date)}</div>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fa fa-map-marker"></i>
                                    <div>
                                        <div style="font-size: 12px; color: var(--text-muted);">Lieu</div>
                                        <div style="color: var(--text-primary);">${event.lieu}</div>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fa fa-users"></i>
                                    <div>
                                        <div style="font-size: 12px; color: var(--text-muted);">Participants</div>
                                        <div style="color: var(--text-primary);">${event.total_participants}</div>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fa fa-star"></i>
                                    <div>
                                        <div style="font-size: 12px; color: var(--text-muted);">Évaluations</div>
                                        <div style="color: var(--text-primary);">${event.total_evaluations}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                        eventInfo.style.display = 'block';
                        
                        // Show statistics if there are evaluations
                        if (event.total_evaluations > 0) {
                            const statsGrid = document.getElementById('statsGrid');
                            statsGrid.innerHTML = `
                                <div class="stat-card">
                                    <div class="stat-number">${parseFloat(event.avg_accessibilite || 0).toFixed(1)}</div>
                                    <div class="stat-label">Accessibilité moyenne</div>
                                    <div class="stars">${renderStars(event.avg_accessibilite)}</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-number">${parseFloat(event.avg_inclusion || 0).toFixed(1)}</div>
                                    <div class="stat-label">Inclusion moyenne</div>
                                    <div class="stars">${renderStars(event.avg_inclusion)}</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-number">${event.total_evaluations}</div>
                                    <div class="stat-label">Total d'évaluations</div>
                                </div>
                            `;
                            statsGrid.style.display = 'grid';
                        }
                    }
                })
                .catch(error => console.error('Error loading event:', error));
        }

        function loadEvaluations() {
            fetch(`../../Control/temoignages_api.php?action=get_evaluations&eventId=${eventId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('loadingContainer').style.display = 'none';
                    document.getElementById('contentContainer').style.display = 'block';
                    
                    if (data.success && data.evaluations.length > 0) {
                        renderEvaluations(data.evaluations);
                    } else {
                        document.getElementById('noEvaluations').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error loading evaluations:', error);
                    document.getElementById('loadingContainer').innerHTML = 
                        '<p style="color: #e74c3c;">Erreur lors du chargement des évaluations</p>';
                });
        }

        function renderEvaluations(evaluations) {
            const container = document.getElementById('evaluationsContainer');
            container.innerHTML = evaluations.map(eval => `
                <div class="evaluation-card">
                    <div class="evaluation-header">
                        <div class="user-info">
                            <img src="${getUserPhoto(eval.photo)}" alt="${eval.prenom} ${eval.nom}" class="user-avatar">
                            <div class="user-details">
                                <h4>${eval.prenom} ${eval.nom}</h4>
                                <div class="evaluation-date">${formatDate(eval.dateEvaluation)}</div>
                            </div>
                        </div>
                        <button class="report-btn" 
                                onclick="openReportModal(${eval.id})" 
                                ${eval.user_reported > 0 ? 'disabled' : ''}>
                            <i class="fa fa-flag"></i> 
                            ${eval.user_reported > 0 ? 'Signalée' : 'Signaler'}
                        </button>
                    </div>
                    
                    <div class="ratings-grid">
                        <div class="rating-item">
                            <div class="rating-label">Accessibilité</div>
                            <div class="stars">${renderStars(eval.note_accessibilite)}</div>
                            <div style="color: var(--text-primary); font-weight: bold; margin-top: 5px;">
                                ${eval.note_accessibilite}/5
                            </div>
                        </div>
                        <div class="rating-item">
                            <div class="rating-label">Inclusion</div>
                            <div class="stars">${renderStars(eval.note_inclusion)}</div>
                            <div style="color: var(--text-primary); font-weight: bold; margin-top: 5px;">
                                ${eval.note_inclusion}/5
                            </div>
                        </div>
                    </div>
                    
                    ${eval.commentaire && eval.commentaire !== 'Not yet evaluated' ? `
                        <div class="comment-box">
                            <div class="comment-text">"${escapeHtml(eval.commentaire)}"</div>
                        </div>
                    ` : ''}
                </div>
            `).join('');
        }

        function renderStars(rating) {
            const fullStars = Math.floor(rating);
            const hasHalfStar = rating % 1 >= 0.5;
            let stars = '';
            
            for (let i = 0; i < fullStars; i++) {
                stars += '<i class="fa fa-star"></i>';
            }
            if (hasHalfStar) {
                stars += '<i class="fa fa-star-half-o"></i>';
            }
            for (let i = fullStars + (hasHalfStar ? 1 : 0); i < 5; i++) {
                stars += '<i class="fa fa-star-o"></i>';
            }
            
            return stars;
        }

        function getUserPhoto(photo) {
            if (photo) {
                return `../../uploads/profiles/${photo}`;
            }
            return '../general/img/team/team-1.jpg';
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function openReportModal(evaluationId) {
            currentEvaluationId = evaluationId;
            document.getElementById('reportModal').style.display = 'block';
        }

        function closeReportModal() {
            document.getElementById('reportModal').style.display = 'none';
            currentEvaluationId = null;
        }

        function confirmReport() {
            if (!currentEvaluationId) return;
            
            const formData = new FormData();
            formData.append('evaluationId', currentEvaluationId);
            
            fetch('../../Control/temoignages_api.php?action=report_evaluation', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    closeReportModal();
                    loadEvaluations(); // Reload to update UI
                } else {
                    alert('❌ ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error reporting evaluation:', error);
                alert('❌ Erreur lors du signalement');
            });
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('reportModal');
            if (event.target == modal) {
                closeReportModal();
            }
        }
    </script>

    <!-- Theme Toggle Script -->
    <script src="js/theme-toggle.js"></script>
</body>
</html>
