<?php
require_once __DIR__ . '/../model/Candidature.php';
require_once __DIR__ . '/../model/Offre.php';

class CandidatureController {
    private $candidatureModel;
    private $offreModel;

    public function __construct() {
        $this->candidatureModel = new Candidature();
        $this->offreModel = new Offre();
    }

    // Postuler à une offre (Front Office)
    public function postuler() {
        $id_offre = $_GET['id_offre'] ?? null;
        
        if (!$id_offre) {
            header('Location: ?controller=offre&action=liste');
            exit;
        }

        $offre = $this->offreModel->getById($id_offre);
        
        if (!$offre) {
            header('Location: ?controller=offre&action=liste');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // VALIDATION DES CHAMPS OBLIGATOIRES
            $errors = [];
            
            // Validation nom complet
            if (empty($_POST['nom_candidat']) || trim($_POST['nom_candidat']) === '') {
                $errors[] = "Le nom complet est obligatoire.";
            }
            
            // Validation email
            if (empty($_POST['email']) || trim($_POST['email']) === '') {
                $errors[] = "L'email est obligatoire.";
            } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "L'email n'est pas valide.";
            }
            
            // Validation CV obligatoire
            if (!isset($_FILES['cv']) || $_FILES['cv']['error'] === UPLOAD_ERR_NO_FILE) {
                $errors[] = "Le CV est obligatoire.";
            } elseif ($_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "Erreur lors de l'upload du CV.";
            } elseif ($_FILES['cv']['size'] > 5242880) { // 5MB max
                $errors[] = "Le CV ne doit pas dépasser 5MB.";
            } else {
                // Vérifier l'extension
                $extension = strtolower(pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['pdf', 'doc', 'docx'];
                if (!in_array($extension, $allowedExtensions)) {
                    $errors[] = "Le CV doit être au format PDF, DOC ou DOCX.";
                }
            }
            
            // Si pas d'erreurs, traiter la candidature
            if (empty($errors)) {
                // Gestion de l'upload du CV
                $cv = null;
                if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../uploads/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $extension = pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION);
                    $filename = uniqid() . '.' . $extension;
                    $uploadPath = $uploadDir . $filename;
                    
                    if (move_uploaded_file($_FILES['cv']['tmp_name'], $uploadPath)) {
                        $cv = $filename;
                    } else {
                        $errors[] = "Erreur lors de la sauvegarde du CV.";
                    }
                }
                
                // Créer la candidature si le CV a été uploadé
                if ($cv) {
                    $data = [
                        'id_offre' => $id_offre,
                        'nom_candidat' => trim($_POST['nom_candidat']),
                        'email' => trim($_POST['email']),
                        'cv' => $cv,
                        'statut' => 'en_attente'
                    ];

                    if ($this->candidatureModel->create($data)) {
                        $success = "Votre candidature a été envoyée avec succès !";
                    } else {
                        $error = "Erreur lors de l'envoi de la candidature";
                    }
                }
            } else {
                // Afficher les erreurs
                $error = implode("<br>", $errors);
            }
        }

        require_once __DIR__ . '/../view/front/postuler.php';
    }

    // Gérer les candidatures (Back Office)
    public function gestion() {
        $action = $_GET['action'] ?? 'list';
        
        switch ($action) {
            case 'list':
                $this->list();
                break;
            case 'update_statut':
                $this->updateStatut();
                break;
            case 'delete':
                $this->delete();
                break;
            default:
                $this->list();
        }
    }

    // Afficher la liste des candidatures
    private function list() {
        $id_offre = $_GET['id_offre'] ?? null;
        
        if ($id_offre) {
            $candidatures = $this->candidatureModel->getByOffre($id_offre);
            $offre = $this->offreModel->getById($id_offre);
        } else {
            $candidatures = $this->candidatureModel->getAll();
            $offre = null;
        }
        
        require_once __DIR__ . '/../view/back/gestion_candidatures.php';
    }

    // Mettre à jour le statut
    private function updateStatut() {
        $id = $_GET['id'] ?? null;
        $statut = $_POST['statut'] ?? null;
        $id_offre = $_GET['id_offre'] ?? null;
        
        if ($id && $statut && $this->candidatureModel->updateStatut($id, $statut)) {
            $redirect = $id_offre ? "?controller=candidature&action=list&id_offre=$id_offre&success=1" 
                                  : "?controller=candidature&action=list&success=1";
            header("Location: $redirect");
            exit;
        } else {
            header('Location: ?controller=candidature&action=list&error=1');
            exit;
        }
    }

    // Supprimer une candidature
    private function delete() {
        $id = $_GET['id'] ?? null;
        $id_offre = $_GET['id_offre'] ?? null;
        
        if ($id && $this->candidatureModel->delete($id)) {
            $redirect = $id_offre ? "?controller=candidature&action=list&id_offre=$id_offre&success=2" 
                                  : "?controller=candidature&action=list&success=2";
            header("Location: $redirect");
            exit;
        } else {
            header('Location: ?controller=candidature&action=list&error=1');
            exit;
        }
    }
}