<?php
require_once __DIR__ . '/Database.php';

class OffreModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Créer une offre
    public function create($data) {
        $sql = "INSERT INTO offres (titre, description, entreprise, localisation, type_contrat, salaire, user_id, statut) 
                VALUES (:titre, :description, :entreprise, :localisation, :type_contrat, :salaire, :user_id, :statut)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':titre' => $data['titre'],
            ':description' => $data['description'],
            ':entreprise' => $data['entreprise'],
            ':localisation' => $data['localisation'],
            ':type_contrat' => $data['type_contrat'],
            ':salaire' => $data['salaire'] ?? null,
            ':user_id' => $data['user_id'] ?? null,
            ':statut' => $data['statut'] ?? 'pending'
        ]);
    }

    // Lire toutes les offres (publiées uniquement)
    public function getAll() {
        $sql = "SELECT * FROM offres WHERE statut = 'published' ORDER BY date_publication DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    // Lire toutes les offres (admin view)
    public function getAllAdmin() {
        $sql = "SELECT * FROM offres ORDER BY date_publication DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    // Lire les offres en attente
    public function getPending() {
        $sql = "SELECT * FROM offres WHERE statut = 'pending' ORDER BY date_publication DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    // Mettre à jour le statut
    public function updateStatus($id, $status) {
        $sql = "UPDATE offres SET statut = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    // Lire une offre par ID
    public function getById($id) {
        $sql = "SELECT * FROM offres WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Mettre à jour une offre
    public function update($id, $data) {
        $sql = "UPDATE offres SET 
                titre = :titre,
                description = :description,
                entreprise = :entreprise,
                localisation = :localisation,
                type_contrat = :type_contrat,
                salaire = :salaire
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':titre' => $data['titre'],
            ':description' => $data['description'],
            ':entreprise' => $data['entreprise'],
            ':localisation' => $data['localisation'],
            ':type_contrat' => $data['type_contrat'],
            ':salaire' => $data['salaire'] ?? null
        ]);
    }

    // Supprimer une offre
    public function delete($id) {
        $sql = "DELETE FROM offres WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // Statistiques globales des offres
    public function getStats() {
        $stats = [
            'total' => 0,
            'published' => 0,
            'pending' => 0,
            'rejected' => 0
        ];

        $sql = "SELECT statut, COUNT(*) as count FROM offres GROUP BY statut";
        $stmt = $this->db->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $row) {
            $stats[$row['statut']] = $row['count'];
            $stats['total'] += $row['count'];
        }
        
        // Handle case where statut might be null or old values
        return $stats;
    }

    // Offres par mois (pour le graph)
    public function getOffersByMonth() {
        $sql = "SELECT DATE_FORMAT(date_publication, '%Y-%m') as mois, COUNT(*) as count 
                FROM offres 
                GROUP BY mois 
                ORDER BY mois DESC 
                LIMIT 6";
        $stmt = $this->db->query($sql);
        return array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // Récupérer les offres d'un utilisateur spécifique
    public function getByUser($userId) {
        $sql = "SELECT * FROM offres WHERE user_id = :user_id ORDER BY date_publication DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }
}
