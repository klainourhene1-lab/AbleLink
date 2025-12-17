<?php
require_once __DIR__ . '/../Model/OffreModel.php';

class OffreController {
    private $offreModel;

    public function __construct() {
        $this->offreModel = new OffreModel();
    }

    // Retourne la liste des offres (pour le Front Office)
    public function index() {
        return $this->offreModel->getAll();
    }

    // Retourne les détails d'une offre
    public function getDetails($id) {
        if (!$id) return null;
        return $this->offreModel->getById($id);
    }

    // Gestion de l'ajout
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Determine user role and corresponding status
            // Assuming session is already started in config or view
            $user_role = $_SESSION['user_role'] ?? 'guest';
            $user_id = $_SESSION['user_id'] ?? null;
            
            $status = ($user_role === 'Admin') ? 'published' : 'pending';

            $data = [
                'titre' => $_POST['titre'],
                'description' => $_POST['description'],
                'entreprise' => $_POST['entreprise'], // Ideally from user profile if company
                'localisation' => $_POST['localisation'],
                'type_contrat' => $_POST['type_contrat'],
                'salaire' => !empty($_POST['salaire']) ? $_POST['salaire'] : null,
                'user_id' => $user_id,
                'statut' => $status
            ];
            
            if ($this->offreModel->create($data)) {
                return ['success' => true, 'status' => $status];
            }
        }
        return ['success' => false, 'error' => 'Erreur lors de la création'];
    }

    // Gestion de la modification
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'titre' => $_POST['titre'],
                'description' => $_POST['description'],
                'entreprise' => $_POST['entreprise'],
                'localisation' => $_POST['localisation'],
                'type_contrat' => $_POST['type_contrat'],
                'salaire' => !empty($_POST['salaire']) ? $_POST['salaire'] : null
            ];
            
            return $this->offreModel->update($id, $data);
        }
        return false;
    }

    // Suppression
    public function delete($id) {
        if ($id) {
            return $this->offreModel->delete($id);
        }
        return false;
    }

    // Admin: Get all pending offers
    public function getPendingOffers() {
        return $this->offreModel->getPending();
    }

    // Admin: Approve offer
    public function approve($id) {
        return $this->offreModel->updateStatus($id, 'published');
    }

    // Admin: Reject offer
    public function reject($id) {
        return $this->offreModel->updateStatus($id, 'rejected');
    }
    
    // Admin: Get all offers including pending/rejected
    public function getAllAdmin() {
        return $this->offreModel->getAllAdmin();
    }
    
    // Analytics: Get Stats
    public function getStats() {
        return $this->offreModel->getStats();
    }

    // Analytics: Get Monthly Trends
    public function getOffersByMonth() {
        return $this->offreModel->getOffersByMonth();
    }
    
    // Filtres de recherche
    public function filter($offres, $filters) {
        if (empty($filters)) return $offres;

        return array_filter($offres, function($offre) use ($filters) {
            // Search filter
            if (!empty($filters['start_search'])) { // using 'start_search' or just 'q'
                $search = strtolower($filters['start_search']);
                $titre = strtolower($offre['titre']);
                $entreprise = strtolower($offre['entreprise']);
                $description = strtolower($offre['description']);
                
                if (strpos($titre, $search) === false && 
                    strpos($entreprise, $search) === false && 
                    strpos($description, $search) === false) {
                    return false;
                }
            }
            // Contract match
            if (!empty($filters['type_contrat']) && $offre['type_contrat'] !== $filters['type_contrat']) {
                return false;
            }
             // Location match
            if (!empty($filters['localisation'])) {
                $searchLoc = strtolower($filters['localisation']);
                if (strpos(strtolower($offre['localisation']), $searchLoc) === false) return false;
            }
             // Date filter
            if (!empty($filters['date_filter'])) {
                $date_pub = strtotime($offre['date_publication'] ?? 'now');
                switch ($filters['date_filter']) {
                    case 'today': if ($date_pub < strtotime('today')) return false; break;
                    case 'week': if ($date_pub < strtotime('-7 days')) return false; break;
                    case 'month': if ($date_pub < strtotime('-30 days')) return false; break;
                }
            }
            return true;
        });
    }

    // Récupérer les offres de l'entreprise connectée
    public function getMyOffers() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) return [];
        return $this->offreModel->getByUser($user_id);
    }
}
