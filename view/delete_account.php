<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once "../config.php";
require_once "../controllers/UserController.php";

if ($_SESSION['user_role'] === 'admin') {
    header('Location: profile.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new UserController();
    
    if ($controller->deleteUser($_SESSION['user_id'])) {
        session_destroy();
        header('Location: index.php?message=account_deleted');
        exit;
    } else {
        header('Location: profile.php?error=delete_failed');
        exit;
    }
}

header('Location: profile.php');
exit;
?>