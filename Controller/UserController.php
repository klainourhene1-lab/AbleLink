<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../Model/User.php';

class UserController {

    private $conn;

    public function __construct() {
        $this->conn = Config::getConnexion();
    }

    // GET USER BY EMAIL
    public function getUserByEmail($email) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM utilisateur WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
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
                $data['photo'] ?? null
            );
            
            return $user;
        } catch (PDOException $e) {
            error_log("Error getting user by email: " . $e->getMessage());
            return null;
        }
    }

public function createUser($user) {
    try {
        $stmt = $this->conn->prepare("
            INSERT INTO utilisateur (nom, prenom, email, telephone, mot_de_passe, role, statut, date_inscription, date_modification, photo)
            VALUES (:nom, :prenom, :email, :telephone, :mot_de_passe, :role, :statut, :date_inscription, :date_modification, :photo)
        ");

        $stmt->execute([
            ':nom' => $user->getNom(),
            ':prenom' => $user->getPrenom(),
            ':email' => $user->getEmail(),
            ':telephone' => $user->getTelephone(),
            ':mot_de_passe' => $user->getMotDePasse(),
            ':role' => $user->getRole(),
            ':statut' => $user->getStatut(),
            ':date_inscription' => $user->getDateInscription(),
            ':date_modification' => $user->getDateModification(),
            ':photo' => $user->getPhoto()
        ]);

        return true;
    } catch (PDOException $e) {
        error_log("Error creating user: " . $e->getMessage());
        return false;
    }
}

    // GET USER BY ID
    public function showUser($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM utilisateur WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
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
                $data['photo'] ?? null
            );
            
            return $user;
        } catch (PDOException $e) {
            error_log("Error getting user by ID: " . $e->getMessage());
            return null;
        }
    }

    // UPDATE USER (for profile updates and ban)
    public function updateUser($user, $id) {
        try {
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
        } catch (PDOException $e) {
            error_log("Error updating user: " . $e->getMessage());
            return false;
        }
    }

    // UPDATE USER PHOTO
    public function updatePhoto($id, $fileName) {
        try {
            $sql = "UPDATE utilisateur SET photo = :photo, date_modification = NOW() WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute(['photo' => $fileName, 'id' => $id]);
        } catch (PDOException $e) {
            error_log("Photo update error: " . $e->getMessage());
            return false;
        }
    }

    // REGISTER USER (FRONT)
    public function register($data) {
        try {
            if ($this->emailExists($data['email'])) {
                return ['success' => false, 'message' => "Cet email est déjà utilisé."];
            }

            $stmt = $this->conn->prepare("
                INSERT INTO utilisateur 
                (nom, prenom, email, mot_de_passe, telephone, role, statut, date_inscription)
                VALUES (:nom, :prenom, :email, :mot_de_passe, :telephone, :role, 'actif', NOW())
            ");

            $success = $stmt->execute([
                'nom' => htmlspecialchars($data['nom']),
                'prenom' => htmlspecialchars($data['prenom']),
                'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
                'mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_BCRYPT),
                'telephone' => htmlspecialchars($data['telephone']),
                'role' => $data['role'] ?? 'Utilisateur'
            ]);

            return [
                'success' => $success,
                'message' => $success ? "Inscription réussie !" : "Erreur lors de l'inscription.",
                'userId' => $success ? $this->conn->lastInsertId() : null
            ];
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return ['success' => false, 'message' => "Une erreur technique est survenue."];
        }
    }

    // CHECK EMAIL EXISTS
    public function emailExists($email) {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = :email");
            $stmt->execute(['email' => filter_var($email, FILTER_SANITIZE_EMAIL)]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Email exists check error: " . $e->getMessage());
            return false;
        }
    }

    // ADD USER (for admin)
    public function addUser($user) {
        try {
            // Check if email already exists
            if ($this->emailExists($user->getEmail())) {
                return false;
            }

            $stmt = $this->conn->prepare("
                INSERT INTO utilisateur 
                (nom, prenom, email, telephone, mot_de_passe, role, statut, date_inscription)
                VALUES (:nom, :prenom, :email, :telephone, :mot_de_passe, :role, 'actif', NOW())
            ");

            // Password is already hashed by User constructor, so just use it as-is
            $password = $user->getMotDePasse();

            return $stmt->execute([
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'telephone' => $user->getTelephone(),
                'mot_de_passe' => $password,
                'role' => $user->getRole()
            ]);
        } catch (PDOException $e) {
            error_log("Add user error: " . $e->getMessage());
            return false;
        }
    }

    // GET ALL USERS
    public function getAllUsers() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM utilisateur ORDER BY id DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all users error: " . $e->getMessage());
            return [];
        }
    }

    // BAN/UNBAN USER
    public function banUser($id) {
        try {
            $user = $this->showUser($id);
            if (!$user) {
                return false;
            }
            
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
        } catch (PDOException $e) {
            error_log("Ban user error: " . $e->getMessage());
            return false;
        }
    }

    // DELETE USER
    public function deleteUser($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM utilisateur WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Delete user error: " . $e->getMessage());
            return false;
        }
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

    // PASSWORD RESET METHODS
public function sendPasswordResetEmail($email, $token) {
    try {

        // Build reset link
        $protocol = (!empty($_SERVER['HTTPS']) ? "https" : "http");
        $host = $_SERVER['HTTP_HOST'];
        $path = "/yerabby/view/general/reset-password.php";
        $resetLink = "$protocol://$host$path?token=$token";

        // SUBJECT + HTML TEMPLATE
        $subject = "Réinitialisation du mot de passe - AbleLink";

        $html = "
        <h2>Reinitialisation du mot de passe</h2>
        <p>Bonjour,</p>
        <p>Cliquez sur le bouton ci-dessous pour réinitialiser votre mot de passe :</p>
        <p><a href='$resetLink'
              style='padding:10px 20px; background:#00bfe7; color:white; border-radius:6px; text-decoration:none;'>
              Réinitialiser mon mot de passe
        </a></p>
        <p>Ou copiez ce lien : <br> $resetLink</p>
        <p>Ce lien expire dans 1 heure.</p>
        ";

        // CALL PHPMailer FUNCTION
require_once __DIR__ . '/../config/mailer.php';

        if (sendMail($email, $subject, $html)) {
            return true;
        }

        // fallback for localhost (debug only)
        if (strpos($host, 'localhost') !== false) {
            $_SESSION['debug_reset_link'] = $resetLink;
            return true;
        }

        return false;

    } catch (Exception $e) {
        error_log("sendPasswordResetEmail error: " . $e->getMessage());
        return false;
    }
}


// Méthode alternative si mail() échoue
private function sendEmailAlternative($email, $token, $resetLink) {
    try {
        // Essayer avec des headers simplifiés
        $subject = "Réinitialisation de mot de passe - AbleLink";
        $message = "Bonjour,\n\n";
        $message .= "Pour réinitialiser votre mot de passe AbleLink, cliquez sur ce lien :\n";
        $message .= $resetLink . "\n\n";
        $message .= "Ce lien expirera dans 1 heure.\n\n";
        $message .= "Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet email.\n\n";
        $message .= "Cordialement,\nL'équipe AbleLink";
        
        $headers = "From: noreply@ablelink.com\r\n";
        $headers .= "Reply-To: support@ablelink.com\r\n";
        
        return mail($email, $subject, $message, $headers);
    } catch (Exception $e) {
        error_log("Alternative email method also failed: " . $e->getMessage());
        return false;
    }
}   

    // Update user password and mark token as used
    public function updateUserPassword($email, $newPassword, $token = null) {
    try {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);

        $stmt = $this->conn->prepare("
            UPDATE utilisateur
            SET mot_de_passe = :pwd, date_modification = NOW()
            WHERE email = :email
        ");

        $result = $stmt->execute([
            'pwd' => $hash,
            'email' => $email
        ]);

        // Mark token as used if provided
        if ($token && $result) {
            $this->markTokenAsUsed($token);
        }

        return $result;

    } catch (PDOException $e) {
        error_log("updateUserPassword error: " . $e->getMessage());
        return false;
    }
}


    // Create password reset token
    public function createResetToken($email) {
    try {
        $user = $this->getUserByEmail($email);
        if (!$user) return false;

        // Delete previous tokens
        $this->deleteExistingTokens($email);

        $token = bin2hex(random_bytes(32));
        $created_at = date('Y-m-d H:i:s');
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $this->conn->prepare("
            INSERT INTO password_resets (email, token, created_at, expires_at)
            VALUES (:email, :token, :created_at, :expires_at)
        ");

        $stmt->execute([
            'email' => $email,
            'token' => $token,
            'created_at' => $created_at,
            'expires_at' => $expires_at
        ]);

        return $token;

    } catch (PDOException $e) {
        error_log("createResetToken error: " . $e->getMessage());
        return false;
    }
}

    // Delete existing tokens
    private function deleteExistingTokens($email) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM password_resets WHERE email = :email");
            $stmt->execute(['email' => $email]);
        } catch (PDOException $e) {
            error_log("Delete existing tokens error: " . $e->getMessage());
        }
    }

    // Validate token
   public function validateToken($token) {
    try {
        $stmt = $this->conn->prepare("
            SELECT * FROM password_resets
            WHERE token = :token AND used = 0
        ");
        $stmt->execute(['token' => $token]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) return false;

        // Check expiration
        if (new DateTime() > new DateTime($data['expires_at'])) {
            $this->deleteExistingTokens($data['email']);
            return false;
        }

        return $data;

    } catch (PDOException $e) {
        error_log("validateToken error: " . $e->getMessage());
        return false;
    }
}


    // Mark token as used
    
   public function markTokenAsUsed($token) {
    try {
        $stmt = $this->conn->prepare("
            UPDATE password_resets
            SET used = 1
            WHERE token = :token
        ");
        return $stmt->execute(['token' => $token]);

    } catch (PDOException $e) {
        error_log("markTokenAsUsed error: " . $e->getMessage());
        return false;
    }
}

    // SEARCH USERS
    public function searchUsers($query, $excludeId) {
        try {
            $sql = "SELECT id, nom, prenom, photo, role FROM utilisateur 
                    WHERE (nom LIKE :query OR prenom LIKE :query OR email LIKE :query) 
                    AND id != :excludeId
                    LIMIT 10";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['query' => "%$query%", 'excludeId' => $excludeId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Search error: " . $e->getMessage());
            return [];
        }
    }
}
?>