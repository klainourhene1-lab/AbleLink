<?php
session_start();
require_once __DIR__ . '/../../Control/config.php';
require_once __DIR__ . '/../../Control/UserController.php';
require_once __DIR__ . '/../../Control/OffreController.php';

$controller = new OffreController();
$offre = $controller->getDetails($_GET['id'] ?? null);

if (!$offre) {
    header('Location: liste_offres.php');
    exit;
}

$userController = new UserController();
$user_id = $_SESSION['user_id'] ?? null;
$user = $user_id ? $userController->showUser($user_id) : null;
$user_role = $user ? $user->getRole() : 'guest';
$role_mapping = ['Admin' => 'admin', 'Entreprise' => 'company', 'Utilisateur' => 'user', 'Inclusion' => 'inclusion', 'guest' => 'guest'];
$js_role = $role_mapping[$user_role] ?? 'user';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($offre['titre']) ?> - AbleLink</title>
    <!-- Styles same as liste_offres.php for consistency -->
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../view/general/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="../../view/general/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="../../view/general/css/style.css" type="text/css">
    <style>
        body { background: #100028; color: #ffffff; min-height: 100vh; }
        .glass { background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 30px; }
        .cta-button { padding: 10px 20px; border-radius: 8px; border: none; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; color: white; transition: 0.3s; }
        .cta-button.primary { background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); }
        .cta-button.secondary { background: transparent; border: 1px solid #a78bfa; color: #a78bfa; }
        .cta-button:hover { transform: translateY(-2px); opacity: 0.9; color: white; }
    </style>
</head>
<body>
    <!-- Header Reuse (Simplified for brevity, ideally include a header file) -->
    <header class="header">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="header__logo">
                         <a href="../../view/general/index.php" style="font-family: 'Josefin Sans'; font-size: 24px; color: white; font-weight: bold;">AbleLink</a>
                    </div>
                </div>
                <!-- ... Nav menu ... -->
                 <div class="col-lg-10">
                    <div class="header__nav__option">
                        <nav class="header__nav__menu mobile-menu">
                            <ul>
                                <li><a href="../../view/general/index.php">Accueil</a></li>
                                <li class="active"><a href="liste_offres.php">Offres d'Emploi</a></li>
                            </ul>
                        </nav>
                    </div>
                 </div>
            </div>
        </div>
    </header>

    <section class="spad" style="padding-top: 100px;">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="glass" style="margin-bottom: 30px;">
                        <span style="color: #a78bfa; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; font-size: 14px;"><?= htmlspecialchars($offre['entreprise']) ?></span>
                        <h1 style="color: white; font-size: 36px; margin: 10px 0 20px;"><?= htmlspecialchars($offre['titre']) ?></h1>
                        
                        <div style="font-size: 16px; line-height: 1.8; color: #d8b4fe; margin-bottom: 40px;">
                            <?= nl2br(htmlspecialchars($offre['description'])) ?>
                        </div>

                        <div style="display: flex; gap: 15px;">
                            <a href="postuler.php?id_offre=<?= $offre['id'] ?>" class="cta-button primary">
                                <i class="fa fa-paper-plane"></i> Postuler maintenant
                            </a>
                            <a href="liste_offres.php" class="cta-button secondary">
                                <i class="fa fa-arrow-left"></i> Retour aux offres
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="glass">
                        <h3 style="color: #fff; margin-bottom: 20px;">Informations</h3>
                        <p><strong style="color: #c4b5fd;">Localisation:</strong><br> <?= htmlspecialchars($offre['localisation']) ?></p>
                        <p><strong style="color: #c4b5fd;">Contrat:</strong><br> <?= htmlspecialchars($offre['type_contrat']) ?></p>
                        <p><strong style="color: #c4b5fd;">Salaire:</strong><br> <?= htmlspecialchars($offre['salaire'] ?? 'Non spécifié') ?></p>
                        <p><strong style="color: #c4b5fd;">Publié le:</strong><br> <?= date('d/m/Y', strtotime($offre['date_publication'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
