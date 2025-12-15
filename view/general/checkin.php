<?php
session_start();

// Get parameters from URL
$participationId = $_GET['p'] ?? null;
$eventId = $_GET['e'] ?? null;
$email = $_GET['email'] ?? null;

if (!$participationId || !$eventId) {
    header('Location: ../FrontOffice/evaluations-evenements.php');
    exit;
}

// Database connection
require_once __DIR__ . '/../../Model/Database.php';

$db = Database::getInstance()->getConnection();

// Get participation and event details
try {
    $stmt = $db->prepare("
        SELECT p.*, e.*, u.prenom, u.nom, u.email as user_email
        FROM participation p
        JOIN evenement e ON p.idEvenement = e.id
        JOIN utilisateur u ON p.idUtilisateur = u.id
        WHERE p.id = ? AND e.id = ?
    ");
    $stmt->execute([$participationId, $eventId]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$data) {
        $error = "Participation ou événement introuvable.";
    } else {
        // Extract data
        $participation = [
            'id' => $data['id'],
            'idUtilisateur' => $data['idUtilisateur'],
            'idEvenement' => $data['idEvenement'],
            'dateInscription' => $data['dateInscription']
        ];
        
        $event = [
            'titre' => $data['titre'],
            'description' => $data['description'],
            'date' => $data['date'],
            'lieu' => $data['lieu']
        ];
        
        $user = [
            'prenom' => $data['prenom'],
            'nom' => $data['nom'],
            'email' => $data['user_email']
        ];
    }
} catch (Exception $e) {
    $error = "Erreur lors de la récupération des données: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation d'inscription - AbeLink</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            padding: 50px 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease-out 0.2s both;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        
        .success-icon::before {
            content: "✓";
            font-size: 60px;
            color: white;
            font-weight: bold;
        }
        
        h1 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 32px;
        }
        
        .welcome-text {
            font-size: 24px;
            color: #333;
            margin-bottom: 30px;
            font-weight: 600;
        }
        
        .event-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            text-align: left;
            border-left: 4px solid #667eea;
        }
        
        .event-card h2 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 20px;
        }
        
        .event-detail {
            display: flex;
            align-items: start;
            margin: 12px 0;
            color: #555;
        }
        
        .event-detail strong {
            min-width: 100px;
            color: #333;
        }
        
        .info-box {
            background: #e7f3ff;
            border: 2px solid #667eea;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .info-box p {
            color: #0c5460;
            line-height: 1.6;
            margin: 5px 0;
        }
        
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 14px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            display: inline-block;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .btn-secondary:hover {
            background: #f8f9fa;
        }
        
        .error-container {
            text-align: center;
            color: #721c24;
        }
        
        .error-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
        
        .participation-id {
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 8px 16px;
            border-radius: 6px;
            display: inline-block;
            margin-top: 10px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($error)): ?>
            <div class="error-container">
                <div class="error-icon">❌</div>
                <h1>Erreur</h1>
                <p style="margin: 20px 0; color: #666;"><?= htmlspecialchars($error) ?></p>
                <a href="../FrontOffice/evaluations-evenements.php" class="btn btn-primary">Retour aux événements</a>
            </div>
        <?php else: ?>
            <div class="success-icon"></div>
            
            <h1>Bienvenue !</h1>
            <div class="welcome-text">
                <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
            </div>
            
            <div class="info-box">
                <p><strong>✓ Votre inscription est confirmée</strong></p>
                <p>Vous êtes bien inscrit(e) à cet événement</p>
            </div>
            
            <div class="event-card">
                <h2><?= htmlspecialchars($event['titre']) ?></h2>
                
                <div class="event-detail">
                    <strong>📅 Date :</strong>
                    <span><?= htmlspecialchars($event['date']) ?></span>
                </div>
                
                <div class="event-detail">
                    <strong>📍 Lieu :</strong>
                    <span><?= htmlspecialchars($event['lieu']) ?></span>
                </div>
                
                <?php if ($event['description']): ?>
                <div class="event-detail">
                    <strong>📝 Description :</strong>
                    <span><?= htmlspecialchars($event['description']) ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="participation-id">
                ID de participation : <?= htmlspecialchars($participationId) ?>
            </div>
            
            <div class="button-group">
                <a href="../FrontOffice/evaluations-evenements.php" class="btn btn-primary">
                    Voir tous les événements
                </a>
                <a href="../../Control/historique.php" class="btn btn-secondary">
                    Mon historique
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
