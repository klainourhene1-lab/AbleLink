<?php
$pageTitle = "Connexion - AbleLink";
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-section set-bg" style="background-image: url('/projetttwebbbbbbbbb/assets/img/breadcrumb-bg.jpg');">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="breadcrumb__text">
                    <h2>Connexion</h2>
                    <div class="breadcrumb__option">
                        <span>Bienvenue sur AbleLink</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- Login Section Begin -->
<section class="login-section spad">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="login-container">
                    <div class="login-header">
                        <h3>Connectez-vous</h3>
                        <p>Accédez à votre espace personnel</p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fa fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="?controller=auth&action=login" class="login-form">
                        <div class="form-group">
                            <label for="username">
                                <i class="fa fa-envelope"></i> Email
                            </label>
                            <input 
                                type="email" 
                                class="form-control" 
                                id="username" 
                                name="username" 
                                placeholder="Entrez votre email"
                                required
                                autofocus
                                value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                            >
                        </div>

                        <div class="form-group">
                            <label for="password">
                                <i class="fa fa-lock"></i> Mot de passe
                            </label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password" 
                                name="password" 
                                placeholder="Entrez votre mot de passe"
                                required
                            >
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">
                                Se souvenir de moi
                            </label>
                        </div>

                        <button type="submit" class="primary-btn btn-block">
                            <i class="fa fa-sign-in"></i> Se connecter
                        </button>
                    </form>

                    <div class="login-footer">
                        <p>Vous n'avez pas de compte ? <a href="#">Inscrivez-vous</a></p>
                        <p><a href="#">Mot de passe oublié ?</a></p>
                    </div>

                    <div class="login-info">
                        <div class="info-box">
                            <i class="fa fa-info-circle"></i>
                            <div class="info-content">
                                <strong>Utilisateurs de test :</strong>
                                <ul>
                                    <li><strong>Admin :</strong> admin@ablelink.com</li>
                                    <li><strong>Utilisateur :</strong> klai12345@gmail.com</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Login Section End -->

<style>
/* Login Container Styles */
.login-section {
    padding: 80px 0;
    min-height: calc(100vh - 400px);
}

.login-container {
    background: rgba(26, 8, 61, 0.8);
    backdrop-filter: blur(15px);
    border: 2px solid rgba(0, 191, 231, 0.3);
    border-radius: 20px;
    padding: 50px 40px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
    position: relative;
    overflow: hidden;
}

.login-container:before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(0, 191, 231, 0.1) 0%, transparent 70%);
    animation: pulse 8s ease-in-out infinite;
}

.login-header {
    text-align: center;
    margin-bottom: 35px;
    position: relative;
    z-index: 1;
}

.login-header h3 {
    color: #ffffff;
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 10px;
    text-shadow: 0 2px 10px rgba(0, 191, 231, 0.4);
}

.login-header p {
    color: #d0d0d0;
    font-size: 16px;
    margin: 0;
}

.login-form {
    position: relative;
    z-index: 1;
}

.login-form .form-group {
    margin-bottom: 25px;
}

.login-form label {
    color: #ffffff !important;
    font-weight: 600 !important;
    font-size: 15px !important;
    margin-bottom: 10px !important;
    display: block;
}

.login-form label i {
    color: #00bfe7;
    margin-right: 8px;
}

.login-form input.form-control {
    background: rgba(255, 255, 255, 0.95) !important;
    border: 2px solid rgba(0, 191, 231, 0.3) !important;
    color: #111 !important;
    font-size: 15px !important;
    padding: 14px 18px !important;
    border-radius: 10px !important;
    transition: all 0.3s ease;
    width: 100%;
}

.login-form input.form-control:focus {
    background: #ffffff !important;
    border-color: #00bfe7 !important;
    outline: none;
    box-shadow: 0 0 20px rgba(0, 191, 231, 0.4);
    transform: translateY(-2px);
}

.login-form input.form-control::placeholder {
    color: #999;
}

.form-check {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 25px;
}

.form-check-input {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.form-check-label {
    color: #d0d0d0 !important;
    font-size: 14px !important;
    font-weight: 400 !important;
    margin: 0 !important;
    cursor: pointer;
}

.btn-block {
    width: 100%;
    padding: 16px 32px !important;
    font-size: 16px !important;
    margin-top: 10px;
}

.login-footer {
    text-align: center;
    margin-top: 30px;
    padding-top: 25px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    position: relative;
    z-index: 1;
}

.login-footer p {
    color: #d0d0d0;
    font-size: 14px;
    margin: 8px 0;
}

.login-footer a {
    color: #00bfe7;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.login-footer a:hover {
    color: #fff;
    text-shadow: 0 0 10px rgba(0, 191, 231, 0.8);
}

.login-info {
    margin-top: 30px;
    position: relative;
    z-index: 1;
}

.info-box {
    background: rgba(76, 110, 245, 0.15);
    border: 1px solid rgba(76, 110, 245, 0.3);
    border-radius: 12px;
    padding: 20px;
    display: flex;
    gap: 15px;
    align-items: flex-start;
}

.info-box i {
    color: #4c6ef5;
    font-size: 24px;
    flex-shrink: 0;
    margin-top: 2px;
}

.info-content {
    flex: 1;
}

.info-content strong {
    color: #ffffff;
    font-size: 14px;
    display: block;
    margin-bottom: 8px;
}

.info-content ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.info-content ul li {
    color: #d0d0d0;
    font-size: 13px;
    margin: 5px 0;
}

.info-content ul li strong {
    color: #00bfe7;
    display: inline;
    margin: 0;
}

/* Alert Styles */
.alert {
    position: relative;
    z-index: 1;
}

/* Responsive */
@media (max-width: 576px) {
    .login-container {
        padding: 35px 25px;
    }

    .login-header h3 {
        font-size: 26px;
    }

    .btn-block {
        padding: 14px 28px !important;
        font-size: 15px !important;
    }
}
</style>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
