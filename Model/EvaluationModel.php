<?php
require_once __DIR__ . '/Database.php';

/**
 * Evaluation Model
 * Handles all database operations related to evaluations
 */
class EvaluationModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create a new evaluation
     */
     public function getByUserId($userId) {
        $query = "SELECT ev.*, 
                         e.titre as event_titre, 
                         e.date as event_date,
                         e.lieu as event_lieu,
                         e.statut as event_statut,
                         e.accessibilite as event_accessibilite,
                         u.nom, 
                         u.prenom,
                         u_org.nom as organizer_nom,
                         u_org.prenom as organizer_prenom
                  FROM evaluation ev
                  INNER JOIN evenement e ON ev.idEvenement = e.id
                  INNER JOIN utilisateur u ON ev.idUtilisateur = u.id
                  INNER JOIN utilisateur u_org ON e.idUtilisateur = u_org.id
                  WHERE ev.idUtilisateur = :userId
                  ORDER BY ev.dateEvaluation DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create($data) {
        $note = ($data['note_accessibilite'] + $data['note_inclusion']) / 2;
        
        $sql = "INSERT INTO evaluation (
                idUtilisateur, 
                idEvenement, 
                note, 
                note_accessibilite, 
                note_inclusion, 
                commentaire, 
                dateEvaluation,
                signalee,
                etat_moderation
                ) VALUES (?, ?, ?, ?, ?, ?, NOW(), 0, 'Visible')";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['idUtilisateur'],
            $data['idEvenement'],
            $note,
            $data['note_accessibilite'],
            $data['note_inclusion'],
            $data['commentaire'] ?? null
        ]);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Update an existing evaluation
     */
    public function update($evaluationId, $data) {
        $note = ($data['note_accessibilite'] + $data['note_inclusion']) / 2;
        
        $sql = "UPDATE evaluation SET 
                note = ?, 
                note_accessibilite = ?, 
                note_inclusion = ?, 
                commentaire = ?, 
                dateEvaluation = NOW(),
                signalee = 0,
                etat_moderation = 'Visible'
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $note,
            $data['note_accessibilite'],
            $data['note_inclusion'],
            $data['commentaire'] ?? null,
            $evaluationId
        ]);
    }
    
    /**
     * Get evaluation by user and event
     */
    public function getByUserAndEvent($userId, $eventId) {
        $sql = "SELECT e.*, u.nom, u.prenom 
                FROM evaluation e 
                JOIN utilisateur u ON e.idUtilisateur = u.id 
                WHERE e.idUtilisateur = ? AND e.idEvenement = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $eventId]);
        return $stmt->fetch();
    }
    
    /**
     * Get all evaluations for an event
     */
    public function getByEventId($eventId) {
        $sql = "SELECT e.*, u.nom, u.prenom 
                FROM evaluation e 
                JOIN utilisateur u ON e.idUtilisateur = u.id 
                WHERE e.idEvenement = ? 
                ORDER BY e.dateEvaluation DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$eventId]);
        return $stmt->fetchAll();
    }
    
    
    
    /**
     * Get reported evaluations
     */
    public function getReported() {
        $sql = "SELECT e.*, ev.titre as event_titre, u.nom, u.prenom, u.email
                FROM evaluation e 
                JOIN evenement ev ON e.idEvenement = ev.id 
                JOIN utilisateur u ON e.idUtilisateur = u.id 
                WHERE e.signalee = 1 
                ORDER BY e.dateEvaluation DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Report an evaluation
     */
    public function report($evaluationId) {
        $sql = "UPDATE evaluation SET signalee = 1, etat_moderation = 'En attente' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$evaluationId]);
    }
    
    /**
     * Approve an evaluation (remove report flag)
     */
    public function approve($evaluationId) {
        $sql = "UPDATE evaluation SET signalee = 0, etat_moderation = 'Visible' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$evaluationId]);
    }
    
    /**
     * Delete an evaluation
     */
    public function delete($evaluationId) {
        $sql = "DELETE FROM evaluation WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$evaluationId]);
    }
    
    /**
     * Get event statistics
     */
    public function getEventStats($eventId) {
        $sql = "SELECT 
                AVG(note_accessibilite) as avg_accessibilite,
                AVG(note_inclusion) as avg_inclusion,
                AVG(note) as avg_overall,
                COUNT(*) as total_evaluations,
                SUM(signalee) as reported_count
                FROM evaluation 
                WHERE idEvenement = ? AND etat_moderation = 'Visible'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$eventId]);
        return $stmt->fetch();
    }
    
    /**
     * Check if user already evaluated an event
     */
    public function userHasEvaluated($userId, $eventId) {
        $sql = "SELECT id FROM evaluation WHERE idUtilisateur = ? AND idEvenement = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $eventId]);
        return $stmt->fetch() !== false;
    }
}







