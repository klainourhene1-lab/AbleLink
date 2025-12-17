<?php
/**
 * Script pour cacher les stories de démonstration
 * Change le status de 'approved' à 'rejected' pour les cacher de l'interface publique
 */

require_once __DIR__ . '/app/Core/Database.php';
use App\Core\Database;

$pdo = Database::getInstance();

// IDs des stories de démonstration à cacher (modifiables)
$storiesToHide = [1, 2, 3, 4]; // Stories créées automatiquement lors du setup

// Si vous voulez cacher des stories spécifiques par titre:
$titlesToHide = [
    "Mon Premier Emploi grâce à AbleLink",
    "De Chômeur à Développeur Web",
    "Reconversion Professionnelle Réussie",
    "Histoire en Attente de Validation"
];

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cacher les Stories de Démo</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            padding: 30px;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            color: #60a5fa;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 10px;
        }
        .card {
            background: #1e293b;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #334155;
            margin-bottom: 20px;
        }
        .story-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #0f172a;
            border-radius: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #3b82f6;
        }
        .story-info {
            flex: 1;
        }
        .story-title {
            color: #60a5fa;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .story-meta {
            color: #94a3b8;
            font-size: 13px;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            margin-right: 5px;
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
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-hide {
            background: #ef4444;
            color: white;
        }
        .btn-hide:hover {
            background: #dc2626;
        }
        .btn-show {
            background: #10b981;
            color: white;
        }
        .btn-show:hover {
            background: #059669;
        }
        .btn-hide-all {
            background: #f59e0b;
            color: white;
            margin-bottom: 20px;
        }
        .btn-hide-all:hover {
            background: #d97706;
        }
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }
        .alert-info {
            background: rgba(59, 130, 246, 0.1);
            border-color: #3b82f6;
            color: #93c5fd;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: #10b981;
            color: #6ee7b7;
        }
        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border-color: #f59e0b;
            color: #fbbf24;
        }
        .links {
            margin-top: 30px;
            padding: 20px;
            background: #1e293b;
            border-radius: 12px;
        }
        .links a {
            color: #60a5fa;
            text-decoration: none;
            margin-right: 20px;
            font-weight: 600;
        }
        .links a:hover {
            color: #93c5fd;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔒 Cacher les Stories de Démonstration</h1>
        
        <?php
        // Traiter les actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['hide_story'])) {
                $id = (int)$_POST['story_id'];
                $stmt = $pdo->prepare("UPDATE success_stories SET status = 'rejected' WHERE id = ?");
                $stmt->execute([$id]);
                echo '<div class="alert alert-success">✅ Story ID ' . $id . ' cachée avec succès!</div>';
            } elseif (isset($_POST['show_story'])) {
                $id = (int)$_POST['story_id'];
                $stmt = $pdo->prepare("UPDATE success_stories SET status = 'approved' WHERE id = ?");
                $stmt->execute([$id]);
                echo '<div class="alert alert-success">✅ Story ID ' . $id . ' affichée avec succès!</div>';
            } elseif (isset($_POST['hide_all_demo'])) {
                $placeholders = implode(',', array_fill(0, count($titlesToHide), '?'));
                $stmt = $pdo->prepare("UPDATE success_stories SET status = 'rejected' WHERE title IN ($placeholders)");
                $stmt->execute($titlesToHide);
                echo '<div class="alert alert-success">✅ Toutes les stories de démonstration ont été cachées!</div>';
            }
        }
        
        // Récupérer toutes les stories
        $stmt = $pdo->query("SELECT * FROM success_stories ORDER BY id ASC");
        $allStories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Séparer les stories visibles et cachées
        $visibleStories = array_filter($allStories, fn($s) => ($s['status'] ?? '') === 'approved');
        $hiddenStories = array_filter($allStories, fn($s) => ($s['status'] ?? '') !== 'approved');
        ?>
        
        <div class="alert alert-info">
            💡 <strong>Information:</strong> Cacher une story change son statut à "rejected". Elle ne sera plus visible sur l'interface publique mais restera dans la base de données.
        </div>

        <form method="post" style="margin-bottom: 20px;">
            <button type="submit" name="hide_all_demo" class="btn-hide-all">
                🚫 Cacher toutes les stories de démonstration
            </button>
        </form>

        <div class="card">
            <h2 style="margin-top: 0; color: #e2e8f0;">✅ Stories Visibles (<?php echo count($visibleStories); ?>)</h2>
            <?php if (empty($visibleStories)): ?>
                <p style="color: #94a3b8; font-style: italic;">Aucune story visible</p>
            <?php else: ?>
                <?php foreach ($visibleStories as $story): ?>
                    <div class="story-item">
                        <div class="story-info">
                            <div class="story-title">
                                <?php echo htmlspecialchars($story['title']); ?>
                            </div>
                            <div class="story-meta">
                                <span class="badge badge-approved">VISIBLE</span>
                                ID: <?php echo $story['id']; ?> | 
                                Par: <?php echo htmlspecialchars($story['author'] ?? 'N/A'); ?> | 
                                ❤️ <?php echo $story['likes'] ?? 0; ?> likes |
                                <?php echo date('d/m/Y', strtotime($story['created_at'])); ?>
                            </div>
                        </div>
                        <form method="post" style="margin: 0;">
                            <input type="hidden" name="story_id" value="<?php echo $story['id']; ?>">
                            <button type="submit" name="hide_story" class="btn-hide">
                                🔒 Cacher
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2 style="margin-top: 0; color: #e2e8f0;">🔒 Stories Cachées (<?php echo count($hiddenStories); ?>)</h2>
            <?php if (empty($hiddenStories)): ?>
                <p style="color: #94a3b8; font-style: italic;">Aucune story cachée</p>
            <?php else: ?>
                <?php foreach ($hiddenStories as $story): ?>
                    <div class="story-item" style="border-left-color: #ef4444;">
                        <div class="story-info">
                            <div class="story-title">
                                <?php echo htmlspecialchars($story['title']); ?>
                            </div>
                            <div class="story-meta">
                                <span class="badge badge-rejected">CACHÉE</span>
                                ID: <?php echo $story['id']; ?> | 
                                Par: <?php echo htmlspecialchars($story['author'] ?? 'N/A'); ?> | 
                                ❤️ <?php echo $story['likes'] ?? 0; ?> likes |
                                <?php echo date('d/m/Y', strtotime($story['created_at'])); ?>
                            </div>
                        </div>
                        <form method="post" style="margin: 0;">
                            <input type="hidden" name="story_id" value="<?php echo $story['id']; ?>">
                            <button type="submit" name="show_story" class="btn-show">
                                👁️ Afficher
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="links">
            <h3 style="margin-top: 0; color: #e2e8f0;">🔗 Liens rapides</h3>
            <a href="/projetweb/ablelink/success-stories">📖 Voir les Stories Publiques</a>
            <a href="/projetweb/ablelink/home">🏠 Page d'accueil</a>
            <a href="/projetweb/ablelink/historique">⚙️ Admin - Historique</a>
            <a href="/projetweb/ablelink/diagnostic_stories.php">🔍 Diagnostic</a>
        </div>
    </div>
</body>
</html>
