<?php
// manage_evaluation.php - Manage event evaluations

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
        throw new Exception('Invalid JSON data received');
    }
    
    $action = $data['action'] ?? '';
    
    switch ($action) {
        case 'submit':
            $userId = $data['idUtilisateur'] ?? null;
            $eventId = $data['idEvenement'] ?? null;
            $note_accessibilite = $data['note_accessibilite'] ?? null;
            $note_inclusion = $data['note_inclusion'] ?? null;
            $commentaire = $data['commentaire'] ?? null;
            
            if (!$userId || !$eventId) {
                throw new Exception('User ID and Event ID are required');
            }
            
            // Validate notes
            if ($note_accessibilite === null || $note_accessibilite < 1 || $note_accessibilite > 5) {
                throw new Exception('Accessibility note must be between 1 and 5');
            }
            if ($note_inclusion === null || $note_inclusion < 1 || $note_inclusion > 5) {
                throw new Exception('Inclusion note must be between 1 and 5');
            }
            
            $result = submitEvaluation($pdo, $userId, $eventId, $note_accessibilite, $note_inclusion, $commentaire);
            break;
            
        case 'get':
            $userId = $data['idUtilisateur'] ?? null;
            $eventId = $data['idEvenement'] ?? null;
            
            if (!$userId || !$eventId) {
                throw new Exception('User ID and Event ID are required');
            }
            
            $result = getEvaluation($pdo, $userId, $eventId);
            break;
            
        case 'get_all':
            $eventId = $data['eventId'] ?? null;
            
            if (!$eventId) {
                throw new Exception('Event ID is required');
            }
            
            $result = getAllEvaluations($pdo, $eventId);
            break;
            
        case 'get_user_evaluations':
            $userId = $data['userId'] ?? null;
            
            if (!$userId) {
                throw new Exception('User ID is required');
            }
            
            $result = getUserEvaluations($pdo, $userId);
            break;
            
        case 'report':
            $evaluationId = $data['evaluationId'] ?? null;
            
            if (!$evaluationId) {
                throw new Exception('Evaluation ID is required');
            }
            
            $result = reportEvaluation($pdo, $evaluationId);
            break;
            
        case 'update':
            $evaluationId = $data['evaluationId'] ?? null;
            $note_accessibilite = $data['note_accessibilite'] ?? null;
            $note_inclusion = $data['note_inclusion'] ?? null;
            $commentaire = $data['commentaire'] ?? null;
            
            if (!$evaluationId) {
                throw new Exception('Evaluation ID is required');
            }
            
            $result = updateEvaluation($pdo, $evaluationId, $note_accessibilite, $note_inclusion, $commentaire);
            break;
            
        case 'delete':
            $evaluationId = $data['evaluationId'] ?? null;
            
            if (!$evaluationId) {
                throw new Exception('Evaluation ID is required');
            }
            
            $result = deleteEvaluation($pdo, $evaluationId);
            break;
            
        case 'get_event_stats':
            $eventId = $data['eventId'] ?? null;
            
            if (!$eventId) {
                throw new Exception('Event ID is required');
            }
            
            $result = getEventStats($pdo, $eventId);
            break;
            
        default:
            throw new Exception('Invalid action: ' . $action);
    }
    
    echo json_encode($result);
    
} catch (Exception $e) {
    error_log('Evaluation error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

function submitEvaluation($pdo, $userId, $eventId, $note_accessibilite, $note_inclusion, $commentaire) {
    // Calculate average note for the main note field
    $note = ($note_accessibilite + $note_inclusion) / 2;
    
    // Check if evaluation already exists
    $stmt = $pdo->prepare("SELECT id FROM evaluation WHERE idUtilisateur = ? AND idEvenement = ?");
    $stmt->execute([$userId, $eventId]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        // Update existing evaluation
        $sql = "UPDATE evaluation SET 
                note = ?, 
                note_accessibilite = ?, 
                note_inclusion = ?, 
                commentaire = ?, 
                dateEvaluation = NOW(),
                signalee = 0,
                etat_moderation = 'Visible'
                WHERE idUtilisateur = ? AND idEvenement = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $note, 
            $note_accessibilite, 
            $note_inclusion, 
            $commentaire, 
            $userId, 
            $eventId
        ]);
        $evaluationId = $existing['id'];
        $action = 'updated';
    } else {
        // Create new evaluation
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
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $userId, 
            $eventId, 
            $note, 
            $note_accessibilite, 
            $note_inclusion, 
            $commentaire
        ]);
        $evaluationId = $pdo->lastInsertId();
        $action = 'created';
    }
    
    if ($result) {
        error_log("Evaluation $action successfully for user $userId, event $eventId");
        return [
            'success' => true,
            'message' => 'Evaluation submitted successfully',
            'evaluationId' => $evaluationId,
            'action' => $action
        ];
    } else {
        throw new Exception('Failed to submit evaluation');
    }
}

function getEvaluation($pdo, $userId, $eventId) {
    $sql = "SELECT e.*, u.nom, u.prenom 
            FROM evaluation e 
            JOIN utilisateur u ON e.idUtilisateur = u.idUtilisateur 
            WHERE e.idUtilisateur = ? AND e.idEvenement = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $eventId]);
    $evaluation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return [
        'success' => true,
        'evaluation' => $evaluation
    ];
}

function getAllEvaluations($pdo, $eventId) {
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

function getUserEvaluations($pdo, $userId) {
    $sql = "SELECT e.*, ev.titre as event_titre, ev.date as event_date
            FROM evaluation e 
            JOIN evenement ev ON e.idEvenement = ev.id 
            WHERE e.idUtilisateur = ? 
            ORDER BY e.dateEvaluation DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return [
        'success' => true,
        'evaluations' => $evaluations
    ];
}

function reportEvaluation($pdo, $evaluationId) {
    $sql = "UPDATE evaluation SET signalee = 1, etat_moderation = 'En attente' WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$evaluationId]);
    
    if ($result) {
        return [
            'success' => true,
            'message' => 'Evaluation reported successfully'
        ];
    } else {
        throw new Exception('Failed to report evaluation');
    }
}

function updateEvaluation($pdo, $evaluationId, $note_accessibilite, $note_inclusion, $commentaire) {
    // Calculate average note
    $note = ($note_accessibilite + $note_inclusion) / 2;
    
    $sql = "UPDATE evaluation SET 
            note = ?, 
            note_accessibilite = ?, 
            note_inclusion = ?, 
            commentaire = ?, 
            dateEvaluation = NOW(),
            signalee = 0,
            etat_moderation = 'Visible'
            WHERE id = ?";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $note, 
        $note_accessibilite, 
        $note_inclusion, 
        $commentaire, 
        $evaluationId
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

function deleteEvaluation($pdo, $evaluationId) {
    $sql = "DELETE FROM evaluation WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$evaluationId]);
    
    if ($result) {
        return [
            'success' => true,
            'message' => 'Evaluation deleted successfully'
        ];
    } else {
        throw new Exception('Failed to delete evaluation');
    }
}

function getEventStats($pdo, $eventId) {
    // Get average ratings
    $sql = "SELECT 
            AVG(note_accessibilite) as avg_accessibilite,
            AVG(note_inclusion) as avg_inclusion,
            AVG(note) as avg_overall,
            COUNT(*) as total_evaluations,
            SUM(signalee) as reported_count
            FROM evaluation 
            WHERE idEvenement = ? AND etat_moderation = 'Visible'";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$eventId]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get rating distribution
    $sql = "SELECT 
            note_accessibilite,
            note_inclusion,
            COUNT(*) as count
            FROM evaluation 
            WHERE idEvenement = ? AND etat_moderation = 'Visible'
            GROUP BY note_accessibilite, note_inclusion";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$eventId]);
    $distribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return [
        'success' => true,
        'stats' => $stats,
        'distribution' => $distribution
    ];
}
?>