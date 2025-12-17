<?php
/**
 * DIAGNOSTIC COMPLET - Pourquoi les stories ne s'affichent pas
 */

require_once __DIR__ . '/app/Core/Database.php';
use App\Core\Database;

$pdo = Database::getInstance();

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Diagnostic Complet</title>
    <style>
        body {
            font-family: monospace;
            background: #0f172a;
            color: #e2e8f0;
            padding: 20px;
            line-height: 1.6;
        }
        .section {
            background: #1e293b;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
        }
        h2 {
            color: #60a5fa;
            margin-top: 0;
        }
        .success {
            color: #10b981;
        }
        .error {
            color: #ef4444;
        }
        .warning {
            color: #f59e0b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #334155;
        }
        th {
            background: #334155;
            color: #60a5fa;
        }
        .code {
            background: #0f172a;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>🔍 DIAGNOSTIC COMPLET - Success Stories</h1>
    
    <div class="section">
        <h2>1. Test de la colonne 'status'</h2>
        <?php
        try {
            $stmt = $pdo->prepare("SHOW COLUMNS FROM success_stories LIKE 'status'");
            $stmt->execute();
            $hasStatus = $stmt->rowCount() > 0;
            if ($hasStatus) {
                echo '<p class="success">✅ La colonne "status" existe</p>';
            } else {
                echo '<p class="error">❌ La colonne "status" N\'EXISTE PAS</p>';
                echo '<p>Solution: Exécutez ce SQL:</p>';
                echo '<div class="code">ALTER TABLE success_stories ADD COLUMN status ENUM("pending","approved","rejected") DEFAULT "pending";</div>';
            }
        } catch (PDOException $e) {
            echo '<p class="error">❌ Erreur: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>
    
    <div class="section">
        <h2>2. Toutes les stories en base de données</h2>
        <?php
        $stmt = $pdo->query("SELECT * FROM success_stories ORDER BY id");
        $allStories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo '<p>Total: <strong>' . count($allStories) . '</strong> stories</p>';
        ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Status</th>
                    <th>Visible?</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allStories as $story): ?>
                    <?php
                        $status = $story['status'] ?? 'NULL';
                        $visible = ($status === 'approved') ? '<span class="success">✅ OUI</span>' : '<span class="error">❌ NON</span>';
                    ?>
                    <tr>
                        <td><?php echo $story['id']; ?></td>
                        <td><?php echo htmlspecialchars(substr($story['title'], 0, 40)); ?></td>
                        <td><?php echo htmlspecialchars($story['author'] ?? 'N/A'); ?></td>
                        <td><?php echo $status; ?></td>
                        <td><?php echo $visible; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="section">
        <h2>3. Test de la requête du CONTROLLER (ce que /success-stories devrait afficher)</h2>
        <?php
        $hasStatus = $pdo->prepare("SHOW COLUMNS FROM success_stories LIKE 'status'")->execute() && $pdo->prepare("SHOW COLUMNS FROM success_stories LIKE 'status'")->rowCount() > 0;
        
        if ($hasStatus) {
            $sql = "SELECT * FROM success_stories WHERE status = 'approved' ORDER BY created_at DESC";
        } else {
            $sql = "SELECT * FROM success_stories ORDER BY created_at DESC";
        }
        
        echo '<p><strong>Requête SQL utilisée:</strong></p>';
        echo '<div class="code">' . htmlspecialchars($sql) . '</div>';
        
        $stmt = $pdo->query($sql);
        $publicStories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo '<p>Résultats: <strong>' . count($publicStories) . '</strong> stories</p>';
        
        if (count($publicStories) > 0) {
            echo '<p class="success">✅ Le controller DEVRAIT afficher ' . count($publicStories) . ' stories</p>';
            echo '<table>';
            echo '<thead><tr><th>ID</th><th>Titre</th><th>Auteur</th></tr></thead><tbody>';
            foreach ($publicStories as $story) {
                echo '<tr>';
                echo '<td>' . $story['id'] . '</td>';
                echo '<td>' . htmlspecialchars($story['title']) . '</td>';
                echo '<td>' . htmlspecialchars($story['author'] ?? 'N/A') . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p class="error">❌ Aucune story ne sera affichée sur la page publique!</p>';
            
            if ($hasStatus) {
                echo '<p class="warning">⚠️ Problème: Toutes vos stories ont un statut différent de "approved"</p>';
                echo '<p><strong>Solution:</strong> Changez le statut de vos stories à "approved":</p>';
                echo '<div class="code">UPDATE success_stories SET status = "approved" WHERE id IN (5, 6);</div>';
                echo '<p>Ou utilisez: <a href="/projetweb/ablelink/hide_demo_stories.php" style="color: #60a5fa;">hide_demo_stories.php</a></p>';
            }
        }
        ?>
    </div>
    
    <div class="section">
        <h2>4. Test de la requête HOME (ce que /home devrait afficher)</h2>
        <?php
        if ($hasStatus) {
            $sql = "SELECT * FROM success_stories WHERE status = 'approved' ORDER BY created_at DESC LIMIT 6";
        } else {
            $sql = "SELECT * FROM success_stories ORDER BY created_at DESC LIMIT 6";
        }
        
        echo '<p><strong>Requête SQL utilisée:</strong></p>';
        echo '<div class="code">' . htmlspecialchars($sql) . '</div>';
        
        $stmt = $pdo->query($sql);
        $homeStories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo '<p>Résultats: <strong>' . count($homeStories) . '</strong> stories (max 6)</p>';
        
        if (count($homeStories) > 0) {
            echo '<p class="success">✅ La page d\'accueil DEVRAIT afficher ' . count($homeStories) . ' stories</p>';
        } else {
            echo '<p class="error">❌ La page d\'accueil n\'affichera AUCUNE story!</p>';
        }
        ?>
    </div>
    
    <div class="section">
        <h2>5. SOLUTION</h2>
        <?php
        $pendingOrRejected = array_filter($allStories, fn($s) => ($s['status'] ?? '') !== 'approved');
        
        if (count($pendingOrRejected) > 0) {
            echo '<p class="warning">⚠️ Vous avez ' . count($pendingOrRejected) . ' stories qui ne sont PAS visibles car leur statut n\'est pas "approved"</p>';
            echo '<p><strong>Pour les rendre visibles, changez leur statut:</strong></p>';
            echo '<ol>';
            echo '<li>Via l\'<a href="/projetweb/ablelink/hide_demo_stories.php" style="color: #60a5fa;">interface web</a></li>';
            echo '<li>Via le <a href="/projetweb/ablelink/historique" style="color: #60a5fa;">panneau admin</a></li>';
            echo '<li>Via SQL:</li>';
            echo '</ol>';
            echo '<div class="code">UPDATE success_stories SET status = "approved" WHERE id IN (';
            echo implode(', ', array_column($pendingOrRejected, 'id'));
            echo ');</div>';
        } else {
            echo '<p class="success">✅ Toutes vos stories sont "approved" et devraient être visibles!</p>';
            echo '<p>Si elles ne s\'affichent toujours pas, vérifiez:</p>';
            echo '<ul>';
            echo '<li>Que vous êtes sur: <a href="/projetweb/ablelink/success-stories" style="color: #60a5fa;">http://localhost/projetweb/ablelink/success-stories</a></li>';
            echo '<li>Videz le cache de votre navigateur (Ctrl+F5)</li>';
            echo '<li>Vérifiez les logs PHP pour des erreurs</li>';
            echo '</ul>';
        }
        ?>
    </div>
    
    <div class="section">
        <h2>6. Liens de vérification</h2>
        <p>Cliquez sur ces liens pour vérifier:</p>
        <ul>
            <li><a href="/projetweb/ablelink/success-stories" style="color: #60a5fa;">📖 Page Success Stories publique</a></li>
            <li><a href="/projetweb/ablelink/home" style="color: #60a5fa;">🏠 Page d'accueil</a></li>
            <li><a href="/projetweb/ablelink/historique" style="color: #60a5fa;">⚙️ Admin - Historique</a></li>
            <li><a href="/projetweb/ablelink/hide_demo_stories.php" style="color: #60a5fa;">🔧 Gérer les stories</a></li>
        </ul>
    </div>
</body>
</html>
