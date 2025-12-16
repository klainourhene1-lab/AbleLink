<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AbleLink Admin - Toutes les Stories</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #7c3aed;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --text: #e2e8f0;
            --text-light: #94a3b8;
            --card-bg: rgba(30, 41, 59, 0.7);
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: var(--text);
            min-height: 100vh;
            padding: 30px;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 32px;
            font-weight: 700;
        }
        .btn-back {
            padding: 10px 20px;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.4);
        }
        .stories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 20px;
        }
        .story-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s;
        }
        .story-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(124, 58, 237, 0.2);
        }
        .story-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        .story-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 10px;
        }
        .story-meta {
            font-size: 13px;
            color: var(--text-light);
        }
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-approved {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success);
        }
        .badge-pending {
            background: rgba(245, 158, 11, 0.2);
            color: var(--warning);
        }
        .badge-rejected {
            background: rgba(239, 68, 68, 0.2);
            color: var(--danger);
        }
        .story-content {
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .story-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-approve {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid var(--success);
            color: var(--success);
        }
        .btn-approve:hover {
            background: var(--success);
            color: white;
        }
        .btn-reject {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid var(--danger);
            color: var(--danger);
        }
        .btn-reject:hover {
            background: var(--danger);
            color: white;
        }
        .btn-view {
            background: rgba(14, 165, 233, 0.2);
            border: 1px solid #0ea5e9;
            color: #0ea5e9;
        }
        .btn-view:hover {
            background: #0ea5e9;
            color: white;
        }
        .btn-delete {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid var(--danger);
            color: var(--danger);
        }
        .btn-delete:hover {
            background: var(--danger);
            color: white;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-light);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-book"></i> Toutes les Success Stories</h1>
            <a href="/projetweb/ablelink/admin" class="btn-back">
                <i class="fas fa-arrow-left"></i> Retour au Dashboard
            </a>
        </div>
        <form method="get" action="/projetweb/ablelink/admin/all-stories" style="display:flex; gap:10px; align-items:flex-end; margin-bottom:20px; flex-wrap:wrap;">
            <div>
                <label style="color:#94a3b8; font-size:12px;">Recherche</label>
                <input type="text" name="q" class="form-control" placeholder="Titre, contenu, auteur" value="<?php echo htmlspecialchars($q ?? ''); ?>" style="padding:8px; border-radius:8px; border:1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.06); color:#e2e8f0;">
            </div>
            <div>
                <label style="color:#94a3b8; font-size:12px;">Du</label>
                <input type="date" name="start" class="form-control" value="<?php echo htmlspecialchars($_GET['start'] ?? ''); ?>" style="padding:8px; border-radius:8px; border:1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.06); color:#e2e8f0;">
            </div>
            <div>
                <label style="color:#94a3b8; font-size:12px;">Au</label>
                <input type="date" name="end" class="form-control" value="<?php echo htmlspecialchars($_GET['end'] ?? ''); ?>" style="padding:8px; border-radius:8px; border:1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.06); color:#e2e8f0;">
            </div>
            <div>
                <label style="color:#94a3b8; font-size:12px;">Inclure</label>
                <div style="display:flex; gap:8px;">
                    <label style="font-size:12px; color:#94a3b8;"><input type="checkbox" name="include_content" <?php echo isset($_GET['include_content'])?'checked':''; ?>> Contenu</label>
                    <label style="font-size:12px; color:#94a3b8;"><input type="checkbox" name="include_image" <?php echo isset($_GET['include_image'])?'checked':''; ?>> Image</label>
                    <label style="font-size:12px; color:#94a3b8;"><input type="checkbox" name="include_video" <?php echo isset($_GET['include_video'])?'checked':''; ?>> Vidéo</label>
                </div>
            </div>
            <div>
                <label style="color:#94a3b8; font-size:12px;">Statut</label>
                <select name="status" class="form-control" style="padding:8px; border-radius:8px; border:1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.06); color:#e2e8f0;">
                    <option value="all" <?php echo (($status ?? '')==='all'?'selected':''); ?>>Tous</option>
                    <option value="approved" <?php echo (($status ?? '')==='approved'?'selected':''); ?>>Approuvées</option>
                    <option value="pending" <?php echo (($status ?? '')==='pending'?'selected':''); ?>>En attente</option>
                    <option value="rejected" <?php echo (($status ?? '')==='rejected'?'selected':''); ?>>Rejetées</option>
                </select>
            </div>
            <div>
                <label style="color:#94a3b8; font-size:12px;">Tri</label>
                <select name="sort" class="form-control" style="padding:8px; border-radius:8px; border:1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.06); color:#e2e8f0;">
                    <option value="recent" <?php echo (($sort ?? '')==='recent'?'selected':''); ?>>Plus récentes</option>
                    <option value="likes" <?php echo (($sort ?? '')==='likes'?'selected':''); ?>>Plus aimées</option>
                    <option value="title" <?php echo (($sort ?? '')==='title'?'selected':''); ?>>Titre A→Z</option>
                </select>
            </div>
            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn-back" style="background:#0ea5e9;">Filtrer</button>
                <?php 
                    $inc = [];
                    if (isset($_GET['include_content'])) $inc[] = 'content';
                    if (isset($_GET['include_image'])) $inc[] = 'image';
                    if (isset($_GET['include_video'])) $inc[] = 'video';
                    $incStr = implode(',', $inc);
                    $start = urlencode($_GET['start'] ?? '');
                    $end = urlencode($_GET['end'] ?? '');
                    $exportHref = '/projetweb/ablelink/admin/export-stories?status=' . urlencode($status ?? 'all') . '&sort=' . urlencode($sort ?? 'recent') . '&q=' . urlencode($q ?? '') . '&start=' . $start . '&end=' . $end . '&include=' . urlencode($incStr);
                ?>
                <a class="btn-back" style="background:#10b981;" href="<?php echo $exportHref; ?>">
                    <i class="fas fa-file-csv"></i> Exporter CSV
                </a>
            </div>
        </form>
        <form method="post" action="/projetweb/ablelink/admin/bulk" onsubmit="return confirm('Confirmer l\'action de masse ?');" style="margin-bottom:16px; display:flex; gap:10px; align-items:center;">
            <select name="action" required class="form-control" style="padding:8px; border-radius:8px; border:1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.06); color:#e2e8f0;">
                <option value="">Action groupée</option>
                <option value="approve">Approuver</option>
                <option value="reject">Rejeter</option>
                <option value="delete">Supprimer</option>
            </select>
            <button type="submit" class="btn-back" style="background:#7c3aed;">Appliquer</button>
        </form>
        
        <?php if (empty($stories)): ?>
        <div class="empty-state">
            <i class="fas fa-book-open" style="font-size: 64px; margin-bottom: 20px; opacity: 0.5;"></i>
            <h2>Aucune story disponible</h2>
            <p>Les stories partagées par les utilisateurs apparaîtront ici</p>
        </div>
        <?php else: ?>
        <div class="stories-grid">
            <?php foreach ($stories as $story): ?>
            <div class="story-card">
                <div class="story-header">
                    <div>
                        <h3 class="story-title"><?php echo htmlspecialchars($story['title'] ?? ''); ?></h3>
                        <div class="story-meta">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($story['author'] ?? ''); ?> | 
                            <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($story['created_at'] ?? 'now')); ?> |
                            <i class="fas fa-heart"></i> <?php echo (int)($story['likes'] ?? 0); ?> likes
                        </div>
                    </div>
                    <span class="badge badge-<?php echo $story['status'] ?? 'pending'; ?>">
                        <?php 
                        $status = $story['status'] ?? 'pending';
                        echo $status === 'approved' ? 'Approuvée' : ($status === 'rejected' ? 'Rejetée' : 'En Attente');
                        ?>
                    </span>
                </div>
                <div style="margin-bottom:8px;"><label style="font-size:12px; color:#94a3b8;"><input type="checkbox" name="ids[]" value="<?php echo (int)$story['id']; ?>"> Sélectionner</label></div>
                <p class="story-content"><?php $txt = $story['content'] ?? ($story['description'] ?? ''); echo htmlspecialchars(mb_substr($txt, 0, 200)) . '...'; ?></p>
                <div class="story-actions">
                    <?php if (($story['status'] ?? 'pending') === 'pending'): ?>
                    <a href="/projetweb/ablelink/admin/approve?id=<?php echo (int)$story['id']; ?>" class="btn btn-approve">
                        <i class="fas fa-check"></i> Approuver
                    </a>
                    <a href="/projetweb/ablelink/admin/reject?id=<?php echo (int)$story['id']; ?>" class="btn btn-reject">
                        <i class="fas fa-times"></i> Rejeter
                    </a>
                    <?php endif; ?>
                    <a href="/projetweb/ablelink/admin/story?id=<?php echo (int)$story['id']; ?>" class="btn btn-view">
                        <i class="fas fa-eye"></i> Voir
                    </a>
                    <a href="/projetweb/ablelink/admin/comments?story_id=<?php echo (int)$story['id']; ?>&reported=all" class="btn btn-view" style="border-color:#9333ea; color:#9333ea;">
                        <i class="fas fa-comments"></i> Commentaires
                    </a>
                    <a href="/projetweb/ablelink/success-stories/delete?id=<?php echo (int)$story['id']; ?>&return=admin&status=<?php echo urlencode($status ?? 'all'); ?>&sort=<?php echo urlencode($sort ?? 'recent'); ?>&q=<?php echo urlencode($q ?? ''); ?>" class="btn btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette story?');">
                        <i class="fas fa-trash"></i> Supprimer
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>



