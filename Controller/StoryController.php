<?php
require_once __DIR__ . '/../Model/StoryModel.php';
require_once __DIR__ . '/../Model/User.php';

class StoryController {
    private $model;
    
    public function __construct() {
        $this->model = new StoryModel();
    }

    public function index() {
        $filter = [
            'q' => $_GET['q'] ?? '',
            'author' => $_GET['author'] ?? '',
            'cat' => $_GET['cat'] ?? ''
        ];
        $sort = $_GET['sort'] ?? 'recent';

        // Check specific custom filter "my_stories"
        if (isset($_GET['filter']) && $_GET['filter'] === 'my_stories' && isset($_SESSION['user_id'])) {
            return $this->model->getByUserId($_SESSION['user_id']);
        }

        return $this->model->getAllApproved($filter, $sort);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_story') {
            if (!isset($_SESSION['user_id'])) {
                header('Location: ../general/signin.php');
                exit;
            }

            $data = [
                'title' => $_POST['title'] ?? '',
                'content' => $_POST['content'] ?? '',
                'author_name' => $_POST['author_name'] ?? 'Anonyme'
            ];

            try {
                $this->model->create($_SESSION['user_id'], $data);
                $redirect = $_POST['redirect_to'] ?? 'success-stories.php?success=1';
                header("Location: $redirect");
                exit;
            } catch (Exception $e) {
                return "Erreur lors de la création: " . $e->getMessage();
            }
        }
        return null;
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
            if (!isset($_SESSION['user_id'])) { header('Location: ../general/signin.php'); exit; }
            
            $id = $_POST['id'];
            $data = [
                'title' => $_POST['title'],
                'author' => $_POST['author_name'],
                'content' => $_POST['content']
            ];

            $this->model->update($id, $_SESSION['user_id'], $data);
            header("Location: historique_stories.php?updated=1");
            exit;
        }
    }

    public function delete() {
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            if (!isset($_SESSION['user_id'])) { header('Location: ../general/signin.php'); exit; }
            
            $this->model->delete($_GET['id'], $_SESSION['user_id']);
            header("Location: historique_stories.php?deleted=1");
            exit;
        }
    }

    public function like() {
        if (isset($_GET['id'])) {
            $this->model->incrementLikes($_GET['id']);
            header('Location: success-stories.php');
            exit;
        }
    }

    public function share() {
        if (isset($_GET['id'])) {
            $this->model->incrementShares($_GET['id']);
            echo json_encode(['success' => true]);
            exit;
        }
    }

    public function getStory() {
        if (isset($_GET['id'])) {
            $story = $this->model->getById($_GET['id']);
            echo json_encode($story);
            exit;
        }
    }
    public function getComments() {
        if (isset($_GET['id'])) {
            $comments = $this->model->getComments($_GET['id']);
            echo json_encode($comments);
            exit;
        }
    }

    public function addComment() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // Read JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        $storyId = $input['story_id'] ?? $_POST['story_id'] ?? null;
        $content = $input['content'] ?? $_POST['content'] ?? null;

        if ($storyId && $content) {
            $result = $this->model->addComment($storyId, $_SESSION['user_id'], $content);
            echo json_encode(['success' => $result]);
        } else {
             echo json_encode(['success' => false, 'message' => 'Missing data']);
        }
    }

    public function reportComment() {
        // ... existing code ...
    }

    public function likeStory() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $storyId = $input['story_id'] ?? $_POST['story_id'] ?? null;

        if ($storyId) {
            $result = $this->model->likeStory($storyId);
            echo json_encode(['success' => $result]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing ID']);
        }
    }
}
