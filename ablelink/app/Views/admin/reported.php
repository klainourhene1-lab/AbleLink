<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commentaires signalés</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); color:#e2e8f0; font-family: 'Poppins', sans-serif; }
        .container { max-width: 1100px; margin: 40px auto; padding: 0 16px; }
        .header { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
        .btn { display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:10px; text-decoration:none; }
        .btn-back { background:#0ea5e9; color:#0b1220; }
        .card { background: rgba(30,41,59,0.7); border:1px solid rgba(255,255,255,0.08); border-radius:14px; padding:16px; margin-bottom:12px; }
        .meta { color:#94a3b8; font-size:12px; margin-bottom:8px; }
        .actions { display:flex; gap:10px; }
        .btn-unreport { background: rgba(16,185,129,0.2); border:1px solid #10b981; color:#10b981; }
        .btn-delete { background: rgba(239,68,68,0.2); border:1px solid #ef4444; color:#ef4444; }
        .empty { text-align:center; color:#94a3b8; padding:30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2><i class="fas fa-flag"></i> Commentaires signalés</h2>
            <a href="/projetweb/ablelink/admin" class="btn btn-back"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
        <?php if (empty($comments ?? [])): ?>
            <div class="card empty"><i class="fas fa-check-circle"></i> Aucun commentaire signalé.</div>
        <?php endif; ?>
        <?php foreach (($comments ?? []) as $c): ?>
            <div class="card">
                <div class="meta">Histoire: « <?php echo htmlspecialchars($c['title'] ?? ''); ?> » — Auteur: <?php echo htmlspecialchars($c['author'] ?? ''); ?> — Le <?php echo date('d/m/Y H:i', strtotime($c['created_at'] ?? 'now')); ?></div>
                <div><?php echo nl2br(htmlspecialchars($c['content'] ?? '')); ?></div>
                <div class="actions" style="margin-top:10px;">
                    <a class="btn btn-unreport" href="/projetweb/ablelink/admin/reported/unreport?id=<?php echo (int)$c['id']; ?>"><i class="fas fa-check"></i> Retirer le signalement</a>
                    <a class="btn btn-delete" href="/projetweb/ablelink/admin/reported/delete?id=<?php echo (int)$c['id']; ?>" onclick="return confirm('Supprimer ce commentaire ?');"><i class="fas fa-trash"></i> Supprimer</a>
                    <a class="btn" style="background:#a78bfa; color:#0b1220;" href="/projetweb/ablelink/success-stories/comments?id=<?php echo (int)$c['story_id']; ?>"><i class="fas fa-eye"></i> Voir l'histoire</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
