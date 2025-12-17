<?php
// get_events_for_evaluation.php - Get events with participation and evaluation data
ob_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    session_start();
    
    // Get user ID from session
    $userId = $_SESSION['user_id'] ?? null;
    $userRole = $_SESSION['user_role'] ?? 'Utilisateur';
    
    if (!$userId) {
        throw new Exception('User not logged in');
    }
    
    // Database connection
    $host = "127.0.0.1";
    $dbname = "projet";
    $username = "root";
    $password = "";
    
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get events with organizer info
    // Admin sees all; non-admin see published
    $query = "
        SELECT e.*, 
               u.nom as nom_entreprise, 
               u.prenom as prenom_entreprise,
               CONCAT(u.prenom, ' ', u.nom) as organisateur,
               u.role as organizer_role
        FROM evenement e
        INNER JOIN utilisateur u ON e.idUtilisateur = u.id
        " . ($userRole === 'Admin' ? "" : "WHERE e.statut = 'Publié'") . "
        ORDER BY e.date DESC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // For each event, get participation and evaluation data
    foreach ($events as &$event) {
        // Get participations for this event
        $partQuery = "
            SELECT p.*, u.nom, u.prenom 
            FROM participation p
            INNER JOIN utilisateur u ON p.idUtilisateur = u.id
            WHERE p.idEvenement = ?
        ";
        $partStmt = $conn->prepare($partQuery);
        $partStmt->execute([$event['id']]);
        $event['participations'] = $partStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get evaluations for this event
        $evalQuery = "
            SELECT ev.*, u.nom, u.prenom 
            FROM evaluation ev
            INNER JOIN utilisateur u ON ev.idUtilisateur = u.id
            WHERE ev.idEvenement = ?
        ";
        $evalStmt = $conn->prepare($evalQuery);
        $evalStmt->execute([$event['id']]);
        $event['evaluations'] = $evalStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Add participant count
        $event['inscrits'] = count($event['participations']);
    }
    
    // Clean output buffer
    if (ob_get_length()) {
        ob_clean();
    }
    
    echo json_encode([
        'success' => true,
        'events' => $events,
        'total' => count($events),
        'userId' => $userId,
        'userRole' => $userRole
    ]);
    
} catch (Exception $e) {
    if (ob_get_length()) {
        ob_clean();
    }
    
    error_log('Error in get_events_for_evaluation: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage(),
        'events' => []
    ]);
}

if (ob_get_length()) {
    ob_end_flush();
}
// Optionally include user's evaluations in a single response (already emitted above)
?>
