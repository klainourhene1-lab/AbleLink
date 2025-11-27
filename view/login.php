<?php
session_start();
require_once __DIR__ . '/../controllers/UserController.php';

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new UserController();
    
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }
    
    if (empty($password)) {
        $errors['password'] = "Password is required";
    }
    
    if (empty($errors)) {
        $user = $controller->login($email, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_email'] = $user->getEmail();
            $_SESSION['user_role'] = $user->getRole();
            $_SESSION['user_nom'] = $user->getNom();
            $_SESSION['user_prenom'] = $user->getPrenom();
            
            // Remember me functionality
            if (isset($_POST['remember'])) {
                setcookie('user_email', $email, time() + (30 * 24 * 60 * 60), "/");
            }
            
            // Redirect based on role
            if ($user->getRole() === 'admin') {
                header('Location: index1.php');
                exit;
            } else {
                header('Location: profile.php');
                exit;
            }
        } else {
            $errors['general'] = "Invalid email or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - AbleLink</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .error { color: red; font-size: 14px; margin-top: 5px; }
        .success { 
            color: green; 
            font-size: 16px; 
            margin-bottom: 15px; 
            padding: 12px; 
            background: #d4edda; 
            border: 1px solid #c3e6cb; 
            border-radius: 5px; 
            text-align: center;
            font-weight: bold;
        }
        .form-group { margin-bottom: 15px; }
        .demo-accounts { 
            background: rgba(255,255,255,0.1); 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            border-left: 4px solid #00ffff;
            color: white;
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
                        <img src="Screenshot_2025-11-01_132947-removebg-preview.png" alt="AbleLink Logo" style="width:400px; height:auto;margin-left: 200px;">
                    </div>
                </div>
                <div class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="index.php#about">About</a>
                    <a href="index.php#services">Services</a>
                    <a href="index.php#contact">Contact</a>
                    <a href="login.php" class="active">Sign In</a>
                    <a href="register.php">Sign Up</a>
                </div>
            </nav>
        </div>
    </header>

    <div class="page active">
        <div class="container">
            <div class="content-wrapper">
                <section class="auth-form glass">
                    <h2>Welcome Back</h2>
                    <p>Sign in to your AbleLink account</p>

                    <?php if (!empty($success)): ?>
                        <div class="success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <?php if (isset($errors['general'])): ?>
                        <div class="error" style="margin-bottom: 15px; padding: 10px; background: rgba(255,107,107,0.2); border: 1px solid rgba(255,107,107,0.3); border-radius: 5px; color: #ff6b6b;">
                            <?php echo $errors['general']; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Demo Accounts Info -->
                    <div class="demo-accounts">
                        <strong>Demo Accounts:</strong><br>
                        Admin: admin@ablelink.com / password
                    </div>

                    <form method="POST" action="" id="loginForm">
                        <div class="form-group">
                            <label for="login-email">Email Address</label>
                            <input type="text" id="login-email" name="email" placeholder="Enter your email" 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : (isset($_COOKIE['user_email']) ? htmlspecialchars($_COOKIE['user_email']) : ''); ?>">
                            <?php if (isset($errors['email'])): ?>
                                <div class="error"><?php echo $errors['email']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="login-password">Password</label>
                            <input type="password" id="login-password" name="password" placeholder="Enter your password">
                            <?php if (isset($errors['password'])): ?>
                                <div class="error"><?php echo $errors['password']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-options">
                            <label class="checkbox">
                                <input type="checkbox" name="remember" <?php echo (isset($_COOKIE['user_email']) ? 'checked' : ''); ?>>
                                Remember me
                            </label>
                            <a href="#" class="forgot-password">Forgot password?</a>
                        </div>

                        <button type="submit" class="cta-button">Sign In</button>

                        <div class="auth-switch">
                            Don't have an account? <a href="register.php">Sign up here</a>
                        </div>
                    </form>
                </section>
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
                        <p>&copy; 2025 AbleLink. All rights reserved.</p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>