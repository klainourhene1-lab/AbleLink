<?php
session_start();

// التحقق من أن المستخدم مسجل الدخول وهو أدمن
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once "../config.php";
require_once "../controllers/UserController.php";

if (isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    $controller = new UserController();
    
    // Prevent admin from deleting themselves
    if ($user_id == $_SESSION['user_id']) {
        header('Location: index1.php?error=cannot_delete_self');
        exit;
    }
    
    if ($controller->deleteUser($user_id)) {
        header('Location: index1.php?success=user_deleted');
        exit;
    } else {
        header('Location: index1.php?error=delete_failed');
        exit;
    }
}

header('Location: index1.php');
exit;
?>