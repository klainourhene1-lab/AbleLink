<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../Model/AmisModel.php';
require_once __DIR__ . '/../Model/PostModel.php';
require_once __DIR__ . '/../Model/CommentaireModel.php';
require_once __DIR__ . '/../Model/ReactionModel.php';
require_once __DIR__ . '/../Model/User.php';
require_once __DIR__ . '/UserController.php';

class SocialController {
    private $amisModel;
    private $postModel;
    private $commentModel;
    private $reactionModel;
    private $userController;

    public function __construct() {
        $this->amisModel = new AmisModel();
        $this->postModel = new PostModel();
        $this->commentModel = new CommentaireModel();
        $this->reactionModel = new ReactionModel();
        $this->userController = new UserController();
    }

    // Load Profile Page Data
    public function getAmisModel() {
        return $this->amisModel;
    }

    public function getProfileData($profileId, $currentUserId) {
        $user = $this->userController->showUser($profileId);
        if (!$user) return null;

        $isOwnProfile = ($profileId == $currentUserId);
        $friendship = null;
        if (!$isOwnProfile) {
            $friendship = $this->amisModel->checkFriendshipStatus($currentUserId, $profileId);
        }

        $friends = $this->amisModel->getFriends($profileId);
        $friends = $this->amisModel->getFriends($profileId);
        
        // Filter posts: Show only if own profile or friends
        $posts = [];
        if ($isOwnProfile || ($friendship && $friendship['statut'] === 'accepte')) {
            $posts = $this->postModel->getPostsByUser($profileId);
        }
        return [
            'user' => $user,
            'isOwnProfile' => $isOwnProfile,
            'friendship' => $friendship,
            'friends' => $friends,
            'posts' => $posts,
            'pendingRequests' => $isOwnProfile ? $this->amisModel->getPendingRequests($currentUserId) : []
        ];
    }

    // ACTIONS

    public function handlePostSubmission($userId, $data) {
        if (!empty($data['content'])) {
            return $this->postModel->createPost($userId, $data['content'], $data['title'] ?? null);
        }
        return false;
    }

    public function handleComment($userId, $data) {
        if (!empty($data['content']) && !empty($data['postId'])) {
            return $this->commentModel->addComment($userId, $data['postId'], $data['content']);
        }
        return false;
    }

    public function handleLike($userId, $postId) {
        return $this->reactionModel->toggleLike($userId, $postId);
    }

    public function sendFriendRequest($senderId, $receiverId) {
        return $this->amisModel->sendRequest($senderId, $receiverId);
    }

    public function acceptFriendRequest($requestId) {
        return $this->amisModel->acceptRequest($requestId);
    }

    public function searchUsers($query, $currentUserId) {
        return $this->userController->searchUsers($query, $currentUserId);
    }

    public function rejectRequest($requestId) {
        return $this->amisModel->removeFriend($requestId);
    }
}
?>
