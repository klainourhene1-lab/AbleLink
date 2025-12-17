<?php
/**
 * Script de diagnostic - Vérification de l'affichage des Success Stories
 * Affiche les stories qui devraient apparaître sur l'interface publique
 */

require_once __DIR__ . '/app/Core/Database.php';
use App\Core\Database;

$pdo = Database::getInstance();

// Simuler ce que fait le contrôleur pour l'affichage public
$stmt = $pdo->query("SELECT * FROM success_stories WHERE status = 'approved' ORDER BY created_at DESC");
$publicStories = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostic Stories</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            padding: 30px;
            line-height: 1.6;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        h1 {
            color: #60a5fa;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        h2 {
            color: #a78bfa;
            margin-top: 30px;
        }
        .stats {
            background: #1e293b;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #334155;
            margin-bottom: 30px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .stat-card {
            background: #0f172a;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
        }
        .stat-label {
            color: #94a3b8;
            font-size: 14px;
        }
        .stat-value {
            color: #60a5fa;
            font-size: 28px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #1e293b;
            border-radius: 12px;
            overflow: hidden;
            margin-top: 20px;
        }
        thead {
            background: #334155;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #334155;
        }
        th {
            color: #60a5fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }
        tr:hover {
            background: #2d3748;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success {
            background: #10b981;
            color: white;
        }
        .badge-warning {
            background: #f59e0b;
            color: white;
        }
        .badge-danger {
            background: #ef4444;
            color: white;
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
        .card {
            background: #1e293b;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #334155;
            margin-bottom: 20px;
        }
        .card-title {
            color: #60a5fa;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .excerpt {
            color: #94a3b8;
            font-size: 14px;
            line-height: 1.6;
            margin-top: 10px;
        }
        .links {
            margin-top: 30px;
            padding: 20px;
            background: #1e293b;
            border-radius: 12px;
            border: 1px solid #334155;
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
        <h1>🔍 Diagnostic des Success Stories</h1>
        
        <div class="stats">
            <h3 style="margin-top: 0; color: #e2e8f0;">📊 Statistiques</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Stories Approuvées</div>
                    <div class="stat-value"><?php echo count($publicStories); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Visibles sur /success-stories</div>
                    <div class="stat-value"><?php echo count($publicStories); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Affichées sur /home</div>
                    <div class="stat-value"><?php echo min(count($publicStories), 6); ?></div>
                </div>
            </div>
        </div>

        <?php if (count($publicStories) > 0): ?>
            <div class="alert alert-success">
                ✅ <strong>Bonne nouvelle!</strong> Il y a <?php echo count($publicStories); ?> stories approuvées qui DEVRAIENT s'afficher sur l'interface.
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                ⚠️ <strong>Attention!</strong> Aucune story approuvée n'a été trouvée. Les stories doivent avoir status='approved' pour être visibles.
            </div>
        <?php endif; ?>

        <h2>📝 Liste des Stories Publiques (status='approved')</h2>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Statut</th>
                    <th>Likes</th>
                    <th>Date de création</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($publicStories as $story): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($story['id']); ?></td>
                        <td><strong><?php echo htmlspecialchars($story['title']); ?></strong></td>
                        <td><?php echo htmlspecialchars($story['author'] ?? 'N/A'); ?></td>
                        <td><span class="badge badge-success">approved</span></td>
                        <td><?php echo $story['likes'] ?? 0; ?> ❤️</td>
                        <td><?php echo date('d/m/Y H:i', strtotime($story['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>📄 Aperçu des Stories</h2>
        
        <?php foreach ($publicStories as $story): ?>
            <div class="card">
                <div class="card-title">
                    <?php echo htmlspecialchars($story['title']); ?>
                </div>
                <div style="color: #94a3b8; font-size: 13px;">
                    Par <?php echo htmlspecialchars($story['author']  ?? 'N/A'); ?> | 
                    <?php echo date('d F Y', strtotime($story['created_at'])); ?> |
                    ❤️ <?php echo $story['likes'] ?? 0; ?> likes
                </div>
                <div class="excerpt">
                    <?php 
                        $content = $story['content'] ?? $story['description'] ?? '';
                        $excerpt = mb_substr(strip_tags($content), 0, 200) . '...';
                        echo htmlspecialchars($excerpt);
                    ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="alert alert-info">
            💡 <strong>Pour vérifier l'affichage:</strong><br>
            • Page publique des stories: <a href="/projetweb/ablelink/success-stories" style="color: #93c5fd;">http://localhost/projetweb/ablelink/success-stories</a><br>
            • Page d'accueil: <a href="/projetweb/ablelink/home" style="color: #93c5fd;">http://localhost/projetweb/ablelink/home</a><br>
            • Panneau admin: <a href="/projetweb/ablelink/historique" style="color: #93c5fd;">http://localhost/projetweb/ablelink/historique</a>
        </div>

        <div class="links">
            <h3 style="margin-top: 0; color: #e2e8f0;">🔗 Liens rapides</h3>
            <a href="/projetweb/ablelink/success-stories">📖 Voir les Stories Publiques</a>
            <a href="/projetweb/ablelink/home">🏠 Page d'accueil</a>
            <a href="/projetweb/ablelink/historique">⚙️ Admin - Historique</a>
            <a href="/projetweb/ablelink/success-stories/create">➕ Créer une nouvelle story</a>
        </div>
    </div>
</body>
</html>
