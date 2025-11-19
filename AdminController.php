<?php
class AdminController extends Controller {
    private $story;
    private $comment;
    
    public function __construct() { 
        $this->story = new Story(); 
        $this->comment = new Comment();
        $this->checkAuth();
    }

    private function render($view, $data = array(), $useAdminLayout = true) {
        if (!is_array($data)) {
            $data = array();
        }
        $this->view($view, $data, $useAdminLayout ? 'admin' : 'main');
    }

    private function dashboardStats() {
        return array(
            'total_stories' => $this->story->countAll(),
            'total_comments' => $this->comment->countAll(),
            'total_likes' => $this->story->countLikes()
        );
    }
    
    private function checkAuth() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            if (!isset($_GET['url']) || strpos($_GET['url'], 'admin/login') === false) {
                $this->redirect('admin/login');
                exit;
            }
        }
    }
    
    public function login() {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            // Authentification simple (à changer avec une vraie authentification)
            if ($_POST['username'] === 'admin' && $_POST['password'] === 'admin123') {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $_POST['username'];
                $_SESSION['flash'] = "Connexion réussie !";
                $this->redirect('admin');
            } else {
                $_SESSION['flash'] = "Identifiants incorrects";
            }
        }
        $this->render('admin/login.html', array(), false);
    }
    
    public function logout() {
        session_destroy();
        $_SESSION['flash'] = "Déconnexion réussie";
        $this->redirect('admin/login');
    }
    
    public function index() {
        $stories = $this->story->allForAdmin();
        $totalStories = is_array($stories) ? count($stories) : 0;
        $stats = $this->dashboardStats();
        $stats['total_stories'] = $totalStories;
        
        $data = compact('stats', 'stories');
        $data['pageTitle'] = 'Tableau de bord';
        $this->render('admin/dashboard.html', $data);
    }
    
    public function stories() {
        $stories = $this->story->allForAdmin();
        if (!is_array($stories)) {
            $stories = array();
        }

        $allowedStatuses = array('pending','published','featured','archived');
        $selectedStatus = isset($_GET['status']) && in_array($_GET['status'], $allowedStatuses, true) ? $_GET['status'] : '';
        $q = isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : '';

        if ($selectedStatus !== '' || $q !== '') {
            $stories = array_filter($stories, function($story) use ($selectedStatus, $q) {
                if ($selectedStatus !== '' && isset($story['status']) && $story['status'] !== $selectedStatus) {
                    return false;
                }
                if ($q !== '') {
                    $hay = strtolower(
                        (isset($story['title']) ? $story['title'] : '') . ' ' .
                        (isset($story['user_name']) ? $story['user_name'] : '')
                    );
                    if (strpos($hay, strtolower($q)) === false) {
                        return false;
                    }
                }
                return true;
            });
        }
        if (is_array($stories)) {
            foreach ($stories as &$s) {
                if (isset($s['id'])) {
                    $s['likes_count'] = $this->story->getLikesCount($s['id']);
                }
            }
            unset($s);
        }
        $stats = $this->dashboardStats();
        $data = compact('stories', 'stats');
        $data['selectedStatus'] = $selectedStatus;
        $data['q'] = $q;
        $data['pageTitle'] = 'Gestion des stories';
        $this->render('admin/stories.html', $data);
    }
    
    public function deleteStory($id) {
        if ($this->story->delete($id)) {
            $_SESSION['flash'] = "Story supprimée avec succès !";
        } else {
            $_SESSION['flash'] = "Erreur lors de la suppression";
        }
        $this->redirect('admin/stories');
    }
    
    public function comments() {
        $allComments = array();
        $stories = $this->story->allForAdmin();
        
        if (is_array($stories)) {
            foreach($stories as $story) {
                if (isset($story['id'])) {
                    $comments = $this->comment->getByStory($story['id']);
                    foreach($comments as $comment) {
                        $comment['story_title'] = isset($story['title']) ? $story['title'] : 'Sans titre';
                        $comment['story_id'] = $story['id'];
                        $allComments[] = $comment;
                    }
                }
            }
        }

        $q = isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : '';
        if ($q !== '') {
            $allComments = array_filter($allComments, function($c) use ($q) {
                $hay = strtolower(
                    (isset($c['story_title']) ? $c['story_title'] : '') . ' ' .
                    (isset($c['user_name']) ? $c['user_name'] : '') . ' ' .
                    (isset($c['content']) ? $c['content'] : '')
                );
                return strpos($hay, strtolower($q)) !== false;
            });
        }
        
        $stats = $this->dashboardStats();
        $data = compact('allComments', 'stats');
        $data['q'] = $q;
        $data['pageTitle'] = 'Commentaires';
        $this->render('admin/comments.html', $data);
    }
    
    public function deleteComment($id) {
        if ($this->comment->delete($id)) {
            $_SESSION['flash'] = "Commentaire supprimé avec succès !";
        } else {
            $_SESSION['flash'] = "Erreur lors de la suppression";
        }
        $this->redirect('admin/comments');
    }

    public function updateStoryStatus($id, $status) {
        $allowed = array('published', 'pending', 'archived', 'featured');
        if (!$id || !in_array($status, $allowed, true)) {
            $_SESSION['flash'] = "Statut invalide";
            $this->redirect('admin/stories');
            return;
        }
        if ($this->story->updateStatus($id, $status)) {
            $_SESSION['flash'] = "Statut mis à jour !";
        } else {
            $_SESSION['flash'] = "Impossible de mettre à jour le statut";
        }
        $this->redirect('admin/stories');
    }
}
