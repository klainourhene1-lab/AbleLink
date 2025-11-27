<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/User.php';

class UserController {
<<<<<<< HEAD

=======
>>>>>>> 8cd957d5fed58dac65bc9e2bb2086919bdd16c84
    private $conn;

    public function __construct() {
        $this->conn = Config::getConnexion();
    }

<<<<<<< HEAD
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
=======
    // Login method - مصحح
    public function login(string $email, string $password): ?User {
        $sql = "SELECT * FROM utilisateur WHERE email = :email AND statut = 'actif'";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data && password_verify($password, $data['mot_de_passe'])) {
                return new User(
                    $data['id'],
                    $data['prenom'],
                    $data['nom'],
                    $data['email'],
                    $data['telephone'],
                    $data['role'],
                    $data['statut'],
                    $data['date_inscription'],
                    $data['date_modification']
                );
            }
            return null;
        } catch (Exception $e) {
            error_log('Error during login: ' . $e->getMessage());
            return null;
        }
    }

    // Get all users - مصحح
    public function listUsers(): array {
        $sql = "SELECT * FROM utilisateur ORDER BY id DESC";
        try {
            $stmt = $this->conn->query($sql);
            $usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $users = [];
            foreach ($usersData as $data) {
                $user = new User(
                    $data['id'],
                    $data['prenom'],
                    $data['nom'],
                    $data['email'],
                    $data['telephone'],
                    $data['role'],
                    $data['statut'],
                    $data['date_inscription'],
                    $data['date_modification']
                );
                $users[] = $user;
            }
            return $users;
        } catch (Exception $e) {
            error_log('Error listing users: ' . $e->getMessage());
            return [];
        }
    }

    // Add new user - مصحح
    public function addUser(User $user): bool {
        $sql = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, telephone, role, statut, date_inscription)
                VALUES (:nom, :prenom, :email, :mot_de_passe, :telephone, :role, :statut, NOW())";
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'mot_de_passe' => $user->getMotDePasse(),
                'telephone' => $user->getTelephone(),
                'role' => $user->getRole(),
                'statut' => $user->getStatut()
            ]);
        } catch (Exception $e) {
            error_log('Error adding user: ' . $e->getMessage());
            return false;
        }
    }

    // Update user - FIXED VERSION
    public function updateUser(User $user, int $id): bool {
        // Start with basic fields
        $sql = "UPDATE utilisateur SET 
                    nom = :nom,
                    prenom = :prenom,
                    email = :email,
                    telephone = :telephone,
                    role = :role,
                    statut = :statut,
                    date_modification = NOW()";
        
        // Add password update if password is set and not empty
        if ($user->getMotDePasse() !== null && !empty($user->getMotDePasse())) {
            $sql .= ", mot_de_passe = :mot_de_passe";
        }
        
        $sql .= " WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $params = [
                'id' => $id,
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'telephone' => $user->getTelephone(),
                'role' => $user->getRole(),
                'statut' => $user->getStatut()
            ];
            
            // Add password to params if it's set and not empty
            if ($user->getMotDePasse() !== null && !empty($user->getMotDePasse())) {
                $params['mot_de_passe'] = $user->getMotDePasse();
            }
            
            return $stmt->execute($params);
        } catch (Exception $e) {
            error_log('Error updating user: ' . $e->getMessage());
            return false;
        }
    }

    // Show user by ID - مصحح
    public function showUser(int $id): ?User {
        $sql = "SELECT * FROM utilisateur WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                return new User(
                    $data['id'],
                    $data['prenom'],
                    $data['nom'],
                    $data['email'],
                    $data['telephone'],
                    $data['role'],
                    $data['statut'],
                    $data['date_inscription'],
                    $data['date_modification']
                );
            }
            return null;
        } catch (Exception $e) {
            error_log('Error showing user: ' . $e->getMessage());
            return null;
        }
    }

    // Check if email exists
    public function emailExists(string $email): bool {
        $sql = "SELECT COUNT(*) FROM utilisateur WHERE email = :email";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            error_log('Error checking email: ' . $e->getMessage());
            return false;
        }
    }

    // Delete user - FIXED VERSION
    public function deleteUser(int $id): bool {
        // Prevent admin from deleting themselves
        if ($id == $_SESSION['user_id']) {
            error_log('Admin cannot delete their own account');
            return false;
        }
        
        $sql = "DELETE FROM utilisateur WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('Error deleting user: ' . $e->getMessage());
            return false;
        }
    }

    // Statistics methods
    public function countTotalUsers(): int {
        $sql = "SELECT COUNT(*) as count FROM utilisateur";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            error_log('Error counting total users: ' . $e->getMessage());
            return 0;
        }
    }

    public function countActiveUsers(): int {
        $sql = "SELECT COUNT(*) as count FROM utilisateur WHERE statut = 'actif'";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            error_log('Error counting active users: ' . $e->getMessage());
            return 0;
        }
    }

    public function countAdminUsers(): int {
        $sql = "SELECT COUNT(*) as count FROM utilisateur WHERE role = 'admin'";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            error_log('Error counting admin users: ' . $e->getMessage());
            return 0;
>>>>>>> 8cd957d5fed58dac65bc9e2bb2086919bdd16c84
        }
    }
}
?>