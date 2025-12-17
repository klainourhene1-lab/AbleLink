<?php
require_once __DIR__ . '/../Model/AdminModel.php';
require_once __DIR__ . '/../Model/EventModel.php';
require_once __DIR__ . '/../Model/EvaluationModel.php';

/**
 * Admin Controller
 * Handles admin dashboard and moderation requests
 */
class AdminController {
    private $adminModel;
    private $eventModel;
    private $evaluationModel;
    /**
 * Get recent activities for dashboard
 */
public function getRecentActivities() {
    $activities = [];
    
    try {
        // Get recent events created (last 7 days)
        $recentEvents = $this->adminModel->getRecentEvents(7);
        foreach ($recentEvents as $event) {
            $activities[] = [
                'title' => 'Nouvel événement créé',
                'description' => '"' . $event['titre'] . '" par ' . $event['prenom'] . ' ' . $event['nom'],
                'time' => $this->getTimeAgo($event['date_creation']),
                'icon' => 'fas fa-calendar-plus',
                'color' => 'linear-gradient(135deg, var(--primary), var(--primary-light))'
            ];
        }
        
        // Get recent evaluations (last 7 days)
        $recentEvals = $this->adminModel->getRecentEvaluations(7);
        foreach ($recentEvals as $eval) {
            $activities[] = [
                'title' => 'Nouvelle évaluation',
                'description' => 'Note ' . $eval['note_accessibilite'] . '/5 pour "' . $eval['event_titre'] . '"',
                'time' => $this->getTimeAgo($eval['dateEvaluation']),
                'icon' => 'fas fa-star',
                'color' => 'linear-gradient(135deg, var(--success), #34d399)'
            ];
        }
        
        // Get reported evaluations
        $reportedEvals = $this->adminModel->getReportedEvaluations();
        foreach ($reportedEvals as $eval) {
            $activities[] = [
                'title' => 'Évaluation signalée',
                'description' => 'Commentaire sur "' . $eval['event_titre'] . '"',
                'time' => $this->getTimeAgo($eval['dateEvaluation']),
                'icon' => 'fas fa-exclamation-triangle',
                'color' => 'linear-gradient(135deg, var(--danger), #f87171)'
            ];
        }
        
        // Get new user registrations (last 7 days)
        $newUsers = $this->adminModel->getRecentUsers(7);
        foreach ($newUsers as $user) {
            $activities[] = [
                'title' => 'Nouvel utilisateur',
                'description' => $user['prenom'] . ' ' . $user['nom'] . ' (' . $user['email'] . ')',
                'time' => $this->getTimeAgo($user['date_inscription']),
                'icon' => 'fas fa-user-plus',
                'color' => 'linear-gradient(135deg, var(--warning), #fbbf24)'
            ];
        }
        
        // Get event status changes (last 7 days)
        $statusChanges = $this->adminModel->getRecentStatusChanges(7);
        foreach ($statusChanges as $change) {
            $statusText = $change['nouveau_statut'] == 'Publié' ? 'approuvé' : 
                         ($change['nouveau_statut'] == 'Rejeté' ? 'rejeté' : 'mis en brouillon');
            $activities[] = [
                'title' => 'Événement ' . $statusText,
                'description' => '"' . $change['titre'] . '" par ' . $change['prenom'] . ' ' . $change['nom'],
                'time' => $this->getTimeAgo($change['date_modification']),
                'icon' => 'fas fa-check-circle',
                'color' => $change['nouveau_statut'] == 'Publié' ? 
                          'linear-gradient(135deg, var(--success), #34d399)' : 
                          'linear-gradient(135deg, var(--danger), #f87171)'
            ];
        }
        
        // Sort by time (newest first) and limit to 10
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });
        
        return array_slice($activities, 0, 10);
        
    } catch (Exception $e) {
        error_log('Error getting recent activities: ' . $e->getMessage());
        return [];
    }
}

/**
 * Helper function to format time ago
 */
private function getTimeAgo($datetime) {
    if (!$datetime) return 'Date inconnue';
    
    $time = strtotime($datetime);
    $timeDiff = time() - $time;
    
    if ($timeDiff < 60) return 'À l\'instant';
    if ($timeDiff < 3600) return 'Il y a ' . round($timeDiff / 60) . ' min';
    if ($timeDiff < 86400) return 'Il y a ' . round($timeDiff / 3600) . ' h';
    if ($timeDiff < 2592000) return 'Il y a ' . round($timeDiff / 86400) . ' j';
    return 'Il y a ' . round($timeDiff / 2592000) . ' mois';
}
    public function __construct() {
        $this->adminModel = new AdminModel();
        $this->eventModel = new EventModel();
        $this->evaluationModel = new EvaluationModel();
    }
    
    /**
     * Handle AJAX requests for admin actions
     */
    public function handleAjaxRequest() {
        // Ensure clean output - no whitespace or errors before JSON
        if (ob_get_level()) {
            ob_clean();
        }
        
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit(0);
        }
        
        try {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Handle both JSON and FormData
                $input = file_get_contents('php://input');
                $data = null;
                
                // Try to decode JSON first
                if (!empty($input)) {
                    $data = json_decode($input, true);
                    // Check for JSON decode errors
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $data = null;
                    }
                }
                
                // If JSON decode failed or empty, try FormData (from $_POST)
                if (!$data && !empty($_POST)) {
                    $data = $_POST;
                }
            } else {
                $data = $_GET;
            }
            
            if (!$data || (is_array($data) && empty($data))) {
                throw new Exception('Invalid data received');
            }
            
            $action = $data['action'] ?? '';
            $eventId = $data['eventId'] ?? $data['event_id'] ?? $data['id'] ?? null;
            $evaluationId = $data['evaluationId'] ?? $data['evaluation_id'] ?? null;
            
            switch ($action) {
                case 'approve_event':
                    if (!$eventId) throw new Exception('Event ID required');
                    $result = $this->approveEvent($eventId);
                    break;
                    
                case 'reject_event':
                    if (!$eventId) throw new Exception('Event ID required');
                    $result = $this->rejectEvent($eventId);
                    break;
                    
                case 'delete_event':
                    if (!$eventId) throw new Exception('Event ID required');
                    $result = $this->deleteEvent($eventId);
                    break;
                    
                case 'approve_evaluation':
                    if (!$evaluationId) throw new Exception('Evaluation ID required');
                    $result = $this->approveEvaluation($evaluationId);
                    break;
                    
                case 'reject_evaluation':
                    if (!$evaluationId) throw new Exception('Evaluation ID required');
                    $result = $this->rejectEvaluation($evaluationId);
                    break;
                    
                case 'get_pending_events':
                    $result = $this->getPendingEvents();
                    break;
                    
                case 'get_reported_evaluations':
                    $result = $this->getReportedEvaluations();
                    break;
                    
                case 'get_event_evaluations':
                    if (!$eventId) throw new Exception('Event ID required');
                    $result = $this->getEventEvaluations($eventId);
                    break;
                    
                case 'get_all_users':
                    $result = $this->getAllUsers();
                    break;
                    
                case 'get_event_details':
                    if (!$eventId) throw new Exception('Event ID required');
                    $result = $this->getEventDetails($eventId);
                    break;
                    
                case 'add_user':
                    $result = $this->addUser($data);
                    break;
                    
                case 'get_evaluation_details':
                    if (!$evaluationId) throw new Exception('Evaluation ID required');
                    $result = $this->getEvaluationDetails($evaluationId);
                    break;

                case 'approve_story':
                    if (!isset($data['storyId'])) throw new Exception('Story ID required');
                    $result = $this->approveStory($data['storyId']);
                    break;
                    
                case 'reject_story':
                    if (!isset($data['storyId'])) throw new Exception('Story ID required');
                    $result = $this->rejectStory($data['storyId']);
                    break;
                    
                case 'delete_story':
                    if (!isset($data['storyId'])) throw new Exception('Story ID required');
                    $result = $this->deleteStoryItem($data['storyId']);
                    break;

                default:
                    throw new Exception('Invalid action: ' . $action);
            }
            
            // Ensure clean output before JSON
            if (ob_get_level()) {
                ob_end_clean();
            }
            echo json_encode($result);
            exit;
            
        } catch (Exception $e) {
            error_log('Moderation error: ' . $e->getMessage());
            // Ensure clean output before JSON
            if (ob_get_level()) {
                ob_end_clean();
            }
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
            exit;
        }
    }
    
    /**
     * Get dashboard data for rendering
     */
    public function getDashboardData() {
        return [
            'stats' => $this->adminModel->getDashboardStats(),
            'pendingEvents' => $this->adminModel->getPendingEvents(),
            'reportedEvaluations' => $this->adminModel->getReportedEvaluations(),
            'enterprisesStats' => $this->adminModel->getEnterprisesStats(),
            'analyticsData' => $this->adminModel->getEventsForAnalytics(),
            'statusDistribution' => $this->adminModel->getStatusDistribution(),
            'storyStats' => $this->adminModel->getStoryStats(),
            'pendingStories' => $this->adminModel->getPendingStories(),
            'storyAnalytics' => [
                'monthly' => $this->adminModel->getStoriesByMonth(),
                'distribution' => $this->adminModel->getStoriesStatusDistribution(),
                'topContributors' => $this->adminModel->getTopContributors()
            ]
        ];
    }
    
    private function approveEvent($eventId) {
        $result = $this->eventModel->updateStatus($eventId, 'Publié');
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Événement approuvé avec succès'
            ];
        } else {
            throw new Exception('Failed to approve event');
        }
    }
    
    private function rejectEvent($eventId) {
        $result = $this->eventModel->updateStatus($eventId, 'Rejeté');
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Événement rejeté'
            ];
        } else {
            throw new Exception('Failed to reject event');
        }
    }
    
    private function deleteEvent($eventId) {
        $result = $this->eventModel->delete($eventId);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Événement supprimé avec succès'
            ];
        } else {
            throw new Exception('Failed to delete event');
        }
    }
    
    private function approveEvaluation($evaluationId) {
        $result = $this->evaluationModel->approve($evaluationId);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Évaluation approuvée'
            ];
        } else {
            throw new Exception('Failed to approve evaluation');
        }
    }
    
    private function rejectEvaluation($evaluationId) {
        $result = $this->evaluationModel->delete($evaluationId);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Évaluation supprimée'
            ];
        } else {
            throw new Exception('Failed to reject evaluation');
        }
    }
    
    private function getPendingEvents() {
        $events = $this->adminModel->getPendingEvents();
        
        return [
            'success' => true,
            'events' => $events
        ];
    }
    
    private function getReportedEvaluations() {
        $evaluations = $this->adminModel->getReportedEvaluations();
        
        return [
            'success' => true,
            'evaluations' => $evaluations
        ];
    }
    
    private function getEventEvaluations($eventId) {
        $evaluations = $this->evaluationModel->getByEventId($eventId);
        
        return [
            'success' => true,
            'evaluations' => $evaluations
        ];
    }

    private function getAllUsers() {
        $users = $this->adminModel->getAllUsers();
        return [
            'success' => true,
            'users' => $users
        ];
    }

    private function addUser($data) {
        // Basic validation
        if (empty($data['nom']) || empty($data['prenom']) || empty($data['email']) || empty($data['password'])) {
            throw new Exception('Tous les champs obligatoires doivent être remplis.');
        }

        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Format d\'email invalide.');
        }

        // Validate password length
        if (strlen($data['password']) < 6) {
            throw new Exception('Le mot de passe doit contenir au moins 6 caractères.');
        }

        $userId = $this->adminModel->addUser($data);

        return [
            'success' => true,
            'message' => 'Utilisateur ajouté avec succès',
            'userId' => $userId
        ];
    }

    private function getEvaluationDetails($evaluationId) {
        try {
            $evaluation = $this->adminModel->getEvaluationDetails($evaluationId);
            
            if (!$evaluation) {
                error_log("Evaluation not found in AdminController: ID = " . $evaluationId);
                throw new Exception('Évaluation introuvable (ID: ' . $evaluationId . ')');
            }
            
            return [
                'success' => true,
                'evaluation' => $evaluation
            ];
        } catch (Exception $e) {
            error_log("Error in AdminController::getEvaluationDetails: " . $e->getMessage());
            throw $e;
        }
    }

    private function approveStory($storyId) {
        try {
            if ($this->adminModel->updateStoryStatus($storyId, 'approved')) {
                return ['success' => true, 'message' => 'Story approuvée avec succès'];
            }
            throw new Exception('Erreur lors de l\'approbation de la story');
        } catch (Exception $e) {
            error_log('Error approving story: ' . $e->getMessage());
            throw $e;
        }
    }

    private function rejectStory($storyId) {
        try {
            if ($this->adminModel->updateStoryStatus($storyId, 'rejected')) {
                return ['success' => true, 'message' => 'Story rejetée'];
            }
            throw new Exception('Erreur lors du rejet de la story');
        } catch (Exception $e) {
            error_log('Error rejecting story: ' . $e->getMessage());
            throw $e;
        }
    }

    private function deleteStoryItem($storyId) {
        try {
            if ($this->adminModel->deleteStory($storyId)) {
                return ['success' => true, 'message' => 'Story supprimée avec succès'];
            }
            throw new Exception('Erreur lors de la suppression de la story');
        } catch (Exception $e) {
            error_log('Error deleting story: ' . $e->getMessage());
            throw $e;
        }
    }

    private function getEventDetails($eventId) {
        try {
            $event = $this->eventModel->getById($eventId);
            
            if ($event) {
                return [
                    'success' => true,
                    'event' => $event
                ];
            } else {
                throw new Exception('Événement non trouvé');
            }
        } catch (Exception $e) {
            error_log('Error getting event details: ' . $e->getMessage());
            throw $e;
        }
    }
}

// If this file is called directly, handle the request
if (basename($_SERVER['PHP_SELF']) == 'AdminController.php' || 
    strpos($_SERVER['REQUEST_URI'], 'admin_moderation.php') !== false) {
    $controller = new AdminController();
    $controller->handleAjaxRequest();
}
