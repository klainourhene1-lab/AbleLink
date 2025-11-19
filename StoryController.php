<?php
class StoryController extends Controller {
    private $story;
    private $comment;
    
    public function __construct() { 
        $this->story = new Story(); 
        $this->comment = new Comment();
    }

    private function getGlobalStats() {
        return array(
            'total_stories' => $this->story->countAll(),
            'total_comments' => $this->comment->countAll(),
            'total_likes' => $this->story->countLikes()
        );
    }

    private function renderWithStats($view, $data = array()) {
        if (!is_array($data)) {
            $data = array();
        }
        $data['stats'] = $this->getGlobalStats();
        $this->view($view, $data);
    }

    private function sendMail($to, $subject, $message) {
        if (empty($to)) {
            return;
        }
        $headers = "From: AbleLink <no-reply@ablelink.local>\r\n".
                   "Content-Type: text/plain; charset=UTF-8";
        @mail($to, $subject, $message, $headers);
    }

    public function index() { 
        $stories = $this->story->all(true);
        if ($stories && is_array($stories)) {
            foreach($stories as &$s) {
                if (isset($s['id'])) {
                    $s['likes_count'] = $this->story->getLikesCount($s['id']);
                    $userIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
                    $s['has_liked'] = $this->story->hasLiked($s['id'], $userIp);
                } else {
                    $s['likes_count'] = 0;
                    $s['has_liked'] = false;
                }
            }
        } else {
            $stories = array();
        }
        $topStory = $this->story->topStory();
        $topStoryId = $topStory && isset($topStory['id']) ? (int)$topStory['id'] : 0;
        $this->renderWithStats('stories/index.html', compact('stories', 'topStoryId')); 
    }
    
    public function create() { 
        $this->renderWithStats('stories/create.html'); 
    }
    
    public function store() { 
        if (isset($_POST['title']) && isset($_POST['content']) && !empty($_POST['title']) && !empty($_POST['content'])) {
            $files = isset($_FILES) ? $_FILES : array();
            $_POST['status'] = 'pending';
            $storyId = $this->story->create($_POST, $files); 
            if ($storyId) {
                $_SESSION['flash'] = "Votre success story a été envoyée et sera publiée après validation.";
                if (!empty($_POST['user_email'])) {
                    $this->sendMail(
                        $_POST['user_email'],
                        "AbleLink · Story soumise",
                        "Merci d'avoir partagé votre success story ! Notre équipe la validera sous peu."
                    );
                }
            } else {
                $_SESSION['flash'] = "Erreur lors de l'enregistrement. Réessayez.";
            }
        } else {
            $_SESSION['flash'] = "Veuillez remplir tous les champs obligatoires";
        }
        $this->redirect('stories'); 
    }
    
    public function show($id) { 
        if (!$id || $id == 0) {
            $_SESSION['flash'] = "ID invalide";
            $this->redirect('stories');
            return;
        }
        
        $story = $this->story->find($id);
        if (!$story || !is_array($story)) {
            $_SESSION['flash'] = "Story introuvable";
            $this->redirect('stories');
            return;
        }
        $isAdmin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
        if (isset($story['status']) && $story['status'] !== 'published' && !$isAdmin) {
            $_SESSION['flash'] = "Cette story n'est pas encore publiée.";
            $this->redirect('stories');
            return;
        }
        $story['likes_count'] = $this->story->getLikesCount($id);
        $userIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $story['has_liked'] = $this->story->hasLiked($id, $userIp);
        $comments = $this->comment->getByStory($id);
        $this->renderWithStats('stories/show.html', compact('story', 'comments')); 
    }
    
    public function edit($id) { 
        if (!$id || $id == 0) {
            $_SESSION['flash'] = "ID invalide";
            $this->redirect('stories');
            return;
        }
        
        $story = $this->story->find($id);
        if (!$story || !is_array($story)) {
            $_SESSION['flash'] = "Story introuvable";
            $this->redirect('stories');
            return;
        }
        $this->renderWithStats('stories/edit.html', compact('story')); 
    }
    
    public function update($id) {
        if (!$id || $id == 0) {
            $_SESSION['flash'] = "ID invalide";
            $this->redirect('stories');
            return;
        }
        
        if (isset($_POST['title']) && isset($_POST['content']) && !empty($_POST['title']) && !empty($_POST['content'])) {
            $files = isset($_FILES) ? $_FILES : array();
            if ($this->story->update($id, $_POST, $files)) {
                $_SESSION['flash'] = "Story modifiée avec succès !"; 
            } else {
                $_SESSION['flash'] = "Erreur lors de la modification";
            }
        } else {
            $_SESSION['flash'] = "Veuillez remplir tous les champs obligatoires";
        }
        $this->redirect("stories/show/$id"); 
    }
    
    public function like($id) {
        header('Content-Type: application/json');
        if (!$id || $id == 0) {
            echo json_encode(array('success' => false, 'message' => 'ID invalide'));
            exit;
        }
        $userIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        if ($this->story->addLike($id, $userIp)) {
            echo json_encode(array('success' => true, 'likes' => $this->story->getLikesCount($id)));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Déjà liké'));
        }
        exit;
    }
    
    public function addComment($storyId) {
        if (!$storyId || $storyId == 0) {
            $_SESSION['flash'] = "ID invalide";
            $this->redirect('stories');
            return;
        }
        $storyData = $this->story->find($storyId);
        if (!$storyData || !is_array($storyData)) {
            $_SESSION['flash'] = "Story introuvable";
            $this->redirect('stories');
            return;
        }
        $isAdmin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
        if (isset($storyData['status']) && $storyData['status'] !== 'published' && $storyData['status'] !== 'featured' && !$isAdmin) {
            $_SESSION['flash'] = "Cette story n'accepte pas encore de commentaires.";
            $this->redirect('stories');
            return;
        }
        
        if (isset($_POST['user_name']) && isset($_POST['content']) && !empty($_POST['user_name']) && !empty($_POST['content'])) {
            $this->comment->create($storyId, $_POST['user_name'], $_POST['content']);
            $_SESSION['flash'] = "Commentaire ajouté !";
            if (isset($storyData['user_email']) && !empty($storyData['user_email'])) {
                $this->sendMail(
                    $storyData['user_email'],
                    "Nouvelle réaction sur votre success story",
                    $_POST['user_name'] . " vient de commenter votre story \"" . ($storyData['title'] ?? '') . "\"."
                );
            }
        } else {
            $_SESSION['flash'] = "Veuillez remplir tous les champs";
        }
        $this->redirect("stories/show/$storyId");
    }
    
    public function delete($id) { 
        if (!$id || $id == 0) {
            $_SESSION['flash'] = "ID invalide";
            $this->redirect('stories');
            return;
        }
        $this->story->delete($id); 
        $_SESSION['flash'] = "Story supprimée !"; 
        $this->redirect('stories'); 
    }
}