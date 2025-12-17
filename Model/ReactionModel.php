<?php
require_once __DIR__ . '/../Control/config.php';

class ReactionModel {
    private $pdo;

    public function __construct() {
        $this->pdo = Config::getConnexion();
    }

    public function toggleLike($userId, $targetId, $type = 'post') {
        try {
            // Check if already liked
            $sql = "SELECT id FROM reaction WHERE idUtilisateur = :userId AND idCible = :targetId AND type = :type";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['userId' => $userId, 'targetId' => $targetId, 'type' => $type]);
            $existing = $stmt->fetch();

            if ($existing) {
                // Unlike
                $sqlDelete = "DELETE FROM reaction WHERE id = :id";
                $this->pdo->prepare($sqlDelete)->execute(['id' => $existing['id']]);
                $this->updatePopularity($targetId, -1);
                return 'unliked';
            } else {
                // Like
                $sqlInsert = "INSERT INTO reaction (idUtilisateur, idCible, type, dateReaction) VALUES (:userId, :targetId, :type, NOW())";
                $this->pdo->prepare($sqlInsert)->execute(['userId' => $userId, 'targetId' => $targetId, 'type' => $type]);
                $this->updatePopularity($targetId, 1);
                return 'liked';
            }
        } catch (PDOException $e) {
            error_log("Error toggling like: " . $e->getMessage());
            return false;
        }
    }

    private function updatePopularity($postId, $increment) {
        try {
            $sql = "UPDATE post SET popularite = popularite + :inc WHERE id = :postId";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['inc' => $increment, 'postId' => $postId]);
        } catch (PDOException $e) {
            // Ignore error if updating popularity fails
        }
    }

    public function getLikeCount($targetId, $type = 'post') {
        try {
            // Usually we can just read from post popularite, but if we want precise count from table:
            $sql = "SELECT COUNT(*) FROM reaction WHERE idCible = :targetId AND type = :type";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['targetId' => $targetId, 'type' => $type]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
}
?>
