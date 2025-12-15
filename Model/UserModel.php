<?php
require_once __DIR__ . '/Database.php';

/**
 * User Model
 * Handles all database operations related to users
 */
class UserModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get user by ID
     */
    public function getById($userId) {
        $sql = "SELECT * FROM utilisateur WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
    
    /**
     * Get valid user ID (for creating events when user doesn't exist)
     */
    public function getValidUserId($providedUserId = null) {
        // First, try to use the provided user ID if it exists
        if ($providedUserId) {
            $user = $this->getById($providedUserId);
            if ($user) {
                return $user['id'];
            }
        }
        
        // If provided user doesn't exist or no user provided, use a default valid user
        // Try to get the first admin user
        $sql = "SELECT id FROM utilisateur WHERE role = 'Admin' ORDER BY id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $admin = $stmt->fetch();
        
        if ($admin) {
            return $admin['id'];
        }
        
        // If no admin, get the first user
        $sql = "SELECT id FROM utilisateur ORDER BY id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $user = $stmt->fetch();
        
        if ($user) {
            return $user['id'];
        }
        
        // If no users exist, create a default one
        return $this->createDefaultUser();
    }
    
    /**
     * Create a default user if no users exist
     */
    private function createDefaultUser() {
        $defaultEmail = 'admin@abelink.com';
        $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO utilisateur (nom, prenom, email, motDePasse, role) 
                VALUES ('Admin', 'System', ?, ?, 'Admin')";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$defaultEmail, $defaultPassword]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Get all users (for admin)
     */
    public function getAll() {
        $sql = "SELECT * FROM utilisateur ORDER BY id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get non-admin users (enterprises)
     */
    public function getEnterprises() {
        $sql = "SELECT * FROM utilisateur WHERE role = 'Entreprise' ORDER BY nom, prenom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}







