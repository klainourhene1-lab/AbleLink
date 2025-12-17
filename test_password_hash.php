<?php
/**
 * Password Hash Test Script
 * Test to verify password hashing and verification works correctly
 */

require_once __DIR__ . '/Model/User.php';

echo "=== PASSWORD HASHING TEST ===\n\n";

$testPassword = "testpass123";

// Test 1: Create new user with plain password (should hash it)
echo "Test 1: Creating NEW user with plain password...\n";
$newUser = new User(
    null,  // id = null (new user)
    "Test", "User", "test@example.com", "12345678", 
    $testPassword  // plain password
);

$storedHash = $newUser->getMotDePasse();
echo "Input password: $testPassword\n";
echo "Stored hash: " . substr($storedHash, 0, 50) . "...\n";
echo "Hash length: " . strlen($storedHash) . "\n";
echo "Is valid bcrypt hash? " . (strlen($storedHash) >= 60 && substr($storedHash, 0, 1) === '$' ? "YES" : "NO") . "\n";

// Test 2: Verify password
echo "\nTest 2: Verifying password against new user...\n";
$verifyResult = $newUser->verifyPassword($testPassword);
echo "Verify result: " . ($verifyResult ? "SUCCESS ✓" : "FAILED ✗") . "\n";

// Test 3: Load user from DB (simulated - with already hashed password)
echo "\nTest 3: Loading user with PRE-HASHED password (from DB simulation)...\n";
$loadedUser = new User(
    1,  // id = 1 (existing user from DB)
    "Test", "User", "test@example.com", "12345678",
    $storedHash  // already hashed password from DB
);

$loadedHash = $loadedUser->getMotDePasse();
echo "Loaded hash: " . substr($loadedHash, 0, 50) . "...\n";
echo "Hash unchanged? " . ($loadedHash === $storedHash ? "YES" : "NO (PROBLEM!)") . "\n";

// Test 4: Verify password on loaded user
echo "\nTest 4: Verifying password against loaded user...\n";
$verifyLoaded = $loadedUser->verifyPassword($testPassword);
echo "Verify result: " . ($verifyLoaded ? "SUCCESS ✓" : "FAILED ✗") . "\n";

// Test 5: Try wrong password
echo "\nTest 5: Verifying WRONG password...\n";
$wrongVerify = $newUser->verifyPassword("wrongpassword");
echo "Verify result: " . ($wrongVerify ? "UNEXPECTED SUCCESS (PROBLEM!)" : "Correctly rejected ✓") . "\n";

echo "\n=== TEST COMPLETE ===\n";
?>
