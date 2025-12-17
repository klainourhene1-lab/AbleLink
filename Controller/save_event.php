<?php
// save_event.php - Route to EventController (MVC)
require_once __DIR__ . '/EventController.php';

$controller = new EventController();
$controller->handleRequest();
?>