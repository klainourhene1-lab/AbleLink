<?php
session_start();
require_once __DIR__ . '/../../Controller/UserController.php';

$error = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    
    if (empty($email)) {
        $error = "Veuillez entrer votre email";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format d'email invalide";
    } else {
        $controller = new UserController();
        
        // Vérifier si l'email existe
        if (!$controller->emailExists($email)) {
            $error = "Cet email n'est pas associé à un compte";
        } else {
            // Créer un token d'envoi
            $token = $controller->createResetToken($email);
            
            if ($token) {
                // Envoyer l'email avec le lien de réinitialisation
                if ($controller->sendPasswordResetEmail($email, $token)) {
                    $success = true;
                    
                   
                } else {
                    $error = "Erreur lors de l'envoi de l'email. Veuillez réessayer.";
                }
            } else {
                $error = "Erreur technique. Veuillez réessayer.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié - AbleLink</title>
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
        
        .container {
            background: rgba(16, 0, 40, 0.95);
            border: 1px solid #2a2144;
            padding: 40px;
            border-radius: 14px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0,0,0,0.35);
        }
        
        h2 {
            color: #fff;
            text-align: center;
            margin-bottom: 20px;
        }
        
        p {
            color: #adadad;
            text-align: center;
            margin-bottom: 25px;
        }
        
        .form-control {
            width: 100%;
            background: transparent;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.4);
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-family: 'Josefin Sans', sans-serif;
        }
        
        .form-control::placeholder {
            color: #777;
        }
        
        .site-btn {
            width: 100%;
            border: none;
            background: #00bfe7;
            padding: 14px;
            border-radius: 6px;
            color: #fff;
            cursor: pointer;
            transition: 0.3s;
            font-family: 'Josefin Sans', sans-serif;
            font-weight: 600;
            font-size: 16px;
        }
        
        .site-btn:hover {
            background: #0099c7;
        }
        
        .error {
            background: rgba(255,107,107,0.2);
            border: 1px solid rgba(255,107,107,0.3);
            color: #ff6b6b;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .success {
            background: rgba(0,191,231,0.2);
            border: 1px solid rgba(0,191,231,0.3);
            color: #00bfe7;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .debug-info {
            background: rgba(255,255,255,0.1);
            border: 1px dashed rgba(255,255,255,0.3);
            color: #ccc;
            padding: 12px;
            border-radius: 6px;
            margin-top: 20px;
            font-size: 12px;
            word-break: break-all;
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Mot de passe oublié</h2>
        <p>Entrez votre email pour recevoir un lien de réinitialisation</p>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success">
                ✔ Email envoyé !<br>
                Vérifiez votre boîte de réception (et vos spams) pour le lien de réinitialisation.
            </div>
            
            <?php 
            // Show debug link on localhost
            if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false && isset($_SESSION['debug_reset_link'])): 
            ?>
                <div class="debug-info">
                    <strong>🔧 Mode Développement (localhost):</strong><br>
                    <a href="<?php echo $_SESSION['debug_reset_link']; ?>" target="_blank" style="color: #00bfe7; word-break: break-all;">
                        <?php echo $_SESSION['debug_reset_link']; ?>
                    </a>
                </div>
            <?php endif; ?>
           
            <div class="back-link">
                <a href="./signin.php">← Retour à la connexion</a>
            </div>
        <?php else: ?>
            <form method="POST">
                <input type="email" 
                       name="email" 
                       class="form-control" 
                       placeholder="votre@email.com" 
                       required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                
                <button type="submit" class="site-btn">Envoyer le lien</button>
            </form>
            
            <div class="back-link">
                <a href="./signin.php">← Retour à la connexion</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
