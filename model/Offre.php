<?php
require_once __DIR__ . '/Database.php';

class Offre {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Créer une offre
    public function create($data) {
        $sql = "INSERT INTO offres (titre, description, entreprise, localisation, type_contrat, salaire) 
                VALUES (:titre, :description, :entreprise, :localisation, :type_contrat, :salaire)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':titre' => $data['titre'],
            ':description' => $data['description'],
            ':entreprise' => $data['entreprise'],
            ':localisation' => $data['localisation'],
            ':type_contrat' => $data['type_contrat'],
            ':salaire' => $data['salaire'] ?? null
        ]);
    }

    // Lire toutes les offres
    public function getAll() {
        $sql = "SELECT * FROM offres ORDER BY date_publication DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
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
}