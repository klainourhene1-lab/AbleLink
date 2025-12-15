<?php
require_once __DIR__ . '/../Model/ParticipationModel.php';
require_once __DIR__ . '/../Model/EventModel.php';
require_once __DIR__ . '/../Model/UserModel.php';

/**
 * Participation Controller
 * Handles HTTP requests related to event participation
 */
class ParticipationController {
    private $participationModel;
    private $eventModel;
    private $userModel;
    
    public function __construct() {
        $this->participationModel = new ParticipationModel();
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
            // Use session identity when available
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            $sessionUserId = $_SESSION['user_id'] ?? null;

            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            // Check for JSON decode errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON data: ' . json_last_error_msg());
            }
            
            if (!$data) {
                throw new Exception('Invalid JSON data received');
            }
            
            $action = $data['action'] ?? '';
            // Always trust session user if present
            $userId = $sessionUserId ?? ($data['idUtilisateur'] ?? null);
            $eventId = $data['idEvenement'] ?? null;

            // Normalize types
            if (is_string($eventId) && ctype_digit($eventId)) {
                $eventId = (int)$eventId;
            }
            if (is_string($userId) && ctype_digit($userId)) {
                $userId = (int)$userId;
            }

            if (!$userId || !$eventId) {
                throw new Exception('User ID and Event ID are required');
            }
            
            switch ($action) {
                case 'register':
                    $result = $this->register($userId, $eventId);
                    break;
                    
                case 'cancel':
                    $result = $this->cancel($userId, $eventId);
                    break;
                    
                case 'get':
                    $result = $this->get($userId, $eventId);
                    break;
                    
                default:
                    throw new Exception('Invalid action');
            }
            
            // Ensure clean output before JSON
            if (ob_get_level()) {
                ob_end_clean();
            }
            echo json_encode($result);
            exit;
            
        } catch (Exception $e) {
            error_log('Participation controller error: ' . $e->getMessage());
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
    
    private function register($userId, $eventId) {
        error_log("PARTICIPATION DEBUG: register() called - User: $userId, Event: $eventId");
        
        // Validate input
        if (($userId === null || $userId === false) || !$eventId) {
            error_log("PARTICIPATION DEBUG: Invalid data - returning early");
            return ['success' => false, 'message' => 'Invalid data'];
        }
        
        // Check if event exists
        $event = $this->eventModel->getById($eventId);
        if (!$event) {
            return ['success' => false, 'message' => 'Event not found'];
        }
        
        // Get user
        $user = $this->userModel->getById($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        
        // Check if user is admin or company
        $role = $user['role'];
        $isAdmin = $role === 'Admin';
        $isCompany = $role === 'Entreprise';
        $isEventCreator = $event['idUtilisateur'] == $userId;
        
        // Prevent companies and admins from registering for any events
        if ($isCompany) {
            return ['success' => false, 'message' => 'Les entreprises ne peuvent pas s\'inscrire à des événements.'];
        }
        
        if ($isAdmin) {
            return ['success' => false, 'message' => 'Les administrateurs ne peuvent pas s\'inscrire à des événements.'];
        }
        
        if ($isEventCreator) {
            return ['success' => false, 'message' => 'Vous ne pouvez pas vous inscrire à votre propre événement.'];
        }
        
        $participationId = $this->participationModel->register($userId, $eventId);
        
        if ($participationId) {
            // Prepare email data (send after response for speed)
            $emailData = [
                'to' => $user['email'],
                'subject' => "Confirmation d'inscription : " . $event['titre'],
                'prenom' => $user['prenom'],
                'nom' => $user['nom'],
                'titre' => $event['titre'],
                'date' => $event['date'],
                'lieu' => $event['lieu'],
                'participationId' => $participationId,
                'eventId' => $eventId
            ];
            
            // Send email asynchronously (after response)
            $this->sendConfirmationEmailAsync($emailData);

            return [
                'success' => true, 
                'message' => 'Inscription réussie',
                'participationId' => $participationId
            ];
        }

        // Idempotent behavior: if already registered, return success
        error_log("PARTICIPATION DEBUG: Checking if already registered");
        $existing = $this->participationModel->getByUserAndEvent($userId, $eventId);
        if ($existing) {
            error_log("PARTICIPATION DEBUG: User already registered - participation ID: " . ($existing['id'] ?? 'unknown'));
            
            // Prepare email data (send after response for speed)
            $emailData = [
                'to' => $user['email'],
                'subject' => "Confirmation d'inscription : " . $event['titre'],
                'prenom' => $user['prenom'],
                'nom' => $user['nom'],
                'titre' => $event['titre'],
                'date' => $event['date'],
                'lieu' => $event['lieu'],
                'participationId' => $existing['id'] ?? null,
                'eventId' => $eventId
            ];
            
            // Send email asynchronously (after response)
            $this->sendConfirmationEmailAsync($emailData);
            
            return [
                'success' => true,
                'message' => 'Déjà inscrit',
                'participationId' => $existing['id'] ?? null
            ];
        }

        error_log("PARTICIPATION DEBUG: Registration failed - no participation ID and not already registered");
        return ['success' => false, 'message' => 'Échec de l\'inscription'];
    }
    
    private function cancel($userId, $eventId) {
        $result = $this->participationModel->cancel($userId, $eventId);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Participation cancelled successfully'
            ];
        } else {
            throw new Exception('Failed to cancel participation');
        }
    }
    
    private function get($userId, $eventId) {
        $participation = $this->participationModel->getByUserAndEvent($userId, $eventId);
        
        return [
            'success' => true,
            'participation' => $participation
        ];
    }
    
    /**
     * Send confirmation email asynchronously (after response is sent)
     */
    private function sendConfirmationEmailAsync($emailData) {
        // This will execute after the response is sent to the user
        register_shutdown_function(function() use ($emailData) {
            // Load HTML template
            $templatePath = __DIR__ . '/email_templates/confirmation.html';
            
            if (file_exists($templatePath)) {
                $htmlMessage = file_get_contents($templatePath);
                
                // Get server host - ALWAYS use IPv4 for mobile compatibility
                $serverHost = '192.168.1.8'; // Your computer's IP address
                
                // Alternative: try to detect from SERVER_ADDR, but filter out IPv6
                if (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] !== '::1' && $_SERVER['SERVER_ADDR'] !== '127.0.0.1') {
                    $serverHost = $_SERVER['SERVER_ADDR'];
                }
                
                // Generate check-in URL for QR code
                $checkinUrl = sprintf(
                    "http://%s/hlili/view/general/checkin.php?p=%d&e=%d&email=%s",
                    $serverHost,
                    $emailData['participationId'],
                    $emailData['eventId'],
                    urlencode($emailData['to'])
                );
                
                // Use QR Server API to generate QR code
                $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($checkinUrl);
                
                error_log("EMAIL DEBUG: Check-in URL: " . $checkinUrl);
                error_log("EMAIL DEBUG: QR Code Image URL: " . $qrCodeUrl);
                
                // Replace placeholders with actual data
                $htmlMessage = str_replace('{{PRENOM}}', htmlspecialchars($emailData['prenom']), $htmlMessage);
                $htmlMessage = str_replace('{{NOM}}', htmlspecialchars($emailData['nom']), $htmlMessage);
                $htmlMessage = str_replace('{{TITRE}}', htmlspecialchars($emailData['titre']), $htmlMessage);
                $htmlMessage = str_replace('{{DATE}}', htmlspecialchars($emailData['date']), $htmlMessage);
                $htmlMessage = str_replace('{{LIEU}}', htmlspecialchars($emailData['lieu']), $htmlMessage);
                $htmlMessage = str_replace('{{QR_CODE_URL}}', $qrCodeUrl, $htmlMessage);
                $htmlMessage = str_replace('{{PARTICIPATION_ID}}', $emailData['participationId'], $htmlMessage);
                $htmlMessage = str_replace('{{CHECKIN_URL}}', $checkinUrl, $htmlMessage);
            } else {
                // Fallback to plain text if template not found
                $htmlMessage = null;
            }
            
            // Create plain text version as fallback
            $textMessage = "Bonjour " . $emailData['prenom'] . " " . $emailData['nom'] . ",\n\n";
            $textMessage .= "Votre inscription à l'événement \"" . $emailData['titre'] . "\" a été confirmée.\n\n";
            $textMessage .= "Détails de l'événement :\n";
            $textMessage .= "Date : " . $emailData['date'] . "\n";
            $textMessage .= "Lieu : " . $emailData['lieu'] . "\n\n";
            $textMessage .= "Merci de votre participation !\n";
            $textMessage .= "L'équipe AbeLink";
            
            // Set up headers for HTML email
            $headers = "From: AbeLink <no-reply@abelink.com>\r\n";
            $headers .= "Reply-To: no-reply@abelink.com\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            
            if ($htmlMessage) {
                // Send multipart email (HTML + plain text)
                $boundary = md5(uniqid(time()));
                $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
                
                $message = "--{$boundary}\r\n";
                $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
                $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                $message .= $textMessage . "\r\n\r\n";
                
                $message .= "--{$boundary}\r\n";
                $message .= "Content-Type: text/html; charset=UTF-8\r\n";
                $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                $message .= $htmlMessage . "\r\n\r\n";
                
                $message .= "--{$boundary}--";
            } else {
                // Send plain text only
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
                $message = $textMessage;
            }
            
            error_log("EMAIL DEBUG: Attempting to send to: " . $emailData['to']);
            error_log("EMAIL DEBUG: Subject: " . $emailData['subject']);
            error_log("EMAIL DEBUG: Using " . ($htmlMessage ? "HTML" : "plain text") . " template");
            $mailResult = mail($emailData['to'], $emailData['subject'], $message, $headers);
            error_log("EMAIL DEBUG: Mail result: " . ($mailResult ? 'TRUE' : 'FALSE'));
        });
    }
}

// If this file is called directly, handle the request
if (basename($_SERVER['PHP_SELF']) == 'ParticipationController.php' || 
    strpos($_SERVER['REQUEST_URI'], 'manage_participation.php') !== false) {
    $controller = new ParticipationController();
    $controller->handleRequest();
}
