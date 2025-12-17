<?php
/**
 * Fix Corrupted Password Hashes
 * Converts plain text passwords to proper bcrypt hashes
 */

require_once __DIR__ . '/Model/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "=== FIXING CORRUPTED PASSWORD HASHES ===\n\n";
    
    // Get all users with invalid hashes
    $stmt = $db->query("SELECT id, email, mot_de_passe FROM utilisateur");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $fixed = 0;
    $errors = 0;
    
    foreach ($users as $user) {
        $hash = $user['mot_de_passe'];
        $isValidBcrypt = strlen($hash) >= 60 && substr($hash, 0, 1) === '$';
        
        if (!$isValidBcrypt) {
            // This is a plain password - hash it
            $plainPassword = $hash;
            $newHash = password_hash($plainPassword, PASSWORD_DEFAULT);
            
            // Update in database
            $updateStmt = $db->prepare("UPDATE utilisateur SET mot_de_passe = ? WHERE id = ?");
            if ($updateStmt->execute([$newHash, $user['id']])) {
                $fixed++;
                echo "✓ Fixed user {$user['id']} ({$user['email']})\n";
                echo "  Original: $plainPassword → Hashed\n";
            } else {
                $errors++;
                echo "✗ Failed to fix user {$user['id']} ({$user['email']})\n";
            }
        }
    }
    
    echo "\n=== SUMMARY ===\n";
    echo "Fixed: $fixed\n";
    echo "Errors: $errors\n";
    
    if ($errors === 0) {
        echo "\n✓ All password hashes have been fixed!\n";
        echo "Users can now sign in with their passwords.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
