<?php
class StoryRepository {
    private $pdo;

    public function __construct() {
        global $pdo;
        if (!isset($pdo)) {
            die('Erreur: Connexion base de données non disponible');
        }
        $this->pdo = $pdo;
    }

    public function all($onlyPublished = false) {
        try {
            $sql = "SELECT * FROM stories";
            if ($onlyPublished) {
                $sql .= " WHERE status IN ('published','featured')";
            }
            $sql .= " ORDER BY created_at DESC";
            $result = $this->pdo->query($sql);
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return array();
        }
    }

    public function allForAdmin() {
        return $this->all(false);
    }

    public function find($id) {
        try {
            $s = $this->pdo->prepare("SELECT * FROM stories WHERE id = ?");
            $s->execute(array($id));
            return $s->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    public function create($payload) {
        try {
            $s = $this->pdo->prepare("INSERT INTO stories (user_name, user_email, title, content, image, status) VALUES (?, ?, ?, ?, ?, ?)");
            $s->execute(array(
                $payload['user_name'],
                $payload['user_email'],
                $payload['title'],
                $payload['content'],
                $payload['image'],
                $payload['status']
            ));
            return $this->pdo->lastInsertId();
        } catch(PDOException $e) {
            return false;
        }
    }

    public function update($sql, $params) {
        try {
            $this->pdo->prepare($sql)->execute($params);
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function delete($id) {
        try {
            $this->pdo->prepare("DELETE FROM stories WHERE id = ?")->execute(array($id));
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function addLike($storyId, $userIp) {
        try {
            $s = $this->pdo->prepare("INSERT INTO story_likes (story_id, user_ip) VALUES (?, ?)");
            $s->execute(array($storyId, $userIp));
            $this->pdo->prepare("UPDATE stories SET likes_count = likes_count + 1 WHERE id = ?")->execute(array($storyId));
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function getLikesCount($storyId) {
        try {
            $s = $this->pdo->prepare("SELECT COUNT(*) as count FROM story_likes WHERE story_id = ?");
            $s->execute(array($storyId));
            $result = $s->fetch(PDO::FETCH_ASSOC);
            return $result ? (int)$result['count'] : 0;
        } catch(PDOException $e) {
            return 0;
        }
    }

    public function hasLiked($storyId, $userIp) {
        try {
            $s = $this->pdo->prepare("SELECT COUNT(*) as count FROM story_likes WHERE story_id = ? AND user_ip = ?");
            $s->execute(array($storyId, $userIp));
            $result = $s->fetch(PDO::FETCH_ASSOC);
            return $result && $result['count'] > 0;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function countAll() {
        try {
            $s = $this->pdo->query("SELECT COUNT(*) AS total FROM stories WHERE status IN ('published','featured')");
            $r = $s->fetch(PDO::FETCH_ASSOC);
            return $r ? (int)$r['total'] : 0;
        } catch(PDOException $e) {
            return 0;
        }
    }

    public function countLikes() {
        try {
            $s = $this->pdo->query("SELECT COUNT(*) AS total FROM story_likes");
            $r = $s->fetch(PDO::FETCH_ASSOC);
            return $r ? (int)$r['total'] : 0;
        } catch(PDOException $e) {
            return 0;
        }
    }

    public function updateStatus($id, $status) {
        try {
            $s = $this->pdo->prepare("UPDATE stories SET status = ? WHERE id = ?");
            $s->execute(array($status, $id));
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function getTopStory() {
        try {
            $s = $this->pdo->query("SELECT * FROM stories WHERE status IN ('published','featured') ORDER BY likes_count DESC, created_at DESC LIMIT 1");
            return $s->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }
}

