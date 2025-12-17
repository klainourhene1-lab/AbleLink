<?php
session_start();
require_once __DIR__ . '/../../Control/config.php';
require_once __DIR__ . '/../../Control/UserController.php';
require_once __DIR__ . '/../../Control/OffreController.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../view/general/signin.php');
    exit;
}

$userController = new UserController();
$user = $userController->showUser($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? 'guest';

// Only Admin and Entreprise can post
if ($user_role !== 'Admin' && $user_role !== 'Entreprise') {
    die("Accès refusé. Seules les entreprises et les administrateurs peuvent déposer des offres.");
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $offreCtrl = new OffreController();
    $result = $offreCtrl->create();
    
    if ($result['success']) {
        if ($result['status'] === 'published') {
            $message = '<div class="alert alert-success">Offre publiée avec succès !</div>';
        } else {
            $message = '<div class="alert alert-info">Offre soumise ! Elle sera visible après validation par un administrateur.</div>';
        }
    } else {
        $message = '<div class="alert alert-danger">Erreur : ' . htmlspecialchars($result['error']) . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Déposer une Offre - AbleLink</title>
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../view/general/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../view/general/css/style.css">
    <style>
        body { background: #100028; color: white; min-height: 100vh; }
        .glass { background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 40px; max-width: 800px; margin: 100px auto; }
        .input, .textarea, .select { width: 100%; padding: 12px; margin-bottom: 20px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 8px; }
        .textarea { min-height: 150px; resize: vertical; }
        .select option { background: #1e1b4b; color: white; }
        .btn { width: 100%; padding: 15px; background: linear-gradient(135deg, #7c3aed, #6d28d9); border: none; color: white; border-radius: 8px; font-weight: bold; cursor: pointer; }
        .btn:hover { opacity: 0.9; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: rgba(46, 204, 113, 0.2); color: #2ecc71; }
        .alert-info { background: rgba(52, 152, 219, 0.2); color: #3498db; }
        .alert-danger { background: rgba(231, 76, 60, 0.2); color: #e74c3c; }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="col-lg-2">
                <a href="liste_offres.php" style="font-family: 'Josefin Sans'; font-size: 24px; color: white; font-weight: bold;">AbleLink</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="glass">
            <h2 style="text-align: center; margin-bottom: 30px;">Déposer une Offre d'Emploi</h2>
            
            <?= $message ?>
            
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <label>Titre de l'offre *</label>
                        <input type="text" name="titre" class="input" required placeholder="Ex: Développeur Web Fullstack">
                    </div>
                    <div class="col-md-6">
                        <label>Nom de l'entreprise *</label>
                        <input type="text" name="entreprise" class="input" required value="<?= $user_role === 'Entreprise' ? htmlspecialchars($user->getNom()) : '' ?>" placeholder="Votre entreprise">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label>Localisation *</label>
                        <input type="text" name="localisation" class="input" required placeholder="Ex: Paris, Télétravail">
                    </div>
                    <div class="col-md-6">
                        <label>Type de contrat *</label>
                        <select name="type_contrat" class="select" required>
                            <option value="CDI">CDI</option>
                            <option value="CDD">CDD</option>
                            <option value="Stage">Stage</option>
                            <option value="Alternance">Alternance</option>
                            <option value="Freelance">Freelance</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>
                </div>

                <label>Salaire (Optionnel)</label>
                <input type="text" name="salaire" class="input" placeholder="Ex: 35k - 45k, Selon profil">

                <label>Description du poste *</label>
                <textarea name="description" class="textarea" required placeholder="Détaillez les missions, le profil recherché..."></textarea>
                
                <div style="display: flex; gap: 10px;">
                    <a href="liste_offres.php" class="btn" style="background: transparent; border: 1px solid white; text-align: center; text-decoration: none;">Annuler</a>
                    <button type="submit" class="btn">Publier l'offre</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
