<style>
    .auth-hero {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        padding: 60px 0 80px;
        min-height: 100vh;
        display: flex;
        align-items: center;
    }
    
    .auth-card {
        background: linear-gradient(180deg, rgba(15,23,42,0.95), rgba(30,41,59,0.9));
        border-radius: 24px;
        padding: 50px;
        border: 1px solid rgba(255,255,255,0.08);
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        max-width: 480px;
        margin: 0 auto;
    }
    
    .auth-card h2 {
        color: #fff;
        font-size: 32px;
        font-weight: 800;
        margin-bottom: 10px;
        text-align: center;
    }
    
    .auth-card p {
        color: #94a3b8;
        text-align: center;
        margin-bottom: 30px;
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-group label {
        color: #e2e8f0;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 8px;
        display: block;
    }
    
    .form-control {
        background-color: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #e2e8f0;
        border-radius: 12px;
        padding: 14px 18px;
        width: 100%;
        font-size: 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        background-color: rgba(15, 23, 42, 0.9);
        border-color: #8b5cf6;
        color: #fff;
        box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15);
        outline: none;
    }
    
    .form-control::placeholder {
        color: #64748b;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 16px 24px;
        font-weight: 700;
        font-size: 16px;
        width: 100%;
        transition: all 0.3s ease;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
        box-shadow: 0 10px 30px rgba(139, 92, 246, 0.4);
        transform: translateY(-2px);
    }
    
    .alert {
        padding: 15px 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        font-size: 14px;
        font-weight: 500;
    }
    
    .alert-danger {
        background: rgba(239, 68, 68, 0.15);
        border: 1px solid #ef4444;
        color: #fca5a5;
    }
    
    .auth-footer {
        text-align: center;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
    }
    
    .auth-footer a {
        color: #8b5cf6;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s ease;
    }
    
    .auth-footer a:hover {
        color: #a78bfa;
        text-decoration: underline;
    }
    
    .auth-logo {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .auth-logo h3 {
        color: #8b5cf6;
        font-size: 28px;
        font-weight: 900;
        margin: 0;
    }
</style>

<section class="auth-hero">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="auth-card">
                    <div class="auth-logo">
                        <h3>AbleLink</h3>
                    </div>
                    
                    <h2>Créer un compte</h2>
                    <p>Rejoignez la communauté AbleLink</p>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="/projetweb/ablelink/auth/do-register">
                        <div class="form-group">
                            <label for="name">
                                <i class="fas fa-user"></i> Nom complet
                            </label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="name" 
                                name="name" 
                                placeholder="Votre nom" 
                                required
                                autocomplete="name"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="email">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <input 
                                type="email" 
                                class="form-control" 
                                id="email" 
                                name="email" 
                                placeholder="votre@gmail.com" 
                                required
                                autocomplete="email"
                            >
                            <small style="color: #64748b; font-size: 12px; margin-top: 5px; display: block;">
                                Utilisez de préférence une adresse Gmail
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">
                                <i class="fas fa-lock"></i> Mot de passe
                            </label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="password" 
                                name="password" 
                                placeholder="••••••••" 
                                required
                                autocomplete="new-password"
                                minlength="6"
                            >
                            <small style="color: #64748b; font-size: 12px; margin-top: 5px; display: block;">
                                Minimum 6 caractères
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">
                                <i class="fas fa-lock"></i> Confirmer le mot de passe
                            </label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="confirm_password" 
                                name="confirm_password" 
                                placeholder="••••••••" 
                                required
                                autocomplete="new-password"
                                minlength="6"
                            >
                        </div>
                        
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-user-plus"></i> Créer mon compte
                        </button>
                    </form>
                    
                    <div class="auth-footer">
                        <p style="color: #94a3b8; margin: 0;">
                            Vous avez déjà un compte? 
                            <a href="/projetweb/ablelink/auth/login">Se connecter</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
