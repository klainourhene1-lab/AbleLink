<?php
// manage_participation.php - Route to ParticipationController (MVC)
require_once __DIR__ . '/ParticipationController.php';

$controller = new ParticipationController();
$controller->handleRequest();
?>