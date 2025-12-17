<?php
require_once __DIR__ . '/../model/Offre.php';
require_once __DIR__ . '/../model/User.php';

class DashboardController {
    private $offreModel;
    private $userModel;

    public function __construct() {
        $this->offreModel = new Offre();
        $this->userModel = new User();
    }

    // Afficher le dashboard principal
    public function index() {
        // Récupérer les statistiques
        $stats = $this->getStats();
        
        // Charger la vue du dashboard
        require_once __DIR__ . '/../view/back/dashboard.php';
    }

    // Récupérer les statistiques pour le dashboard
    private function getStats() {
        $totalOffres = 0;
        $totalUsers = 0;
        $totalCandidatures = 0;
        $totalVisiteurs = 0;

        // Compter les offres (avec gestion d'erreur)
        try {
            $offres = $this->offreModel->getAll();
            $totalOffres = count($offres);
        } catch (PDOException $e) {
            // Table offres n'existe pas encore
            $totalOffres = 0;
        }

        // Compter les utilisateurs (avec gestion d'erreur)
        try {
            $users = $this->userModel->getAll();
            $totalUsers = count($users);
        } catch (PDOException $e) {
            // Table utilisateur n'existe pas encore
            $totalUsers = 0;
        }

        // Récupérer les candidatures
        try {
            require_once __DIR__ . '/../model/Candidature.php';
            $candidatureModel = new Candidature();
            $candidatures = $candidatureModel->getAll();
            $totalCandidatures = count($candidatures);
        } catch (Exception $e) {
            $totalCandidatures = 0;
        }

        // Statistiques fictives pour les visiteurs
        $totalVisiteurs = rand(1000, 5000);

        return [
            'totalOffres' => $totalOffres,
            'totalUsers' => $totalUsers,
            'totalCandidatures' => $totalCandidatures,
            'totalVisiteurs' => $totalVisiteurs
        ];
    }
}
