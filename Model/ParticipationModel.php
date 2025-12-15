<?php
require_once __DIR__ . '/Database.php';

/**
 * Participation Model
 * Handles all database operations related to event participation
 */
class ParticipationModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Register user for an event
     */
    public function register($userId, $eventId) {
        // Check if already registered
        $stmt = $this->db->prepare("SELECT id FROM participation WHERE idUtilisateur = ? AND idEvenement = ?");
        $stmt->execute([$userId, $eventId]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            return false; // Already registered
        }
        
        $sql = "INSERT INTO participation (idUtilisateur, idEvenement, dateInscription, statut, presence) 
                VALUES (?, ?, NOW(), 'Confirmée', 0)";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([$userId, $eventId]);
        
        if ($result) {
            // Update event inscrits count
            $this->updateEventInscrits($eventId);
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Cancel participation
     */
    public function cancel($userId, $eventId) {
        $sql = "DELETE FROM participation WHERE idUtilisateur = ? AND idEvenement = ?";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([$userId, $eventId]);
        
        if ($result) {
            // Update event inscrits count
            $this->updateEventInscrits($eventId);
        }
        return $result;
    }
    
    /**
     * Get participation by user and event
     */
    public function getByUserAndEvent($userId, $eventId) {
        $sql = "SELECT * FROM participation WHERE idUtilisateur = ? AND idEvenement = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $eventId]);
        return $stmt->fetch();
    }
    
    /**
     * Get all participations for an event
     */
    public function getByEventId($eventId) {
        $sql = "SELECT p.*, u.nom, u.prenom, u.email
                FROM participation p
                JOIN utilisateur u ON p.idUtilisateur = u.id
                WHERE p.idEvenement = ?
                ORDER BY p.dateInscription DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$eventId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get all participations by user
     */
    public function getByUserId($userId) {
        $sql = "SELECT p.*, e.titre as event_titre, e.date as event_date, e.lieu
                FROM participation p
                JOIN evenement e ON p.idEvenement = e.id
                WHERE p.idUtilisateur = ?
                ORDER BY e.date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get count of participants for an event
     */
    public function getCountByEventId($eventId) {
        $sql = "SELECT COUNT(*) as count FROM participation WHERE idEvenement = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$eventId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
    
    /**
     * Check if user is registered for event
     */
    public function isUserRegistered($userId, $eventId) {
        $sql = "SELECT id FROM participation WHERE idUtilisateur = ? AND idEvenement = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $eventId]);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Update event inscrits count
     */
    private function updateEventInscrits($eventId) {
        $count = $this->getCountByEventId($eventId);
        $sql = "UPDATE evenement SET inscrits = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$count, $eventId]);
    }
}







