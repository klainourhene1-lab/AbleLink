<?php
// admin_jobs.php - Controller for Job Board Moderation
ob_start();

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../view/general/signin.php');
    exit;
}

// Check if user is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header('Location: ../view/FrontOffice/evaluations-evenements.php');
    exit;
}

require_once __DIR__ . '/OffreController.php';
require_once __DIR__ . '/CandidatureController.php';

$offreCtrl = new OffreController();
$candidatureCtrl = new CandidatureController();

// Handle AJAX POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Fallback if not JSON
    if (!$input) $input = $_POST;
    
    $action = $input['action'] ?? '';
    
    ob_clean();
    header('Content-Type: application/json');

    try {
        switch ($action) {
            case 'approve_offer':
                if (empty($input['id'])) throw new Exception("ID manquant");
                $res = $offreCtrl->approve($input['id']);
                echo json_encode(['success' => (bool)$res]);
                break;
                
            case 'reject_offer':
                if (empty($input['id'])) throw new Exception("ID manquant");
                $res = $offreCtrl->reject($input['id']);
                echo json_encode(['success' => (bool)$res]);
                break;
                
            case 'delete_offer':
                if (empty($input['id'])) throw new Exception("ID manquant");
                $res = $offreCtrl->delete($input['id']);
                echo json_encode(['success' => (bool)$res]);
                break;

            default:
                throw new Exception("Action inconnue");
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Get data for view
$pendingOffers = $offreCtrl->getPendingOffers();
$allOffers = $offreCtrl->getAllAdmin();
$allCandidatures = $candidatureCtrl->getAll();

// Analytics Data
$jobStats = $offreCtrl->getStats(); // Custom method on controller needed or access model directly
// Since controller doesn't expose model methods directly, I need to add proxy methods or instantiate models here.
// Better approach: Instantiate models directly for analytics, or add getter in controller. 
// Let's use the controllers if I add the methods there, or just use the existing controller instances if I update them. 
// I didn't update OffreController.php to expose getStats. I updated OffreModel.php.
// So I should update OffreController.php first or access model.
// Accessing model via controller property is private.
// I will add wrapper methods in OffreController and CandidatureController to expose stats.

// Wait, I missed updating the Controllers to expose the Model methods. 
// I will act as if I updated them or update them now? 
// The plan said "Update admin_jobs.php to fetch", implying I can access them. 
// I'll quickly update the controllers to expose these methods first.

// Let's act on OffreController and CandidatureController first.
// I will cancel this edit and update controllers first.

// Include the view
$jobStats = $offreCtrl->getStats();
$offerTrends = $offreCtrl->getOffersByMonth();
$candidatureStats = $candidatureCtrl->getStats();
$topOffers = $candidatureCtrl->getTopOffers();
$candidatureTrends = $candidatureCtrl->getApplicationsByMonth();

// Consolidated Analytics Data
$analyticsData = [
    'jobStats' => $jobStats,
    'offerTrends' => $offerTrends,
    'candidatureStats' => $candidatureStats,
    'topOffers' => $topOffers,
    'candidatureTrends' => $candidatureTrends
];

require_once __DIR__ . '/../view/BackOffice/admin_jobs_view.php';
?>
