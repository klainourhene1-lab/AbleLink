<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../view/general/signin.php'); exit; }

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'] ?? 'Utilisateur';

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/UserController.php';
require_once __DIR__ . '/../Model/User.php';
require_once __DIR__ . '/../Model/Database.php';
require_once __DIR__ . '/../Model/StoryModel.php';
require_once __DIR__ . '/StoryController.php';

$controller = new UserController();
$user = $controller->showUser($user_id);
$storyModel = new StoryModel();
$storyController = new StoryController();

// Handle Actions (Create/Update/Delete logic is inside StoryController methods but we need to trigger them if POST/GET params exist)
// In a pure Router setup, this would be cleaner. Here we invoke them to catch actions targeting this page.
$storyController->create();
$storyController->update();
$storyController->delete();

// Get user photo logic
function getPhotoUrl($photo, $prenom, $nom) {
    if ($photo && file_exists(__DIR__ . '/../../uploads/profiles/' . $photo)) {
        return '../uploads/profiles/' . $photo;
    }
    return '../view/general/img/team/team-1.jpg'; // Default photo
}
$user_photo = $user ? getPhotoUrl($user->getPhoto(), $user->getPrenom(), $user->getNom()) : '../view/general/img/team/team-1.jpg';
$user_name = $user ? $user->getPrenom() . ' ' . $user->getNom() : 'Utilisateur';

if ($user) {
    $_SESSION['user_role'] = $user->getRole();
    $user_role = $user->getRole();
}

$role_mapping = ['Admin' => 'admin', 'Entreprise' => 'company', 'Utilisateur' => 'user', 'Inclusion' => 'inclusion'];
$js_role = $role_mapping[$user_role] ?? 'user';
$role_names = ['admin' => 'Administrateur', 'company' => 'Entreprise', 'user' => 'Utilisateur', 'inclusion' => 'Responsable'];

$myStories = $storyModel->getByUserId($user_id);
$total = count($myStories);
$approved = count(array_filter($myStories, fn($s) => ($s['status'] ?? '') == 'approved'));
$pending = count(array_filter($myStories, fn($s) => ($s['status'] ?? '') == 'pending'));
$totalLikes = array_sum(array_column($myStories, 'likes'));

// Load the View
require_once __DIR__ . '/../view/FrontOffice/historique_stories_view.php';
?>
