<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/EventModel.php';
require_once __DIR__ . '/EvaluationModel.php';
require_once __DIR__ . '/UserModel.php';

/**
 * Admin Model
 * Handles admin dashboard statistics and operations
 */
class AdminModel {
    private $db;
    private $eventModel;
    private $evaluationModel;
    private $userModel;
    /**
 * Get recent events
 */
public function getRecentEvents($days = 7) {
    try {
        $sql = "SELECT e.*, u.prenom, u.nom 
                FROM evenement e 
                JOIN utilisateur u ON e.idUtilisateur = u.id 
                WHERE e.date_creation >= DATE_SUB(NOW(), INTERVAL ? DAY)
                ORDER BY e.date_creation DESC 
                LIMIT 10";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Error getting recent events: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get recent evaluations
 */
public function getRecentEvaluations($days = 7) {
    try {
        $sql = "SELECT e.*, ev.titre as event_titre 
                FROM evaluation e 
                JOIN evenement ev ON e.idEvenement = ev.id 
                WHERE e.dateEvaluation >= DATE_SUB(NOW(), INTERVAL ? DAY)
                ORDER BY e.dateEvaluation DESC 
                LIMIT 10";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Error getting recent evaluations: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get recent user registrations
 */
public function getRecentUsers($days = 7) {
    try {
        $sql = "SELECT u.prenom, u.nom, u.email, u.date_inscription 
                FROM utilisateur u 
                WHERE u.date_inscription >= DATE_SUB(NOW(), INTERVAL ? DAY)
                ORDER BY u.date_inscription DESC 
                LIMIT 10";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Error getting recent users: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get recent event status changes
 */
public function getRecentStatusChanges($days = 7) {
    try {
        // This assumes you have an event_history table or you can modify based on your schema
        $sql = "SELECT e.titre, e.statut as nouveau_statut, e.date_modification, u.prenom, u.nom 
                FROM evenement e 
                JOIN utilisateur u ON e.idUtilisateur = u.id 
                WHERE e.date_modification >= DATE_SUB(NOW(), INTERVAL ? DAY)
                AND e.statut IN ('Publié', 'Rejeté')
                ORDER BY e.date_modification DESC 
                LIMIT 10";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Error getting recent status changes: ' . $e->getMessage());
        return [];
    }
}
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->eventModel = new EventModel();
        $this->evaluationModel = new EvaluationModel();
        $this->userModel = new UserModel();
    }
    
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats() {
        $stats = [];
        
        // Total events
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM evenement");
        $stats['totalEvents'] = $stmt->fetch()['total'];
        
        // Pending events (Brouillon status)
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM evenement WHERE statut = 'Brouillon'");
        $stats['pendingEvents'] = $stmt->fetch()['total'];
        
        // Total evaluations
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM evaluation");
        $stats['totalEvaluations'] = $stmt->fetch()['total'];
        
        // Total enterprises (users who are companies)
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM utilisateur WHERE role = 'Entreprise'");
        $stats['totalEnterprises'] = $stmt->fetch()['total'];
        
        // Average rating
        $stmt = $this->db->query("SELECT AVG(note) as avg FROM evaluation");
        $stats['avgRating'] = round($stmt->fetch()['avg'], 1);
        
        // Events this month
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM evenement WHERE MONTH(date) = MONTH(CURRENT_DATE()) AND YEAR(date) = YEAR(CURRENT_DATE())");
        $stats['eventsThisMonth'] = $stmt->fetch()['total'];
        
        // Total participants
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM participation");
        $stats['totalParticipants'] = $stmt->fetch()['total'];
        
        return $stats;
    }
    
    /**
     * Get pending events for moderation
     */
    public function getPendingEvents() {
        return $this->eventModel->getPending();
    }
    
    /**
     * Get reported evaluations
     */
    public function getReportedEvaluations() {
        return $this->evaluationModel->getReported();
    }
    
    /**
     * Get enterprises statistics
     */
    public function getEnterprisesStats() {
        $sql = "SELECT u.id as idUtilisateur, u.nom, u.prenom, u.email,
                COUNT(DISTINCT ev.id) as events_count,
                AVG(eval.note) as avg_rating,
                COUNT(DISTINCT p.id) as total_participations,
                (COUNT(DISTINCT p.id) * 100.0 / GREATEST(COUNT(DISTINCT ev.id) * 50, 1)) as participation_rate
                FROM utilisateur u
                LEFT JOIN evenement ev ON u.id = ev.idUtilisateur
                LEFT JOIN evaluation eval ON ev.id = eval.idEvenement
                LEFT JOIN participation p ON ev.id = p.idEvenement
                WHERE u.role = 'Entreprise'
                GROUP BY u.id, u.nom, u.prenom, u.email
                ORDER BY avg_rating DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get events for analytics
     */
    public function getEventsForAnalytics() {
        $sql = "SELECT 
                MONTH(date) as month,
                COUNT(*) as event_count,
                AVG(participants_count) as avg_participants
                FROM evenement e
                LEFT JOIN (
                    SELECT idEvenement, COUNT(*) as participants_count 
                    FROM participation 
                    GROUP BY idEvenement
                ) p ON e.id = p.idEvenement
                WHERE YEAR(date) = YEAR(CURRENT_DATE())
                GROUP BY MONTH(date)
                ORDER BY month";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get status distribution
     */
    public function getStatusDistribution() {
        $stmt = $this->db->query("SELECT statut, COUNT(*) as count FROM evenement GROUP BY statut");
        $statusData = $stmt->fetchAll();
        
        $distribution = [
            'Publié' => 0,
            'Brouillon' => 0,
            'Rejeté' => 0
        ];
        
        foreach ($statusData as $data) {
            $distribution[$data['statut']] = (int)$data['count'];
        }
        
        return $distribution;
    }
    /**
     * Get all users
     */
    public function getAllUsers() {
        try {
            $sql = "SELECT id, nom, prenom, email, role, statut, date_inscription, photo FROM utilisateur ORDER BY date_inscription DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error getting all users: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Add a new user
     */
    public function addUser($data) {
        try {
            // Check if email already exists
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Cet email est déjà utilisé.');
            }

            $sql = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role, statut, date_inscription) 
                    VALUES (:nom, :prenom, :email, :password, :role, :statut, NOW())";
            
            $stmt = $this->db->prepare($sql);
            
            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            
            $stmt->execute([
                ':nom' => $data['nom'],
                ':prenom' => $data['prenom'],
                ':email' => $data['email'],
                ':password' => $hashedPassword,
                ':role' => $data['role'],
                ':statut' => $data['statut'] ?? 'actif'
            ]);
            
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            error_log('Error adding user: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get detailed evaluation information
     */
    public function getEvaluationDetails($evaluationId) {
        try {
            $sql = "SELECT e.*, 
                    ev.titre as event_titre, 
                    ev.date as event_date,
                    u.prenom, 
                    u.nom
                    FROM evaluation e
                    JOIN evenement ev ON e.idEvenement = ev.id
                    JOIN utilisateur u ON e.idUtilisateur = u.id
                    WHERE e.id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$evaluationId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                error_log("No evaluation found with ID: " . $evaluationId);
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log('Database error in getEvaluationDetails: ' . $e->getMessage());
            error_log('SQL: ' . $sql);
            error_log('Evaluation ID: ' . $evaluationId);
            return null;
        } catch (Exception $e) {
            error_log('Error getting evaluation details: ' . $e->getMessage());
            return null;
        }
    }
}






