<?php
// save_event.php - Save events to database

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Database configuration
$host = '127.0.0.1';
$dbname = 'projet';
$username = 'root';
$password = '';

try {
    // Get the raw POST data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception('Invalid JSON data received');
    }
    
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $action = $data['action'] ?? 'save';
    
    switch ($action) {
        case 'save':
            $result = saveEvent($pdo, $data);
            break;
            
        case 'get':
            $eventId = $data['id'] ?? null;
            if (!$eventId) {
                throw new Exception('Event ID is required');
            }
            $result = getEvent($pdo, $eventId);
            break;
            
        case 'get_all':
            $result = getAllEvents($pdo, $data);
            break;
            
        case 'delete':
            $eventId = $data['id'] ?? null;
            if (!$eventId) {
                throw new Exception('Event ID is required');
            }
            $result = deleteEvent($pdo, $eventId);
            break;
            
        case 'update_status':
            $eventId = $data['id'] ?? null;
            $status = $data['statut'] ?? null;
            if (!$eventId || !$status) {
                throw new Exception('Event ID and Status are required');
            }
            $result = updateEventStatus($pdo, $eventId, $status);
            break;
            
        case 'get_user_events':
            $userId = $data['userId'] ?? null;
            if (!$userId) {
                throw new Exception('User ID is required');
            }
            $result = getUserEvents($pdo, $userId);
            break;
            
        default:
            throw new Exception('Invalid action: ' . $action);
    }
    
    echo json_encode($result);
    
} catch (Exception $e) {
    error_log('Event save error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

function saveEvent($pdo, $data) {
    // Validate required fields
    $required = ['titre', 'description', 'date', 'lieu'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Field '$field' is required");
        }
    }
    
    // Validate and get a valid user ID
    $validUserId = getValidUserId($pdo, $data['idUtilisateur'] ?? null);
    
    if (!$validUserId) {
        throw new Exception('Aucun utilisateur valide trouvé pour créer l\'événement');
    }
    
    // Determine status based on user role (in real app, you'd check user role)
    $statut = $data['statut'] ?? 'Brouillon';
    $participants_max = $data['participants_max'] ?? 50;
    $inscrits = $data['inscrits'] ?? 0;
    $theme = $data['theme'] ?? 'Inclusion';
    $accessibilite = $data['accessibilite'] ?? '';
    
    // Check if we're updating or inserting
    if (isset($data['id']) && !empty($data['id'])) {
        // Update existing event
        return updateEvent($pdo, $data['id'], [
            'titre' => $data['titre'],
            'description' => $data['description'],
            'date' => $data['date'],
            'lieu' => $data['lieu'],
            'theme' => $theme,
            'accessibilite' => $accessibilite,
            'statut' => $statut,
            'participants_max' => $participants_max,
            'inscrits' => $inscrits,
            'idUtilisateur' => $validUserId
        ]);
    } else {
        // Insert new event
        return createEvent($pdo, [
            'titre' => $data['titre'],
            'description' => $data['description'],
            'date' => $data['date'],
            'lieu' => $data['lieu'],
            'theme' => $theme,
            'accessibilite' => $accessibilite,
            'statut' => $statut,
            'participants_max' => $participants_max,
            'inscrits' => $inscrits,
            'idUtilisateur' => $validUserId
        ]);
    }
}

function createEvent($pdo, $eventData) {
    $sql = "INSERT INTO evenement 
            (titre, description, date, lieu, theme, accessibilite, statut, participants_max, inscrits, idUtilisateur) 
            VALUES 
            (:titre, :description, :date, :lieu, :theme, :accessibilite, :statut, :participants_max, :inscrits, :idUtilisateur)";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':titre' => $eventData['titre'],
        ':description' => $eventData['description'],
        ':date' => $eventData['date'],
        ':lieu' => $eventData['lieu'],
        ':theme' => $eventData['theme'],
        ':accessibilite' => $eventData['accessibilite'],
        ':statut' => $eventData['statut'],
        ':participants_max' => $eventData['participants_max'],
        ':inscrits' => $eventData['inscrits'],
        ':idUtilisateur' => $eventData['idUtilisateur']
    ]);
    
    if ($result) {
        $eventId = $pdo->lastInsertId();
        
        // Debug logging
        error_log("Event created with ID: " . $eventId);
        error_log("User ID: " . $eventData['idUtilisateur']);
        
        // Automatically create participation record for the event creator if it's published
        if ($eventData['statut'] === 'Publié') {
            $participationId = createParticipation($pdo, $eventData['idUtilisateur'], $eventId);
            error_log("Participation created with ID: " . $participationId);
        }
        
        return [
            'success' => true,
            'message' => 'Événement créé avec succès',
            'eventId' => $eventId,
            'participationId' => $participationId ?? null
        ];
    } else {
        throw new Exception('Failed to create event in database');
    }
}

function updateEvent($pdo, $eventId, $eventData) {
    $sql = "UPDATE evenement SET 
            titre = :titre, 
            description = :description, 
            date = :date, 
            lieu = :lieu, 
            theme = :theme, 
            accessibilite = :accessibilite, 
            statut = :statut,
            participants_max = :participants_max,
            inscrits = :inscrits,
            idUtilisateur = :idUtilisateur
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':titre' => $eventData['titre'],
        ':description' => $eventData['description'],
        ':date' => $eventData['date'],
        ':lieu' => $eventData['lieu'],
        ':theme' => $eventData['theme'],
        ':accessibilite' => $eventData['accessibilite'],
        ':statut' => $eventData['statut'],
        ':participants_max' => $eventData['participants_max'],
        ':inscrits' => $eventData['inscrits'],
        ':idUtilisateur' => $eventData['idUtilisateur'],
        ':id' => $eventId
    ]);
    
    if ($result) {
        return [
            'success' => true,
            'message' => 'Événement mis à jour avec succès',
            'eventId' => $eventId
        ];
    } else {
        throw new Exception('Failed to update event in database');
    }
}

function getEvent($pdo, $eventId) {
    $sql = "SELECT e.*, u.nom, u.prenom 
            FROM evenement e 
            JOIN utilisateur u ON e.idUtilisateur = u.idUtilisateur 
            WHERE e.id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$eventId]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($event) {
        // Get participants count
        $stmt = $pdo->prepare("SELECT COUNT(*) as participants_count FROM participation WHERE idEvenement = ?");
        $stmt->execute([$eventId]);
        $participants = $stmt->fetch(PDO::FETCH_ASSOC);
        $event['participants_count'] = $participants['participants_count'];
        
        // Get evaluations count
        $stmt = $pdo->prepare("SELECT COUNT(*) as evaluations_count FROM evaluation WHERE idEvenement = ?");
        $stmt->execute([$eventId]);
        $evaluations = $stmt->fetch(PDO::FETCH_ASSOC);
        $event['evaluations_count'] = $evaluations['evaluations_count'];
        
        return [
            'success' => true,
            'event' => $event
        ];
    } else {
        throw new Exception('Event not found');
    }
}

function getAllEvents($pdo, $filters = []) {
    $sql = "SELECT e.*, u.nom, u.prenom 
            FROM evenement e 
            JOIN utilisateur u ON e.idUtilisateur = u.idUtilisateur 
            WHERE 1=1";
    
    $params = [];
    
    // Apply filters
    if (!empty($filters['statut'])) {
        $sql .= " AND e.statut = ?";
        $params[] = $filters['statut'];
    }
    
    if (!empty($filters['userId'])) {
        $sql .= " AND e.idUtilisateur = ?";
        $params[] = $filters['userId'];
    }
    
    if (!empty($filters['search'])) {
        $sql .= " AND (e.titre LIKE ? OR e.description LIKE ?)";
        $searchTerm = '%' . $filters['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    // Date filters
    if (!empty($filters['date_from'])) {
        $sql .= " AND e.date >= ?";
        $params[] = $filters['date_from'];
    }
    
    if (!empty($filters['date_to'])) {
        $sql .= " AND e.date <= ?";
        $params[] = $filters['date_to'];
    }
    
    $sql .= " ORDER BY e.date DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get additional data for each event
    foreach ($events as &$event) {
        // Get participants count
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM participation WHERE idEvenement = ?");
        $stmt->execute([$event['id']]);
        $participants = $stmt->fetch(PDO::FETCH_ASSOC);
        $event['participants_count'] = $participants['count'];
        
        // Get average rating
        $stmt = $pdo->prepare("SELECT AVG(note) as avg_rating FROM evaluation WHERE idEvenement = ? AND etat_moderation = 'Visible'");
        $stmt->execute([$event['id']]);
        $rating = $stmt->fetch(PDO::FETCH_ASSOC);
        $event['avg_rating'] = $rating['avg_rating'] ? round($rating['avg_rating'], 1) : 0;
    }
    
    return [
        'success' => true,
        'events' => $events,
        'total' => count($events)
    ];
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

function updateEventStatus($pdo, $eventId, $status) {
    $validStatuses = ['Brouillon', 'Publié', 'Archivé', 'Rejeté'];
    if (!in_array($status, $validStatuses)) {
        throw new Exception('Invalid status: ' . $status);
    }
    
    $sql = "UPDATE evenement SET statut = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$status, $eventId]);
    
    if ($result) {
        return [
            'success' => true,
            'message' => 'Statut de l\'événement mis à jour avec succès'
        ];
    } else {
        throw new Exception('Failed to update event status');
    }
}

function getUserEvents($pdo, $userId) {
    $sql = "SELECT e.*, 
            (SELECT COUNT(*) FROM participation p WHERE p.idEvenement = e.id) as participants_count,
            (SELECT COUNT(*) FROM evaluation ev WHERE ev.idEvenement = e.id AND ev.etat_moderation = 'Visible') as evaluations_count
            FROM evenement e 
            WHERE e.idUtilisateur = ? 
            ORDER BY e.date DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return [
        'success' => true,
        'events' => $events,
        'total' => count($events)
    ];
}

/**
 * Get a valid user ID from the database
 */
function getValidUserId($pdo, $providedUserId = null) {
    // First, try to use the provided user ID if it exists
    if ($providedUserId) {
        // Check if this user exists in the database
        $stmt = $pdo->prepare("SELECT idUtilisateur FROM utilisateur WHERE idUtilisateur = ?");
        $stmt->execute([$providedUserId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            return $user['idUtilisateur'];
        }
    }
    
    // If provided user doesn't exist or no user provided, use a default valid user
    // Try to get the first admin user
    $stmt = $pdo->prepare("SELECT idUtilisateur FROM utilisateur WHERE isAdmin = 1 ORDER BY idUtilisateur LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        return $admin['idUtilisateur'];
    }
    
    // If no admin, get the first user
    $stmt = $pdo->prepare("SELECT idUtilisateur FROM utilisateur ORDER BY idUtilisateur LIMIT 1");
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        return $user['idUtilisateur'];
    }
    
    // If no users exist, we need to create one
    return createDefaultUser($pdo);
}

/**
 * Create a default user if no users exist
 */
function createDefaultUser($pdo) {
    $defaultEmail = 'admin@abelink.com';
    $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO utilisateur (nom, prenom, email, motDePasse, isAdmin) 
            VALUES ('Admin', 'System', ?, ?, 1)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$defaultEmail, $defaultPassword]);
    
    return $pdo->lastInsertId();
}

/**
 * Create automatic participation record for event creator
 */
function createParticipation($pdo, $userId, $eventId) {
    try {
        // First check if participation already exists
        $checkStmt = $pdo->prepare("SELECT id FROM participation WHERE idUtilisateur = ? AND idEvenement = ?");
        $checkStmt->execute([$userId, $eventId]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            error_log("Participation already exists for user $userId in event $eventId");
            return $existing['id'];
        }
        
        $sql = "INSERT INTO participation (idUtilisateur, idEvenement, dateInscription, statut, presence) 
                VALUES (?, ?, NOW(), 'confirmed', 0)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$userId, $eventId]);
        
        if ($result) {
            $participationId = $pdo->lastInsertId();
            error_log("Successfully created participation record: " . $participationId);
            return $participationId;
        } else {
            error_log("Failed to execute participation insert");
            return false;
        }
    } catch (Exception $e) {
        error_log('Error creating participation: ' . $e->getMessage());
        return false;
    }
}
?>