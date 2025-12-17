<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AbleLink Admin - Story</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary:#7c3aed; --success:#10b981; --warning:#f59e0b; --danger:#ef4444; --dark:#0f172a; --text:#e2e8f0; --text-light:#94a3b8; --card-bg:rgba(30,41,59,.7); }
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Poppins',sans-serif;background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 100%);color:var(--text);min-height:100vh;padding:30px}
        .container{max-width:1100px;margin:0 auto}
        .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
        .header h1{font-size:28px;font-weight:700}
        .btn{padding:8px 14px;border-radius:8px;text-decoration:none;font-weight:600;font-size:13px;transition:.3s;display:inline-flex;align-items:center;gap:6px}
        .btn-back{background:#0ea5e9;color:#fff}
        .btn-action{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.15);color:#e2e8f0}
        .btn-approve{background:rgba(16,185,129,.2);border:1px solid var(--success);color:var(--success)}
        .btn-reject{background:rgba(239,68,68,.2);border:1px solid var(--danger);color:var(--danger)}
        .btn-approve:hover{background:var(--success);color:#fff}
        .btn-reject:hover{background:var(--danger);color:#fff}
        .card{background:var(--card-bg);backdrop-filter:blur(10px);border-radius:16px;padding:20px;border:1px solid rgba(255,255,255,.1);margin-bottom:16px}
        .meta{color:var(--text-light);font-size:13px;margin-top:6px}
        .badge{padding:4px 10px;border-radius:12px;font-size:12px;font-weight:600}
        .badge-approved{background:rgba(16,185,129,.2);color:var(--success)}
        .badge-pending{background:rgba(245,158,11,.2);color:var(--warning)}
        .badge-rejected{background:rgba(239,68,68,.2);color:var(--danger)}
        table{width:100%;border-collapse:collapse}
        th,td{padding:10px;border-bottom:1px solid rgba(255,255,255,.1);vertical-align:top}
        th{color:#94a3b8;text-align:left;font-weight:600}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-book"></i> Détail de la Story</h1>
            <div style="display:flex;gap:8px;">
                <a href="/projetweb/ablelink/admin/all-stories" class="btn btn-back"><i class="fas fa-arrow-left"></i> Retour</a>
                <a href="/projetweb/ablelink/admin/comments?story_id=<?php echo (int)($story['id'] ?? 0); ?>&reported=all" class="btn btn-action"><i class="fas fa-comments"></i> Commentaires</a>
                <a href="/projetweb/ablelink/admin/comments?story_id=<?php echo (int)($story['id'] ?? 0); ?>&reported=1" class="btn btn-action" style="border-color:#ef4444; color:#ef4444;"><i class="fas fa-flag"></i> Signalés</a>
            </div>
        </div>

        <div class="card">
            <h2 style="margin-bottom:6px;"><?php echo htmlspecialchars($story['title'] ?? ''); ?></h2>
            <?php $status = $story['status'] ?? 'pending'; $badge = 'badge-pending'; if($status==='approved') $badge='badge-approved'; elseif($status==='rejected') $badge='badge-rejected'; ?>
            <span class="badge <?php echo $badge; ?>" style="margin-right:8px;">
                <?php echo $status==='approved'?'Approuvée':($status==='rejected'?'Rejetée':'En Attente'); ?>
            </span>
            <div class="meta"><i class="fas fa-user"></i> <?php echo htmlspecialchars($story['author'] ?? ''); ?> • <i class="fas fa-calendar"></i> <?php echo htmlspecialchars(date('d/m/Y', strtotime($story['created_at'] ?? 'now'))); ?> • <i class="fas fa-heart"></i> <?php echo (int)($story['likes'] ?? 0); ?> likes</div>
            <div style="margin-top:16px; color:#cbd5e1; line-height:1.7;">
                <?php echo nl2br(htmlspecialchars($story['content'] ?? ($story['description'] ?? ''))); ?>
            </div>
            <div style="margin-top:16px; display:flex; gap:8px;">
                <?php if (($story['status'] ?? 'pending') === 'pending'): ?>
                    <a class="btn btn-approve" href="/projetweb/ablelink/admin/approve?id=<?php echo (int)($story['id'] ?? 0); ?>"><i class="fas fa-check"></i> Approuver</a>
                    <a class="btn btn-reject" href="/projetweb/ablelink/admin/reject?id=<?php echo (int)($story['id'] ?? 0); ?>"><i class="fas fa-times"></i> Rejeter</a>
                <?php endif; ?>
                <a class="btn btn-action" href="/projetweb/ablelink/success-stories/delete?id=<?php echo (int)($story['id'] ?? 0); ?>&return=admin"><i class="fas fa-trash"></i> Supprimer</a>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-bottom:10px;">Commentaires récents</h3>
            <?php if (empty($comments)): ?>
                <div style="color:#94a3b8;">Aucun commentaire</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr><th>ID</th><th>Auteur</th><th>Contenu</th><th>Likes</th><th>Date</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comments as $c): ?>
                        <tr>
                            <td><?php echo (int)$c['id']; ?></td>
                            <td><?php echo htmlspecialchars($c['author'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($c['content'] ?? ''); ?></td>
                            <td><?php echo (int)($c['likes'] ?? 0); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($c['created_at'] ?? 'now'))); ?></td>
                            <td style="display:flex; gap:8px;">
                                <a href="/projetweb/ablelink/admin/reported/delete?id=<?php echo (int)$c['id']; ?>&return=story&story_id=<?php echo (int)($story['id'] ?? 0); ?>" class="btn btn-action" style="border-color:#ef4444; color:#ef4444;" onclick="return confirm('Supprimer ce commentaire ?');"><i class="fas fa-trash"></i> Supprimer</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div style="margin-top:10px;">
                    <a href="/projetweb/ablelink/admin/comments?story_id=<?php echo (int)($story['id'] ?? 0); ?>&reported=all" class="btn btn-back" style="background:#7c3aed;"><i class="fas fa-list"></i> Voir tous les commentaires</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

