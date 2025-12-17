<?php
require_once __DIR__ . '/Database.php';

class FavorisModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Ajouter aux favoris
    public function add($userId, $offreId) {
        $sql = "INSERT INTO favoris (id_user, id_offre) VALUES (:id_user, :id_offre)";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id_user' => $userId, ':id_offre' => $offreId]);
            return true;
        } catch (PDOException $e) {
            // Probably duplicate entry
            return false;
        }
    }

    // Supprimer des favoris
    public function remove($userId, $offreId) {
        $sql = "DELETE FROM favoris WHERE id_user = :id_user AND id_offre = :id_offre";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_user' => $userId, ':id_offre' => $offreId]);
    }

    // Vérifier si favori
    public function isFavorite($userId, $offreId) {
        $sql = "SELECT COUNT(*) FROM favoris WHERE id_user = :id_user AND id_offre = :id_offre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_user' => $userId, ':id_offre' => $offreId]);
        return $stmt->fetchColumn() > 0;
    }

    // Liste des favoris pour un utilisateur
    public function getByUser($userId) {
        $sql = "SELECT o.*, f.date_ajout 
                FROM favoris f
                INNER JOIN offres o ON f.id_offre = o.id
                WHERE f.id_user = :id_user
                ORDER BY f.date_ajout DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_user' => $userId]);
        return $stmt->fetchAll();
    }
}
