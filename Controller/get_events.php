<?php
// get_events.php - Route to EventController (MVC)
require_once __DIR__ . '/EventController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$controller = new EventController();
$data = ['action' => 'get_all'];
$result = $controller->getAll($data);

// Return simplified list for compatibility
if (isset($result['events'])) {
    $simpleList = array_map(function($event) {
        return ['id' => $event['id'], 'titre' => $event['titre']];
    }, $result['events']);
    echo json_encode($simpleList);
} else {
    echo json_encode(['error' => 'Failed to fetch events']);
}
?>