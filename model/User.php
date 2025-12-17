<?php
require_once __DIR__ . '/Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Rechercher un utilisateur par email (username)
     */
    public function findByUsername($username) {
        $sql = "SELECT * FROM utilisateur WHERE email = :username LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();
        
        // Map database columns to expected format
        if ($user) {
            $user['username'] = $user['email'];
            $user['password'] = $user['mot_de_passe'];
            $user['date_creation'] = $user['date_inscription'];
        }
        
        return $user;
    }

    /**
     * Authentifier un utilisateur
     * Retourne les données de l'utilisateur si authentification réussie, false sinon
     */
    public function authenticate($username, $password) {
        $user = $this->findByUsername($username);
        
        if ($user && password_verify($password, $user['password'])) {
            // Ne pas retourner le mot de passe
            unset($user['password']);
            return $user;
        }
        
        return false;
    }

    /**
     * Récupérer un utilisateur par ID
     */
    public function getById($id) {
        $sql = "SELECT id, email, nom, prenom, role, date_inscription FROM utilisateur WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        
        // Map database columns to expected format
        if ($user) {
            $user['username'] = $user['email'];
            $user['date_creation'] = $user['date_inscription'];
        }
        
        return $user;
    }

    /**
     * Lire tous les utilisateurs (pour la gestion admin)
     */
    public function getAll() {
        $sql = "SELECT id, email as username, email, nom, prenom, role, date_inscription as date_creation FROM utilisateur ORDER BY date_inscription DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function create($data) {
        $sql = "INSERT INTO utilisateur (email, mot_de_passe, nom, prenom, role) 
                VALUES (:email, :mot_de_passe, :nom, :prenom, :role)";
        
        // Hacher le mot de passe
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':email' => $data['email'] ?? $data['username'],
            ':mot_de_passe' => $hashedPassword,
            ':nom' => $data['nom'] ?? null,
            ':prenom' => $data['prenom'] ?? null,
            ':role' => $data['role'] ?? 'Utilisateur'
        ]);
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update($id, $data) {
        // Si un nouveau mot de passe est fourni, on le met à jour
        if (!empty($data['password'])) {
            $sql = "UPDATE utilisateur SET 
                    email = :email,
                    mot_de_passe = :mot_de_passe,
                    nom = :nom,
                    prenom = :prenom,
                    role = :role
                    WHERE id = :id";
            
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':email' => $data['email'] ?? $data['username'],
                ':mot_de_passe' => $hashedPassword,
                ':nom' => $data['nom'] ?? null,
                ':prenom' => $data['prenom'] ?? null,
                ':role' => $data['role'] ?? 'Utilisateur'
            ]);
        } else {
            // Mise à jour sans changer le mot de passe
            $sql = "UPDATE utilisateur SET 
                    email = :email,
                    nom = :nom,
                    prenom = :prenom,
                    role = :role
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':email' => $data['email'] ?? $data['username'],
                ':nom' => $data['nom'] ?? null,
                ':prenom' => $data['prenom'] ?? null,
                ':role' => $data['role'] ?? 'Utilisateur'
            ]);
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function delete($id) {
        $sql = "DELETE FROM utilisateur WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Vérifier si un email existe déjà
     */
    public function usernameExists($username, $excludeId = null) {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) FROM utilisateur WHERE email = :username AND id != :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':username' => $username, ':id' => $excludeId]);
        } else {
            $sql = "SELECT COUNT(*) FROM utilisateur WHERE email = :username";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':username' => $username]);
        }
        
        return $stmt->fetchColumn() > 0;
    }
}
