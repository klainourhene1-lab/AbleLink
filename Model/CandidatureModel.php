<?php
require_once __DIR__ . '/Database.php';

class CandidatureModel {
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

    // Statistiques des candidatures
    public function getStats() {
        $sql = "SELECT COUNT(*) as total FROM candidatures";
        $stmt = $this->db->query($sql);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        return ['total' => $total];
    }

    // Top 5 offres les plus populaires
    public function getTopOffers() {
        $sql = "SELECT o.titre, COUNT(c.id) as count 
                FROM candidatures c 
                JOIN offres o ON c.id_offre = o.id 
                GROUP BY o.id 
                ORDER BY count DESC 
                LIMIT 5";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Candidatures par mois
    public function getApplicationsByMonth() {
        $sql = "SELECT DATE_FORMAT(date_candidature, '%Y-%m') as mois, COUNT(*) as count 
                FROM candidatures 
                GROUP BY mois 
                ORDER BY mois DESC 
                LIMIT 6";
        $stmt = $this->db->query($sql);
        return array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // Récupérer les candidatures par email du candidat
    public function getByCandidateEmail($email) {
        $sql = "SELECT c.*, o.titre as offre_titre, o.entreprise, o.localisation 
                FROM candidatures c 
                JOIN offres o ON c.id_offre = o.id 
                WHERE c.email = :email 
                ORDER BY c.date_candidature DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchAll();
    }
}
