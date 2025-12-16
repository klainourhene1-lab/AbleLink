<?php
// Test des stories
require __DIR__ . '/app/Core/Database.php';

try {
    $pdo = App\Core\Database::getInstance();
    
    // Count all stories
    $stmt = $pdo->query('SELECT COUNT(*) as c FROM success_stories');
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total stories: " . $row['c'] . "\n";
    
    // Count approved stories
    $stmt2 = $pdo->query('SELECT COUNT(*) as c FROM success_stories WHERE status = "approved"');
    $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    echo "Approved stories: " . $row2['c'] . "\n";
    
    // Afficher les 3 premières stories
    $stmt3 = $pdo->query('SELECT id, title, status FROM success_stories LIMIT 3');
    $stories = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    echo "\nPremières stories:\n";
    foreach ($stories as $story) {
        echo "- ID: {$story['id']}, Title: {$story['title']}, Status: " . ($story['status'] ?? 'N/A') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
