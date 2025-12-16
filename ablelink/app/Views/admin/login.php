<style>
.admin-login-card { background: rgba(30,41,59,0.7); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; box-shadow: 0 12px 28px rgba(0,0,0,0.35); padding: 22px; }
.admin-login-title { font-size: 26px; font-weight: 800; color: #fff; display:flex; align-items:center; gap:10px; margin-bottom: 14px; }
.admin-login-title i { color:#0ea5e9; }
.admin-help { font-size:12px; color:#94a3b8; text-align:center; margin-top:16px; }
.admin-error { background: rgba(239,68,68,0.15); border:1px solid #ef4444; color:#ef4444; padding:10px; border-radius:10px; margin-bottom:12px; }
.admin-back { text-align:center; margin-top:12px; }
.admin-back a { color:#0ea5e9; text-decoration:none; }
.admin-back a:hover { color:#fff; }
</style>
<div class="container" style="padding-top:40px; padding-bottom:40px;">
  <div class="row justify-content-center">
      <div class="col-lg-5 col-md-7">
      <div class="admin-login-card">
        <div class="admin-login-title"><i class="fa fa-lock"></i> Connexion Admin</div>
        <?php if (!empty($error)): ?>
          <div class="admin-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="/projetweb/ablelink/admin-login">
          <div class="form-group" style="margin-bottom:12px;">
            <label style="color:#e2e8f0; font-weight:600;">Nom d'utilisateur</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="form-group" style="margin-bottom:12px;">
            <label style="color:#e2e8f0; font-weight:600;">Mot de passe</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button type="submit" class="site-btn" style="width:100%;">Connexion</button>
        </form>
        <div class="admin-back"><a href="/projetweb/ablelink/">← Retour à l'accueil</a></div>
        <div class="admin-help">Identifiants de test: admin / admin123</div>
      </div>
    </div>
  </div>
</div>
