<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AbleLink Admin - Commentaires</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary:#7c3aed; --success:#10b981; --warning:#f59e0b; --danger:#ef4444; --dark:#0f172a; --text:#e2e8f0; --text-light:#94a3b8; --card-bg:rgba(30,41,59,.7); }
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Poppins',sans-serif;background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 100%);color:var(--text);min-height:100vh;padding:30px}
        .container{max-width:1400px;margin:0 auto}
        .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px}
        .header h1{font-size:32px;font-weight:700}
        .btn{padding:8px 16px;border-radius:8px;text-decoration:none;font-weight:600;font-size:13px;transition:.3s;display:inline-flex;align-items:center;gap:6px}
        .btn-back{background:#0ea5e9;color:#fff}
        .btn-action{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.15);color:#e2e8f0}
        .btn-action:hover{background:rgba(255,255,255,.12)}
        .card{background:var(--card-bg);backdrop-filter:blur(10px);border-radius:16px;padding:20px;border:1px solid rgba(255,255,255,.1)}
        table{width:100%;border-collapse:collapse}
        th,td{padding:12px;border-bottom:1px solid rgba(255,255,255,.1);vertical-align:top}
        th{color:#94a3b8;text-align:left;font-weight:600}
        .badge{padding:5px 10px;border-radius:12px;font-size:12px;font-weight:600}
        .badge-reported{background:rgba(239,68,68,.2);color:var(--danger)}
        .badge-ok{background:rgba(16,185,129,.2);color:var(--success)}
        .filters{display:flex;gap:10px;align-items:flex-end;margin-bottom:16px;flex-wrap:wrap}
        .form-control{padding:8px;border-radius:8px;border:1px solid rgba(255,255,255,.2);background:rgba(255,255,255,.06);color:#e2e8f0}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-comments"></i> Commentaires</h1>
            <div style="display:flex;gap:8px;">
                <a href="/projetweb/ablelink/admin" class="btn btn-back"><i class="fas fa-arrow-left"></i> Dashboard</a>
                <a href="/projetweb/ablelink/admin/all-stories" class="btn btn-action"><i class="fas fa-book"></i> Stories</a>
            </div>
        </div>

        <form method="get" action="/projetweb/ablelink/admin/comments" class="filters">
            <input type="hidden" name="story_id" value="<?php echo (int)($story_id ?? 0); ?>">
            <div>
                <label style="color:#94a3b8; font-size:12px;">Affichage</label>
                <select name="reported" class="form-control">
                    <option value="all" <?php echo (($reported ?? 'all')==='all'?'selected':''); ?>>Tous</option>
                    <option value="1" <?php echo (($reported ?? 'all')==='1'?'selected':''); ?>>Signalés</option>
                    <option value="0" <?php echo (($reported ?? 'all')==='0'?'selected':''); ?>>Non signalés</option>
                </select>
            </div>
            <div>
                <label style="color:#94a3b8; font-size:12px;">Tri</label>
                <select name="sort" class="form-control">
                    <option value="recent" <?php echo (($sort ?? 'recent')==='recent'?'selected':''); ?>>Plus récents</option>
                    <option value="oldest" <?php echo (($sort ?? 'recent')==='oldest'?'selected':''); ?>>Plus anciens</option>
                    <option value="best" <?php echo (($sort ?? 'recent')==='best'?'selected':''); ?>>Plus aimés</option>
                </select>
            </div>
            <button type="submit" class="btn btn-back" style="background:#7c3aed;">Filtrer</button>
        </form>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Story</th>
                        <th>Auteur</th>
                        <th>Contenu</th>
                        <th>Likes</th>
                        <th>Signalement</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($comments)): ?>
                        <tr><td colspan="8" style="text-align:center; color:#94a3b8;">Aucun commentaire</td></tr>
                    <?php else: foreach ($comments as $c): ?>
                        <tr>
                            <td><?php echo (int)$c['id']; ?></td>
                            <td><a href="/projetweb/ablelink/admin/story?id=<?php echo (int)$c['story_id']; ?>" class="btn btn-action" style="padding:4px 8px;">#<?php echo (int)$c['story_id']; ?> — <?php echo htmlspecialchars($c['story_title'] ?? ''); ?></a></td>
                            <td><?php echo htmlspecialchars($c['author'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($c['content'] ?? ''); ?></td>
                            <td><?php echo (int)($c['likes'] ?? 0); ?></td>
                            <td>
                                <?php if ((int)($c['reported'] ?? 0) === 1): ?>
                                    <span class="badge badge-reported">Signalé</span>
                                <?php else: ?>
                                    <span class="badge badge-ok">OK</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($c['created_at'] ?? 'now'))); ?></td>
                            <td style="display:flex; gap:8px;">
                                <?php if ((int)($c['reported'] ?? 0) === 1): ?>
                                    <a href="/projetweb/ablelink/admin/reported/unreport?id=<?php echo (int)$c['id']; ?>&return=comments&story_id=<?php echo (int)$c['story_id']; ?>&reported=<?php echo urlencode($reported ?? 'all'); ?>" class="btn btn-action" style="border-color:#10b981; color:#10b981;"><i class="fas fa-flag"></i> Annuler signalement</a>
                                <?php endif; ?>
                                <a href="/projetweb/ablelink/admin/reported/delete?id=<?php echo (int)$c['id']; ?>&return=comments&story_id=<?php echo (int)$c['story_id']; ?>&reported=<?php echo urlencode($reported ?? 'all'); ?>" class="btn btn-action" style="border-color:#ef4444; color:#ef4444;" onclick="return confirm('Supprimer ce commentaire ?');"><i class="fas fa-trash"></i> Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

