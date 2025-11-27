<?php
session_start();

// التحقق من أن المستخدم مسجل الدخول وهو أدمن
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once "../config.php";
require_once "../controllers/UserController.php";

$controller = new UserController();
$users = $controller->listUsers();

// Calculate statistics
$totalUsers = $controller->countTotalUsers();
$activeUsers = $controller->countActiveUsers();
$adminUsers = $controller->countAdminUsers();

// Check if editing
$editingUser = null;
if (isset($_GET['edit'])) {
    $editingUser = $controller->showUser((int)$_GET['edit']);
}

// Handle POST requests
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $telephone = trim($_POST['telephone'] ?? '');
    $role = $_POST['user_type'] ?? 'candidat';
    $statut = 'actif';

    // Check if we're adding or updating
    $is_editing = isset($_POST['user_id']) && !empty($_POST['user_id']);
    $user_id = $is_editing ? (int)$_POST['user_id'] : null;

    // Validation
    if (empty($nom)) {
        $errors['nom'] = "Last name is required";
    }
    
    if (empty($prenom)) {
        $errors['prenom'] = "First name is required";
    }
    
    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    } elseif (!$is_editing && $controller->emailExists($email)) {
        $errors['email'] = "Email already exists";
    } elseif ($is_editing) {
        // For editing, check if email exists for other users
        $current_user = $controller->showUser($user_id);
        if ($current_user && $current_user->getEmail() !== $email && $controller->emailExists($email)) {
            $errors['email'] = "Email already exists";
        }
    }
    
    if (!$is_editing && empty($password)) {
        $errors['password'] = "Password is required for new users";
    } elseif (!$is_editing && strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters";
    }
    
    if (!$is_editing && $password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }
    
    if (empty($role)) {
        $errors['user_type'] = "Please select a role";
    }

    if (empty($errors)) {
        $user = new User();
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setEmail($email);
        $user->setTelephone($telephone);
        $user->setRole($role);
        $user->setStatut($statut);

        // Set password only if provided (for editing) or required (for new users)
        if (!$is_editing || !empty($password)) {
            $user->setMotDePasse($password);
        }

        if ($is_editing) {
            // Update existing user
            if ($controller->updateUser($user, $user_id)) {
                $success = "User updated successfully!";
                // Refresh the page to show updated data
                header('Location: index1.php?success=user_updated');
                exit;
            } else {
                $errors['general'] = "Error updating user. Please try again.";
            }
        } else {
            // Add new user
            if ($controller->addUser($user)) {
                $success = "User created successfully!";
                // Refresh the page to show new user
                header('Location: index1.php?success=user_created');
                exit;
            } else {
                $errors['general'] = "Error creating user. Please try again.";
            }
        }
    }
}

// Check for success messages from redirect
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'user_created') {
        $success = "User created successfully!";
    } elseif ($_GET['success'] === 'user_updated') {
        $success = "User updated successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<style>
/* GENERAL */
body  {
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
    background: #0d1117;
    color: #d1d5db;
}

a { text-decoration: none; }

/* APP WRAPPER */
.app-container {
    display: flex;
    min-height: 100vh;
}

/* SIDEBAR */
.sidebar {
    width: 280px;
    background: #0f1624;
    padding: 30px 20px;
    color: white;
}
.sidebar .logo {
    font-size: 26px;
    font-weight: 700;
    margin-bottom: 40px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.sidebar .logo span { color: #4f9cff; }
.sidebar a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 15px;
    color: #d1d5db;
    border-radius: 8px;
    margin-bottom: 10px;
    font-size: 16px;
    transition: 0.2s;
}
.sidebar a.active { background: #7c3aed; color: #fff; }
.sidebar a:hover { background: #4f9cff; color: #fff; }
.sidebar .sublink { 
    font-size: 14px; 
    margin-left: 25px; 
    color: #9ca3af;
    padding: 10px 15px;
    border-radius: 6px;
    margin-bottom: 5px;
    display: block;
}
.sidebar .sublink.active { 
    background: rgba(124, 58, 237, 0.2); 
    color: #a78bfa;
    border-left: 3px solid #7c3aed;
}
.sidebar .sublink:hover { 
    background: rgba(79, 156, 255, 0.1); 
    color: #fff; 
}

/* MAIN CONTENT */
.main-content { 
    flex: 1; 
    padding: 40px;
    display: flex;
    flex-direction: column;
    gap: 30px;
}

/* CARD */
.card {
    background: #111827;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 0 20px rgba(0, 150, 255, 0.15);
    border: 1px solid rgba(0, 150, 255, 0.2);
}

/* STATS CARDS */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.stat-card {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    border-radius: 12px;
    padding: 25px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
}

.stat-card.total { border-top: 4px solid #4f9cff; }
.stat-card.active { border-top: 4px solid #10b981; }
.stat-card.admin { border-top: 4px solid #f59e0b; }

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 10px 0;
    display: block;
}

.stat-card.total .stat-number { color: #4f9cff; }
.stat-card.active .stat-number { color: #10b981; }
.stat-card.admin .stat-number { color: #f59e0b; }

.stat-label {
    color: #9ca3af;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.stat-icon {
    font-size: 2rem;
    margin-bottom: 10px;
    opacity: 0.8;
}

/* IMPROVED TABLE STYLES */
.table-container {
    overflow-x: auto;
    border-radius: 10px;
    background: #111827;
}

.user-table {
    width: 100%;
    border-collapse: collapse;
    color: #e5e7eb;
    background: #111827;
    border-radius: 10px;
    overflow: hidden;
}

.user-table thead {
    background: linear-gradient(135deg, #7c3aed 0%, #4f9cff 100%);
}

.user-table th {
    padding: 16px 12px;
    text-transform: uppercase;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.5px;
    color: #ffffff;
    text-align: left;
}

.user-table tbody tr {
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}

.user-table tbody tr:hover {
    background: rgba(79, 156, 255, 0.08);
    transform: translateX(5px);
}

.user-table td {
    padding: 14px 12px;
    font-size: 14px;
}

/* Role badges */
.role-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.role-badge.candidat {
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.role-badge.recruteur {
    background: rgba(59, 130, 246, 0.15);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.3);
}

.role-badge.admin {
    background: rgba(245, 158, 11, 0.15);
    color: #f59e0b;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

/* ACTION BUTTONS */
.action-buttons {
    display: flex;
    gap: 8px;
}

.action-btn {
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-decoration: none;
    display: inline-block;
}

.action-btn.edit { 
    background: linear-gradient(135deg, #facc15 0%, #eab308 100%);
    color: #000;
}

.action-btn.edit:hover {
    background: linear-gradient(135deg, #eab308 0%, #ca8a04 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(234, 179, 8, 0.3);
}

.action-btn.delete { 
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: #fff;
}

.action-btn.delete:hover {
    background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

.action-btn.cancel { 
    background: transparent; 
    color: #9ca3af; 
    border: 1px solid #9ca3af;
    margin-left: 10px;
}

/* FORM */
.form-group { margin-bottom: 20px; }
.form-group label { 
    display: block; 
    color: #cbd5e1; 
    margin-bottom: 8px;
    font-weight: 500;
}
.form-group input,
.form-group select {
    width: 100%;
    padding: 12px;
    border-radius: 6px;
    border: none;
    background: #1e293b;
    color: white;
    box-sizing: border-box;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #4f9cff;
    box-shadow: 0 0 0 3px rgba(79, 156, 255, 0.1);
}

input::placeholder { color: #9ca3af; }
.btn-submit {
    background: linear-gradient(135deg, #4f9cff 0%, #3b82f6 100%);
    border: none;
    padding: 14px 24px;
    border-radius: 8px;
    color: white;
    cursor: pointer;
    font-size: 15px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-submit:hover { 
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(79, 156, 255, 0.4);
}

.success { 
    color: #4ade80; 
    margin-bottom: 15px;
    padding: 12px 16px;
    background: rgba(74, 222, 128, 0.1);
    border-radius: 8px;
    border-left: 4px solid #4ade80;
}

.error { 
    color: #f87171; 
    margin-bottom: 15px;
    padding: 12px 16px;
    background: rgba(248, 113, 113, 0.1);
    border-radius: 8px;
    border-left: 4px solid #f87171;
}

/* FORM SECTIONS */
.form-section {
    margin-bottom: 30px;
}
.form-section h3 {
    color: #cbd5e1;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

/* EDITING INDICATOR */
.editing-indicator {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    padding: 12px 16px;
    background: rgba(124, 58, 237, 0.1);
    border-radius: 8px;
    border-left: 4px solid #7c3aed;
}
.editing-indicator span {
    font-weight: 600;
    color: #a78bfa;
}

/* TWO COLUMN LAYOUT */
.two-column-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

@media (max-width: 1200px) {
    .two-column-layout {
        grid-template-columns: 1fr;
    }
    .stats-container {
        grid-template-columns: 1fr;
    }
}

/* PAGE MANAGEMENT */
.page {
    display: none;
}
.page.active {
    display: block;
}

/* CARD HEADER */
.card-header {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.card-header h2 {
    margin: 0;
    color: #e5e7eb;
    font-size: 1.5rem;
    font-weight: 600;
}
</style>
</head>
<body>

<div class="app-container">
    <div class="sidebar">
        <div class="logo">ABLE<span>LINK</span></div>
        <a href="#" class="nav-link active" onclick="showPage('userList')">User Management</a>
        <a href="#" class="sublink <?= !$editingUser ? 'active' : '' ?>" onclick="showPage('addUser')">Add User</a>
        <?php if($editingUser): ?>
            <a href="#" class="sublink active" onclick="showPage('editUser')">Edit User</a>
        <?php endif; ?>
    </div>

    <div class="main-content">
        <?php if($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php foreach($errors as $e): ?>
            <div class="error"><?= $e ?></div>
        <?php endforeach; ?>

        <!-- User List Page -->
        <div id="userList" class="page active">
            <!-- Statistics Cards -->
            <div class="stats-container">
                <div class="stat-card total">
                    <div class="stat-icon">👥</div>
                    <div class="stat-number"><?= $totalUsers ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-card active">
                    <div class="stat-icon">✅</div>
                    <div class="stat-number"><?= $activeUsers ?></div>
                    <div class="stat-label">Active Users</div>
                </div>
                <div class="stat-card admin">
                    <div class="stat-icon">👑</div>
                    <div class="stat-number"><?= $adminUsers ?></div>
                    <div class="stat-label">Administrators</div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>User List</h2>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="user-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($users as $u): ?>
                                <tr>
                                    <td><?= $u->getId() ?></td>
                                    <td><?= htmlspecialchars($u->getNom()) ?></td>
                                    <td><?= htmlspecialchars($u->getPrenom()) ?></td>
                                    <td><?= htmlspecialchars($u->getEmail()) ?></td>
                                    <td><?= htmlspecialchars($u->getTelephone()) ?></td>
                                    <td>
                                        <span class="role-badge <?= $u->getRole() ?>">
                                            <?= htmlspecialchars($u->getRole()) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="?edit=<?= $u->getId() ?>" class="action-btn edit">Edit</a>
                                            <a href="delete_user.php?id=<?= $u->getId() ?>" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add User Page -->
        <div id="addUser" class="page <?= !$editingUser ? '' : 'active' ?>">
            <div class="card">
                <div class="card-header">
                    <h2>Add New User</h2>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="prenom" placeholder="Enter first name" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="nom" placeholder="Enter last name" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" placeholder="Enter email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" placeholder="Enter password" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" placeholder="Repeat password" required>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="telephone" placeholder="Enter phone" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select name="user_type" required>
                                <option value="candidat" <?= (($_POST['user_type'] ?? '')=='candidat')?'selected':'' ?>>Candidate</option>
                                <option value="recruteur" <?= (($_POST['user_type'] ?? '')=='recruteur')?'selected':'' ?>>Employer</option>
                                <option value="admin" <?= (($_POST['user_type'] ?? '')=='admin')?'selected':'' ?>>Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-submit">Create User</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit User Page -->
        <?php if($editingUser): ?>
        <div id="editUser" class="page active">
            <div class="card">
                <div class="card-header">
                    <h2>Edit User</h2>
                    <div class="editing-indicator">
                        Currently editing: <span><?= htmlspecialchars($editingUser->getPrenom() . ' ' . $editingUser->getNom()) ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="user_id" value="<?= $editingUser->getId() ?>">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="prenom" placeholder="Enter first name" value="<?= htmlspecialchars($editingUser->getPrenom()) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="nom" placeholder="Enter last name" value="<?= htmlspecialchars($editingUser->getNom()) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" placeholder="Enter email" value="<?= htmlspecialchars($editingUser->getEmail()) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Password (leave blank to keep current)</label>
                            <input type="password" name="password" placeholder="Enter new password">
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" placeholder="Repeat new password">
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="telephone" placeholder="Enter phone" value="<?= htmlspecialchars($editingUser->getTelephone()) ?>">
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select name="user_type" required>
                                <option value="candidat" <?= ($editingUser->getRole()=='candidat')?'selected':'' ?>>Candidate</option>
                                <option value="recruteur" <?= ($editingUser->getRole()=='recruteur')?'selected':'' ?>>Employer</option>
                                <option value="admin" <?= ($editingUser->getRole()=='admin')?'selected':'' ?>>Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-submit">Update User</button>
                        <a href="?" class="action-btn cancel">Cancel Edit</a>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<script>
function showPage(pageId) {
    // Hide all pages
    document.querySelectorAll('.page').forEach(page => {
        page.classList.remove('active');
    });
    
    // Show selected page
    document.getElementById(pageId).classList.add('active');
    
    // Update sidebar active states
    document.querySelectorAll('.sidebar a').forEach(link => {
        link.classList.remove('active');
    });
    
    // Set active state based on page
    if (pageId === 'userList') {
        document.querySelector('.sidebar .nav-link').classList.add('active');
    } else if (pageId === 'addUser') {
        document.querySelector('.sidebar .sublink').classList.add('active');
    } else if (pageId === 'editUser') {
        document.querySelectorAll('.sidebar .sublink')[1].classList.add('active');
    }
}

// Auto-show edit page when editing
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($editingUser): ?>
        showPage('editUser');
    <?php endif; ?>
});
</script>

</body>
</html>