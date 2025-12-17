<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

session_start();
$userId = $_SESSION['user_id'] ?? null;

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    // Handle JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    if ($input && isset($input['action'])) {
        $action = $input['action'];
    }

    switch ($action) {
        // --- FAVORITES ---
        case 'toggle_favorite':
            if (!$userId) throw new Exception('Not logged in');
            require_once __DIR__ . '/FavorisController.php';
            $ctrl = new FavorisController();
            $offreId = $input['offre_id'] ?? $_POST['offre_id'] ?? null;
            if (!$offreId) throw new Exception('Missing offer ID');
            
            echo json_encode($ctrl->toggle($userId, $offreId));
            break;

        case 'check_favorite':
            if (!$userId) { echo json_encode(['isFavorite' => false]); break; }
            require_once __DIR__ . '/FavorisController.php';
            $ctrl = new FavorisController();
            $offreId = $_GET['offre_id'] ?? null;
            if (!$offreId) throw new Exception('Missing offer ID');
            
            echo json_encode(['isFavorite' => $ctrl->isFavorite($userId, $offreId)]);
            break;

        // --- AI ---
        case 'analyze_job':
            require_once __DIR__ . '/AIAssistantController.php';
            $ctrl = new AIAssistantController();
            $desc = $input['description'] ?? $_POST['description'] ?? '';
            echo json_encode($ctrl->analyze($desc));
            break;

        case 'optimize_job':
            require_once __DIR__ . '/AIAssistantController.php';
            $ctrl = new AIAssistantController();
            $desc = $input['description'] ?? $_POST['description'] ?? '';
            echo json_encode($ctrl->optimize($desc));
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
