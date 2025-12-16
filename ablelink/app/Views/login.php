<section class="contact spad" style="padding-top:40px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-8" style="margin:0 auto;">
                <div class="section-title" style="text-align:center; margin-bottom:20px;">
                    <h2 style="color:#a78bfa;">Connexion</h2>
                    <p>Accédez au site avec votre email et mot de passe</p>
                </div>
                <?php if (!empty($error)): ?>
                <div style="background: rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.4); color:#fecaca; border-radius:8px; padding:10px; margin-bottom:12px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>
                <form method="post" action="/projetweb/ablelink/login" style="background: rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); border-radius:12px; padding:16px;">
                    <div class="form-group" style="margin-bottom:12px;">
                        <label style="color:#94a3b8;">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="exemple@domaine.com" required style="padding:10px; border-radius:8px; border:1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.06); color:#e2e8f0;">
                    </div>
                    <div class="form-group" style="margin-bottom:12px;">
                        <label style="color:#94a3b8;">Mot de passe</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required style="padding:10px; border-radius:8px; border:1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.06); color:#e2e8f0;">
                    </div>
                    <button type="submit" class="site-btn">Se connecter</button>
                </form>
            </div>
        </div>
    </div>
</section>
