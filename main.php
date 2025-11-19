<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AbleLink — Expériences Inclusives</title>
    <style>
        :root {
            --bg-0: #07070a;
            --bg-1: #141426;
            --accent: #4bb1ff;
            --glass: rgba(255, 255, 255, 0.08);
            --text: #f7f8fb;
            --muted: rgba(247, 248, 251, 0.7);
            --success: #2ecc71;
            --danger: #ff6b6b;
            --warning: #ffd166;
            --radius: 16px;
            --e: .28s cubic-bezier(.2, .9, .3, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--bg-0) 0%, var(--bg-1) 100%);
            min-height: 100vh;
            overflow-x: hidden;
            color: var(--text);
            -webkit-font-smoothing: antialiased;
        }

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

        .shape:nth-child(1) { width: 120px; height: 80px; top: 20%; left: 10%; animation-delay: 0s; transform: rotate(15deg); }
        .shape:nth-child(2) { width: 90px; height: 140px; top: 60%; right: 15%; animation-delay: 2s; transform: rotate(-20deg); }
        .shape:nth-child(3) { width: 100px; height: 60px; bottom: 20%; left: 20%; animation-delay: 4s; transform: rotate(25deg); }
        .shape:nth-child(4) { width: 80px; height: 120px; top: 10%; right: 30%; animation-delay: 1s; transform: rotate(-10deg); }
        .shape:nth-child(5) { width: 110px; height: 70px; bottom: 40%; right: 20%; animation-delay: 3s; transform: rotate(30deg); }
        .shape:nth-child(6) { width: 95px; height: 95px; top: 40%; left: 5%; animation-delay: 5s; transform: rotate(-15deg); }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

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

        header { padding: 20px; }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px 45px;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: white;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
        }

        .logo-icon {
            width: 54px;
            height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(90deg, var(--accent), #7ed6ff);
            border-radius: 10px;
            font-weight: 800;
            color: #021225;
        }

        .nav-links {
            display: flex;
            gap: 20px;
            margin-right: 20px;
        }

        .nav-links a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        .role-selector {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .role-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .role-badge.admin { background: rgba(255, 107, 107, 0.2); border-color: rgba(255, 107, 107, 0.4); }
        .role-badge.company { background: rgba(77, 171, 247, 0.2); border-color: rgba(77, 171, 247, 0.4); }
        .role-badge.user { background: rgba(46, 204, 113, 0.2); border-color: rgba(46, 204, 113, 0.4); }
        .role-badge.inclusion { background: rgba(155, 89, 182, 0.2); border-color: rgba(155, 89, 182, 0.4); }

        .content-wrapper {
            min-height: calc(100vh - 300px);
            padding: 20px 0 60px;
        }

        .hero {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
            align-items: center;
            padding: 50px;
            margin-bottom: 40px;
        }

        .hero h1 {
            font-size: 2.6rem;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .cta-button {
            display: inline-block;
            padding: 14px 32px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.3);
            cursor: pointer;
        }

        .cta-button.primary {
            background: linear-gradient(90deg, var(--accent), #7ed6ff);
            color: #021225;
            border: none;
        }

        .cta-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .stat-card {
            padding: 20px;
            text-align: center;
        }

        .stat-number {
            font-size: 2.1rem;
            font-weight: bold;
        }

        .filters {
            padding: 20px;
            margin: 40px 0 20px;
        }

        .filters .input,
        .filters select {
            padding: 10px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            background: transparent;
            color: var(--text);
            width: 100%;
        }

        .dynamic-view {
            padding: 30px;
            margin-top: 30px;
        }

        footer {
            margin: 40px auto;
            padding: 20px;
            color: var(--muted);
            text-align: center;
        }

        .flash-message {
            background: rgba(46, 204, 113, 0.2);
            border: 1px solid rgba(46, 204, 113, 0.6);
            color: #d1fae5;
            padding: 14px 18px;
            border-radius: 12px;
            margin: 20px 0;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                gap: 20px;
                padding: 20px 25px;
            }

            .nav-links {
                flex-direction: column;
                align-items: center;
            }

            .hero {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="bg-shapes" aria-hidden="true">
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
                <div class="logo" onclick="window.location.href='/ablelink/'">
                    <div class="logo-icon">AL</div>
                    <span>AbleLink — Plateforme Inclusive</span>
                </div>
                <div style="display:flex; align-items:center;">
                    <?php $adminLogged = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true; ?>
                    <div class="nav-links">
                        <a href="/ablelink/" class="<?php echo ($_GET['url'] ?? '') === '' ? 'active' : ''; ?>">Accueil</a>
                        <a href="/ablelink/stories" class="<?php echo (isset($_GET['url']) && strpos($_GET['url'], 'stories') === 0) ? 'active' : ''; ?>">Success Stories</a>
                        <a href="/ablelink/stories/create">Partager</a>
                        <?php if($adminLogged): ?>
                            <a href="/ablelink/admin" class="<?php echo (isset($_GET['url']) && strpos($_GET['url'], 'admin') === 0) ? 'active' : ''; ?>">Dashboard</a>
                        <?php else: ?>
                            <a href="/ablelink/admin/login" class="<?php echo (isset($_GET['url']) && strpos($_GET['url'], 'admin/login') === 0) ? 'active' : ''; ?>">Admin</a>
                        <?php endif; ?>
                    </div>
                    <div class="role-selector">
                        <select id="roleSelect" class="cta-button" style="border-radius:12px; border:1px solid rgba(255,255,255,0.3);" onchange="changeUserRole()">
                            <option value="user">Utilisateur</option>
                            <option value="company">Entreprise</option>
                            <option value="inclusion">Responsable Inclusion</option>
                            <option value="admin">Administrateur</option>
                        </select>
                        <div id="roleBadge" class="role-badge user">Utilisateur</div>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="content-wrapper">
            <section class="hero glass">
                <div>
                    <h1>Partageons les success stories qui inspirent l’inclusion</h1>
                    <p>
                        AbleLink réunit vos réussites, vos parcours et vos retours d’expérience pour inspirer les
                        personnes en situation de handicap et sensibiliser les organisations.
                    </p>
                    <a href="/ablelink/stories/create" class="cta-button primary">+ Publier ma story</a>
                </div>
                <div class="hero-image" aria-hidden="true">
                    <div style="width:100%; max-width:420px; height:280px; background:rgba(255,255,255,0.08); border-radius:20px; display:flex; align-items:center; justify-content:center; font-size:48px;">
                        🌟
                    </div>
                </div>
            </section>

            <section class="stats">
                <div class="stat-card glass">
                    <div class="stat-number"><?php echo isset($stats['total_stories']) ? (int)$stats['total_stories'] : 0; ?></div>
                    <div class="stat-label">Stories partagées</div>
                </div>
                <div class="stat-card glass">
                    <div class="stat-number"><?php echo isset($stats['total_comments']) ? (int)$stats['total_comments'] : 0; ?></div>
                    <div class="stat-label">Commentaires</div>
                </div>
                <div class="stat-card glass">
                    <div class="stat-number"><?php echo isset($stats['total_likes']) ? (int)$stats['total_likes'] : 0; ?></div>
                    <div class="stat-label">Likes</div>
                </div>
                <div class="stat-card glass">
                    <div class="stat-number"><?php echo isset($stats['total_members']) ? (int)$stats['total_members'] : 12; ?></div>
                    <div class="stat-label">Ambassadeurs</div>
                </div>
            </section>

            <section class="filters glass">
                <div style="display:flex; gap:12px; flex-wrap:wrap;">
                    <input class="input" type="search" placeholder="Rechercher..." style="flex:1; min-width:200px;">
                    <select class="input" style="flex:1; min-width:180px;">
                        <option value="">Tous les modules</option>
                        <option value="stories">Success Stories</option>
                        <option value="events">Événements</option>
                    </select>
                    <select class="input" style="flex:1; min-width:180px;">
                        <option value="">Tous les statuts</option>
                        <option value="actif">Actifs</option>
                        <option value="archive">Archivés</option>
                    </select>
                </div>
                <div style="display:flex; gap:10px; margin-top:16px; flex-wrap:wrap;">
                    <button class="cta-button" onclick="filterBy('all')">Tous</button>
                    <button class="cta-button" onclick="filterBy('active')">Actifs</button>
                    <button class="cta-button" onclick="filterBy('featured')">À la une</button>
                </div>
            </section>

            <?php if(isset($_SESSION['flash'])): ?>
                <div class="flash-message">
                    <?php echo htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?>
                </div>
            <?php endif; ?>

            <section class="glass dynamic-view" id="dynamicContent">
                <?php 
                if (isset($view) && file_exists($view)) {
                    include $view; 
                } else {
                    echo "<p>Erreur: Vue non trouvée</p>";
                }
                ?>
            </section>
        </div>
    </div>

    <div class="container">
        <footer class="glass">
            <div style="display:flex; flex-direction:column; gap:12px; align-items:center; text-align:center;">
                <div style="display:flex; gap:18px; flex-wrap:wrap; justify-content:center;">
                    <a href="/ablelink/" style="color:var(--muted); text-decoration:none;">Accueil</a>
                    <a href="/ablelink/stories" style="color:var(--muted); text-decoration:none;">Success Stories</a>
                    <a href="#" style="color:var(--muted); text-decoration:none;">Politique de confidentialité</a>
                    <a href="#" style="color:var(--muted); text-decoration:none;">Conditions d’utilisation</a>
                </div>
                <div>&copy; 2025 AbleLink. Tous droits réservés.</div>
            </div>
        </footer>
    </div>

    <script>
        function changeUserRole() {
            var roleSelect = document.getElementById('roleSelect');
            var newRole = roleSelect.value;
            var roleBadge = document.getElementById('roleBadge');

            roleBadge.className = 'role-badge ' + newRole;
            roleBadge.textContent =
                newRole === 'user' ? 'Utilisateur' :
                newRole === 'company' ? 'Entreprise' :
                newRole === 'inclusion' ? 'Responsable Inclusion' : 'Administrateur';

            var createLinks = document.querySelectorAll('.cta-button.primary');
            createLinks.forEach(function(btn) {
                btn.style.display = newRole === 'user' ? 'none' : 'inline-block';
            });
        }

        function filterBy(type) {
            console.log('Filtrer par:', type);
        }

        document.addEventListener('DOMContentLoaded', function() {
            changeUserRole();
        });
    </script>
    <script src="/ablelink/public/js/main.js"></script>
</body>
</html>