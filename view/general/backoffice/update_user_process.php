<?php
require_once __DIR__ . '/../../../controllers/UserController.php';
require_once __DIR__ . '/../../../models/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Méthode non autorisée.");
}

$controller = new UserController();

// Get POST values
$id        = $_POST['id'];
$nom       = trim($_POST['nom']);
$prenom    = trim($_POST['prenom']);
$email     = trim($_POST['email']);
$telephone = trim($_POST['telephone']);
$role      = trim($_POST['role']);
$statut    = trim($_POST['statut']);

// Load user by ID
$user = $controller->showUser($id);
if (!$user) {
    die("Utilisateur introuvable.");
}

// Update object values
$user->setNom($nom);
$user->setPrenom($prenom);
$user->setEmail($email);
$user->setTelephone($telephone);
$user->setRole($role);
$user->setStatut($statut);

// Save into DB
$updated = $controller->updateUser($user, $id);

if ($updated) {
    header("Location: user_list.php?updated=1");
    exit;
} else {
    header("Location: edit_user.php?id=$id&error=1");
    exit;
}
?>
