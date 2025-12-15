<?php
require_once __DIR__ . '/Database.php';

/**
 * Event Model
 * Handles all database operations related to events
 */
class EventModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create a new event
     */
    public function create($data) {
        $sql = "INSERT INTO evenement 
                (titre, description, date, lieu, theme, accessibilite, statut, participants_max, inscrits, idUtilisateur) 
                VALUES 
                (:titre, :description, :date, :lieu, :theme, :accessibilite, :statut, :participants_max, :inscrits, :idUtilisateur)";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':titre' => $data['titre'],
            ':description' => $data['description'],
            ':date' => $data['date'],
            ':lieu' => $data['lieu'],
            ':theme' => $data['theme'] ?? 'Inclusion',
            ':accessibilite' => $data['accessibilite'] ?? '',
            ':statut' => $data['statut'] ?? 'Brouillon',
            ':participants_max' => $data['participants_max'] ?? 50,
            ':inscrits' => $data['inscrits'] ?? 0,
            ':idUtilisateur' => $data['idUtilisateur']
        ]);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Update an existing event
     */
    public function update($eventId, $data) {
        $sql = "UPDATE evenement SET 
                titre = :titre, 
                description = :description, 
                date = :date, 
                lieu = :lieu, 
                theme = :theme, 
                accessibilite = :accessibilite, 
                statut = :statut,
                participants_max = :participants_max,
                inscrits = :inscrits,
                idUtilisateur = :idUtilisateur
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':titre' => $data['titre'],
            ':description' => $data['description'],
            ':date' => $data['date'],
            ':lieu' => $data['lieu'],
            ':theme' => $data['theme'] ?? 'Inclusion',
            ':accessibilite' => $data['accessibilite'] ?? '',
            ':statut' => $data['statut'],
            ':participants_max' => $data['participants_max'],
            ':inscrits' => $data['inscrits'],
            ':idUtilisateur' => $data['idUtilisateur'],
            ':id' => $eventId
        ]);
    }
    
    /**
     * Get event by ID
     */
    public function getById($eventId) {
        $sql = "SELECT e.*, u.nom, u.prenom 
                FROM evenement e 
                JOIN utilisateur u ON e.idUtilisateur = u.id 
                WHERE e.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$eventId]);
        $event = $stmt->fetch();
        
        if ($event) {
            // Get participants count
            $stmt = $this->db->prepare("SELECT COUNT(*) as participants_count FROM participation WHERE idEvenement = ?");
            $stmt->execute([$eventId]);
            $participants = $stmt->fetch();
            $event['participants_count'] = $participants['participants_count'];
            
            // Get evaluations count
            $stmt = $this->db->prepare("SELECT COUNT(*) as evaluations_count FROM evaluation WHERE idEvenement = ?");
            $stmt->execute([$eventId]);
            $evaluations = $stmt->fetch();
            $event['evaluations_count'] = $evaluations['evaluations_count'];
        }
        
        return $event;
    }
    
    /**
     * Get all events with optional filters
     */
    public function getAll($filters = []) {
        $sql = "SELECT e.*, u.nom, u.prenom 
                FROM evenement e 
                JOIN utilisateur u ON e.idUtilisateur = u.id 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['statut'])) {
            $sql .= " AND e.statut = ?";
            $params[] = $filters['statut'];
        }
        
        if (!empty($filters['userId'])) {
            $sql .= " AND e.idUtilisateur = ?";
            $params[] = $filters['userId'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (e.titre LIKE ? OR e.description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND e.date >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND e.date <= ?";
            $params[] = $filters['date_to'];
        }
        
        $sql .= " ORDER BY e.date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $events = $stmt->fetchAll();
        
        // Get additional data for each event
        foreach ($events as &$event) {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM participation WHERE idEvenement = ?");
            $stmt->execute([$event['id']]);
            $participants = $stmt->fetch();
            $event['participants_count'] = $participants['count'];
            
            $stmt = $this->db->prepare("SELECT AVG(note) as avg_rating FROM evaluation WHERE idEvenement = ? AND etat_moderation = 'Visible'");
            $stmt->execute([$event['id']]);
            $rating = $stmt->fetch();
            $event['avg_rating'] = $rating['avg_rating'] ? round($rating['avg_rating'], 1) : 0;
        }
        
        return $events;
    }
    
    /**
     * Delete an event
     */
    public function delete($eventId) {
        $this->db->beginTransaction();
        
        try {
            $stmt = $this->db->prepare("DELETE FROM participation WHERE idEvenement = ?");
            $stmt->execute([$eventId]);
            
            $stmt = $this->db->prepare("DELETE FROM evaluation WHERE idEvenement = ?");
            $stmt->execute([$eventId]);
            
            $stmt = $this->db->prepare("DELETE FROM evenement WHERE id = ?");
            $result = $stmt->execute([$eventId]);
            
            $this->db->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Update event status
     */
    public function updateStatus($eventId, $status) {
        $validStatuses = ['Brouillon', 'Publié', 'Archivé', 'Rejeté'];
        if (!in_array($status, $validStatuses)) {
            throw new Exception('Invalid status: ' . $status);
        }
        
        $sql = "UPDATE evenement SET statut = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $eventId]);
    }
    
    /**
     * Get events by user ID
     */
    public function getByUserId($userId) {
        $sql = "SELECT e.*, 
                (SELECT COUNT(*) FROM participation p WHERE p.idEvenement = e.id) as participants_count,
                (SELECT COUNT(*) FROM evaluation ev WHERE ev.idEvenement = e.id AND ev.etat_moderation = 'Visible') as evaluations_count
                FROM evenement e 
                WHERE e.idUtilisateur = ? 
                ORDER BY e.date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get pending events (Brouillon status)
     */
    public function getPending() {
        $sql = "SELECT e.*, u.nom, u.prenom, u.email,
                (SELECT COUNT(*) FROM participation p WHERE p.idEvenement = e.id) as participants_count
                FROM evenement e 
                JOIN utilisateur u ON e.idUtilisateur = u.id 
                WHERE e.statut = 'Brouillon'
                ORDER BY e.date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}







