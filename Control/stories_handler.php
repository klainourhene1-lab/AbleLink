<?php
// stories_handler.php in Control directory
// Turn off error display to prevent HTML error output in JSON response
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    session_start();
    require_once __DIR__ . '/StoryController.php';

    $controller = new StoryController();
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    // Handle JSON input for POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input && isset($input['action'])) {
            $action = $input['action'];
        }
    }

    file_put_contents('debug_handler.log', date('Y-m-d H:i:s') . " - Action: $action - User: " . ($_SESSION['user_id'] ?? 'Guest') . "\n", FILE_APPEND);

    switch ($action) {
        case 'get_comments':
            $controller->getComments();
            break;
        case 'add_comment':
            try {
                $controller->addComment();
            } catch (Exception $e) {
                file_put_contents('debug_handler.log', "Error addComment: " . $e->getMessage() . "\n", FILE_APPEND);
                throw $e;
            }
            break;
        case 'report_comment':
            $controller->reportComment();
            break;
        case 'like_story':
             $controller->likeStory();
             break;
        case 'like':
             if (isset($_GET['id'])) {
                 $id = $_GET['id'];
             }
             break;
        default:
             echo json_encode(['success' => false, 'message' => 'Invalid action ' . $action]);
             break;
    }

} catch (Exception $e) {
    file_put_contents('debug_handler.log', "Exception: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
