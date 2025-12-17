<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/User.php';

class UserController {

    private $conn;

    public function __construct() {
        $this->conn = Config::getConnexion();
    }

    // GET USER BY EMAIL
  // GET USER BY EMAIL
public function getUserByEmail($email) {
    $stmt = $this->conn->prepare("SELECT * FROM utilisateur WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // AJOUTE CETTE VERIFICATION
    if (!$data) {
        return null;
    }

    $user = new User(
        $data['id'],
        $data['nom'],
        $data['prenom'],
        $data['email'],
        $data['telephone'],
        $data['mot_de_passe'],
        $data['role'],
        $data['statut'],
        $data['date_inscription'],
        $data['date_modification'],
        $data['photo'] // AJOUTE PHOTO ICI AUSSI!
    );
    return $user;
}
    // GET USER BY ID (for showUser method)
  // GET USER BY ID (for showUser method)
public function showUser($id) {
    $stmt = $this->conn->prepare("SELECT * FROM utilisateur WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // AJOUTE CETTE VERIFICATION
    if (!$data) {
        return null;
    }

    $user = new User(
        $data['id'],
        $data['nom'],
        $data['prenom'],
        $data['email'],
        $data['telephone'],
        $data['mot_de_passe'],
        $data['role'],
        $data['statut'],
        $data['date_inscription'],
        $data['date_modification'],
        $data['photo'] // AJOUTE PHOTO ICI AUSSI!
    );
    return $user;
}

    // UPDATE USER (for profile updates and ban)
    public function updateUser($user, $id) {
        $stmt = $this->conn->prepare("
            UPDATE utilisateur 
            SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone, 
                role = :role, statut = :statut, date_modification = NOW()
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'email' => $user->getEmail(),
            'telephone' => $user->getTelephone(),
            'role' => $user->getRole(),
            'statut' => $user->getStatut(),
            'id' => $id
        ]);
    }
 public function updatePhoto($id, $fileName)
{
    try {
        $sql = "UPDATE utilisateur SET photo = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$fileName, $id]);
    } catch (PDOException $e) {
        error_log("Photo update error: " . $e->getMessage());
        return false;
    }
}


    // REGISTER USER (FRONT)
    public function register($data) {
        if ($this->emailExists($data['email'])) {
            return ['success' => false, 'message' => "Cet email est déjà utilisé."];
        }

        $stmt = $this->conn->prepare("
            INSERT INTO utilisateur 
            (nom, prenom, email, mot_de_passe, telephone, role, statut, date_inscription)
            VALUES (:nom, :prenom, :email, :mot_de_passe, :telephone, :role, 'actif', NOW())
        ");

        $ok = $stmt->execute([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_BCRYPT),
            'telephone' => $data['telephone'],
            'role' => $data['role'] ?? 'Utilisateur'
        ]);

        return [
            'success' => $ok,
            'message' => $ok ? "Inscription réussie !" : "Erreur lors de l'inscription."
        ];
    }

    // CHECK EMAIL EXISTS
    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    // ADD USER (for admin)
   public function addUser($user) {
    $stmt = $this->conn->prepare("
        INSERT INTO utilisateur 
        (nom, prenom, email, telephone, mot_de_passe, role, statut, date_inscription)
        VALUES (:nom, :prenom, :email, :telephone, :mot_de_passe, :role, 'actif', NOW())
    ");

    return $stmt->execute([
        'nom' => $user->getNom(),
        'prenom' => $user->getPrenom(),
        'email' => $user->getEmail(),
        'telephone' => $user->getTelephone(),
        // ❗ HASH NEJEM KEN MARA WA7DA
        'mot_de_passe' => $user->getMotDePasse(),
        'role' => $user->getRole()
    ]);
}


    // GET ALL USERS
    public function getAllUsers() {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateur ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // BAN/UNBAN USER
    public function banUser($id) {
        $user = $this->showUser($id);
        if ($user) {
            $newStatus = ($user->getStatut() === 'actif') ? 'banni' : 'actif';
            
            $stmt = $this->conn->prepare("
                UPDATE utilisateur 
                SET statut = :statut, date_modification = NOW() 
                WHERE id = :id
            ");
            
            return $stmt->execute([
                'statut' => $newStatus,
                'id' => $id
            ]);
        }
        return false;
    }

    // DELETE USER
    public function deleteUser($id) {
        $stmt = $this->conn->prepare("DELETE FROM utilisateur WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    

    // HANDLE ACTIONS
    public function handleAction($action, $id) {
        switch($action) {
            case 'delete':
                return $this->deleteUser($id);
            case 'ban':
                return $this->banUser($id);
            default:
                return false;
        }
    }
}
?>