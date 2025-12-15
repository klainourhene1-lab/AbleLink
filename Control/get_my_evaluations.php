<?php
// get_my_evaluations.php - Simple endpoint to get user evaluations

// Start session
session_start();

// Set headers for JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

// Database connection
try {
    $host = "127.0.0.1";
    $dbname = "projet";
    $username = "root";
    $password = "";
    
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query to get user evaluations with event details
    $query = "
        SELECT 
            e.id as evaluation_id,
            e.note_accessibilite,
            e.note_inclusion,
            e.commentaire,
            e.dateEvaluation,
            e.signalee,
            ev.id as event_id,
            ev.titre as event_titre,
            ev.date as event_date,
            ev.lieu as event_lieu,
            ev.description as event_description,
            ev.accessibilite as event_accessibilite,
            u_org.id as organizer_id,
            u_org.prenom as organizer_prenom,
            u_org.nom as organizer_nom
        FROM evaluation e
        INNER JOIN evenement ev ON e.idEvenement = ev.id
        INNER JOIN utilisateur u_org ON ev.idUtilisateur = u_org.id
        WHERE e.idUtilisateur = :userId
        ORDER BY e.dateEvaluation DESC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    $evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the response
    $formattedEvaluations = array_map(function($eval) {
        return [
            'id' => $eval['evaluation_id'],
            'idEvenement' => $eval['event_id'],
            'event_titre' => $eval['event_titre'],
            'event_date' => $eval['event_date'],
            'event_lieu' => $eval['event_lieu'],
            'event_description' => $eval['event_description'],
            'event_accessibilite' => $eval['event_accessibilite'],
            'organizer_prenom' => $eval['organizer_prenom'],
            'organizer_nom' => $eval['organizer_nom'],
            'note_accessibilite' => (int)$eval['note_accessibilite'],
            'note_inclusion' => (int)$eval['note_inclusion'],
            'commentaire' => $eval['commentaire'],
            'dateEvaluation' => $eval['dateEvaluation'],
            'signalee' => (bool)$eval['signalee']
        ];
    }, $evaluations);
    
    echo json_encode([
        'success' => true,
        'evaluations' => $formattedEvaluations,
        'count' => count($formattedEvaluations),
        'userId' => $userId
    ]);
    
} catch (Exception $e) {
    error_log('Error in get_my_evaluations.php: ' . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'evaluations' => []
    ]);
}
?>