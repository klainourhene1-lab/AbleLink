<?php
// get_event_details.php - Get complete event details with evaluations

require_once __DIR__ . '/EventController.php';

$hasBuffer = ob_get_level();
if ($hasBuffer) {
    ob_clean();
}
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

$controller = new EventController();

// Get event ID
$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$eventId) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Event ID required'
    ]);
    exit;
}

try {
    $eventData = ['id' => $eventId];
    $eventResult = $controller->get($eventData);
    
    if (!isset($eventResult['success']) || !$eventResult['success']) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Event not found'
        ]);
        exit;
    }
    
    $event = $eventResult['event'];
    
    require_once __DIR__ . '/../Model/EvaluationModel.php';
    $evaluationModel = new EvaluationModel();
    $evaluations = $evaluationModel->getByEventId($eventId);
    $event['evaluations'] = $evaluations;
    
    require_once __DIR__ . '/../Model/UserModel.php';
    $userModel = new UserModel();
    $organizer = $userModel->getById($event['idUtilisateur']);
    if ($organizer) {
        $event['nom_entreprise'] = $organizer['nom'];
        $event['prenom_entreprise'] = $organizer['prenom'];
        $event['organisateur'] = $organizer['prenom'] . ' ' . $organizer['nom'];
    }
    
    echo json_encode([
        'success' => true,
        'event' => $event
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
