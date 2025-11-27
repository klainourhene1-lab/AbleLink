<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once "../config.php";
require_once "../controllers/UserController.php";

$controller = new UserController();
$user = $controller->showUser($_SESSION['user_id']);
$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

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
    } elseif ($email !== $user->getEmail() && $controller->emailExists($email)) {
        $errors['email'] = "Email already exists";
    }

    // Password change validation
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $errors['current_password'] = "Current password is required to change password";
        } elseif (!$user->verifyPassword($current_password)) {
            $errors['current_password'] = "Current password is incorrect";
        } elseif (strlen($new_password) < 6) {
            $errors['new_password'] = "New password must be at least 6 characters";
        } elseif ($new_password !== $confirm_password) {
            $errors['confirm_password'] = "New passwords do not match";
        }
    }

    if (empty($errors)) {
        // Update user object
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setEmail($email);
        $user->setTelephone($telephone);
        
        // Update password if provided
        if (!empty($new_password)) {
            $user->setMotDePasse($new_password);
        }

        if ($controller->updateUser($user, $_SESSION['user_id'])) {
            $success = "Profile updated successfully!";
            // Refresh user data
            $user = $controller->showUser($_SESSION['user_id']);
            // Update session
            $_SESSION['user_email'] = $user->getEmail();
            $_SESSION['user_nom'] = $user->getNom();
            $_SESSION['user_prenom'] = $user->getPrenom();
        } else {
            $errors['general'] = "Error updating profile. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - AbleLink</title>
    <style>
        @charset "utf-8";
        /*

        TemplateMo 592 glossy touch

        https://templatemo.com/tm-592-glossy-touch

        */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            button[type="submit"] {
                font-family: inherit;
                font-size: 16px;
            }
        }
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(
                135deg,
                #0c0c0c 0%,
                #1a1a2e 15%,
                #16213e 35%,
                #0f3460 50%,
                #533a7d 70%,
                #8b5a8c 85%,
                #a0616a 100%
            );
            min-height: 100vh;
            overflow-x: hidden;
        }
        /* Animated background elements */
        .bg-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.08);
            animation: float 6s ease-in-out infinite;
            box-shadow: 0 8px 32px rgba(255, 255, 255, 0.1);
        }
        .shape:nth-child(1) {
            width: 120px;
            height: 80px;
            border-radius: 15px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
            transform: rotate(15deg);
        }
        .shape:nth-child(2) {
            width: 90px;
            height: 140px;
            border-radius: 12px;
            top: 60%;
            right: 15%;
            animation-delay: 2s;
            transform: rotate(-20deg);
        }
        .shape:nth-child(3) {
            width: 100px;
            height: 60px;
            border-radius: 10px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
            transform: rotate(25deg);
        }
        .shape:nth-child(4) {
            width: 80px;
            height: 120px;
            border-radius: 8px;
            top: 10%;
            right: 30%;
            animation-delay: 1s;
            transform: rotate(-10deg);
        }
        .shape:nth-child(5) {
            width: 110px;
            height: 70px;
            border-radius: 14px;
            bottom: 40%;
            right: 20%;
            animation-delay: 3s;
            transform: rotate(30deg);
        }
        .shape:nth-child(6) {
            width: 95px;
            height: 95px;
            border-radius: 20px;
            top: 40%;
            left: 5%;
            animation-delay: 5s;
            transform: rotate(-15deg);
        }
        @keyframes float {
            0%,
            100% {
                transform: translateY(0px) rotate(var(--start-rotation, 0deg));
            }
            50% {
                transform: translateY(-20px)
                    rotate(calc(var(--start-rotation, 0deg) + 180deg));
            }
        }
        /* Glass container styles */
        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .glass:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-5px);
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.15);
        }
        /* Header styles */
        header {
            padding: 20px;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        /* Navigation styles */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px 45px;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            color: white;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
        }
        .logo-icon {
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-icon svg {
            width: 100%;
            height: 100%;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.3));
            transition: all 0.3s ease;
        }
        .logo:hover .logo-icon svg {
            transform: scale(1.1) translateY(-2px);
            filter: drop-shadow(0 4px 12px rgba(255, 255, 255, 0.4));
        }
        .nav-links {
            display: flex;
            gap: 30px;
        }
        .nav-links a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 12px 24px 13px 24px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.6);
        }
        .nav-links a:hover,
        .nav-links a.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        /* Profile Specific Styles */
        .profile-container {
            margin-top: 40px;
            padding: 20px 0;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .profile-header h1 {
            font-size: 2.8rem;
            color: white;
            margin-bottom: 20px;
            text-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            line-height: 1.2;
        }

        .profile-header p {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .profile-card {
            padding: 60px 40px;
            color: white;
            max-width: 800px;
            margin: 0 auto;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            margin: 0 auto 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: bold;
            color: white;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .user-info {
            text-align: center;
            margin-bottom: 40px;
        }

        .user-info h2 {
            font-size: 2rem;
            margin-bottom: 10px;
            color: white;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .user-info .role {
            display: inline-block;
            padding: 8px 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .form-section {
            margin-bottom: 40px;
        }

        .form-section h3 {
            color: white;
            font-size: 1.5rem;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 10px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            color: white;
            font-size: 16px;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.1);
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .btn-group {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .cta-button {
            display: inline-block;
            padding: 15px 40px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.3);
            cursor: pointer;
            text-align: center;
        }

        .cta-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .btn-danger {
            background: rgba(220, 53, 69, 0.3);
            border-color: rgba(220, 53, 69, 0.5);
        }

        .btn-danger:hover {
            background: rgba(220, 53, 69, 0.4);
        }

        /* Messages */
        .success {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            border-left: 4px solid #28a745;
            text-align: center;
            font-weight: 500;
        }

        .error {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            border-left: 4px solid #dc3545;
            text-align: center;
            font-weight: 500;
        }

        .field-error {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 8px;
            display: block;
            font-weight: 500;
        }

        /* Footer styles */
        #footer {
            margin-top: 60px;
            padding: 30px 0;
        }
        .footer-content {
            padding: 30px;
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
        }
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
            padding: 5px 10px;
        }
        .footer-links a:hover {
            color: white;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }
        .copyright {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
        }
        .copyright a {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
        }

        /* Responsive design */
        @media (max-width: 1024px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .profile-container {
                margin-top: 20px;
            }

            .profile-header h1 {
                font-size: 2.2rem;
            }

            .profile-card {
                padding: 40px 25px;
            }

            .profile-avatar {
                width: 100px;
                height: 100px;
                font-size: 2rem;
            }

            nav {
                flex-direction: column;
                gap: 20px;
                padding: 20px 30px;
            }

            .nav-links {
                gap: 15px;
                justify-content: center;
                flex-direction: column;
                align-items: center;
            }

            .nav-links a {
                display: block;
                text-align: center;
                width: 100%;
                max-width: 200px;
                font-size: 14px;
                padding: 10px 20px;
            }

            .btn-group {
                flex-direction: column;
                align-items: center;
            }

            .cta-button {
                width: 100%;
                max-width: 300px;
            }

            .logo-icon {
                width: 40px;
                height: 40px;
            }

            .logo {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="bg-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <header>
        <div class="container">
            <nav class="glass">
                <div class="logo" onclick="window.location.href='index.php'">
                    <div class="logo-icon">
                        <img src="Screenshot_2025-11-01_132947-removebg-preview.png" 
                             alt="AbleLink Logo" 
                             style="width:400px; height:auto;margin-left: 200px;">
                    </div>
                </div>

                <div class="nav-links">
                   <a href="index.php">Home</a>
                    <a href="index.php#about">About</a>
                    <a href="index.php#services">Services</a>
                    <a href="index.php#contact">Contact</a>
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                        <a href="index1.php">Dashboard</a>
                    <?php endif; ?>
                    <a href="profile.php" class="active">My Profile</a>
                    <a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['user_prenom']); ?>)</a>
                </div>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="profile-container">
            <div class="profile-header">
                <h1>My Profile</h1>
                <p>Manage your personal information and account settings</p>
            </div>

            <?php if ($success): ?>
                <div class="success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (isset($errors['general'])): ?>
                <div class="error"><?php echo $errors['general']; ?></div>
            <?php endif; ?>

            <div class="profile-card glass">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($user->getPrenom(), 0, 1) . substr($user->getNom(), 0, 1)); ?>
                </div>

                <div class="user-info">
                    <h2><?php echo htmlspecialchars($user->getPrenom() . ' ' . $user->getNom()); ?></h2>
                    <span class="role"><?php echo htmlspecialchars($user->getRole()); ?></span>
                </div>

                <form method="POST" action="">
                    <div class="form-section">
                        <h3>Personal Information</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="prenom">First Name</label>
                                <input type="text" id="prenom" name="prenom" 
                                       value="<?php echo htmlspecialchars($user->getPrenom()); ?>" 
                                       required>
                                <?php if (isset($errors['prenom'])): ?>
                                    <span class="field-error"><?php echo $errors['prenom']; ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="nom">Last Name</label>
                                <input type="text" id="nom" name="nom" 
                                       value="<?php echo htmlspecialchars($user->getNom()); ?>" 
                                       required>
                                <?php if (isset($errors['nom'])): ?>
                                    <span class="field-error"><?php echo $errors['nom']; ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group full-width">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user->getEmail()); ?>" 
                                       required>
                                <?php if (isset($errors['email'])): ?>
                                    <span class="field-error"><?php echo $errors['email']; ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group full-width">
                                <label for="telephone">Phone Number</label>
                                <input type="text" id="telephone" name="telephone" 
                                       value="<?php echo htmlspecialchars($user->getTelephone()); ?>" 
                                       placeholder="Enter your phone number">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Change Password</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input type="password" id="current_password" name="current_password" 
                                       placeholder="Enter current password">
                                <?php if (isset($errors['current_password'])): ?>
                                    <span class="field-error"><?php echo $errors['current_password']; ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" id="new_password" name="new_password" 
                                       placeholder="Enter new password">
                                <?php if (isset($errors['new_password'])): ?>
                                    <span class="field-error"><?php echo $errors['new_password']; ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" 
                                       placeholder="Confirm new password">
                                <?php if (isset($errors['confirm_password'])): ?>
                                    <span class="field-error"><?php echo $errors['confirm_password']; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="cta-button">Update Profile</button>
                        <a href="index.php" class="cta-button btn-secondary">Back to Home</a>
                        <?php if ($_SESSION['user_role'] !== 'admin'): ?>
                            <a href="delete_account.php" class="cta-button btn-danger" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">Delete Account</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="footer">
        <div class="container">
            <footer class="glass">
                <div class="footer-content">
                    <div class="footer-links">
                        <a href="index.php#about">About Us</a>
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Service</a>
                        <a href="#">XML Sitemap</a>
                        <a href="index.php#contact">Contact</a>
                    </div>
                    <div class="copyright">
                        <p>&copy; 2025 AbleLink. All rights reserved. Empowering inclusive employment. Crafted with modern web technologies.</p>
                        Provided by <a rel="nofollow" href="#" target="_blank">AbleLink</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>