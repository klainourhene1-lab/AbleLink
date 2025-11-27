<?php 
session_start();
require_once __DIR__ . '/../controllers/UserController.php'; 

$errors = []; 
$success = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $controller = new UserController(); 

    $nom = trim($_POST['nom'] ?? ''); 
    $prenom = trim($_POST['prenom'] ?? ''); 
    $email = trim($_POST['email'] ?? ''); 
    $password = $_POST['password'] ?? ''; 
    $confirm_password = $_POST['confirm_password'] ?? ''; 
    $telephone = trim($_POST['telephone'] ?? ''); 
    $role = $_POST['user_type'] ?? 'candidat'; 

    // منع إنشاء أدمن من قبل المستخدمين العاديين
    if ($role === 'admin') {
        $errors['user_type'] = "Admin role cannot be selected during registration";
    }

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
    } elseif ($controller->emailExists($email)) {
        $errors['email'] = "Email already exists";
    }
    
    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters";
    }
    
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }
    
    if (empty($role)) {
        $errors['user_type'] = "Please select your role";
    }

    if (empty($errors)) { 
        $user = new User(); 
        $user->setNom($nom); 
        $user->setPrenom($prenom); 
        $user->setEmail($email); 
        $user->setMotDePasse($password); 
        $user->setTelephone($telephone); 
        $user->setRole($role); 
        $user->setStatut('actif'); 

        if ($controller->addUser($user)) { 
            $success = "Account created successfully! You can now login."; 
            $_POST = array(); 
        } else { 
            $errors['general'] = "Error creating account. Please try again."; 
        } 
    } 
} 
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up - AbleLink</title>
<link rel="stylesheet" href="style.css">

<style>
.error { color: red; font-size: 14px; margin-top: 5px; }
.success { color: green; font-size: 14px; margin-bottom: 15px; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; }
.form-group { margin-bottom: 15px; }
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
<a href="login.php">Sign In</a>
<a href="register.php" class="active">Sign Up</a>
</div>
</nav>
</div>
</header>

<div class="page active">
<div class="container">
<div class="content-wrapper">

<section class="auth-form glass">

<h2>Join AbleLink</h2>
<p>Create your account and start your journey</p>

<?php if ($success): ?>
<div class="success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if (isset($errors['general'])): ?>
<div class="error"><?php echo $errors['general']; ?></div>
<?php endif; ?>

<form method="POST" action="" id="registerForm">

<div class="form-group">
    <label for="register-prenom">First Name</label>
    <input type="text" id="register-prenom" name="prenom" placeholder="Enter your first name" value="<?php echo htmlspecialchars($_POST['prenom'] ?? ''); ?>">
    <?php if (isset($errors['prenom'])): ?><div class="error"><?php echo $errors['prenom']; ?></div><?php endif; ?>
</div>

<div class="form-group">
    <label for="register-nom">Last Name</label>
    <input type="text" id="register-nom" placeholder="Enter your last name" name="nom" value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>">
    <?php if (isset($errors['nom'])): ?><div class="error"><?php echo $errors['nom']; ?></div><?php endif; ?>
</div>

<div class="form-group">
    <label for="register-email">Email Address</label>
    <input type="text" id="register-email" placeholder="Enter your email address" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
    <?php if (isset($errors['email'])): ?><div class="error"><?php echo $errors['email']; ?></div><?php endif; ?>
</div>

<div class="form-group">
    <label for="register-password">Password</label>
    <input type="password" id="register-password" placeholder="Create a password" name="password">
    <?php if (isset($errors['password'])): ?><div class="error"><?php echo $errors['password']; ?></div><?php endif; ?>
</div>

<div class="form-group">
    <label for="register-confirm">Confirm Password</label>
   