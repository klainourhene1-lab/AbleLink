<?php
// manage_participation.php - Manage event participation

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
    
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception('Invalid JSON data received');
    }
    
    $action = $data['action'] ?? '';
    $userId = $data['idUtilisateur'] ?? null;
    $eventId = $data['idEvenement'] ?? null;
    
    if (!$userId || !$eventId) {
        throw new Exception('User ID and Event ID are required');
    }
    
    switch ($action) {
        case 'register':
            $result = registerParticipation($pdo, $userId, $eventId);
            break;
            
        case 'cancel':
            $result = cancelParticipation($pdo, $userId, $eventId);
            break;
            
        case 'get':
            $result = getParticipation($pdo, $userId, $eventId);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

function registerParticipation($pdo, $userId, $eventId) {
    // Check if participation already exists
    $stmt = $pdo->prepare("SELECT id FROM participation WHERE idUtilisateur = ? AND idEvenement = ?");
    $stmt->execute([$userId, $eventId]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        return [
            'success' => false,
            'message' => 'Already registered for this event'
        ];
    }
    
    $sql = "INSERT INTO participation (idUtilisateur, idEvenement, dateInscription, statut) 
            VALUES (?, ?, NOW(), 'registered')";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$userId, $eventId]);
    
    if ($result) {
        return [
            'success' => true,
            'message' => 'Successfully registered for the event',
            'participationId' => $pdo->lastInsertId()
        ];
    } else {
        throw new Exception('Failed to register participation');
    }
}

function cancelParticipation($pdo, $userId, $eventId) {
    $sql = "DELETE FROM participation WHERE idUtilisateur = ? AND idEvenement = ?";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$userId, $eventId]);
    
    if ($result) {
        return [
            'success' => true,
            'message' => 'Participation cancelled successfully'
        ];
    } else {
        throw new Exception('Failed to cancel participation');
    }
}

function getParticipation($pdo, $userId, $eventId) {
    $sql = "SELECT * FROM participation WHERE idUtilisateur = ? AND idEvenement = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $eventId]);
    $participation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return [
        'success' => true,
        'participation' => $participation
    ];
}
?>