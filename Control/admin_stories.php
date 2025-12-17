<?php
// admin_stories.php - Controller for Success Stories Moderation
ob_start();

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../view/general/signin.php');
    exit;
}

// Check if user is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header('Location: ../view/FrontOffice/evaluations-evenements.php');
    exit;
}

require_once __DIR__ . '/AdminController.php';

// Handle AJAX POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $isJsonRequest = strpos($contentType, 'application/json') !== false;
    $isFormDataRequest = isset($_POST['action']);
    
    if ($isJsonRequest || $isFormDataRequest) {
        ob_clean();
        $controller = new AdminController();
        $controller->handleAjaxRequest();
        exit;
    }
}

// Get data
$controller = new AdminController();
$dashboardData = $controller->getDashboardData();

// Extract relevant data
$pendingStories = $dashboardData['pendingStories'] ?? [];
$storyStats = $dashboardData['storyStats'] ?? [];
$storyAnalytics = $dashboardData['storyAnalytics'] ?? [];

// Helper functions (required because view might use them)
function truncateText($text, $length) {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}

require_once __DIR__ . '/../view/BackOffice/admin_stories_view.php';
