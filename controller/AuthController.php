<?php
require_once __DIR__ . '/../model/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Afficher le formulaire de connexion et gérer la soumission
     */
    public function login() {
        // Si déjà connecté, rediriger selon le rôle
        if (isset($_SESSION['user'])) {
            $this->redirectByRole($_SESSION['user']['role']);
            exit;
        }

        $error = null;

        // Traitement du formulaire de connexion
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            // Validation basique
            if (empty($username) || empty($password)) {
                $error = "Veuillez remplir tous les champs.";
            } else {
                // Authentification
                $user = $this->userModel->authenticate($username, $password);

                if ($user) {
                    // Stocker les infos utilisateur en session
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'nom' => $user['nom'],
                        'prenom' => $user['prenom'],
                        'role' => $user['role']
                    ];

                    // Redirection basée sur le rôle
                    $this->redirectByRole($user['role']);
                    exit;
                } else {
                    $error = "Identifiants invalides.";
                }
            }
        }

        // Afficher la vue de connexion
        require_once __DIR__ . '/../view/auth/login.php';
    }

    /**
     * Déconnexion
     */
    public function logout() {
        // Détruire la session
        session_unset();
        session_destroy();

        // Rediriger vers la page de connexion
        header('Location: ?controller=auth&action=login');
        exit;
    }

    /**
     * Redirection basée sur le rôle utilisateur
     */
    private function redirectByRole($role) {
        if ($role === 'admin') {
            // Admin → Back office (gestion des offres)
            header('Location: ?controller=offre&action=gestion');
        } else {
            // User → Front office (liste des offres)
            header('Location: ?controller=offre&action=liste');
        }
    }

    /**
     * Vérifier si l'utilisateur est connecté
     * Redirige vers login si non connecté
     */
    public static function requireAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: ?controller=auth&action=login');
            exit;
        }
    }

    /**
     * Vérifier si l'utilisateur est admin
     * Redirige vers la page d'accueil si non admin
     */
    public static function requireAdmin() {
        self::requireAuth();
        
        if ($_SESSION['user']['role'] !== 'admin') {
            // Non-admin : rediriger vers le front office avec message d'erreur
            header('Location: ?controller=offre&action=liste&error=access_denied');
            exit;
        }
    }

    /**
     * Vérifier si l'utilisateur est connecté (sans redirection)
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user']);
    }

    /**
     * Vérifier si l'utilisateur est admin (sans redirection)
     */
    public static function isAdmin() {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
    }

    /**
     * Récupérer l'utilisateur actuel
     */
    public static function getCurrentUser() {
        return $_SESSION['user'] ?? null;
    }
}
