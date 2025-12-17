<?php
require_once __DIR__ . '/../Controller/config.php';

class CommentaireModel {
    private $pdo;

    public function __construct() {
        $this->pdo = Config::getConnexion();
    }

    public function addComment($userId, $postId, $content) {
        try {
            $sql = "INSERT INTO commentaire (idUtilisateur, idPost, contenu, dateCommentaire) 
                    VALUES (:userId, :postId, :content, NOW())";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'userId' => $userId,
                'postId' => $postId,
                'content' => $content
            ]);
        } catch (PDOException $e) {
            error_log("Error adding comment: " . $e->getMessage());
            return false;
        }
    }

    public function getCommentsByPost($postId) {
        try {
            $sql = "
                SELECT c.*, u.nom, u.prenom, u.photo 
                FROM commentaire c
                JOIN utilisateur u ON c.idUtilisateur = u.id
                WHERE c.idPost = :postId
                ORDER BY c.dateCommentaire ASC
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['postId' => $postId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching comments: " . $e->getMessage());
            return [];
        }
    }
}
?>
