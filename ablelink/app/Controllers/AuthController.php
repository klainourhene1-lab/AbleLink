<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class AuthController extends Controller {
    private \PDO $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
    
    // ========== HELPER METHODS (SQL QUERIES) ==========
    
    private function findUserByEmail(string $email): ?array {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }
    
    private function createUser(string $name, string $email, string $password): int {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, "user")');
            $stmt->execute([$name, $email, $hashedPassword]);
            return (int)$this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            return 0;
        }
    }
    
    private function setUserSession(array $user): void {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_logged_in'] = true;
    }
    
    private function clearUserSession(): void {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_role']);
        unset($_SESSION['user_logged_in']);
    }
    
    // ========== PUBLIC CONTROLLER ACTIONS ==========
    
    public function showLogin(): void {
        // If already logged in, redirect
        if (!empty($_SESSION['user_logged_in'])) {
            $this->redirectBasedOnRole();
            return;
        }
        
        $this->render('auth/login', [
            'error' => $_SESSION['login_error'] ?? '',
            'success' => $_SESSION['register_success'] ?? ''
        ]);
        
        unset($_SESSION['login_error']);
        unset($_SESSION['register_success']);
    }
    
    public function login(): void {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        
        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = 'Veuillez remplir tous les champs.';
            header('Location: /projetweb/ablelink/auth/login');
            return;
        }
        
        $user = $this->findUserByEmail($email);
        
        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['login_error'] = 'Email ou mot de passe incorrect.';
            header('Location: /projetweb/ablelink/auth/login');
            return;
        }
        
        // Login successful
        $this->setUserSession($user);
        $this->redirectBasedOnRole();
    }
    
    public function showRegister(): void {
        // If already logged in, redirect
        if (!empty($_SESSION['user_logged_in'])) {
            $this->redirectBasedOnRole();
            return;
        }
        
        $this->render('auth/register', [
            'error' => $_SESSION['register_error'] ?? ''
        ]);
        
        unset($_SESSION['register_error']);
    }
    
    public function register(): void {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');
        
        // Validation
        if (empty($name) || empty($email) || empty($password)) {
            $_SESSION['register_error'] = 'Tous les champs sont obligatoires.';
            header('Location: /projetweb/ablelink/auth/register');
            return;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['register_error'] = 'Email invalide.';
            header('Location: /projetweb/ablelink/auth/register');
            return;
        }
        
        if (strlen($password) < 6) {
            $_SESSION['register_error'] = 'Le mot de passe doit contenir au moins 6 caractères.';
            header('Location: /projetweb/ablelink/auth/register');
            return;
        }
        
        if ($password !== $confirmPassword) {
            $_SESSION['register_error'] = 'Les mots de passe ne correspondent pas.';
            header('Location: /projetweb/ablelink/auth/register');
            return;
        }
        
        // Check if email already exists
        if ($this->findUserByEmail($email)) {
            $_SESSION['register_error'] = 'Cet email est déjà utilisé.';
            header('Location: /projetweb/ablelink/auth/register');
            return;
        }
        
        // Create user
        $userId = $this->createUser($name, $email, $password);
        
        if (!$userId) {
            $_SESSION['register_error'] = 'Erreur lors de la création du compte. Veuillez réessayer.';
            header('Location: /projetweb/ablelink/auth/register');
            return;
        }
        
        // Registration successful
        $_SESSION['register_success'] = 'Compte créé avec succès! Vous pouvez maintenant vous connecter.';
        header('Location: /projetweb/ablelink/auth/login');
    }
    
    public function logout(): void {
        $this->clearUserSession();
        session_destroy();
        header('Location: /projetweb/ablelink/');
    }
    
    private function redirectBasedOnRole(): void {
        $role = $_SESSION['user_role'] ?? 'user';
        
        if ($role === 'admin') {
            header('Location: /projetweb/ablelink/success-stories/history');
        } else {
            header('Location: /projetweb/ablelink/success-stories');
        }
    }
}
