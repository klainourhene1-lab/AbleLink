<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/SocialController.php';

header('Content-Type: application/json');
session_start();

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? $input['action'] ?? '';
$social = new SocialController();

switch ($action) {
    case 'post':
        $res = $social->handlePostSubmission($userId, $input);
        echo json_encode(['success' => $res]);
        break;

    case 'comment':
        $res = $social->handleComment($userId, $input);
        echo json_encode(['success' => $res]);
        break;

    case 'like':
        $postId = $input['postId'] ?? null;
        if ($postId) {
            $res = $social->handleLike($userId, $postId);
            echo json_encode(['success' => true, 'status' => $res]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing postId']);
        }
        break;

    case 'friend_request':
        $targetId = $input['targetId'] ?? null;
        if ($targetId) {
            $res = $social->sendFriendRequest($userId, $targetId);
            echo json_encode(['success' => $res]);
        }
         else {
            echo json_encode(['success' => false, 'message' => 'Missing targetId']);
        }
        break;

    case 'accept_friend':
        $requestId = $input['requestId'] ?? null;
        if ($requestId) {
            $res = $social->acceptFriendRequest($requestId);
            echo json_encode(['success' => $res]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing requestId']);
        }
        break;

    case 'search_users':
        $query = $input['query'] ?? '';
        if (strlen($query) < 2) {
            echo json_encode(['success' => true, 'users' => []]);
            break;
        }
        $users = $social->searchUsers($query, $userId);
        
        // Enrich with friendship status
        foreach ($users as &$u) {
            $status = $social->getAmisModel()->checkFriendshipStatus($userId, $u['id']);
            $u['friendship'] = $status;
        }
        
        echo json_encode(['success' => true, 'users' => $users]);
        break;

    case 'reject_request':
        $requestId = $input['requestId'] ?? null;
        if ($requestId && $social->rejectRequest($requestId)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'get_notifications':
        $requests = $social->getAmisModel()->getPendingRequests($userId);
        echo json_encode(['success' => true, 'notifications' => $requests]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action inconnue']);
        break;
}
?>
