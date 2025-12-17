<?php
require_once __DIR__ . '/Model/Database.php';

try {
    $db = Database::getInstance()->getConnection();

    $tables = ['evenement', 'offre', 'candidature', 'evaluation', 'utilisateur'];
    echo "<h1>Database Counts</h1>";
    echo "<ul>";
    foreach ($tables as $table) {
        try {
            $stmt = $db->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo "<li><strong>$table</strong>: $count</li>";
        } catch (Exception $e) {
            echo "<li><strong>$table</strong>: Error - " . $e->getMessage() . "</li>";
        }
    }
    echo "</ul>";

} catch (Exception $e) {
    echo "Error connecting to database: " . $e->getMessage();
}
?>
