<?php
session_start();
require_once __DIR__ . '/../../Controller/config.php';
require_once __DIR__ . '/../../Controller/CandidatureController.php';
require_once __DIR__ . '/../../Controller/OffreController.php';

$id_offre = $_GET['id_offre'] ?? $_POST['id_offre'] ?? null;
if (!$id_offre) {
    header('Location: liste_offres.php');
    exit;
}

$offreCtrl = new OffreController();
$offre = $offreCtrl->getDetails($id_offre);
if (!$offre) {
    die("Offre introuvable");
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $candidatureCtrl = new CandidatureController();
    $result = $candidatureCtrl->submit($_POST);
    if ($result['success']) {
        $message = '<div style="background: rgba(46, 204, 113, 0.2); color: #2ecc71; padding: 15px; border-radius: 8px; margin-bottom: 20px;">Candidature envoyée avec succès !</div>';
    } else {
        $message = '<div style="background: rgba(231, 76, 60, 0.2); color: #e74c3c; padding: 15px; border-radius: 8px; margin-bottom: 20px;">Erreur: ' . htmlspecialchars($result['error']) . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Postuler - <?= htmlspecialchars($offre['titre']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../view/general/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../view/general/css/style.css">
    <style>
        body { background: #100028; color: white; min-height: 100vh; }
        .glass { background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 40px; max-width: 600px; margin: 100px auto; }
        .input { width: 100%; padding: 12px; margin-bottom: 20px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; }
        .btn { width: 100%; padding: 15px; background: linear-gradient(135deg, #7c3aed, #6d28d9); border: none; color: white; border-radius: 8px; font-weight: bold; cursor: pointer; }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="glass">
            <h2 style="text-align: center; margin-bottom: 30px;">Postuler à l'offre<br><span style="color: #a78bfa; font-size: 0.8em;"><?= htmlspecialchars($offre['titre']) ?></span></h2>
            
            <?= $message ?>
            
            <?php if (empty($message) || strpos($message, 'Erreur') !== false): ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_offre" value="<?= htmlspecialchars($id_offre) ?>">
                
                <label>Votre Nom Complet</label>
                <input type="text" name="nom_candidat" class="input" required>
                
                <label>Votre Email</label>
                <input type="email" name="email" class="input" required>
                
                <label>Votre CV (PDF, DOCX)</label>
                <input type="file" name="cv" class="input" accept=".pdf,.doc,.docx" required>
                
                <div style="display: flex; gap: 10px;">
                    <a href="details_offre.php?id=<?= $id_offre ?>" class="btn" style="background: transparent; border: 1px solid white; text-align: center; text-decoration: none;">Annuler</a>
                    <button type="submit" class="btn">Envoyer ma candidature</button>
                </div>
            </form>
            <?php else: ?>
                <div style="text-align: center;">
                    <a href="liste_offres.php" class="btn">Retour aux offres</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
