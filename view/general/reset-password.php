<?php
session_start();
require_once __DIR__ . '/../../Control/UserController.php';

$errors = [];
$success = false;
$validToken = false;
$email = '';

// Check token
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $controller = new UserController();
    $tokenData = $controller->validateToken($token);
    
    if ($tokenData) {
        $validToken = true;
        $email = $tokenData['email'];
        $_SESSION['reset_token'] = $token;
        $_SESSION['reset_email'] = $email;
    } else {
        $errors['general'] = "Lien invalide ou expiré. Veuillez demander un nouveau lien.";
    }
} else {
    $errors['general'] = "Lien de réinitialisation manquant.";
}

// Process password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['reset_token'])) {
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    
    // Validation
    if (empty($password)) {
        $errors['password'] = "Veuillez entrer un mot de passe";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères";
    }
    
    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Veuillez confirmer le mot de passe";
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = "Les mots de passe ne correspondent pas";
    }
    
    if (empty($errors)) {
        $controller = new UserController();
        
        // Update password and mark token as used
        if ($controller->updateUserPassword($_SESSION['reset_email'], $password, $_SESSION['reset_token'])) {
            // Clear session
            unset($_SESSION['reset_token']);
            unset($_SESSION['reset_email']);
            
            $success = true;
        } else {
            $errors['general'] = "Erreur lors de la réinitialisation. Veuillez réessayer.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation du mot de passe - AbleLink</title>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: #100028;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            font-family: 'Josefin Sans', sans-serif;
        }
        
        .reset-container {
            background: rgba(16, 0, 40, 0.95);
            border: 1px solid #2a2144;
            border-radius: 14px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.35);
        }
        
        .reset-container h2 {
            color: #fff;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .form-control {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.5);
            height: 45px;
            border-radius: 6px;
            color: #fff;
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            font-family: 'Josefin Sans', sans-serif;
        }
        
        .form-control::placeholder {
            color: #777;
        }
        
        .site-btn {
            background: #00bfe7;
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 6px;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Josefin Sans', sans-serif;
            font-size: 16px;
        }
        
        .site-btn:hover {
            background: #0099c7;
        }
        
        .error {
    color: #ff6b6b;
    margin-top: 0px;
    margin-bottom: 15px;   /* ✔ يخليها تحت input ومتبعدها */
    font-size: 12px;
    display: block;        /* ✔ باش ما تطلعش فوق النص */
}



        
        .error-box, .success-box {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .error-box {
            background: rgba(255,107,107,0.2);
            border: 1px solid rgba(255,107,107,0.3);
            color: #ff6b6b;
        }
        
        .success-box {
            background: rgba(0,191,231,0.2);
            border: 1px solid rgba(0,191,231,0.3);
            color: #00bfe7;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #00bfe7;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
        
        .password-requirements {
            color: #adadad;
            font-size: 12px;
            margin-top: -15px;
            margin-bottom: 15px;
        }
        
    </style>
</head>
<body>
    <div class="reset-container">
        <?php if (!$validToken && !$success): ?>
            <div class="error-box">
                <?php echo $errors['general']; ?>
            </div>
            <div class="back-link">
                <a href="./forgot-password.php">Demander un nouveau lien</a>
            </div>
        <?php elseif ($success): ?>
            <h2>✓ Mot de passe réinitialisé !</h2>
            <div class="success-box">
                Votre mot de passe a été réinitialisé avec succès.
            </div>
            <div class="back-link">
                <a href="./signin.php">Se connecter avec le nouveau mot de passe</a>
            </div>
        <?php else: ?>
            <h2>Nouveau mot de passe</h2>
            <p style="color:#adadad; text-align:center;">Entrez votre nouveau mot de passe</p>
            
            <?php if (isset($errors['general'])): ?>
                <div class="error-box"><?php echo $errors['general']; ?></div>
            <?php endif; ?>
            
            <form action="" method="POST">
                <div class="mb-3">
                    <input type="password" class="form-control" name="password" 
                           placeholder="Nouveau mot de passe">
                    <?php if (isset($errors['password'])): ?>
                        <div class="error"><?php echo $errors['password']; ?></div>
                    <?php endif; ?>
                    
                </div>
                
                <div class="mb-3">
                    <input type="password" class="form-control" name="confirm_password" 
                           placeholder="Confirmer le mot de passe" >
                    <?php if (isset($errors['confirm_password'])): ?>
                        <div class="error"><?php echo $errors['confirm_password']; ?></div>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="site-btn">Réinitialiser le mot de passe</button>
            </form>
            
            <div class="back-link">
                <a href="./signin.php">← Retour à la connexion</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>