<?php
// admin_dashboard.php - Controller for Admin Dashboard (MVC)
// Start output buffering to prevent any accidental output
ob_start();

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../view/general/signin.php');
    exit;
}

// Check if user is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    // Redirect non-admin users to events page
    header('Location: ../view/FrontOffice/evaluations-evenements.php');
    exit;
}

require_once __DIR__ . '/AdminController.php';

// Handle AJAX POST requests (both JSON and FormData)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check Content-Type to determine if it's an AJAX request
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $isJsonRequest = strpos($contentType, 'application/json') !== false;
    $isFormDataRequest = isset($_POST['action']);
    
    // Route to AdminController for AJAX requests
    if ($isJsonRequest || $isFormDataRequest) {
        // Clear any output that might have been generated
        ob_clean();
        $controller = new AdminController();
        $controller->handleAjaxRequest();
        exit;
    }
}

// Get dashboard data using AdminController
$controller = new AdminController();
$dashboardData = $controller->getDashboardData();

// Get recent activities
$recentActivities = $controller->getRecentActivities();

// Extract data for view
$stats = $dashboardData['stats'];
$pendingEvents = $dashboardData['pendingEvents'];
$reportedEvaluations = $dashboardData['reportedEvaluations'];
$enterprisesStats = $dashboardData['enterprisesStats'];
$analyticsData = $dashboardData['analyticsData'];
$statusDistribution = $dashboardData['statusDistribution'];
$storyStats = $dashboardData['storyStats'] ?? [];

// Prepare chart data
$monthlyEvents = array_fill(0, 12, 0);
$monthlyParticipants = array_fill(0, 12, 0);
foreach ($analyticsData as $data) {
    $monthlyEvents[$data['month'] - 1] = (int)$data['event_count'];
    $monthlyParticipants[$data['month'] - 1] = (int)$data['avg_participants'];
}

// Helper functions for view
function truncateText($text, $length) {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}

function renderStars($rating) {
    $numRating = floatval($rating);
    $fullStars = floor($numRating);
    $hasHalfStar = ($numRating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
    
    $stars = '';
    for ($i = 0; $i < $fullStars; $i++) {
        $stars .= '★';
    }
    if ($hasHalfStar) {
        $stars .= '½';
    }
    for ($i = 0; $i < $emptyStars; $i++) {
        $stars .= '☆';
    }
    
    return $stars;
}

// Include the view
require_once __DIR__ . '/../view/Backoffice/admin_dashboard_view.php';
