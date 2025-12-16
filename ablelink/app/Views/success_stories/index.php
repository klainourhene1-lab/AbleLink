<style>
    .stories-hero { 
        position: relative; 
        overflow: hidden; 
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        padding: 60px 0 40px;
    }
    
    .hero-frame { 
        background: linear-gradient(90deg, #5b7ff8 0%, #9b59d0 100%);
        border: none;
        border-radius: 20px; 
        padding: 40px 60px; 
        box-shadow: 0 15px 35px rgba(91, 127, 248, 0.4);
        margin: 0 auto;
        max-width: 900px;
    }
    
    .stories-hero h2 { 
        color: #fff; 
        font-size: 48px; 
        font-weight: 800; 
        margin: 0;
        text-align: center;
        letter-spacing: 0.5px;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }
    
    .stories-hero p { 
        color: rgba(255, 255, 255, 0.9);
        text-align: center;
        font-size: 16px;
        margin: 15px 0 0 0;
    }
    
    .accent { 
        width: 100px; 
        height: 3px; 
        background: rgba(255, 255, 255, 0.8);
        border-radius: 3px; 
        margin: 15px auto 0;
    }
</style>
<section class="stories-hero">
    <div class="container inner">
        <div class="row">
            <div class="col-lg-12">
                <div class="hero-frame">
                    <h2>Success Stories</h2>
                    <p>Témoignages et parcours inspirants</p>
                    <div class="accent"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="blog spad">
    <div class="container">
        <style>
            .story-card { background: linear-gradient(180deg, rgba(15,23,42,0.9), rgba(30,41,59,0.85)); border-radius: 18px; padding: 22px; border: 1px solid rgba(255,255,255,0.06); box-shadow: 0 12px 28px rgba(0,0,0,0.35); transition: transform .2s ease, box-shadow .2s ease; }
            .story-card:hover { transform: translateY(-6px); box-shadow: 0 18px 36px rgba(0,0,0,0.45); }
            .story-header { display:flex; align-items:center; justify-content:space-between; margin-bottom: 14px; }
            .story-brand { display:flex; align-items:center; gap:12px; }
            .story-avatar { width:38px; height:38px; border-radius:50%; background: linear-gradient(135deg,#7c3aed,#0ea5e9); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; box-shadow: 0 4px 12px rgba(124,58,237,0.35); }
            .story-brand-name { color:#e2e8f0; font-weight:600; font-size:14px; letter-spacing: .2px; }
            .story-likes { color:#ef4444; font-weight:700; display:flex; align-items:center; gap:6px; font-size:14px; }
            .story-title { font-size:22px; font-weight:700; margin:6px 0 10px; color:#fff; }
            .story-snippet { color:#94a3b8; font-size:14px; line-height:1.7; min-height:48px; }
            .story-meta { display:flex; align-items:center; gap:18px; color:#a8dadc; font-size:13px; margin:12px 0 16px; }
            .story-meta i { color:#38bdf8; }
            .story-actions { display:flex; align-items:center; gap:12px; }
            .btn-read { background: rgba(14,165,233,0.18); color:#cbd5e1; border:1px solid #0ea5e9; border-radius:12px; padding:10px 16px; text-decoration:none; font-weight:600; transition:.2s ease; }
            .btn-read:hover { background:#0ea5e9; color:#fff; }
            .btn-heart { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; border:1px solid #ef4444; color:#ef4444; text-decoration:none; background: rgba(239,68,68,0.15); transition:.2s ease; }
            .btn-heart:hover { background:#ef4444; color:#fff; }
            .actions-row { display:flex; gap:12px; margin-top:14px; }
            .actions-row a { color:#94a3b8; text-decoration:none; font-size:13px; }
            .actions-row a:hover { color:#e2e8f0; }
            .badge { display:inline-block; padding:4px 8px; border-radius:12px; font-size:11px; font-weight:700; }
            .badge-bronze { background: rgba(205,127,50,0.2); color:#cd7f32; border:1px solid #cd7f32; }
            .badge-silver { background: rgba(192,192,192,0.2); color:#c0c0c0; border:1px solid #c0c0c0; }
            .badge-gold { background: rgba(255,215,0,0.2); color:#ffd700; border:1px solid #ffd700; }
            .badge-diamond { background: rgba(167,139,250,0.2); color:#a78bfa; border:1px solid #a78bfa; }
            .tags { display:flex; gap:6px; flex-wrap:wrap; margin-top:8px; }
            .tag { font-size:11px; color:#a8dadc; background: rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); border-radius:10px; padding:4px 8px; }
            
        <style>
             /* Filter Section Styles */
            .filter-section { margin-bottom: 30px; }
            .filter-row { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 20px; }
            .filter-group { flex: 1; min-width: 200px; display: flex; flex-direction: column; gap: 8px; }
            .filter-label { color: #a8dadc; font-size: 14px; font-weight: 500; margin-left: 4px; }
            
            .filter-input { 
                background-color: rgba(15, 23, 42, 0.6); 
                border: 1px solid rgba(255, 255, 255, 0.1); 
                color: #e2e8f0; 
                border-radius: 12px; 
                padding: 12px 16px;
                font-size: 14px;
                transition: all 0.3s ease;
                height: 50px;
                width: 100%;
            }
            .filter-input:focus { 
                background-color: rgba(15, 23, 42, 0.9); 
                border-color: #00bfe7; 
                color: #fff; 
                box-shadow: 0 0 0 4px rgba(0, 191, 231, 0.15); 
                outline: none;
            }
            .filter-input::placeholder { color: #64748b; }
            select.filter-input { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23a8dadc'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 16px center; background-size: 16px; }
            select.filter-input option { background-color: #1e293b; color: #fff; padding: 10px; }
            
            .actions-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
            
            .btn-filter, .btn-share {
                background: #00bfe7;
                color: #fff;
                border: none;
                border-radius: 4px;
                padding: 12px 30px;
                font-weight: 700;
                font-size: 14px;
                letter-spacing: 1px;
                text-transform: uppercase;
                cursor: pointer;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                height: 50px;
            }
            
            .btn-filter:hover, .btn-share:hover {
                background: #0099b9;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 191, 231, 0.3);
                color: #fff;
            }
            
            .results-count { color: #94a3b8; font-size: 14px; margin-top: 10px; }
        </style>
        
        <div class="filter-section">
            <form method="get" action="/projetweb/ablelink/success-stories">
                <div class="filter-row">
                    <div class="filter-group" style="flex: 2;">
                        <label class="filter-label">Recherche</label>
                        <input type="text" name="q" class="filter-input" placeholder="Titre, contenu, auteur" value="<?php echo htmlspecialchars($q ?? ''); ?>">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Tri</label>
                        <select name="sort" class="filter-input">
                            <option value="recent" <?php echo (($sort ?? '')==='recent' ? 'selected' : ''); ?>>Plus récentes</option>
                            <option value="likes" <?php echo (($sort ?? '')==='likes' ? 'selected' : ''); ?>>Plus aimées</option>
                            <option value="title" <?php echo (($sort ?? '')==='title' ? 'selected' : ''); ?>>Titre A→Z</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Auteur</label>
                        <input type="text" name="author" class="filter-input" placeholder="Auteur" value="<?php echo htmlspecialchars($author ?? ''); ?>">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Catégorie</label>
                        <input type="text" name="cat" class="filter-input" placeholder="Catégorie" value="<?php echo htmlspecialchars($cat ?? ''); ?>">
                    </div>
                </div>
                
                <div class="actions-bar">
                    <div>
                        <button type="submit" class="btn-filter">FILTRER</button>
                        <div class="results-count">Résultats: <?php echo (int)($total ?? count($stories ?? [])); ?></div>
                    </div>
                    <a href="/projetweb/ablelink/success-stories/create" class="btn-share">PARTAGER UNE HISTOIRE</a>
                </div>
            </form>
        </div>
        
        <div class="row">
            <?php if (empty($stories)): ?>
                <div class="col-lg-12">
                    <div class="blog__item" style="text-align:center; padding:30px; background:#0f172a1a; border:1px solid rgba(255,255,255,0.08); border-radius:12px;">
                        <h4 style="color:#A8DADC;">Aucune histoire publiée pour le moment</h4>
                        <p style="color:#94a3b8; margin:10px 0 20px;">Partagez votre histoire ou demandez à l'admin d'approuver les nouvelles submissions.</p>
                        <a href="/projetweb/ablelink/success-stories/create" class="site-btn">Partager une histoire</a>
                        <a href="/projetweb/ablelink/admin" class="read__more" style="margin-left:10px;">Accéder à l'Admin</a>
                    </div>
                </div>
            <?php endif; ?>
            <?php foreach (($stories ?? []) as $s): ?>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="story-card">
                    <div class="story-header">
                        <div class="story-brand">
                            <div class="story-avatar"><?php echo strtoupper(substr(($s['author'] ?? 'A'),0,1)); ?></div>
                            <div class="story-brand-name">AbleLink</div>
                        </div>
                        <div class="story-likes"><i class="fas fa-heart"></i> <?php echo (int)($s['likes'] ?? 0); ?></div>
                    </div>
                    <div class="story-title"><?php echo htmlspecialchars($s['title']); ?></div>
                    <?php if (!empty($s['image'])): ?>
                        <div style="margin:10px 0; border-radius:12px; overflow:hidden; border:1px solid rgba(255,255,255,0.06);">
                            <img src="<?php echo htmlspecialchars($s['image']); ?>" alt="Image" style="width:100%; display:block;">
                        </div>
                    <?php endif; ?>
                    <?php 
                        $text = (string)($s['content'] ?? ($s['description'] ?? ''));
                        if (function_exists('mb_strimwidth')) { $snippet = mb_strimwidth($text, 0, 120, '...'); }
                        else { $snippet = strlen($text) > 120 ? substr($text, 0, 120) . '...' : $text; }
                        
                        // Use Helpers class for utility methods
                        $mins = \App\Core\Helpers::estimateReadingMinutes($text);
                        $tags = \App\Core\Helpers::extractKeywords($text, 4);
                        
                        // Badge logic simplified
                        $badge = '';
                        $likesTotal = (int)($s['likes'] ?? 0);
                        if ($likesTotal >= 100) $badge = 'diamond';
                        elseif ($likesTotal >= 50) $badge = 'gold';
                        elseif ($likesTotal >= 25) $badge = 'silver';
                        elseif ($likesTotal >= 10) $badge = 'bronze';
                    ?>
                    <div class="story-snippet"><?php echo nl2br(htmlspecialchars($snippet)); ?></div>
                    <div class="story-meta">
                        <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($s['author']); ?></span>
                        <span><i class="fas fa-calendar"></i> <?php echo date('d F Y', strtotime($s['created_at'])); ?></span>
                        <span><i class="fas fa-clock"></i> ~<?php echo (int)$mins; ?> min</span>
                        <?php if ($badge): ?>
                            <span class="badge badge-<?php echo $badge; ?>"><?php echo strtoupper($badge); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($tags)): ?>
                        <div class="tags">
                            <?php foreach ($tags as $t): ?><span class="tag">#<?php echo htmlspecialchars($t); ?></span><?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="story-actions">
                        <a href="/projetweb/ablelink/success-stories/comments?id=<?php echo (int)$s['id']; ?>" class="btn-read">Lire l'histoire</a>
                        <a href="/projetweb/ablelink/success-stories/like?id=<?php echo (int)$s['id']; ?>" class="btn-heart"><i class="fas fa-heart"></i></a>
                    </div>
                    <div class="actions-row">
                        <a href="/projetweb/ablelink/success-stories/edit?id=<?php echo (int)$s['id']; ?>">Modifier</a>
                        <a href="/projetweb/ablelink/success-stories/delete?id=<?php echo (int)$s['id']; ?>">Supprimer</a>
                        <a href="/projetweb/ablelink/success-stories/comments?id=<?php echo (int)$s['id']; ?>">Commentaires</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <style>
            .pagination-container {
                display: flex;
                align-items: stretch;
                margin-top: 20px;
                height: 50px;
            }
            .pagination-info {
                display: flex;
                align-items: center;
                padding-right: 20px;
                color: #a8dadc;
                font-size: 14px;
                white-space: nowrap;
            }
            .pagination-btn {
                flex: 1;
                background: #00bfe7;
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 1px;
                transition: all 0.3s ease;
                border-radius: 4px;
            }
            .pagination-btn:hover {
                background: #0099b9;
                color: #fff;
            }
            .pagination-btn.disabled {
                background: rgba(15, 23, 42, 0.6);
                cursor: not-allowed;
                opacity: 0.5;
            }
        </style>
        <?php 
            $page = (int)($page ?? 1); $per = (int)($per ?? 6); $total = (int)($total ?? 0);
            $pages = max(1, (int)ceil($total / max(1,$per)));
            $base = '/projetweb/ablelink/success-stories?q=' . urlencode($q ?? '') . '&sort=' . urlencode($sort ?? '') . '&author=' . urlencode($author ?? '') . '&cat=' . urlencode($cat ?? '') . '&per=' . $per;
        ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="pagination-container">
                    <div class="pagination-info">
                        Page <?php echo $page; ?> / <?php echo $pages; ?>
                    </div>
                    <?php if ($page < $pages): ?>
                        <a href="<?php echo $base . '&page=' . ($page+1); ?>" class="pagination-btn">
                            SUIVANT &rarr;
                        </a>
                    <?php elseif ($page > 1): ?>
                         <!-- If on last page but has previous pages, maybe show Previous as the main button or just disabled Next? 
                              Screenshot only shows Next. I'll make it behave like a "Next" flow, 
                              but if we are at the end, maybe we want to go back? 
                              For strict adherence to "Change this too" (the screenshot), I will focus on the wide bar look.
                              Since the screenshot shows "SUIVANT", I will prioritize that. 
                              If we are at the end, I'll show a "PRECEDENT" button in the same style or keep layout consistent.
                              Let's keep it simple: If has next, show Next. If not, but has prev, show Prev. -->
                        <a href="<?php echo $base . '&page=' . ($page-1); ?>" class="pagination-btn">
                            &larr; PRÉCÉDENT
                        </a>
                    <?php else: ?>
                        <!-- No pages to navigate -->
                        <div class="pagination-btn disabled">
                            SUIVANT &rarr;
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
