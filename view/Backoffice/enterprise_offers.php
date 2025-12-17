<?php
// enterprise_offers.php - Dashboard for Enterprises to view their offers
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../general/signin.php');
    exit;
}
// Optionally check role if you want to restrict to strict "Enterprise" role
// if ($_SESSION['user_role'] !== 'Enterprise') { ... }

require_once __DIR__ . '/../../Controller/OffreController.php';
$offreCtrl = new OffreController();
$myOffers = $offreCtrl->getMyOffers();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Offres - AbleLink</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Reusing consistent admin/dashboard styles */
        :root { --primary: #7c3aed; --dark: #0f172a; --text: #e2e8f0; --btn-bg: rgba(255,255,255,0.1); }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); color: var(--text); padding: 40px; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { margin-bottom: 30px; font-size: 28px; }
        
        .card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); padding: 25px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.05); }
        
        table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
        th { text-align: left; padding: 15px; color: #94a3b8; }
        td { background: rgba(255,255,255,0.03); padding: 15px; first-child: border-radius: 10px 0 0 10px; last-child: border-radius: 0 10px 10px 0; }
        
        .btn { padding: 8px 16px; border-radius: 8px; text-decoration: none; color: white; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; transition: 0.2s; cursor: pointer; border: none; }
        .btn-view { background: rgba(124, 58, 237, 0.4); }
        .btn-view:hover { background: rgba(124, 58, 237, 0.6); }
        
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 12px; }
        .published { background: rgba(46, 204, 113, 0.2); color: #2ecc71; }
        .pending { background: rgba(241, 196, 15, 0.2); color: #f1c40f; }
    </style>
</head>
<body>
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center;">
             <h1><i class="fas fa-briefcase"></i> Mes Offres Publiées</h1>
             <a href="../general/index.php" class="btn btn-view"><i class="fas fa-arrow-left"></i> Retour au site</a>
        </div>

        <div class="card">
            <?php if(empty($myOffers)): ?>
                <p style="text-align:center; padding:30px; color:#94a3b8;">Vous n'avez publié aucune offre pour le moment.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Candidatures</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($myOffers as $offer): ?>
                        <tr>
                            <td>
                                <div style="font-weight:600;"><?= htmlspecialchars($offer['titre']) ?></div>
                                <small style="color:#94a3b8;"><?= htmlspecialchars($offer['localisation']) ?></small>
                            </td>
                            <td><?= date('d/m/Y', strtotime($offer['date_publication'])) ?></td>
                            <td>
                                <?php 
                                    $s = $offer['statut'] ?? 'pending';
                                    $cls = ($s === 'published') ? 'published' : 'pending';
                                    $lbl = ($s === 'published') ? 'En ligne' : 'En attente';
                                ?>
                                <span class="badge <?= $cls ?>"><?= $lbl ?></span>
                            </td>
                            <td>
                                <!-- We could add a count here if efficient, for now just a link -->
                                <a href="manage_candidatures.php?id_offre=<?= $offer['id'] ?>" class="btn btn-view" style="font-size:12px;">
                                    <i class="fas fa-users"></i> Voir Candidats
                                </a>
                            </td>
                            <td>
                                <a href="#" class="btn btn-view" style="background:rgba(255,255,255,0.1);"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
