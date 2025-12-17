<?php
require_once __DIR__ . '/../Controller/config.php';

class AmisModel {
    private $pdo;

    public function __construct() {
        $this->pdo = Config::getConnexion();
    }

    // Send Friend Request
    public function sendRequest($idSender, $idReceiver) {
        try {
            // Check if request already exists
            if ($this->checkFriendshipStatus($idSender, $idReceiver)) {
                return false;
            }

            $sql = "INSERT INTO amis (idUtilisateur1, idUtilisateur2, statut, date) VALUES (:sender, :receiver, 'attente', NOW())";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['sender' => $idSender, 'receiver' => $idReceiver]);
        } catch (PDOException $e) {
            error_log("Error sending friend request: " . $e->getMessage());
            return false;
        }
    }

    // Accept Friend Request
    public function acceptRequest($idRequest) {
        try {
            $sql = "UPDATE amis SET statut = 'accepte' WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['id' => $idRequest]);
        } catch (PDOException $e) {
            error_log("Error accepting friend request: " . $e->getMessage());
            return false;
        }
    }

    // Refuse/Delete Friend
    public function removeFriend($idRequest) {
        try {
            $sql = "DELETE FROM amis WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['id' => $idRequest]);
        } catch (PDOException $e) {
            error_log("Error removing friend: " . $e->getMessage());
            return false;
        }
    }

    // Get Friends List (Accepted)
    public function getFriends($userId) {
        try {
            $sql = "
                SELECT u.*, a.id as friendship_id 
                FROM utilisateur u
                JOIN amis a ON (a.idUtilisateur1 = u.id OR a.idUtilisateur2 = u.id)
                WHERE (a.idUtilisateur1 = :userId OR a.idUtilisateur2 = :userId)
                AND u.id != :userId
                AND a.statut = 'accepte'
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['userId' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting friends: " . $e->getMessage());
            return [];
        }
    }

    // Get Pending Requests (Received)
    public function getPendingRequests($userId) {
        try {
            $sql = "
                SELECT u.*, a.id as friendship_id, a.date
                FROM utilisateur u
                JOIN amis a ON a.idUtilisateur1 = u.id
                WHERE a.idUtilisateur2 = :userId
                AND a.statut = 'attente'
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['userId' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting pending requests: " . $e->getMessage());
            return [];
        }
    }

    // Check Status between two users
    public function checkFriendshipStatus($user1, $user2) {
        try {
            $sql = "
                SELECT * FROM amis 
                WHERE (idUtilisateur1 = :u1 AND idUtilisateur2 = :u2)
                OR (idUtilisateur1 = :u2 AND idUtilisateur2 = :u1)
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['u1' => $user1, 'u2' => $user2]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
}
?>
