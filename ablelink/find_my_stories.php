<?php
/**
 * Script pour trouver les stories que VOUS avez créées
 */

require_once __DIR__ . '/app/Core/Database.php';
use App\Core\Database;

$pdo = Database::getInstance();

header('Content-Type: text/html; charset=utf-8');

// Récupérer toutes les stories
$stmt = $pdo->query("SELECT * FROM success_stories ORDER BY created_at DESC");
$allStories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Séparer les stories de démo vs les stories personnelles
$demoAuthors = ['Ahmed Ben Ali', 'Fatima Khadra', 'Mohamed Salah', 'User Test'];
$myStories = array_filter($allStories, function($s) use ($demoAuthors) {
    $author = $s['author'] ?? '';
    return !in_array($author, $demoAuthors);
});
$demoStories = array_filter($allStories, function($s) use ($demoAuthors) {
    $author = $s['author'] ?? '';
    return in_array($author, $demoAuthors);
});
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Stories</title>
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
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid;
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
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border-color: #ef4444;
            color: #fca5a5;
        }
        .section {
            background: #1e293b;
            padding: 25px;
            border-radius: 12px;
            border: 1px solid #334155;
            margin-bottom: 25px;
        }
        .story-card {
            background: #0f172a;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #10b981;
        }
        .story-card.demo {
            border-left-color: #94a3b8;
            opacity: 0.7;
        }
        .story-title {
            color: #60a5fa;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .story-meta {
            color: #94a3b8;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            margin-right: 8px;
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
        .story-excerpt {
            color: #cbd5e1;
            font-size: 14px;
            line-height: 1.6;
            margin-top: 10px;
        }
        .links {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .links a {
            padding: 8px 16px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
        }
        .links a:hover {
            background: #2563eb;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        .stat-box {
            background: #1e293b;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid #334155;
        }
        .stat-value {
            font-size: 36px;
            font-weight: 700;
            color: #60a5fa;
        }
        .stat-label {
            color: #94a3b8;
            font-size: 13px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📖 Mes Success Stories</h1>
        
        <div class="stats">
            <div class="stat-box">
                <div class="stat-value"><?php echo count($myStories); ?></div>
                <div class="stat-label">VOS STORIES</div>
            </div>
            <div class="stat-box">
                <div class="stat-value"><?php echo count($demoStories); ?></div>
                <div class="stat-label">STORIES DE DÉMO</div>
            </div>
            <div class="stat-box">
                <div class="stat-value"><?php echo count($allStories); ?></div>
                <div class="stat-label">TOTAL</div>
            </div>
        </div>

        <?php if (empty($myStories)): ?>
            <div class="alert alert-danger">
                ❌ <strong>Aucune story trouvée!</strong> Vous n'avez pas encore partagé de stories, ou elles ont été supprimées.
                <br><br>
                <a href="/projetweb/ablelink/success-stories/create" style="color: #fca5a5; font-weight: 600;">➕ Créer votre première story</a>
            </div>
        <?php else: ?>
            <div class="alert alert-success">
                ✅ <strong>Trouvé <?php echo count($myStories); ?> story(s) que vous avez créée(s)!</strong>
            </div>
            
            <div class="section">
                <h2 style="margin-top: 0; color: #10b981;">✨ VOS STORIES</h2>
                <?php foreach ($myStories as $story): ?>
                    <?php
                        $status = $story['status'] ?? 'pending';
                        $statusBadge = $status === 'approved' ? 'badge-approved' : ($status === 'rejected' ? 'badge-rejected' : 'badge-pending');
                        $statusText = $status === 'approved' ? '✅ VISIBLE' : ($status === 'rejected' ? '❌ CACHÉE' : '⏳ EN ATTENTE');
                        $content = $story['content'] ?? $story['description'] ?? '';
                        $excerpt = mb_substr(strip_tags($content), 0, 150) . '...';
                    ?>
                    <div class="story-card">
                        <div class="story-title"><?php echo htmlspecialchars($story['title']); ?></div>
                        <div class="story-meta">
                            <span class="badge <?php echo $statusBadge; ?>"><?php echo $statusText; ?></span>
                            Par: <strong><?php echo htmlspecialchars($story['author'] ?? 'N/A'); ?></strong> | 
                            ID: <?php echo $story['id']; ?> | 
                            ❤️ <?php echo $story['likes'] ?? 0; ?> likes | 
                            📅 <?php echo date('d/m/Y à H:i', strtotime($story['created_at'])); ?>
                        </div>
                        <div class="story-excerpt"><?php echo htmlspecialchars($excerpt); ?></div>
                        <div class="links">
                            <a href="/projetweb/ablelink/success-stories/comments?id=<?php echo $story['id']; ?>">📖 Lire</a>
                            <a href="/projetweb/ablelink/success-stories/edit?id=<?php echo $story['id']; ?>">✏️ Modifier</a>
                            <?php if ($status !== 'approved'): ?>
                                <a href="/projetweb/ablelink/hide_demo_stories.php" style="background: #10b981;">✅ Rendre visible</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($demoStories)): ?>
            <div class="section">
                <h2 style="margin-top: 0; color: #94a3b8;">📦 STORIES DE DÉMONSTRATION</h2>
                <p style="color: #94a3b8; font-size: 14px; margin-bottom: 15px;">
                    Ces stories ont été créées automatiquement lors de l'installation. 
                    <a href="/projetweb/ablelink/hide_demo_stories.php" style="color: #60a5fa; font-weight: 600;">Cliquez ici pour les cacher</a>
                </p>
                <?php foreach ($demoStories as $story): ?>
                    <?php
                        $status = $story['status'] ?? 'pending';
                        $statusBadge = $status === 'approved' ? 'badge-approved' : ($status === 'rejected' ? 'badge-rejected' : 'badge-pending');
                        $statusText = $status === 'approved' ? '✅ VISIBLE' : ($status === 'rejected' ? '❌ CACHÉE' : '⏳ EN ATTENTE');
                    ?>
                    <div class="story-card demo">
                        <div class="story-title"><?php echo htmlspecialchars($story['title']); ?></div>
                        <div class="story-meta">
                            <span class="badge <?php echo $statusBadge; ?>"><?php echo $statusText; ?></span>
                            Par: <?php echo htmlspecialchars($story['author'] ?? 'N/A'); ?> | 
                            ID: <?php echo $story['id']; ?> | 
                            📅 <?php echo date('d/m/Y', strtotime($story['created_at'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="alert alert-warning">
            💡 <strong>Conseil:</strong> Pour que vos stories soient visibles sur l'interface publique, elles doivent avoir le statut <strong>"approved"</strong>. 
            Vous pouvez changer le statut depuis le <a href="/projetweb/ablelink/historique" style="color: #fbbf24; font-weight: 600;">panneau admin</a>.
        </div>

        <div style="margin-top: 30px; padding: 20px; background: #1e293b; border-radius: 12px;">
            <h3 style="margin-top: 0; color: #e2e8f0;">🔗 Liens rapides</h3>
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <a href="/projetweb/ablelink/success-stories" style="color: #60a5fa; text-decoration: none; font-weight: 600;">📖 Page publique</a>
                <a href="/projetweb/ablelink/success-stories/create" style="color: #60a5fa; text-decoration: none; font-weight: 600;">➕ Créer une story</a>
                <a href="/projetweb/ablelink/historique" style="color: #60a5fa; text-decoration: none; font-weight: 600;">⚙️ Admin</a>
                <a href="/projetweb/ablelink/hide_demo_stories.php" style="color: #60a5fa; text-decoration: none; font-weight: 600;">🔒 Gérer les stories</a>
            </div>
        </div>
    </div>
</body>
</html>
