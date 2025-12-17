<?php
require_once __DIR__ . '/../Controller/config.php';

class PostModel {
    private $pdo;

    public function __construct() {
        $this->pdo = Config::getConnexion();
    }

    public function createPost($userId, $content, $title = null, $type = 'text') {
        try {
            $sql = "INSERT INTO post (idUtilisateur, titre, contenu, type, datePublication, popularite) 
                    VALUES (:userId, :title, :content, :type, NOW(), 0)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'userId' => $userId, 
                'title' => $title, 
                'content' => $content, 
                'type' => $type
            ]);
        } catch (PDOException $e) {
            error_log("Error creating post: " . $e->getMessage());
            return false;
        }
    }

    public function getPosts() {
        try {
            // Fetch posts with user info
            $sql = "
                SELECT p.*, u.nom, u.prenom, u.photo 
                FROM post p
                JOIN utilisateur u ON p.idUtilisateur = u.id
                ORDER BY p.datePublication DESC
            ";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching posts: " . $e->getMessage());
            return [];
        }
    }

    public function getPostById($id) {
        try {
            $sql = "SELECT * FROM post WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function deletePost($id) {
         try {
            $sql = "DELETE FROM post WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
        }
    }

    public function getPostsByUser($userId) {
        try {
            $sql = "
                SELECT p.*, u.nom, u.prenom, u.photo 
                FROM post p
                JOIN utilisateur u ON p.idUtilisateur = u.id
                WHERE p.idUtilisateur = :userId
                ORDER BY p.datePublication DESC
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['userId' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching user posts: " . $e->getMessage());
            return [];
        }
    }
}
?>
