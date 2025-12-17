<?php
/**
 * Database Password Hash Fix
 * This script checks for corrupted password hashes and fixes them
 */

require_once __DIR__ . '/Model/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "=== PASSWORD HASH ANALYSIS ===\n\n";
    
    // Get all users
    $stmt = $db->query("SELECT id, email, mot_de_passe FROM utilisateur");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total users: " . count($users) . "\n";
    echo "Analyzing password hashes...\n\n";
    
    $corrupted = 0;
    $valid = 0;
    
    foreach ($users as $user) {
        $hash = $user['mot_de_passe'];
        $hashLen = strlen($hash);
        $startsWithDollar = substr($hash, 0, 1) === '$';
        $isValidBcrypt = $hashLen >= 60 && $startsWithDollar;
        
        if (!$isValidBcrypt) {
            $corrupted++;
            echo "❌ User {$user['id']} ({$user['email']}): ";
            echo "Hash length=$hashLen, Starts with $=" . ($startsWithDollar ? 'YES' : 'NO') . "\n";
            echo "   Hash preview: " . substr($hash, 0, 50) . "\n";
        } else {
            $valid++;
        }
    }
    
    echo "\n=== SUMMARY ===\n";
    echo "Valid bcrypt hashes: $valid\n";
    echo "Corrupted/Invalid hashes: $corrupted\n";
    
    if ($corrupted === 0) {
        echo "\n✓ All password hashes are valid!\n";
    } else {
        echo "\n❌ Found $corrupted users with invalid hashes.\n";
        echo "You may need to reset these users' passwords.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
