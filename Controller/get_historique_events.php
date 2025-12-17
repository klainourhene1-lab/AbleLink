<?php
// get_historique_events.php - Get events for historique page based on user role

// Start output buffering to catch any accidental output
ob_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Get parameters
    $role = $_GET['role'] ?? 'user';
    $userId = $_GET['userId'] ?? '';
    
    error_log("DEBUG get_historique_events: Request received");
    error_log("DEBUG get_historique_events: role = $role");
    error_log("DEBUG get_historique_events: userId (raw) = $userId");
    
    // Simple database connection
    $host = "127.0.0.1";
    $dbname = "projet";
    $username = "root";
    $password = "";
    
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Extract numeric ID from userId
    $numericUserId = 0;
    
    if (is_numeric($userId)) {
        $numericUserId = (int)$userId;
    } elseif (is_string($userId) && !empty($userId)) {
        // Handle different formats: "user_6", "id_6", "6", etc.
        if (strpos($userId, '_') !== false) {
            $parts = explode('_', $userId);
            foreach ($parts as $part) {
                if (is_numeric($part)) {
                    $numericUserId = (int)$part;
                    break;
                }
            }
        } else {
            // Try to extract any numbers from the string
            preg_match_all('/\d+/', $userId, $matches);
            if (!empty($matches[0])) {
                $numericUserId = (int)$matches[0][0];
            }
        }
    }
    
    // If still 0, try to get from session or use default
    if ($numericUserId <= 0) {
        error_log("DEBUG get_historique_events: Could not extract numeric ID from '$userId', using fallback");
        $numericUserId = 1; // Fallback to admin ID
    }
    
    error_log("DEBUG get_historique_events: numericUserId = $numericUserId");
    
    // Test: Check if user exists
    $testUserQuery = "SELECT id, nom, prenom, role FROM utilisateur WHERE id = :userId";
    $testUserStmt = $conn->prepare($testUserQuery);
    $testUserStmt->bindParam(':userId', $numericUserId, PDO::PARAM_INT);
    $testUserStmt->execute();
    $userInfo = $testUserStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($userInfo) {
        error_log("DEBUG get_historique_events: User found - ID: {$userInfo['id']}, Name: {$userInfo['prenom']} {$userInfo['nom']}, Role: {$userInfo['role']}");
    } else {
        error_log("DEBUG get_historique_events: User ID $numericUserId NOT FOUND in database!");
    }
    
    // Test: Check participations for this user
    $testPartQuery = "SELECT COUNT(*) as count FROM participation WHERE idUtilisateur = :userId";
    $testPartStmt = $conn->prepare($testPartQuery);
    $testPartStmt->bindParam(':userId', $numericUserId, PDO::PARAM_INT);
    $testPartStmt->execute();
    $partCount = $testPartStmt->fetch(PDO::FETCH_ASSOC);
    error_log("DEBUG get_historique_events: User has {$partCount['count']} participations");
    
    // Build query based on user role
    switch ($role) {
        case 'user':
            // Users see ONLY events they participated in (and not rejected)
            error_log("DEBUG get_historique_events: Building query for USER role");
            $query = "
                SELECT DISTINCT e.*, 
                       u.nom as nom_entreprise, 
                       u.prenom as prenom_entreprise,
                       CONCAT(u.prenom, ' ', u.nom) as organisateur,
                       (
                           SELECT COUNT(*) 
                           FROM participation p2 
                           WHERE p2.idEvenement = e.id
                       ) as inscrits_count
                FROM evenement e
                INNER JOIN participation p ON e.id = p.idEvenement
                INNER JOIN utilisateur u ON e.idUtilisateur = u.id
                WHERE p.idUtilisateur = :userId
                AND e.statut != 'Rejeté'
                ORDER BY e.date DESC
            ";
            break;
            
        case 'company':
            // Companies see ONLY events they created (including rejected ones they created)
            error_log("DEBUG get_historique_events: Building query for COMPANY role");
            $query = "
                SELECT e.*, 
                       u.nom as nom_entreprise, 
                       u.prenom as prenom_entreprise,
                       CONCAT(u.prenom, ' ', u.nom) as organisateur,
                       (
                           SELECT COUNT(*) 
                           FROM participation p2 
                           WHERE p2.idEvenement = e.id
                       ) as inscrits_count
                FROM evenement e
                INNER JOIN utilisateur u ON e.idUtilisateur = u.id
                WHERE e.idUtilisateur = :userId
                ORDER BY e.date DESC
            ";
            break;
            
        case 'inclusion':
        case 'admin':
            // Inclusion managers and admins see ALL events (including rejected)
            error_log("DEBUG get_historique_events: Building query for ADMIN/INCLUSION role");
            $query = "
                SELECT e.*, 
                       u.nom as nom_entreprise, 
                       u.prenom as prenom_entreprise,
                       CONCAT(u.prenom, ' ', u.nom) as organisateur,
                       (
                           SELECT COUNT(*) 
                           FROM participation p2 
                           WHERE p2.idEvenement = e.id
                       ) as inscrits_count
                FROM evenement e
                INNER JOIN utilisateur u ON e.idUtilisateur = u.id
                ORDER BY e.date DESC
            ";
            break;
            
        default:
            throw new Exception('Invalid user role: ' . $role);
    }
    
    error_log("DEBUG get_historique_events: SQL Query = " . str_replace(["\n", "  "], " ", $query));
    
    $stmt = $conn->prepare($query);
    
    // Bind parameters for queries that need userId
    if ($role === 'user' || $role === 'company') {
        $stmt->bindParam(':userId', $numericUserId, PDO::PARAM_INT);
        error_log("DEBUG get_historique_events: Binding userId = $numericUserId");
    }
    
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Update inscrits field with actual count
    foreach ($events as &$event) {
        $event['inscrits'] = $event['inscrits_count'] ?? 0;
    }
    
    // Clean any output buffer
    if (ob_get_length()) {
        ob_clean();
    }
    
    // Get user participations for debugging
    $debugPartQuery = "
        SELECT p.idEvenement, e.titre, e.statut, p.dateInscription 
        FROM participation p 
        LEFT JOIN evenement e ON p.idEvenement = e.id 
        WHERE p.idUtilisateur = :userId
        ORDER BY p.dateInscription DESC
    ";
    $debugStmt = $conn->prepare($debugPartQuery);
    $debugStmt->bindParam(':userId', $numericUserId, PDO::PARAM_INT);
    $debugStmt->execute();
    $userParticipations = $debugStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the response with debugging info
    $response = [
        'success' => true,
        'events' => $events,
        'total' => count($events),
        'role' => $role,
        'debug' => [
            'user_id_received' => $userId,
            'user_id_numeric' => $numericUserId,
            'user_info' => $userInfo,
            'user_participations_count' => count($userParticipations),
            'user_participations_sample' => array_slice($userParticipations, 0, 3),
            'query_type' => $role,
            'has_user_filter' => in_array($role, ['user', 'company']),
            'events_count' => count($events),
            'event_ids' => array_column($events, 'id')
        ]
    ];
    
    error_log("DEBUG get_historique_events: Response prepared - " . count($events) . " events found");
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    // Clean any output buffer
    if (ob_get_length()) {
        ob_clean();
    }
    
    error_log('ERROR in get_historique_events: ' . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
    
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors du chargement des événements: ' . $e->getMessage(),
        'events' => [],
        'debug' => [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]
    ], JSON_PRETTY_PRINT);
}

// End output buffering and flush
if (ob_get_length()) {
    ob_end_flush();
}
?>