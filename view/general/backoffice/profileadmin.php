
<?php
session_start();
require_once __DIR__ . '/../../../Controller/config.php';
require_once __DIR__ . '/../../../Controller/UserController.php';
require_once __DIR__ . '/../../../Model/User.php';

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté.");
}

$controller = new UserController();
$user = $controller->showUser($_SESSION['user_id']);

if (!$user) {
    die("Utilisateur introuvable.");
}
// === UPLOAD PHOTO ===
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["photo"]) && $_FILES["photo"]["error"] === 0) {
    
    // Create directory if it doesn't exist
    $uploadDir = __DIR__ . "/../../../uploads/profile/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = $_FILES["photo"]["name"];
    $fileTmp = $_FILES["photo"]["tmp_name"];
    $fileSize = $_FILES["photo"]["size"];

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($fileTmp);
    
    if (!in_array($fileType, $allowedTypes)) {
        die("Type de fichier non autorisé.");
    }

    // Validate file size (max 5MB)
    if ($fileSize > 5 * 1024 * 1024) {
        die("Fichier trop volumineux (max 5MB).");
    }

    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
    $newName = "profile_" . $user->getId() . "_" . time() . "." . $extension;
    $uploadPath = $uploadDir . $newName;

    if (move_uploaded_file($fileTmp, $uploadPath)) {
        // Save in DB
        if ($controller->updatePhoto($user->getId(), $newName)) {
            $user->setPhoto($newName);
            // Refresh the page to show new photo
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            die("Erreur lors de la mise à jour en base de données.");
        }
    } else {
        die("Erreur lors du téléchargement.");
    }
}

?>
<style>
.profile-header {
    background: #1e293b;
    padding: 25px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 25px;
}

.profile-header img {
    width: 90px;
    height: 90px;
    object-fit: cover;
    border-radius: 50%;
    border: 3px solid #7c3aed;
}

.profile-header h2 {
    margin: 0;
    font-size: 22px;
    font-weight: 600;
}

.profile-section {
    background: #1e293b;
    padding: 25px;
    border-radius: 16px;
    margin-bottom: 25px;
}

.section-title {
    font-size: 18px;
    margin-bottom: 15px;
    font-weight: 600;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.info-box {
    background: rgba(255, 255, 255, 0.05);
    padding: 15px;
    border-radius: 12px;
}

.info-label {
    font-size: 13px;
    color: #94a3b8;
}

.info-value {
    font-size: 15px;
    font-weight: 500;
}

.edit-btn {
    float: right;
    padding: 8px 15px;
    background: #f59e0b;
    border-radius: 8px;
    color: black;
    font-size: 13px;
    cursor: pointer;
    border: none;
}
</style>

<!-- Profile Header -->
<div class="profile-header">

    <div style="position:relative; display:flex; align-items:center;">
        
        <!-- Photo Profil -->
        <img id="profileImg" 
            src="<?= $user->getPhoto() ? '/googo/uploads/profile/' . $user->getPhoto() : '/googo/view/general/img/team/team-1.jpg'; ?>"

             style="width:90px; height:90px; object-fit:cover; border-radius:50%; border:3px solid #7c3aed;">

        <!-- Bouton Modifier Photo -->
        <button 
            onclick="document.getElementById('photoInput').click()" 
            style="
                position:absolute;
                bottom:-8px;
                left:5px;
                background:#7c3aed;
                border:none;
                padding:5px 10px;
                border-radius:6px;
                color:white;
                cursor:pointer;
                font-size:12px;">
            Modifier Photo
        </button>

        <!-- Formulaire upload hidden -->
        <form method="POST" enctype="multipart/form-data" id="photoForm" style="display:none;">
            <input type="file" name="photo" id="photoInput" accept="image/*" onchange="uploadPhoto()">
        </form>

    </div>

    <div style="margin-left:20px;">
        <h2><?= htmlspecialchars($user->getPrenom() . " " . $user->getNom()); ?></h2>
        <p style="margin:5px 0; color:#94a3b8;"><?= $user->getRole(); ?></p>
        <p style="margin:0; color:#94a3b8;">Profil Administrateur</p>
    </div>

</div>


<!-- Personal Information -->
<div class="profile-section">
    <a href="edit_user.php?id=<?= $user->getId(); ?>">
        <button class="edit-btn">Modifier</button>
    </a>

    <div class="section-title">Informations personnelles</div>

    <div class="info-grid">
        <div class="info-box">
            <div class="info-label">Prénom</div>
            <div class="info-value"><?= $user->getPrenom(); ?></div>
        </div>

        <div class="info-box">
            <div class="info-label">Nom</div>
            <div class="info-value"><?= $user->getNom(); ?></div>
        </div>

        <div class="info-box">
            <div class="info-label">Adresse Email</div>
            <div class="info-value"><?= $user->getEmail(); ?></div>
        </div>

        <div class="info-box">
            <div class="info-label">Téléphone</div>
            <div class="info-value"><?= $user->getTelephone(); ?></div>
        </div>

        <div class="info-box">
            <div class="info-label">Rôle</div>
            <div class="info-value"><?= $user->getRole(); ?></div>
        </div>

        <div class="info-box">
            <div class="info-label">Statut</div>
            <div class="info-value"><?= $user->getStatut(); ?></div>
        </div>
    </div>
</div>
<script>
function uploadPhoto() {
    document.getElementById("photoForm").submit();
}
</script>
