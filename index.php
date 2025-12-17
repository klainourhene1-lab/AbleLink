<?php
// Point d'entrée de l'application
session_start();

// Récupération du contrôleur et de l'action
$controller = $_GET['controller'] ?? 'offre';
$action = $_GET['action'] ?? 'liste';

// Routage simple
switch ($controller) {
    case 'offre':
        require_once __DIR__ . '/controller/OffreController.php';
        $offreController = new OffreController();
        
        if ($action === 'liste') {
            $offreController->liste();
        } elseif ($action === 'evenements') {
            $offreController->evenements();
        } elseif ($action === 'details') {
            $offreController->details();
        } else {
            $offreController->gestion();
        }
        break;

    
    case 'candidature':
        require_once __DIR__ . '/controller/CandidatureController.php';
        $candidatureController = new CandidatureController();
        
        if ($action === 'postuler') {
            $candidatureController->postuler();
        } else {
            $candidatureController->gestion();
        }
        break;
    
    case 'dashboard':
        require_once __DIR__ . '/controller/DashboardController.php';
        $dashboardController = new DashboardController();
        $dashboardController->index();
        break;
    
    case 'ai':
        require_once __DIR__ . '/controller/AIAssistantController.php';
        $aiController = new AIAssistantController();
        
        if ($action === 'analyze') {
            $aiController->analyze();
        } elseif ($action === 'optimize') {
            $aiController->optimize();
        }
        break;
    
    case 'favoris':
        require_once __DIR__ . '/controller/FavorisController.php';
        $favorisController = new FavorisController();
        
        if ($action === 'ajouter') {
            $favorisController->ajouter();
        } elseif ($action === 'supprimer') {
            $favorisController->supprimer();
        } elseif ($action === 'liste') {
            $favorisController->liste();
        } elseif ($action === 'verifier') {
            $favorisController->verifier();
        }
        break;
    
    default:
        header('Location: ?controller=offre&action=liste');
        exit;
}