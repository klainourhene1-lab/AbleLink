<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AbleLink · Backoffice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --sidebar-bg:#ffffff;
            --sidebar-border:#e9eef5;
            --sidebar-active:#6758f3;
            --text-muted:#6c7b95;
            --bg:#f4f6fb;
        }
        *{box-sizing:border-box;}
        body{
            margin:0;
            font-family:'Inter',sans-serif;
            background:var(--bg);
            color:#1f2a37;
        }
        a{text-decoration:none;color:inherit;}
        .admin-shell{
            display:flex;
            min-height:100vh;
        }
        .admin-sidebar{
            width:250px;
            background:var(--sidebar-bg);
            border-right:1px solid var(--sidebar-border);
            padding:30px 20px;
            position:sticky;
            top:0;
            height:100vh;
        }
        .brand{
            font-size:1.3rem;
            font-weight:700;
            display:flex;
            align-items:center;
            gap:10px;
            margin-bottom:40px;
            color:#3f3d56;
        }
        .brand span{
            display:inline-block;
            background:#eef2ff;
            color:#4f46e5;
            padding:8px 10px;
            border-radius:10px;
        }
        .sidebar-menu{
            display:flex;
            flex-direction:column;
            gap:6px;
        }
        .sidebar-link{
            display:flex;
            align-items:center;
            gap:12px;
            padding:12px 14px;
            border-radius:10px;
            color:#475467;
            transition:all .2s ease;
            font-weight:500;
        }
        .sidebar-link.active,
        .sidebar-link:hover{
            background:rgba(103,88,243,.12);
            color:#4f46e5;
        }
        .admin-main{
            flex:1;
            padding:30px;
            display:flex;
            flex-direction:column;
            gap:24px;
        }
        .top-bar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-wrap:wrap;
            gap:16px;
        }
        .top-bar h1{margin:0;font-size:1.6rem;}
        .user-chip{
            background:#fff;
            padding:10px 16px;
            border-radius:30px;
            display:flex;
            align-items:center;
            gap:10px;
            box-shadow:0 5px 15px rgba(15,23,42,0.08);
        }
        .flash{
            background:#ecfdf3;
            border:1px solid #86efac;
            color:#166534;
            padding:14px 18px;
            border-radius:12px;
            font-weight:600;
        }
        .content-card{
            background:#fff;
            border-radius:18px;
            padding:24px;
            box-shadow:0 20px 45px rgba(15,23,42,0.06);
        }
        table{
            width:100%;
            border-collapse:collapse;
        }
        thead{
            background:#f8fafc;
            color:#64748b;
            font-size:.85rem;
        }
        th,td{
            padding:14px 12px;
            border-bottom:1px solid #eef2f6;
            text-align:left;
        }
        .badge{
            display:inline-flex;
            align-items:center;
            gap:6px;
            padding:6px 12px;
            font-size:.8rem;
            border-radius:20px;
            background:#eef2ff;
            color:#4f46e5;
        }
        @media(max-width:960px){
            .admin-shell{flex-direction:column;}
            .admin-sidebar{
                position:relative;
                width:100%;
                height:auto;
                border-right:none;
                border-bottom:1px solid var(--sidebar-border);
                flex-direction:row;
                overflow:auto;
            }
        }
    </style>
</head>
<body>
    <?php $currentUrl = isset($_GET['url']) ? trim($_GET['url']) : ''; ?>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div class="brand"><span>AL</span> AbleLink Admin</div>
            <nav class="sidebar-menu">
                <a class="sidebar-link <?php echo $currentUrl === 'admin' ? 'active' : ''; ?>" href="/ablelink/admin"><i class="bi bi-speedometer"></i>Dashboard</a>
                <a class="sidebar-link <?php echo strpos($currentUrl, 'admin/stories') === 0 ? 'active' : ''; ?>" href="/ablelink/admin/stories"><i class="bi bi-journal-text"></i>Stories</a>
                <a class="sidebar-link <?php echo strpos($currentUrl, 'admin/comments') === 0 ? 'active' : ''; ?>" href="/ablelink/admin/comments"><i class="bi bi-chat-dots"></i>Commentaires</a>
                <a class="sidebar-link" href="/ablelink/stories" target="_blank"><i class="bi bi-box-arrow-up-right"></i>Voir le site</a>
                <a class="sidebar-link" href="/ablelink/admin/logout"><i class="bi bi-box-arrow-right"></i>Déconnexion</a>
            </nav>
        </aside>
        <main class="admin-main">
            <div class="top-bar">
                <div>
                    <p style="margin:0;color:var(--text-muted);font-size:.9rem;">Module de gestion AbleLink</p>
                    <h1><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Tableau de bord'; ?></h1>
                </div>
                <div class="user-chip">
                    <i class="bi bi-person-circle" style="font-size:1.3rem;color:#4f46e5;"></i>
                    <div>
                        <strong><?php echo isset($_SESSION['admin_username']) ? htmlspecialchars($_SESSION['admin_username']) : 'Admin'; ?></strong>
                        <div style="font-size:.8rem;color:var(--text-muted);">Administrateur</div>
                    </div>
                </div>
            </div>
            <?php if(isset($_SESSION['flash'])): ?>
                <div class="flash">
                    <?php echo htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?>
                </div>
            <?php endif; ?>
            <div class="content-card">
                <?php 
                if (isset($view) && file_exists($view)) {
                    include $view; 
                } else {
                    echo "<p>Erreur: Vue non trouvée</p>";
                }
                ?>
            </div>
        </main>
    </div>
</body>
</html>
