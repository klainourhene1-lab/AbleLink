<?php
require_once __DIR__ . '/../../../Control/config.php';
require_once __DIR__ . '/../../../Control/UserController.php';
require_once __DIR__ . '/../../../Model/User.php';
$controller = new UserController();

// Handle actions and show messages
$success_message = "";
if (isset($_GET['deleted']) && $_GET['deleted'] == 1) {
    $success_message = "Utilisateur supprimé avec succès!";
}
if (isset($_GET['banned']) && $_GET['banned'] == 1) {
    $success_message = "Statut utilisateur modifié avec succès!";
}
if (isset($_GET['updated']) && $_GET['updated'] == 1) {
    $success_message = "Utilisateur modifié avec succès!";
}

// Handle actions via GET parameters
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];
    
    if ($action === 'delete') {
        if ($controller->deleteUser($id)) {
            header("Location: user_list.php?deleted=1");
            exit;
        }
    } elseif ($action === 'ban') {
        if ($controller->banUser($id)) {
            header("Location: user_list.php?banned=1");
            exit;
        }
    }
}

$users = $controller->getAllUsers();

/* --- Stats --- */
$totalUsers = count($users);
$activeUsers = 0;
$adminUsers = 0;

foreach ($users as $u) {
    if ($u['statut'] === 'actif') $activeUsers++;
    if ($u['role'] === 'Admin') $adminUsers++;
}


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nexus Admin - Liste des Utilisateurs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #7c3aed;
            --primary-light: #8b5cf6;
            --secondary: #0ea5e9;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --dark-light: #1e293b;
            --text: #e2e8f0;
            --text-light: #94a3b8;
            --card-bg: rgba(30, 41, 59, 0.7);
            --sidebar-width: 260px;
            --transition: all 0.3s;
            --border-radius: 16px;
            --shadow: 0 10px 25px rgba(0,0,0,0.3);
            --glow: 0 0 20px rgba(124,58,237,0.3);
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: var(--text);
            display: flex;
            overflow-x: hidden;
        }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-width);
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            padding: 25px 0px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255,255,255,0.1);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 20px;
            box-shadow: var(--glow);
        }

        .logo-text {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            color: var(--text-light);
            border-radius: 12px;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            transition: var(--transition);
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(124,58,237,0.2);
            box-shadow: 0 0 15px rgba(124,58,237,0.3);
            color: white;
        }

        .nav-link i {
            width: 24px;
            font-size: 20px;
            text-align: center;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
            width: 100%;
        }

        .header-title h1 {
            font-size: 32px;
            background: linear-gradient(to right, white, var(--text-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-title p {
            color: var(--text-light);
        }

        /* SUCCESS MESSAGE */
        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* STAT CARDS */
        .stats-container {
            margin-top: 25px;
            display: flex;
            gap: 25px;
        }

        .stat-card {
            flex: 1;
            background: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 25px;
            text-align: center;
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--glow);
        }

        .stat-icon {
            font-size: 38px;
            margin-bottom: 10px;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
        }

        .stat-label {
            font-size: 14px;
            color: var(--text-light);
        }

        /* USER TABLE */
        .card {
            margin-top: 35px;
            background: var(--card-bg);
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px 12px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        th {
            color: var(--text-light);
            text-transform: uppercase;
            font-size: 13px;
        }

        tr:hover {
            background: rgba(255,255,255,0.05);
        }

        .actions a {
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            margin-right: 5px;
            text-decoration: none;
            transition: 0.3s;
            display: inline-block;
        }

        .btn-edit { background: var(--primary); color: white; }
        .btn-delete { background: var(--danger); color: white; }
        .btn-ban { background: var(--warning); color: black; }
        .btn-unban { background: var(--success); color: black; }

        .actions a:hover {
            transform: translateY(-2px);
            opacity: 0.85;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success);
        }

        .status-banned {
            background: rgba(239, 68, 68, 0.2);
            color: var(--danger);
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">
            <div class="logo site-title">
    <a href="dashboard.php" style="text-decoration:none; display:flex; align-items:center; gap:10px;">
        <span class="letter-a">A</span>
        <span class="letter-b">B</span>
        <span class="letter-l">L</span>
        <span class="letter-e">E</span>
        <span class="letter-link">LINK</span>
    </a>
</div>
<style>
    /* ===== AbleLink Logo (Admin Version) ===== */

.site-title a {
    font-family: 'Josefin Sans', sans-serif;
    font-size: 26px;
    font-weight: 700;
    letter-spacing: 1px;
    display: flex;
    align-items: center;
    gap: 3px;
    transition: 0.3s ease;
}

.letter-a  { color: #FF4F5E; text-shadow: 0 0 6px rgba(255,79,94,0.7); }
.letter-b  { color: #4C8DF5; text-shadow: 0 0 6px rgba(76,141,245,0.7); }
.letter-l  { color: #B87BFF; text-shadow: 0 0 6px rgba(184,123,255,0.7); }
.letter-e  { color: #FFB247; text-shadow: 0 0 6px rgba(255,178,71,0.7); }

.letter-link {
    color: #FFFFFF;
    margin-left: 5px;
    text-shadow: 0 0 10px rgba(255,255,255,0.8);
}

/* Hover animation */
.site-title:hover a {
    transform: scale(1.05);
}

.site-title a span:hover {
    transform: translateY(-2px);
    transition: 0.2s ease;
}

</style>
    </div>

    <div class="nav-links">
        <a href="../../../Control/admin_dashboard.php" class="nav-link"><i class="fas fa-home"></i> Tableau de Bord</a>

        <div class="nav-link active"><i class="fas fa-users"></i> Utilisateurs</div>

        <div style="margin-left:35px; margin-top:10px;">
            <a href="user_list.php" class="nav-link active"><i class="fas fa-list"></i> Liste des utilisateurs</a>
            <a href="add_user.php" class="nav-link"><i class="fas fa-user-plus"></i> Ajouter un utilisateur</a>
        </div>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <div class="header-title">
        <h1>Liste des utilisateurs</h1>
        <p>Gérez, modifiez ou bannissez des utilisateurs</p>
    </div>

    <!-- SUCCESS MESSAGE -->
    <?php if (!empty($success_message)): ?>
        <div class="alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <!-- STATISTICS -->
    <div class="stats-container">
        <div class="stat-card total">
            <div class="stat-icon">👥</div>
            <div class="stat-number"><?= $totalUsers ?></div>
            <div class="stat-label">Total Utilisateurs</div>
        </div>

        <div class="stat-card active">
            <div class="stat-icon">✅</div>
            <div class="stat-number"><?= $activeUsers ?></div>
            <div class="stat-label">Actifs</div>
        </div>

        <div class="stat-card admin">
            <div class="stat-icon">👑</div>
            <div class="stat-number"><?= $adminUsers ?></div>
            <div class="stat-label">Administrateurs</div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="card">
        <table>
            <tr>
                <th>ID</th>
                <th>Nom complet</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>

            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= $u['prenom'] . " " . $u['nom'] ?></td>
                    <td><?= $u['email'] ?></td>
                    <td><?= $u['role'] ?></td>
                    <td>
                        <span class="status-badge <?= $u['statut'] === 'actif' ? 'status-active' : 'status-banned' ?>">
                            <?= $u['statut'] ?>
                        </span>
                    </td>

                    <td class="actions">
                        <a class="btn-edit" href="edit_user.php?id=<?= $u['id'] ?>">
                            <i class="fas fa-edit"></i> Modifier
                        </a>

                        <a class="btn-delete"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')"
                           href="user_list.php?action=delete&id=<?= $u['id'] ?>">
                            <i class="fas fa-trash"></i> Supprimer
                        </a>

                        <?php if ($u['statut'] === 'actif'): ?>
                            <a class="btn-ban"
                               onclick="return confirm('Êtes-vous sûr de vouloir bannir cet utilisateur ?')"
                               href="user_list.php?action=ban&id=<?= $u['id'] ?>">
                                <i class="fas fa-ban"></i> Bannir
                            </a>
                        <?php else: ?>
                            <a class="btn-unban"
                               onclick="return confirm('Êtes-vous sûr de vouloir activer cet utilisateur ?')"
                               href="user_list.php?action=ban&id=<?= $u['id'] ?>">
                                <i class="fas fa-check-circle"></i> Activer
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

</body>
</html>