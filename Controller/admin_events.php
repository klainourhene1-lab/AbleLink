<?php
// admin_events.php - Controller for Event Moderation
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
    // Check if it's a JSON request or standard form POST
    $isJsonRequest = strpos($contentType, 'application/json') !== false;
    $isFormDataRequest = isset($_POST['action']);
    
    if ($isJsonRequest || $isFormDataRequest) {
        // Clear buffer ensuring no extra whitespace
        ob_clean();
        $controller = new AdminController();
        $controller->handleAjaxRequest();
        exit;
    }
}

// Get data
$controller = new AdminController();
$dashboardData = $controller->getDashboardData();

// Extract Event specific data
$stats = $dashboardData['stats'] ?? [];
$pendingEvents = $dashboardData['pendingEvents'] ?? [];
$reportedEvaluations = $dashboardData['reportedEvaluations'] ?? [];
$analyticsData = $dashboardData['analyticsData'] ?? [];
$statusDistribution = $dashboardData['statusDistribution'] ?? [];
$enterprisesStats = $dashboardData['enterprisesStats'] ?? [];

// Prepare chart data
$monthlyEvents = array_fill(0, 12, 0);
$monthlyParticipants = array_fill(0, 12, 0);
foreach ($analyticsData as $data) {
    if (isset($data['month'])) {
        $monthlyEvents[$data['month'] - 1] = (int)($data['event_count'] ?? 0);
        $monthlyParticipants[$data['month'] - 1] = (int)($data['avg_participants'] ?? 0);
    }
}

// Helper functions (required because view might use them)
function truncateText($text, $length) {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}

function renderStars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $stars .= '<i class="fas fa-star text-warning"></i>';
        } else {
            $stars .= '<i class="far fa-star text-gray-400"></i>';
        }
    }
    return $stars;
}

require_once __DIR__ . '/../view/BackOffice/admin_events_view.php';
