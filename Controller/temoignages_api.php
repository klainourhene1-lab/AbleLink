<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

require_once __DIR__ . '/../Model/Database.php';

$db = Database::getInstance()->getConnection();
$action = $_GET['action'] ?? '';

// Get event evaluations (exclude signaled ones)
if ($action === 'get_evaluations') {
    $eventId = $_GET['eventId'] ?? null;
    $userId = $_SESSION['user_id'];
    
    if (!$eventId) {
        echo json_encode(['success' => false, 'message' => 'Event ID manquant']);
        exit;
    }
    
    try {
        // Get evaluations that are not signaled
        $stmt = $db->prepare("
            SELECT e.*, u.prenom, u.nom, u.photo,
                   (SELECT COUNT(*) FROM signalement 
                    WHERE cibleType = 'evaluation' 
                    AND cibleId = e.id 
                    AND idUtilisateur = ?) as user_reported
            FROM evaluation e
            JOIN utilisateur u ON e.idUtilisateur = u.id
            WHERE e.idEvenement = ? AND e.signalee = 0
            ORDER BY e.dateEvaluation DESC
        ");
        $stmt->execute([$userId, $eventId]);
        $evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'evaluations' => $evaluations
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Report an evaluation
if ($action === 'report_evaluation') {
    $evaluationId = $_POST['evaluationId'] ?? null;
    $userId = $_SESSION['user_id'];
    
    if (!$evaluationId) {
        echo json_encode(['success' => false, 'message' => 'Evaluation ID manquant']);
        exit;
    }
    
    try {
        // Check if user already reported this evaluation
        $stmt = $db->prepare("
            SELECT id FROM signalement 
            WHERE idUtilisateur = ? 
            AND cibleType = 'evaluation' 
            AND cibleId = ?
        ");
        $stmt->execute([$userId, $evaluationId]);
        
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Vous avez déjà signalé cette évaluation']);
            exit;
        }
        
        // Create signalement record
        $stmt = $db->prepare("
            INSERT INTO signalement (idUtilisateur, type, cibleType, cibleId, date, statut, description)
            VALUES (?, 'Contenu inapproprié', 'evaluation', ?, NOW(), 'En attente', 'Signalé depuis la page témoignages')
        ");
        $stmt->execute([$userId, $evaluationId]);
        
        // Mark evaluation as signaled (hide it)
        $stmt = $db->prepare("UPDATE evaluation SET signalee = 1, etat_moderation = 'En attente' WHERE id = ?");
        $stmt->execute([$evaluationId]);
        
        echo json_encode(['success' => true, 'message' => 'Évaluation signalée avec succès']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Get event details
if ($action === 'get_event') {
    $eventId = $_GET['eventId'] ?? null;
    
    if (!$eventId) {
        echo json_encode(['success' => false, 'message' => 'Event ID manquant']);
        exit;
    }
    
    try {
        $stmt = $db->prepare("
            SELECT e.*, u.prenom as creator_prenom, u.nom as creator_nom,
                   (SELECT COUNT(*) FROM participation WHERE idEvenement = e.id) as total_participants,
                   (SELECT AVG(note_accessibilite) FROM evaluation WHERE idEvenement = e.id AND signalee = 0) as avg_accessibilite,
                   (SELECT AVG(note_inclusion) FROM evaluation WHERE idEvenement = e.id AND signalee = 0) as avg_inclusion,
                   (SELECT COUNT(*) FROM evaluation WHERE idEvenement = e.id AND signalee = 0) as total_evaluations
            FROM evenement e
            JOIN utilisateur u ON e.idUtilisateur = u.id
            WHERE e.id = ?
        ");
        $stmt->execute([$eventId]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$event) {
            echo json_encode(['success' => false, 'message' => 'Événement introuvable']);
            exit;
        }
        
        echo json_encode([
            'success' => true,
            'event' => $event
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Action invalide']);
