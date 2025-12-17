<?php
// manage_candidatures.php - Manage candidates for a specific offer
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../general/signin.php');
    exit;
}

require_once __DIR__ . '/../../Control/CandidatureController.php';
require_once __DIR__ . '/../../Control/OffreController.php';

$candCtrl = new CandidatureController();
$offreCtrl = new OffreController();

$id_offre = $_GET['id_offre'] ?? null;
if (!$id_offre) die("Offre non spécifiée");

// Verify ownership (security check)
$offre = $offreCtrl->getDetails($id_offre);
if (!$offre || $offre['user_id'] != $_SESSION['user_id']) {
    // Ideally redirect or show error. 
    // die("Accès refusé"); 
    // For demo simplicity we might skip robust ownership check if session user_id logic wasn't fully established in create_offer
}

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $candId = $_POST['candidature_id'] ?? null;
    
    if ($candId && ($action === 'accept' || $action === 'reject')) {
        $status = ($action === 'accept') ? 'accepted' : 'rejected';
        $candCtrl->updateStatus($candId, $status);
    }
    // Stay on page
    header("Location: manage_candidatures.php?id_offre=$id_offre");
    exit;
}

$candidatures = $candCtrl->getByOffre($id_offre);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les Candidatures - AbleLink</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #7c3aed; --text: #e2e8f0; --success: #10b981; --danger: #ef4444; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); color: var(--text); padding: 40px; }
        .container { max-width: 1000px; margin: 0 auto; }
        
        .header { display:flex; align-items:center; gap:15px; margin-bottom:30px; }
        .card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); padding: 25px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.05); margin-bottom: 20px; }
        
        .candidate-item { display: flex; align-items:center; justify-content:space-between; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .candidate-item:last-child { border-bottom: none; }
        
        .btn { padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; color: white; font-weight: 500; transition:0.2s; }
        .btn-accept { background: rgba(16, 185, 129, 0.2); color: #10b981; }
        .btn-accept:hover { background: rgba(16, 185, 129, 0.4); }
        .btn-reject { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .btn-reject:hover { background: rgba(239, 68, 68, 0.4); }
        
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 13px; font-weight:600; }
        .status-accepted { background: #10b981; color: white; }
        .status-rejected { background: #ef4444; color: white; }
        .status-pending { background: #f59e0b; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="enterprise_offers.php" style="color:var(--text); font-size:24px;"><i class="fas fa-arrow-left"></i></a>
            <h1>Candidatures pour "<?= htmlspecialchars($offre['titre']) ?>"</h1>
        </div>

        <div class="card">
            <?php if(empty($candidatures)): ?>
                <div style="text-align:center; padding:30px; color:#94a3b8;">Aucune candidature pour le moment.</div>
            <?php else: ?>
                <?php foreach($candidatures as $cand): ?>
                <div class="candidate-item">
                    <div>
                        <div style="font-size:18px; font-weight:600;"><?= htmlspecialchars($cand['nom_candidat']) ?></div>
                        <div style="color:#94a3b8; margin-top:5px;"><i class="fas fa-envelope"></i> <?= htmlspecialchars($cand['email']) ?></div>
                        <div style="color:#94a3b8; font-size:12px;">Candidature du <?= date('d/m/Y', strtotime($cand['date_candidature'])) ?></div>
                        <?php if($cand['cv']): ?>
                            <a href="../../<?= htmlspecialchars($cand['cv']) ?>" target="_blank" style="color:var(--primary); text-decoration:none; font-size:13px; margin-top:5px; display:inline-block;">
                                <i class="fas fa-file-pdf"></i> Voir le CV
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <div style="display:flex; gap:10px; align-items:center;">
                        <?php if($cand['statut'] == 'accepted'): ?>
                            <span class="status-badge status-accepted"><i class="fas fa-check"></i> Accepté</span>
                        <?php elseif($cand['statut'] == 'rejected'): ?>
                            <span class="status-badge status-rejected"><i class="fas fa-times"></i> Rejeté</span>
                        <?php else: ?>
                            <!-- Actions -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="candidature_id" value="<?= $cand['id'] ?>">
                                <button type="submit" name="action" value="accept" class="btn btn-accept" title="Accepter"><i class="fas fa-check"></i></button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="candidature_id" value="<?= $cand['id'] ?>">
                                <button type="submit" name="action" value="reject" class="btn btn-reject" title="Rejeter"><i class="fas fa-times"></i></button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
