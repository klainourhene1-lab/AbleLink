<?php
/**
 * SOLUTION RAPIDE - Approuver toutes les stories d'un coup
 */

require_once __DIR__ . '/app/Core/Database.php';
use App\Core\Database;

$pdo = Database::getInstance();

header('Content-Type: text/html; charset=utf-8');

// Si formulaire soumis, approuver les stories
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_all'])) {
    try {
        $stmt = $pdo->prepare("UPDATE success_stories SET status = 'approved'");
        $stmt->execute();
        $message = "✅ Toutes les stories ont été approuvées et sont maintenant visibles!";
        $messageType = "success";
    } catch (PDOException $e) {
        $message = "❌ Erreur: " . $e->getMessage();
        $messageType = "error";
    }
}

// Récupérer toutes les stories
$stmt = $pdo->query("SELECT * FROM success_stories ORDER BY created_at DESC");
$stories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$approved = array_filter($stories, fn($s) => ($s['status'] ?? '') === 'approved');
$notApproved = array_filter($stories, fn($s) => ($s['status'] ?? '') !== 'approved');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approuver les Stories</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            padding: 30px;
            line-height: 1.6;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        h1 {
            color: #60a5fa;
            text-align: center;
            margin-bottom: 30px;
        }
        .alert {
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            border-left: 4px solid;
            font-size: 16px;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border-color: #10b981;
            color: #6ee7b7;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border-color: #ef4444;
            color: #fca5a5;
        }
        .alert-warning {
            background: rgba(245, 158, 11, 0.15);
            border-color: #f59e0b;
            color: #fbbf24;
        }
        .big-button {
            display: block;
            width: 100%;
            padding: 25px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 20px;
            font-weight: 700;
            cursor: pointer;
            margin: 30px 0;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
            transition: all 0.3s;
        }
        .big-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.4);
        }
        .stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 25px 0;
        }
        .stat-box {
            background: #1e293b;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border: 2px solid #334155;
        }
        .stat-value {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .stat-value.visible {
            color: #10b981;
        }
        .stat-value.hidden {
            color: #ef4444;
        }
        .stat-label {
            color: #94a3b8;
            font-size: 14px;
        }
        .story-list {
            background: #1e293b;
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
        }
        .story-item {
            padding: 15px;
            background: #0f172a;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .story-title {
            color: #e2e8f0;
            font-weight: 600;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
        }
        .badge-approved {
            background: #10b981;
            color: white;
        }
        .badge-rejected {
            background: #ef4444;
            color: white;
        }
        .badge-pending {
            background: #f59e0b;
            color: white;
        }
        .links {
            text-align: center;
            margin-top: 30px;
        }
        .links a {
            color: #60a5fa;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 600;
        }
        .links a:hover {
            color: #93c5fd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✅ Approuver les Stories</h1>
        
        <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="stats">
            <div class="stat-box">
                <div class="stat-value visible"><?php echo count($approved); ?></div>
                <div class="stat-label">✅ Stories VISIBLES<br>(sur le front office)</div>
            </div>
            <div class="stat-box">
                <div class="stat-value hidden"><?php echo count($notApproved); ?></div>
                <div class="stat-label">❌ Stories CACHÉES<br>(non visibles)</div>
            </div>
        </div>
        
        <?php if (count($notApproved) > 0): ?>
            <div class="alert alert-warning">
                <strong>⚠️ Problème identifié:</strong> Vous avez <?php echo count($notApproved); ?> stories qui sont CACHÉES car elles n'ont pas le statut "approved".
                <br><br>
                <strong>Solution:</strong> Cliquez sur le bouton ci-dessous pour les rendre TOUTES visibles sur le front office.
            </div>
            
            <form method="post">
                <button type="submit" name="approve_all" class="big-button" onclick="return confirm('Voulez-vous vraiment approuver TOUTES les stories?')">
                    ✅ APPROUVER TOUTES LES STORIES
                </button>
            </form>
            
            <div class="story-list">
                <h3 style="color: #ef4444; margin-top: 0;">❌ Stories qui seront approuvées:</h3>
                <?php foreach ($notApproved as $story): ?>
                    <div class="story-item">
                        <div>
                            <div class="story-title"><?php echo htmlspecialchars($story['title']); ?></div>
                            <div style="color: #94a3b8; font-size: 13px; margin-top: 5px;">
                                Par: <?php echo htmlspecialchars($story['author'] ?? 'N/A'); ?>
                            </div>
                        </div>
                        <span class="badge badge-<?php echo $story['status'] ?? 'pending'; ?>">
                            <?php echo strtoupper($story['status'] ?? 'pending'); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-success">
                <strong>✅ Parfait!</strong> Toutes vos stories sont déjà approuvées et visibles sur le front office.
                <br><br>
                Si elles ne s'affichent toujours pas, videz le cache de votre navigateur (Ctrl+F5).
            </div>
        <?php endif; ?>
        
        <?php if (count($approved) > 0): ?>
            <div class="story-list">
                <h3 style="color: #10b981; margin-top: 0;">✅ Stories déjà visibles:</h3>
                <?php foreach ($approved as $story): ?>
                    <div class="story-item">
                        <div>
                            <div class="story-title"><?php echo htmlspecialchars($story['title']); ?></div>
                            <div style="color: #94a3b8; font-size: 13px; margin-top: 5px;">
                                Par: <?php echo htmlspecialchars($story['author'] ?? 'N/A'); ?>
                            </div>
                        </div>
                        <span class="badge badge-approved">APPROVED</span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="links">
            <h3 style="color: #e2e8f0;">🔗 Vérifier le résultat:</h3>
            <a href="/projetweb/ablelink/success-stories" target="_blank">📖 Page Success Stories</a>
            <a href="/projetweb/ablelink/home" target="_blank">🏠 Page d'accueil</a>
            <a href="/projetweb/ablelink/historique" target="_blank">⚙️ Admin</a>
        </div>
    </div>
</body>
</html>
