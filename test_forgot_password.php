<?php
session_start();
require_once __DIR__ . '/Control/UserController.php';

// Test forgot password workflow
echo "<h2>Forgot Password Test</h2>";

$controller = new UserController();

// Test 1: Check if a test user exists
$testEmail = "klai.nourhene1@gmail.com";
echo "<p><strong>Test 1:</strong> Check if email exists ($testEmail)</p>";
if ($controller->emailExists($testEmail)) {
    echo "✓ Email exists!<br>";
    
    // Test 2: Create reset token
    echo "<p><strong>Test 2:</strong> Create password reset token</p>";
    $token = $controller->createResetToken($testEmail);
    if ($token) {
        echo "✓ Token created: <code>" . substr($token, 0, 10) . "...</code><br>";
        
        // Test 3: Validate token
        echo "<p><strong>Test 3:</strong> Validate reset token</p>";
        $tokenData = $controller->validateToken($token);
        if ($tokenData) {
            echo "✓ Token is valid!<br>";
            echo "Token data: <pre>" . json_encode($tokenData, JSON_PRETTY_PRINT) . "</pre>";
            
            // Test 4: Send reset email (won't actually work on localhost without proper SMTP)
            echo "<p><strong>Test 4:</strong> Test reset email sending</p>";
            if ($controller->sendPasswordResetEmail($testEmail, $token)) {
                echo "✓ Email sent successfully!<br>";
            } else {
                echo "✗ Email sending failed (check SMTP config)<br>";
                if (isset($_SESSION['debug_reset_link'])) {
                    echo "Debug link: <a href='" . $_SESSION['debug_reset_link'] . "' target='_blank'>" . $_SESSION['debug_reset_link'] . "</a><br>";
                }
            }
        } else {
            echo "✗ Token validation failed<br>";
        }
    } else {
        echo "✗ Token creation failed<br>";
    }
} else {
    echo "✗ Email not found in database<br>";
}

echo "<p><strong>Reset Password Link (for manual testing):</strong></p>";
echo "Forgot Password Form: <a href='view/general/forgot-password.php' target='_blank'>view/general/forgot-password.php</a>";
?>
