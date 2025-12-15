<?php
// admin_moderation.php - Route to AdminController (MVC)
require_once __DIR__ . '/AdminController.php';

$controller = new AdminController();
$controller->handleAjaxRequest();
?>