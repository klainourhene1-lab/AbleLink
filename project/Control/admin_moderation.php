<?php
// admin_moderation.php - Handle admin moderation actions

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$host = '127.0.0.1';
$dbname = 'projet';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Handle both POST and GET requests
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
    } else {
        $data = $_GET;
    }
    
    if (!$data) {
        throw new Exception('Invalid data received');
    }
    
    $action = $data['action'] ?? '';
    $eventId = $data['eventId'] ?? null;
    $evaluationId = $data['evaluationId'] ?? null;
    
    switch ($action) {
        case 'approve_event':
            if (!$eventId) throw new Exception('Event ID required');
            $result = approveEvent($pdo, $eventId);
            break;
            
        case 'reject_event':
            if (!$eventId) throw new Exception('Event ID required');
            $result = rejectEvent($pdo, $eventId);
            break;
            
        case 'delete_event':
            if (!$eventId) throw new Exception('Event ID required');
            $result = deleteEvent($pdo, $eventId);
            break;
            
        case 'approve_evaluation':
            if (!$evaluationId) throw new Exception('Evaluation ID required');
            $result = approveEvaluation($pdo, $evaluationId);
            break;
            
        case 'reject_evaluation':
            if (!$evaluationId) throw new Exception('Evaluation ID required');
            $result = rejectEvaluation($pdo, $evaluationId);
            break;
            
        case 'get_pending_events':
            $result = getPendingEvents($pdo);
            break;
            
        case 'get_reported_evaluations':
            $result = getReportedEvaluations($pdo);
            break;
            
        case 'get_event_evaluations':
            if (!$eventId) throw new Exception('Event ID required');
            $result = getEventEvaluations($pdo, $eventId);
            break;
            
        default:
            throw new Exception('Invalid action: ' . $action);
    }
    
    echo json_encode($result);
    
} catch (Exception $e) {
    error_log('Moderation error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

function approveEvent($pdo, $eventId) {
    $sql = "UPDATE evenement SET statut = 'Publié' WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$eventId]);
    
    if ($result) {
        return [
            'success' => true,
            'message' => 'Événement approuvé avec succès'
        ];
    } else {
        throw new Exception('Failed to approve event');
    }
}

function rejectEvent($pdo, $eventId) {
    $sql = "UPDATE evenement SET statut = 'Rejeté' WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$eventId]);
    
    if ($result) {
        return [
            'success' => true,
            'message' => 'Événement rejeté'
        ];
    } else {
        throw new Exception('Failed to reject event');
    }
}

function deleteEvent($pdo, $eventId) {
    $pdo->beginTransaction();
    
    try {
        // Delete related records first
        $stmt = $pdo->prepare("DELETE FROM participation WHERE idEvenement = ?");
        $stmt->execute([$eventId]);
        
        $stmt = $pdo->prepare("DELETE FROM evaluation WHERE idEvenement = ?");
        $stmt->execute([$eventId]);
        
        // Delete event
        $stmt = $pdo->prepare("DELETE FROM evenement WHERE id = ?");
        $result = $stmt->execute([$eventId]);
        
        $pdo->commit();
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Événement supprimé avec succès'
            ];
        } else {
            throw new Exception('Failed to delete event');
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function approveEvaluation($pdo, $evaluationId) {
    $sql = "UPDATE evaluation SET signalee = 0, etat_moderation = 'Visible' WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$evaluationId]);
    
    if ($result) {
        return [
            'success' => true,
            'message' => 'Évaluation approuvée'
        ];
    } else {
        throw new Exception('Failed to approve evaluation');
    }
}

function rejectEvaluation($pdo, $evaluationId) {
    $sql = "DELETE FROM evaluation WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$evaluationId]);
    
    if ($result) {
        return [
            'success' => true,
            'message' => 'Évaluation supprimée'
        ];
    } else {
        throw new Exception('Failed to reject evaluation');
    }
}

function getPendingEvents($pdo) {
    $sql = "SELECT e.*, u.nom, u.prenom 
            FROM evenement e 
            JOIN utilisateur u ON e.idUtilisateur = u.idUtilisateur 
            WHERE e.statut = 'Brouillon' 
            ORDER BY e.date DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return [
        'success' => true,
        'events' => $events
    ];
}

function getReportedEvaluations($pdo) {
    $sql = "SELECT e.*, ev.titre as event_titre, u.nom, u.prenom 
            FROM evaluation e 
            JOIN evenement ev ON e.idEvenement = ev.id 
            JOIN utilisateur u ON e.idUtilisateur = u.idUtilisateur 
            WHERE e.signalee = 1 
            ORDER BY e.dateEvaluation DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return [
        'success' => true,
        'evaluations' => $evaluations
    ];
}

function getEventEvaluations($pdo, $eventId) {
    $sql = "SELECT e.*, u.nom, u.prenom 
            FROM evaluation e 
            JOIN utilisateur u ON e.idUtilisateur = u.idUtilisateur 
            WHERE e.idEvenement = ? 
            ORDER BY e.dateEvaluation DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$eventId]);
    $evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return [
        'success' => true,
        'evaluations' => $evaluations
    ];
}
?>