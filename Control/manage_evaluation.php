<?php
// manage_evaluation.php - Route to EvaluationController (MVC)

// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering to catch errors
ob_start();

// Include the controller
require_once __DIR__ . '/EvaluationController.php';

// Create and handle request
$controller = new EvaluationController();
$controller->handleRequest();

// Clean any output buffer
if (ob_get_length()) {
    ob_end_clean();
}
?>