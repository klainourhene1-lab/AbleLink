<?php
session_start();

// Check if user is logged in - redirect to login if not
if (!isset($_SESSION['user_id'])) {
    header('Location: ../view/general/signin.php');
    exit;
}

// Get user information from session
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'] ?? 'Utilisateur';

// Load user data from database
require_once __DIR__ . '/../Controller/config.php';
require_once __DIR__ . '/../Controller/UserController.php';
require_once __DIR__ . '/../Model/User.php';

$controller = new UserController();
$user = $controller->showUser($user_id);

// Get user photo URL
function getPhotoUrl($photo, $prenom, $nom) {
    if ($photo && file_exists(__DIR__ . '/../../uploads/profiles/' . $photo)) {
        return '../uploads/profiles/' . $photo;
    }
    return '../view/general/img/team/team-1.jpg'; // Default photo
}

$user_photo = $user ? getPhotoUrl($user->getPhoto(), $user->getPrenom(), $user->getNom()) : '../view/general/img/team/team-1.jpg';
$user_name = $user ? $user->getPrenom() . ' ' . $user->getNom() : 'Utilisateur';

// Map PHP session roles to JavaScript roles
$role_mapping = [
    'Admin' => 'admin',
    'Entreprise' => 'company', 
    'Utilisateur' => 'user',
    'Inclusion' => 'inclusion'
];

// Always sync session role with database role (to reflect real-time changes)
if ($user) {
    $db_role = $user->getRole();
    // Update session to match database
    $_SESSION['user_role'] = $db_role;
    $current_role = $db_role;
} else {
    $current_role = $_SESSION['user_role'] ?? 'Utilisateur';
}

// Update user_role for HTML checks
$user_role = $current_role;

$js_role = $role_mapping[$current_role] ?? 'user';

// Role display names
$role_display_names = [
    'admin' => 'Administrateur',
    'company' => 'Entreprise',
    'user' => 'Utilisateur',
    'inclusion' => 'Responsable Inclusion'
];

// Include the View
require_once __DIR__ . '/../view/FrontOffice/historique_view.php';
?>