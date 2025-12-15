<?php
require_once __DIR__ . '/../Model/EventModel.php';
require_once __DIR__ . '/../Model/UserModel.php';

/**
 * Event Controller
 * Handles HTTP requests related to events
 */
class EventController {
    private $eventModel;
    private $userModel;
    
    public function __construct() {
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
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit(0);
        }
        
        try {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            // Check for JSON decode errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON data: ' . json_last_error_msg());
            }
            
            if (!$data) {
                throw new Exception('Invalid JSON data received');
            }
            
            $action = $data['action'] ?? 'save';
            
            switch ($action) {
                case 'save':
                    $result = $this->save($data);
                    break;
                    
                case 'get':
                    $result = $this->get($data);
                    break;
                    
                case 'get_all':
                    $result = $this->getAll($data);
                    break;
                    
                case 'delete':
                    $result = $this->delete($data);
                    break;
                    
                case 'update_status':
                    $result = $this->updateStatus($data);
                    break;
                    
                case 'get_user_events':
                    $result = $this->getUserEvents($data);
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
            error_log('Event controller error: ' . $e->getMessage());
            // Ensure clean output before JSON
            if (ob_get_level()) {
                ob_end_clean();
            }
            // Return 500 to ensure frontend sees it as error, but include message
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement: ' . $e->getMessage()
            ]);
            exit;
        }
    }
    
    private function save($data) {
    // Validate required fields
    $required = ['titre', 'description', 'date', 'lieu'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Field '$field' is required");
        }
    }

    // Get valid user ID
    $validUserId = $this->userModel->getValidUserId($data['idUtilisateur'] ?? null);
    
    if ($validUserId === false || $validUserId === null) {
        throw new Exception('Aucun utilisateur valide trouvé pour créer l\'événement');
    }
    
    // Get user to check role
    $user = $this->userModel->getById($validUserId);
    if (!$user) {
        throw new Exception('Utilisateur non trouvé');
    }
    
    // Check if user has permission to create events
    // Only Admin and Entreprise can create events
    if ($user['role'] !== 'Admin' && $user['role'] !== 'Entreprise') {
        throw new Exception('Vous n\'avez pas la permission de créer des événements. Seuls les administrateurs et les entreprises peuvent créer des événements.');
    }
    
    // Determine event status based on user role
    // Admin events are published immediately (override incoming), Enterprise events need approval (Brouillon)
    $defaultStatus = ($user['role'] === 'Admin') ? 'Publié' : 'Brouillon';
    
    // Prepare event data
    $eventData = [
        'titre' => $data['titre'],
        'description' => $data['description'],
        'date' => $data['date'],
        'lieu' => $data['lieu'],
        'theme' => $data['theme'] ?? 'Inclusion',
        'accessibilite' => $data['accessibilite'] ?? '',
        'statut' => ($user['role'] === 'Admin') ? 'Publié' : ($data['statut'] ?? $defaultStatus),
        'participants_max' => $data['participants_max'] ?? 50,
        'inscrits' => 0, // Always start with 0 participants
        'idUtilisateur' => $validUserId
    ];
    
    if (isset($data['id']) && !empty($data['id'])) {
        // Update existing event
        $result = $this->eventModel->update($data['id'], $eventData);
        if ($result) {
            return [
                'success' => true,
                'message' => 'Événement mis à jour avec succès',
                'eventId' => $data['id']
            ];
        }
    } else {
        // Create new event
        $eventId = $this->eventModel->create($eventData);
        if ($eventId) {
            return [
                'success' => true,
                'message' => 'Événement créé avec succès',
                'eventId' => $eventId
            ];
        }
    }
    
    throw new Exception('Failed to save event');
}
    
    public function get($data) {
        $eventId = $data['id'] ?? null;
        if (!$eventId) {
            throw new Exception('Event ID is required');
        }
        
        $event = $this->eventModel->getById($eventId);
        
        if ($event) {
            return [
                'success' => true,
                'event' => $event
            ];
        } else {
            throw new Exception('Event not found');
        }
    }

    public function getAll($data) {
        $filters = $data;
        unset($filters['action']);
        
        $events = $this->eventModel->getAll($filters);
        
        return [
            'success' => true,
            'events' => $events,
            'total' => count($events)
        ];
    }
    
    private function delete($data) {
        $eventId = $data['id'] ?? null;
        if (!$eventId) {
            throw new Exception('Event ID is required');
        }
        
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
    
    private function updateStatus($data) {
        $eventId = $data['id'] ?? null;
        $status = $data['statut'] ?? null;
        
        if (!$eventId || !$status) {
            throw new Exception('Event ID and Status are required');
        }
        
        $result = $this->eventModel->updateStatus($eventId, $status);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Statut de l\'événement mis à jour avec succès'
            ];
        } else {
            throw new Exception('Failed to update event status');
        }
    }
    
    private function getUserEvents($data) {
        $userId = $data['userId'] ?? null;
        if (!$userId) {
            throw new Exception('User ID is required');
        }
        
        $events = $this->eventModel->getByUserId($userId);
        
        return [
            'success' => true,
            'events' => $events,
            'total' => count($events)
        ];
    }
}

// If this file is called directly, handle the request
if (basename($_SERVER['PHP_SELF']) == 'EventController.php' || 
    strpos($_SERVER['REQUEST_URI'], 'save_event.php') !== false) {
    $controller = new EventController();
    $controller->handleRequest();
}
