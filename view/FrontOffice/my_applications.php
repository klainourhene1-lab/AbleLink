<?php
// my_applications.php - View for Candidates to see their history
session_start();
// Check if user is logged in (Candidate or any user)
if (!isset($_SESSION['user_id'])) {
    header('Location: ../general/signin.php');
    exit;
}

require_once __DIR__ . '/../../Controller/CandidatureController.php';
$candCtrl = new CandidatureController();
$myApps = $candCtrl->getMyApplications();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Candidatures - AbleLink</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #7c3aed; --text: #e2e8f0; --card-bg: rgba(30, 41, 59, 0.7); }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); color: var(--text); padding: 40px; }
        .container { max-width: 1000px; margin: 0 auto; }
        
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }
        .card { background: var(--card-bg); backdrop-filter: blur(10px); padding: 25px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.05); }
        
        .app-item { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center; }
        .app-item:last-child { border-bottom: none; }
        
        .status-badge { padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; }
        .accepted { background: #10b981; color: white; }
        .rejected { background: #ef4444; color: white; }
        .pending { background: #f59e0b; color: white; }
        
        .btn-home { color: white; text-decoration: none; display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.1); padding: 8px 16px; border-radius: 8px; transition: 0.2s; }
        .btn-home:hover { background: rgba(255,255,255,0.2); }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-file-signature"></i> Mes Candidatures</h1>
            <a href="../general/index.php" class="btn-home"><i class="fas fa-home"></i> Accueil</a>
        </div>

        <div class="card">
            <?php if(empty($myApps)): ?>
                <div style="text-align:center; padding:40px; color:#94a3b8;">
                    <i class="fas fa-folder-open" style="font-size:40px; margin-bottom:15px;"></i>
                    <p>Vous n'avez envoyé aucune candidature pour le moment.</p>
                    <a href="liste_offres.php" style="color:var(--primary); text-decoration:none; margin-top:10px; display:inline-block;">Voir les offres</a>
                </div>
            <?php else: ?>
                <?php foreach($myApps as $app): ?>
                <div class="app-item">
                    <div>
                        <div style="font-size:18px; font-weight:600;"><?= htmlspecialchars($app['offre_titre']) ?></div>
                        <div style="color:#94a3b8; font-size:14px;">
                            <i class="fas fa-building"></i> <?= htmlspecialchars($app['entreprise']) ?> 
                            <span style="margin:0 5px;">•</span> 
                            <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($app['localisation']) ?>
                        </div>
                        <div style="font-size:12px; color:#64748b; margin-top:5px;">
                            Envoyé le <?= date('d/m/Y', strtotime($app['date_candidature'])) ?>
                        </div>
                    </div>
                    <div>
                        <?php
                            $st = $app['statut'] ?? 'pending';
                            $cls = 'pending'; $lbl = 'En attente';
                            if($st === 'accepted') { $cls = 'accepted'; $lbl = 'Acceptée'; }
                            if($st === 'rejected') { $cls = 'rejected'; $lbl = 'Refusée'; }
                            // Map 'en_attente' from DB default to 'pending'
                            if($st === 'en_attente') { $cls = 'pending'; $lbl = 'En attente'; }
                        ?>
                        <span class="status-badge <?= $cls ?>"><?= $lbl ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
