<?php
require_once __DIR__ . '/../model/Offre.php';

class OffreController {
    private $offreModel;

    public function __construct() {
        $this->offreModel = new Offre();
    }

    // Afficher la liste des offres (Front Office)
    public function liste() {
        $offres = $this->offreModel->getAll();
        require_once __DIR__ . '/../view/front/liste_offres.php';
    }

    // Afficher la page événements (Template moderne)
    public function evenements() {
        require_once __DIR__ . '/../view/front/evenements_exemple.php';
    }


    // Gérer les offres (Back Office)
    public function gestion() {
        $action = $_GET['action'] ?? 'list';
        
        switch ($action) {
            case 'add':
                $this->add();
                break;
            case 'edit':
                $this->edit();
                break;
            case 'delete':
                $this->delete();
                break;
            case 'details':
                $this->details();
                break;
            default:
                $this->list();
        }
    }


    // Afficher la liste (Back Office)
    private function list() {
        $offres = $this->offreModel->getAll();
        
        // Apply filters if they exist
        if (!empty($_GET['search']) || !empty($_GET['type_contrat']) || !empty($_GET['localisation']) || !empty($_GET['date_filter'])) {
            $offres = array_filter($offres, function($offre) {
                // Search filter (titre, entreprise, description)
                if (!empty($_GET['search'])) {
                    $search = strtolower($_GET['search']);
                    $titre = strtolower($offre['titre']);
                    $entreprise = strtolower($offre['entreprise']);
                    $description = strtolower($offre['description']);
                    
                    if (strpos($titre, $search) === false && 
                        strpos($entreprise, $search) === false && 
                        strpos($description, $search) === false) {
                        return false;
                    }
                }
                
                // Contract type filter
                if (!empty($_GET['type_contrat']) && $offre['type_contrat'] !== $_GET['type_contrat']) {
                    return false;
                }
                
                // Location filter
                if (!empty($_GET['localisation'])) {
                    $loc_search = strtolower($_GET['localisation']);
                    $loc_offre = strtolower($offre['localisation']);
                    if (strpos($loc_offre, $loc_search) === false) {
                        return false;
                    }
                }
                
                // Date filter
                if (!empty($_GET['date_filter'])) {
                    $date_pub = strtotime($offre['date_publication'] ?? 'now');
                    $now = time();
                    
                    switch ($_GET['date_filter']) {
                        case 'today':
                            if ($date_pub < strtotime('today')) {
                                return false;
                            }
                            break;
                        case 'week':
                            if ($date_pub < strtotime('-7 days')) {
                                return false;
                            }
                            break;
                        case 'month':
                            if ($date_pub < strtotime('-30 days')) {
                                return false;
                            }
                            break;
                    }
                }
                
                return true;
            });
        }
        
        require_once __DIR__ . '/../view/back/gestion_offres.php';
    }

    // Ajouter une offre
    private function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'titre' => $_POST['titre'],
                'description' => $_POST['description'],
                'entreprise' => $_POST['entreprise'],
                'localisation' => $_POST['localisation'],
                'type_contrat' => $_POST['type_contrat'],
                'salaire' => !empty($_POST['salaire']) ? $_POST['salaire'] : null
            ];
            
            if ($this->offreModel->create($data)) {
                header('Location: /projetttwebbbbbbbbb/index.php?controller=offre&action=gestion&success=1');
                exit;
            } else {
                $error = "Erreur lors de l'ajout de l'offre";
            }
        }
        $offres = []; // Initialiser pour éviter l'erreur undefined variable
        require_once __DIR__ . '/../view/back/gestion_offres.php';
    }

    // Modifier une offre
    private function edit() {
        $id = $_GET['id'] ?? null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'titre' => $_POST['titre'],
                'description' => $_POST['description'],
                'entreprise' => $_POST['entreprise'],
                'localisation' => $_POST['localisation'],
                'type_contrat' => $_POST['type_contrat'],
                'salaire' => !empty($_POST['salaire']) ? $_POST['salaire'] : null
            ];
            
            if ($this->offreModel->update($id, $data)) {
                header('Location: /projetttwebbbbbbbbb/index.php?controller=offre&action=gestion&success=2');
                exit;
            } else {
                $error = "Erreur lors de la modification";
            }
        }
        
        $offre = $this->offreModel->getById($id);
        if (!$offre) {
            header('Location: /projetttwebbbbbbbbb/index.php?controller=offre&action=gestion');
            exit;
        }
        
        require_once __DIR__ . '/../view/back/gestion_offres.php';
    }

    // Supprimer une offre
    private function delete() {
        $id = $_GET['id'] ?? null;
        
        if ($id && $this->offreModel->delete($id)) {
            header('Location: /projetttwebbbbbbbbb/index.php?controller=offre&action=gestion&success=3');
            exit;
        } else {
            header('Location: /projetttwebbbbbbbbb/index.php?controller=offre&action=gestion&error=1');
            exit;
        }
    }

    // Afficher les détails d'une offre
    public function details() {
        // Debug: Log the request
        error_log("Details action called");
        error_log("GET params: " . print_r($_GET, true));
        
        if (!isset($_GET['id'])) {
            error_log("No ID provided, redirecting to liste");
            header('Location: /projetttwebbbbbbbbb/index.php?controller=offre&action=liste');
            exit;
        }

        $id = $_GET['id'];
        error_log("Fetching offer with ID: " . $id);
        
        $offre = $this->offreModel->getById($id);
        
        if (!$offre) {
            error_log("Offer not found with ID: " . $id);
            header('Location: /projetttwebbbbbbbbb/index.php?controller=offre&action=liste');
            exit;
        }

        error_log("Offer found, loading view");
        require_once __DIR__ . '/../view/front/details_offre.php';
    }
}