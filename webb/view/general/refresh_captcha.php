<?php
session_start();

function generateCaptcha() {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $captcha = '';
    for ($i = 0; $i < 5; $i++) {
        $captcha .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $captcha;
}

$_SESSION['captcha'] = generateCaptcha();

header('Content-Type: application/json');
echo json_encode(['captcha' => $_SESSION['captcha']]);
?>