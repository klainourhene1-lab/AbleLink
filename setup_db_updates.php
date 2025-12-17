<?php
require_once __DIR__ . '/Control/config.php';

echo "Updating database schema for Job Board Moderation...\n";

try {
    $db = Config::getConnexion();
    
    // Add statut column if not exists
    try {
        $db->query("SELECT statut FROM offres LIMIT 1");
        echo "Column 'statut' already exists.\n";
    } catch (PDOException $e) {
        $sql = "ALTER TABLE offres ADD COLUMN statut ENUM('pending', 'published', 'rejected') DEFAULT 'pending'";
        $db->exec($sql);
        echo "Column 'statut' added.\n";
    }

    // Add user_id column if not exists
    try {
        $db->query("SELECT user_id FROM offres LIMIT 1");
        echo "Column 'user_id' already exists.\n";
    } catch (PDOException $e) {
        $sql = "ALTER TABLE offres ADD COLUMN user_id INT DEFAULT NULL";
        $db->exec($sql);
        // Add FK
        try {
            $db->exec("ALTER TABLE offres ADD CONSTRAINT fk_offres_user FOREIGN KEY (user_id) REFERENCES utilisateur(id) ON DELETE SET NULL");
            echo "Foreign key for 'user_id' added.\n";
        } catch (Exception $ex) {
            echo "Warning adding FK: " . $ex->getMessage() . "\n";
        }
        echo "Column 'user_id' added.\n";
    }

    echo "Database updates completed successfully.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
