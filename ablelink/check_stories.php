<?php
// Script de diagnostic pour identifier les stories non affichées

require_once __DIR__ . '/app/Core/Database.php';

use App\Core\Database;

// Connexion à la base de données
$pdo = Database::getInstance();

echo "=== DIAGNOSTIC DES SUCCESS STORIES ===\n\n";

// Compter toutes les stories
$stmt = $pdo->query("SELECT COUNT(*) as total FROM success_stories");
$totalStories = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
echo "📊 Total stories en base de données: $totalStories\n\n";

// Compter par statut
echo "📈 Répartition par statut:\n";
$stmt = $pdo->query("SELECT status, COUNT(*) as count FROM success_stories GROUP BY status");
$statusCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($statusCounts as $row) {
    $status = $row['status'] ?? 'NULL';
    $count = $row['count'];
    $icon = $status === 'approved' ? '✅' : ($status === 'pending' ? '⏳' : '❌');
    echo "  $icon $status: $count\n";
}

echo "\n";

// Lister toutes les stories avec détails
echo "📝 Liste détaillée de toutes les stories:\n";
echo str_repeat('-', 120) . "\n";
printf("%-5s | %-40s | %-20s | %-10s | %-20s\n", "ID", "Titre", "Auteur", "Statut", "Date création");
echo str_repeat('-', 120) . "\n";

$stmt = $pdo->query("SELECT id, title, author, status, created_at FROM success_stories ORDER BY id");
$allStories = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($allStories as $story) {
    $id = $story['id'];
    $title = substr($story['title'], 0, 40);
    $author = substr($story['author'] ?? 'N/A', 0, 20);
    $status = $story['status'] ?? 'NULL';
    $created = $story['created_at'] ?? 'N/A';
    
    $icon = $status === 'approved' ? '✅' : ($status === 'pending' ? '⏳' : '❌');
    
    printf("%-5s | %-40s | %-20s | %-10s | %-20s %s\n", 
        $id, $title, $author, $status, $created, $icon);
}

echo str_repeat('-', 120) . "\n";

// Stories qui s'affichent sur l'interface publique
$stmt = $pdo->query("SELECT COUNT(*) as count FROM success_stories WHERE status = 'approved'");
$approvedCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

echo "\n✅ Stories VISIBLES sur l'interface publique (status='approved'): $approvedCount\n";

// Stories qui NE s'affichent PAS
$hiddenCount = $totalStories - $approvedCount;
echo "🔒 Stories CACHÉES (pending/rejected/NULL): $hiddenCount\n";

echo "\n";

// Lister les stories cachées
if ($hiddenCount > 0) {
    echo "🔍 Détails des stories CACHÉES:\n";
    echo str_repeat('-', 120) . "\n";
    printf("%-5s | %-40s | %-20s | %-10s | Raison\n", "ID", "Titre", "Auteur", "Statut");
    echo str_repeat('-', 120) . "\n";
    
    $stmt = $pdo->query("SELECT id, title, author, status FROM success_stories WHERE status != 'approved' OR status IS NULL ORDER BY id");
    $hiddenStories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($hiddenStories as $story) {
        $id = $story['id'];
        $title = substr($story['title'], 0, 40);
        $author = substr($story['author'] ?? 'N/A', 0, 20);
        $status = $story['status'] ?? 'NULL';
        
        $reason = '';
        if ($status === 'pending') {
            $reason = "⏳ En attente d'approbation";
        } elseif ($status === 'rejected') {
            $reason = "❌ Rejetée";
        } elseif ($status === 'NULL' || $status === null) {
            $reason = "⚠️ Statut manquant";
        }
        
        printf("%-5s | %-40s | %-20s | %-10s | %s\n", 
            $id, $title, $author, $status, $reason);
    }
    echo str_repeat('-', 120) . "\n";
}

echo "\n💡 Pour rendre une story visible:\n";
echo "   • Aller dans le panneau admin: http://localhost/projetweb/ablelink/historique\n";
echo "   • Cliquer sur 'Approve' pour changer le status à 'approved'\n";
echo "\n";
echo "💡 Ou exécuter directement en SQL:\n";
echo "   UPDATE success_stories SET status = 'approved' WHERE id = <ID>;\n";
echo "\n";
