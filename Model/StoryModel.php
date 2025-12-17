<?php
require_once __DIR__ . '/Database.php';

class StoryModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllApproved($filter = [], $sort = 'recent') {
        // Utilise la table success_stories de ablelink_db
        $sql = "SELECT * FROM success_stories WHERE status = 'approved'";
        $params = [];

        // Apply filters
        if (!empty($filter['q'])) {
            $sql .= " AND (title LIKE :q OR content LIKE :q OR author LIKE :q)";
            $params[':q'] = "%" . $filter['q'] . "%";
        }

        if (!empty($filter['author'])) {
            $sql .= " AND author LIKE :author";
            $params[':author'] = "%" . $filter['author'] . "%";
        }

        if (!empty($filter['cat'])) {
            $sql .= " AND title LIKE :cat";
            $params[':cat'] = "%" . $filter['cat'] . "%";
        }

        // Apply sorting
        switch ($sort) {
            case 'likes': 
                $sql .= " ORDER BY likes DESC, created_at DESC";
                break;
            case 'title': 
                $sql .= " ORDER BY title ASC"; 
                break;
            default: 
                $sql .= " ORDER BY created_at DESC";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByUserId($userId) {
        $sql = "SELECT * FROM success_stories WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT * FROM success_stories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($userId, $data) {
        $query = "INSERT INTO success_stories (user_id, title, author, content, status, created_at) 
                  VALUES (:user_id, :title, :author, :content, 'pending', NOW())";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':user_id' => $userId,
            ':title' => $data['title'],
            ':author' => $data['author_name'] ?? 'Anonyme',
            ':content' => $data['content']
        ]);
    }

    public function incrementLikes($id) {
        $sql = "UPDATE success_stories SET likes = likes + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function incrementShares($id) {
        $sql = "UPDATE success_stories SET shares = shares + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function update($id, $userId, $data) {
        $sql = "UPDATE success_stories SET title = :title, author = :author, content = :content WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':user_id' => $userId,
            ':title' => $data['title'],
            ':author' => $data['author'],
            ':content' => $data['content']
        ]);
    }

    public function delete($id, $userId) {
        $sql = "DELETE FROM success_stories WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id, ':user_id' => $userId]);
    }
    public function getComments($storyId) {
        $sql = "SELECT sc.*, u.prenom, u.nom, u.photo 
                FROM story_comments sc 
                JOIN utilisateur u ON sc.user_id = u.id 
                WHERE sc.story_id = :story_id 
                ORDER BY sc.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':story_id' => $storyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addComment($storyId, $userId, $content) {
        $sql = "INSERT INTO story_comments (story_id, user_id, content) VALUES (:story_id, :user_id, :content)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':story_id' => $storyId,
            ':user_id' => $userId,
            ':content' => $content
        ]);
    }

    public function getCommentsCount($storyId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as c FROM story_comments WHERE story_id = ?");
        $stmt->execute([$storyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['c'];
    }

    public function reportComment($commentId) {
        $stmt = $this->db->prepare("UPDATE story_comments SET reported = 1 WHERE id = ?");
        return $stmt->execute([$commentId]);
    }

    public function likeStory($storyId) {
        $stmt = $this->db->prepare("UPDATE success_stories SET likes = likes + 1 WHERE id = ?");
        return $stmt->execute([$storyId]);
    }
}
