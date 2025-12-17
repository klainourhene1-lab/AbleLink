<?php
require_once __DIR__ . '/Control/config.php';
try {
    $pdo = new PDO("mysql:host=localhost;dbname=projet", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("DESCRIBE success_stories");
    echo "Table 'success_stories' columns:\n";
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
        echo "- {$col['Field']} ({$col['Type']})\n";
    }

    $stmt = $pdo->query("DESCRIBE story_comments");
    echo "\nTable 'story_comments' columns:\n";
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
        echo "- {$col['Field']} ({$col['Type']})\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
