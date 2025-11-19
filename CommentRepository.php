<?php
class CommentRepository {
    private $pdo;

    public function __construct() {
        global $pdo;
        if (!isset($pdo)) {
            die('Erreur: Connexion base de données non disponible');
        }
        $this->pdo = $pdo;
    }

    public function getByStory($storyId) {
        try {
            $s = $this->pdo->prepare("SELECT * FROM comments WHERE story_id = ? ORDER BY created_at DESC");
            $s->execute(array($storyId));
            $result = $s->fetchAll(PDO::FETCH_ASSOC);
            return $result ? $result : array();
        } catch(PDOException $e) {
            return array();
        }
    }

    public function create($storyId, $userName, $content) {
        try {
            $s = $this->pdo->prepare("INSERT INTO comments (story_id, user_name, content) VALUES (?, ?, ?)");
            $s->execute(array($storyId, $userName, $content));
            return $this->pdo->lastInsertId();
        } catch(PDOException $e) {
            return false;
        }
    }

    public function delete($id) {
        try {
            $this->pdo->prepare("DELETE FROM comments WHERE id = ?")->execute(array($id));
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function countAll() {
        try {
            $s = $this->pdo->query("SELECT COUNT(*) AS total FROM comments");
            $r = $s->fetch(PDO::FETCH_ASSOC);
            return $r ? (int)$r['total'] : 0;
        } catch(PDOException $e) {
            return 0;
        }
    }
}









