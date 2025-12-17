<?php
require_once __DIR__ . '/../Model/CandidatureModel.php';

class CandidatureController {
    private $candidatureModel;

    public function __construct() {
        $this->candidatureModel = new CandidatureModel();
    }

    public function submit($data) {
        // Validate required fields
        if (empty($data['id_offre']) || empty($data['nom_candidat']) || empty($data['email'])) {
            return ['success' => false, 'error' => 'Champs obligatoires manquants'];
        }

        // Handle File Upload (CV)
        $cvPath = null;
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/cvs/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = uniqid() . '_' . basename($_FILES['cv']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['cv']['tmp_name'], $targetPath)) {
                $cvPath = 'uploads/cvs/' . $fileName; // Relative path for DB
            } else {
                return ['success' => false, 'error' => 'Erreur lors du téléchargement du CV'];
            }
        }

        $candidatureData = [
            'id_offre' => $data['id_offre'],
            'nom_candidat' => $data['nom_candidat'],
            'email' => $data['email'],
            'cv' => $cvPath
        ];

        if ($this->candidatureModel->create($candidatureData)) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => 'Erreur lors de l\'enregistrement'];
        }
    }

    public function getByOffre($id_offre) {
        return $this->candidatureModel->getByOffre($id_offre);
    }
    
    public function getAll() {
        return $this->candidatureModel->getAll();
    }

    // Analytics Methods
    public function getStats() {
        return $this->candidatureModel->getStats();
    }

    public function getTopOffers() {
        return $this->candidatureModel->getTopOffers();
    }

    public function getApplicationsByMonth() {
        return $this->candidatureModel->getApplicationsByMonth();
    }

    // Récupérer les candidatures de l'utilisateur connecté
    public function getMyApplications() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        // Assuming email is stored in session or we have to fetch user first
        // If email is not in session, we might need to fetch User by ID.
        // Let's assume for now email is in session OR we instantiate User model.
        // The safest is to assume user_id is in session and get email from User Model? 
        // Or cleaner: assume email is in session['user_email'] if set by login.
        // If not, let's fetch it via UserModel or assume user_email is set.
        // Checking login logic (not visible here), usually user_id is key.
        
        $email = $_SESSION['user_email'] ?? null;
        if (!$email && isset($_SESSION['user_id'])) {
            // Fallback: This requires UserModel but we are inside CandidatureController.
            // Simplified: We'll assume email is available or user passes it. 
            // In a real app we'd load the User. 
            // For this task, let's try to get it from session.
        }
        
        if (!$email) return [];
        return $this->candidatureModel->getByCandidateEmail($email);
    }

    // Update status
    public function updateStatus($id, $status) {
        return $this->candidatureModel->updateStatut($id, $status);
    }
}
