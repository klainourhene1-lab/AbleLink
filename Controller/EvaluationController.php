<?php
require_once __DIR__ . '/../Model/EvaluationModel.php';
require_once __DIR__ . '/../Model/EventModel.php';
require_once __DIR__ . '/../Model/UserModel.php';

/**
 * Evaluation Controller
 * Handles HTTP requests related to evaluations
 */
class EvaluationController {
    private $evaluationModel;
    private $eventModel;
    private $userModel;
    
    public function __construct() {
        $this->evaluationModel = new EvaluationModel();
        $this->eventModel = new EventModel();
        $this->userModel = new UserModel();
    }
    
    /**
     * Handle request
     */
    public function handleRequest() {
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
                $input = file_get_contents('php://input');
                $data = json_decode($input, true);
                
                // Check for JSON decode errors
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Invalid JSON data: ' . json_last_error_msg());
                }
            } else {
                $data = $_GET;
            }
            
            if (!$data) {
                throw new Exception('Invalid JSON data received');
            }
            
            $action = $data['action'] ?? '';
            
            switch ($action) {
                case 'submit':
                    $result = $this->submit($data);
                    break;
                    
                case 'get':
                    $result = $this->get($data);
                    break;
                    
                case 'get_all':
                    $result = $this->getAll($data);
                    break;
                    
                case 'get_user_evaluations':
                    $result = $this->getUserEvaluations($data);
                    break;
                    
                case 'report':
                    $result = $this->report($data);
                    break;
                    
                case 'update':
                    $result = $this->update($data);
                    break;
                    
                case 'delete':
                    $result = $this->delete($data);
                    break;
                    
                case 'get_event_stats':
                    $result = $this->getEventStats($data);
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
            error_log('Evaluation controller error: ' . $e->getMessage());
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
    
    private function submit($data) {
        $userId = $data['idUtilisateur'] ?? null;
        $eventId = $data['idEvenement'] ?? null;
        $note_accessibilite = $data['note_accessibilite'] ?? null;
        $note_inclusion = $data['note_inclusion'] ?? null;
        $commentaire = $data['commentaire'] ?? null;
        
        if (!$userId || !$eventId) {
            throw new Exception('User ID and Event ID are required');
        }
        
        // Get event to check creator
        $event = $this->eventModel->getById($eventId);
        if (!$event) {
            throw new Exception('Event not found');
        }
        
        // Get user to check role
        $user = $this->userModel->getById($userId);
        if (!$user) {
            throw new Exception('User not found');
        }
        
        // Check if user is admin or company (isAdmin = 0 means company/enterprise)
        $isAdmin = $user['isAdmin'] == 1;
        $isCompany = $user['isAdmin'] == 0;
        $isEventCreator = $event['idUtilisateur'] == $userId;
        
        // Prevent admin or company from evaluating events they created
        if (($isAdmin || $isCompany) && $isEventCreator) {
            throw new Exception('Vous ne pouvez pas évaluer un événement que vous avez créé.');
        }
        
        if ($note_accessibilite === null || $note_accessibilite < 1 || $note_accessibilite > 5) {
            throw new Exception('Accessibility note must be between 1 and 5');
        }
        if ($note_inclusion === null || $note_inclusion < 1 || $note_inclusion > 5) {
            throw new Exception('Inclusion note must be between 1 and 5');
        }
        
        // Check if evaluation already exists
        $existing = $this->evaluationModel->getByUserAndEvent($userId, $eventId);
        
        if ($existing) {
            // Update existing evaluation
            $result = $this->evaluationModel->update($existing['id'], [
                'note_accessibilite' => $note_accessibilite,
                'note_inclusion' => $note_inclusion,
                'commentaire' => $commentaire
            ]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Evaluation updated successfully',
                    'evaluationId' => $existing['id'],
                    'action' => 'updated'
                ];
            }
        } else {
            // Create new evaluation
            $evaluationId = $this->evaluationModel->create([
                'idUtilisateur' => $userId,
                'idEvenement' => $eventId,
                'note_accessibilite' => $note_accessibilite,
                'note_inclusion' => $note_inclusion,
                'commentaire' => $commentaire
            ]);
            
            if ($evaluationId) {
                return [
                    'success' => true,
                    'message' => 'Evaluation submitted successfully',
                    'evaluationId' => $evaluationId,
                    'action' => 'created'
                ];
            }
        }
        
        throw new Exception('Failed to submit evaluation');
    }
    
    private function get($data) {
        $userId = $data['idUtilisateur'] ?? null;
        $eventId = $data['idEvenement'] ?? null;
        
        if (!$userId || !$eventId) {
            throw new Exception('User ID and Event ID are required');
        }
        
        $evaluation = $this->evaluationModel->getByUserAndEvent($userId, $eventId);
        
        return [
            'success' => true,
            'evaluation' => $evaluation
        ];
    }
    
    private function getAll($data) {
        $eventId = $data['eventId'] ?? null;
        
        if (!$eventId) {
            throw new Exception('Event ID is required');
        }
        
        $evaluations = $this->evaluationModel->getByEventId($eventId);
        
        return [
            'success' => true,
            'evaluations' => $evaluations
        ];
    }
    
    private function getUserEvaluations($data) {
        $userId = $data['userId'] ?? null;
        
        if (!$userId) {
            throw new Exception('User ID is required');
        }
        
        // Get evaluations with event details
        $evaluations = $this->evaluationModel->getByUserId($userId);
        
        return [
            'success' => true,
            'evaluations' => $evaluations
        ];
    }
    
    private function report($data) {
        $evaluationId = $data['evaluationId'] ?? null;
        
        if (!$evaluationId) {
            throw new Exception('Evaluation ID is required');
        }
        
        $result = $this->evaluationModel->report($evaluationId);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Evaluation reported successfully'
            ];
        } else {
            throw new Exception('Failed to report evaluation');
        }
    }
    
    private function update($data) {
        $evaluationId = $data['evaluationId'] ?? null;
        $note_accessibilite = $data['note_accessibilite'] ?? null;
        $note_inclusion = $data['note_inclusion'] ?? null;
        $commentaire = $data['commentaire'] ?? null;
        
        if (!$evaluationId) {
            throw new Exception('Evaluation ID is required');
        }
        
        $result = $this->evaluationModel->update($evaluationId, [
            'note_accessibilite' => $note_accessibilite,
            'note_inclusion' => $note_inclusion,
            'commentaire' => $commentaire
        ]);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Evaluation updated successfully'
            ];
        } else {
            throw new Exception('Failed to update evaluation');
        }
    }
    
    private function delete($data) {
        $evaluationId = $data['evaluationId'] ?? null;
        
        if (!$evaluationId) {
            throw new Exception('Evaluation ID is required');
        }
        
        $result = $this->evaluationModel->delete($evaluationId);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Evaluation deleted successfully'
            ];
        } else {
            throw new Exception('Failed to delete evaluation');
        }
    }
    
    private function getEventStats($data) {
        $eventId = $data['eventId'] ?? null;
        
        if (!$eventId) {
            throw new Exception('Event ID is required');
        }
        
        $stats = $this->evaluationModel->getEventStats($eventId);
        
        return [
            'success' => true,
            'stats' => $stats
        ];
    }
}

// If this file is called directly, handle the request
if (basename($_SERVER['PHP_SELF']) == 'EvaluationController.php' || 
    strpos($_SERVER['REQUEST_URI'], 'manage_evaluation.php') !== false) {
    $controller = new EvaluationController();
    $controller->handleRequest();
}
?>