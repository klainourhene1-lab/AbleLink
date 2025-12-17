<?php
require_once __DIR__ . '/Database.php';

class Candidature {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Créer une candidature
    public function create($data) {
        $sql = "INSERT INTO candidatures (id_offre, nom_candidat, email, cv, statut) 
                VALUES (:id_offre, :nom_candidat, :email, :cv, :statut)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_offre' => $data['id_offre'],
            ':nom_candidat' => $data['nom_candidat'],
            ':email' => $data['email'],
            ':cv' => $data['cv'] ?? null,
            ':statut' => $data['statut'] ?? 'en_attente'
        ]);
    }

    // Lire toutes les candidatures d'une offre
    public function getByOffre($id_offre) {
        $sql = "SELECT c.*, o.titre as offre_titre 
                FROM candidatures c 
                JOIN offres o ON c.id_offre = o.id 
                WHERE c.id_offre = :id_offre 
                ORDER BY c.date_candidature DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_offre' => $id_offre]);
        return $stmt->fetchAll();
    }

    // Lire toutes les candidatures
    public function getAll() {
        $sql = "SELECT c.*, o.titre as offre_titre 
                FROM candidatures c 
                JOIN offres o ON c.id_offre = o.id 
                ORDER BY c.date_candidature DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    // Lire une candidature par ID
    public function getById($id) {
        $sql = "SELECT c.*, o.titre as offre_titre 
                FROM candidatures c 
                JOIN offres o ON c.id_offre = o.id 
                WHERE c.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Mettre à jour le statut d'une candidature
    public function updateStatut($id, $statut) {
        $sql = "UPDATE candidatures SET statut = :statut WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':statut' => $statut
        ]);
    }

    // Supprimer une candidature
    public function delete($id) {
        $sql = "DELETE FROM candidatures WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}