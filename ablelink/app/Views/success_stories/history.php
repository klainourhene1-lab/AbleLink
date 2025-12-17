<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .stories-hero { 
        position: relative; 
        overflow: hidden; 
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #3730a3 100%);
        padding: 80px 0 60px;
    }
    
    .stories-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background: 
            radial-gradient(circle at 20% 50%, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(59, 130, 246, 0.15) 0%, transparent 50%);
        pointer-events: none;
    }
    
    .hero-frame { 
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
        border: none;
        border-radius: 24px; 
        padding: 50px 70px; 
        box-shadow: 
            0 20px 60px rgba(139, 92, 246, 0.4),
            0 0 0 1px rgba(255, 255, 255, 0.1) inset;
        margin: 0 auto;
        max-width: 1000px;
        position: relative;
        overflow: hidden;
    }
    
    .hero-frame::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    }
    
    .stories-hero h2 { 
        color: #fff; 
        font-size: 56px; 
        font-weight: 800; 
        margin: 0;
        text-align: center;
        letter-spacing: -0.5px;
        text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        background: linear-gradient(to bottom, #ffffff, #e0e7ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .stories-hero p { 
        color: rgba(255, 255, 255, 0.95);
        text-align: center;
        font-size: 18px;
        font-weight: 500;
        margin: 18px 0 0 0;
        letter-spacing: 0.3px;
    }
    
    .accent { 
        width: 120px; 
        height: 4px; 
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.9), transparent);
        border-radius: 4px; 
        margin: 20px auto 0;
        box-shadow: 0 2px 10px rgba(255, 255, 255, 0.3);
    }
</style>
<section class="stories-hero">
    <div class="container inner">
        <div class="row">
            <div class="col-lg-12">
                <div class="hero-frame">
                    <h2>Historique</h2>
                    <p>Découvrez toutes les histoires</p>
                    <div class="accent"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="blog spad">
    <div class="container">
        <style>
            /* Filter Section */
            .filter-section {
                background: linear-gradient(135deg, rgba(30, 41, 59, 0.7), rgba(15, 23, 42, 0.7));
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 20px;
                padding: 32px;
                margin-bottom: 40px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            }
            
            /* Story Cards */
            .story-card { 
                background: linear-gradient(135deg, rgba(30, 41, 59, 0.8), rgba(15, 23, 42, 0.9)); 
                border-radius: 20px; 
                padding: 28px; 
                border: 1px solid rgba(139, 92, 246, 0.2); 
                box-shadow: 
                    0 10px 30px rgba(0, 0, 0, 0.4),
                    0 0 0 1px rgba(255, 255, 255, 0.05) inset; 
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
                margin-bottom: 28px; 
                position: relative; 
                overflow: hidden;
                backdrop-filter: blur(10px);
            }
            
            .story-card::before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 2px;
                background: linear-gradient(90deg, transparent, rgba(139, 92, 246, 0.5), transparent);
                opacity: 0;
                transition: opacity 0.3s;
            }
            
            .story-card:hover {
                transform: translateY(-8px) scale(1.02);
                box-shadow: 
                    0 20px 50px rgba(139, 92, 246, 0.3),
                    0 0 0 1px rgba(139, 92, 246, 0.3) inset;
                border-color: rgba(139, 92, 246, 0.5);
            }
            
            .story-card:hover::before {
                opacity: 1;
            }
            
            .story-header { display:flex; align-items:center; justify-content:space-between; margin-bottom: 18px; }
            .story-brand { display:flex; align-items:center; gap:14px; }
            .story-avatar { 
                width: 44px; 
                height: 44px; 
                border-radius: 50%; 
                background: linear-gradient(135deg, #8b5cf6, #6366f1); 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                color: #fff; 
                font-weight: 700;
                font-size: 16px;
                box-shadow: 
                    0 4px 12px rgba(139, 92, 246, 0.4),
                    0 0 0 3px rgba(139, 92, 246, 0.2);
            }
            .story-brand-name { color: #e0e7ff; font-weight: 700; font-size: 15px; letter-spacing: 0.3px; }
            .story-likes { 
                color: #f472b6; 
                font-weight: 700; 
                display: flex; 
                align-items: center; 
                gap: 8px; 
                font-size: 15px;
                background: rgba(244, 114, 182, 0.1);
                padding: 6px 14px;
                border-radius: 12px;
                border: 1px solid rgba(244, 114, 182, 0.2);
            }
            .story-title { 
                font-size: 24px; 
                font-weight: 800; 
                margin: 10px 0 14px; 
                color: #fff; 
                line-height: 1.3;
                letter-spacing: -0.3px;
            }
            .story-snippet { 
                color: #cbd5e1; 
                font-size: 15px; 
                line-height: 1.7; 
                min-height: 52px; 
                margin-bottom: 16px;
            }
            .story-meta { 
                display: flex; 
                align-items: center; 
                gap: 20px; 
                color: #a5b4fc; 
                font-size: 13px; 
                margin: 14px 0 20px; 
                flex-wrap: wrap;
                font-weight: 500;
            }
            .story-meta i { color: #818cf8; }
            .story-actions { display: flex; align-items: center; gap: 14px; margin-top: 22px; }
            .btn-read { 
                background: linear-gradient(135deg, #6366f1, #8b5cf6); 
                color: #fff; 
                border: none; 
                border-radius: 14px; 
                padding: 12px 24px; 
                text-decoration: none; 
                font-weight: 700; 
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
                font-size: 14px;
                box-shadow: 
                    0 4px 15px rgba(99, 102, 241, 0.3),
                    0 0 0 1px rgba(255, 255, 255, 0.1) inset;
                letter-spacing: 0.3px;
            }
            .btn-read:hover { 
                transform: translateY(-2px);
                box-shadow: 
                    0 8px 25px rgba(99, 102, 241, 0.5),
                    0 0 0 1px rgba(255, 255, 255, 0.2) inset;
                color: #fff;
            }
            .btn-heart { 
                width: 48px; 
                height: 48px; 
                border-radius: 14px; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                border: 1px solid rgba(244, 114, 182, 0.3); 
                color: #f472b6; 
                text-decoration: none; 
                background: rgba(244, 114, 182, 0.1); 
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 4px 12px rgba(244, 114, 182, 0.2);
            }
            .btn-heart:hover { 
                background: linear-gradient(135deg, #f472b6, #ec4899); 
                color: #fff;
                transform: scale(1.1);
                box-shadow: 0 8px 20px rgba(244, 114, 182, 0.4);
            }
            .actions-row { 
                display: flex; 
                gap: 18px; 
                margin-top: 18px; 
                border-top: 1px solid rgba(139, 92, 246, 0.1); 
                padding-top: 14px;
            }
            .actions-row a { 
                color: #a5b4fc; 
                text-decoration: none; 
                font-size: 13px; 
                transition: all 0.2s;
                font-weight: 500;
                padding: 6px 12px;
                border-radius: 8px;
                background: rgba(139, 92, 246, 0.05);
            }
            .actions-row a:hover { 
                color: #c7d2fe;
                background: rgba(139, 92, 246, 0.15);
            }
            .badge { display: inline-block; padding: 5px 10px; border-radius: 14px; font-size: 11px; font-weight: 700; }
            .tags { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 12px; }
            .tag { 
                font-size: 11px; 
                color: #c7d2fe; 
                background: rgba(139, 92, 246, 0.15); 
                border: 1px solid rgba(139, 92, 246, 0.3); 
                border-radius: 12px; 
                padding: 5px 12px;
                font-weight: 600;
                transition: all 0.2s;
            }
            .tag:hover {
                background: rgba(139, 92, 246, 0.25);
                border-color: rgba(139, 92, 246, 0.5);
            }
            
            /* Form Controls */
            .form-control { 
                background: rgba(15, 23, 42, 0.8); 
                border: 1px solid rgba(139, 92, 246, 0.3); 
                color: #e0e7ff; 
                border-radius: 12px; 
                height: 50px;
                padding: 0 18px;
                font-size: 14px;
                font-weight: 500;
                transition: all 0.3s;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            }
            .form-control:focus { 
                background: rgba(15, 23, 42, 0.95); 
                border-color: #8b5cf6; 
                color: #fff; 
                box-shadow: 
                    0 0 0 4px rgba(139, 92, 246, 0.15),
                    0 4px 12px rgba(139, 92, 246, 0.3);
                outline: none;
            }
            .form-control::placeholder { color: #94a3b8; }
            select.form-control { 
                color: #e0e7ff;
                cursor: pointer;
                padding-right: 40px;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%23a5b4fc' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
                background-position: right 14px center;
                background-repeat: no-repeat;
                background-size: 20px;
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
            }
            select.form-control option { 
                background-color: #1e293b; 
                color: #fff; 
                padding: 12px;
            }
            
            .site-btn-cyan { 
                background: linear-gradient(135deg, #06b6d4, #0ea5e9); 
                color: #fff; 
                border: none; 
                border-radius: 12px; 
                padding: 0 28px; 
                height: 50px; 
                font-weight: 700; 
                display: inline-flex; 
                align-items: center; 
                justify-content: center; 
                text-decoration: none; 
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 
                    0 4px 15px rgba(6, 182, 212, 0.3),
                    0 0 0 1px rgba(255, 255, 255, 0.1) inset;
                letter-spacing: 0.5px;
                font-size: 14px;
            }
            .site-btn-cyan:hover { 
                transform: translateY(-2px);
                box-shadow: 
                    0 8px 25px rgba(6, 182, 212, 0.5),
                    0 0 0 1px rgba(255, 255, 255, 0.2) inset;
                color: #fff;
            }
            
            /* Results count */
            .results-info {
                color: #94a3b8;
                font-size: 14px;
                font-weight: 600;
                background: rgba(30, 41, 59, 0.5);
                padding: 12px 20px;
                border-radius: 12px;
                border: 1px solid rgba(139, 92, 246, 0.1);
            }
        </style>

        <div class="filter-section">
            <div class="row">
                <div class="col-lg-10">
                    <form method="get" action="/projetweb/ablelink/historique" class="row" style="gap:15px; align-items:flex-end;">
                        <div class="col-md-4">
                            <label style="color:#a5b4fc; font-size:13px; margin-bottom:8px; font-weight:600; display:block;">🔍 Recherche</label>
                            <input type="text" name="q" class="form-control" placeholder="Titre, contenu, auteur" value="<?php echo htmlspecialchars($q ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label style="color:#a5b4fc; font-size:13px; margin-bottom:8px; font-weight:600; display:block;">📊 Statut</label>
                            <select name="status" class="form-control">
                                <option value="">Tous</option>
                                <option value="approved" <?php echo (($statusFilter ?? '')==='approved' ? 'selected' : ''); ?>>Publiée</option>
                                <option value="pending" <?php echo (($statusFilter ?? '')==='pending' ? 'selected' : ''); ?>>En attente</option>
                                <option value="rejected" <?php echo (($statusFilter ?? '')==='rejected' ? 'selected' : ''); ?>>Rejetée</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <button type="submit" class="site-btn-cyan" style="width:100%">FILTRER</button>
                        </div>
                    </form>
                </div>
                <div class="col-lg-2" style="display:flex; justify-content:flex-end; align-items:flex-end;">
                    <a href="/projetweb/ablelink/success-stories/create" class="site-btn-cyan" style="white-space:nowrap;">+ PARTAGER</a>
                </div>
            </div>
        </div>

        <div class="row" style="margin-bottom:20px">
            <div class="col-lg-12">
                <div class="results-info">
                    <i class="fas fa-chart-bar" style="margin-right:8px;"></i>
                    Résultats: <strong style="color:#e0e7ff;"><?php echo count($stories ?? []); ?></strong> histoire(s)
                </div>
            </div>
        </div>

        <div class="row">
        <?php if (!empty($stories)): ?>
            <?php foreach ($stories as $s): ?>
                <?php 
                    $text = (string)($s['content'] ?? ($s['description'] ?? ''));
                    if (function_exists('mb_strimwidth')) { $snippet = mb_strimwidth($text, 0, 120, '...'); }
                    else { $snippet = strlen($text) > 120 ? substr($text, 0, 120) . '...' : $text; }
                    // Use Helpers for utility methods
                    $mins = \App\Core\Helpers::estimateReadingMinutes($text);
                    $tags = \App\Core\Helpers::extractKeywords($text, 4);
                ?>
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
                        
                        <div class="story-snippet"><?php echo nl2br(htmlspecialchars($snippet)); ?></div>
                        
                        <div class="story-meta">
                            <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($s['author'] ?? 'Anonyme'); ?></span>
                            <span><i class="fas fa-calendar"></i> <?php echo date('d F Y', strtotime($s['created_at'])); ?></span>
                            <span><i class="fas fa-clock"></i> ~<?php echo (int)$mins; ?> min</span>
                            
                            <?php 
                                $st = $s['status'] ?? 'pending';
                                $badgeColor = '#94a3b8'; // gray
                                $badgeText = 'En attente';
                                if ($st === 'approved') { $badgeColor = '#10b981'; $badgeText = 'Publiée'; } // green
                                elseif ($st === 'rejected') { $badgeColor = '#ef4444'; $badgeText = 'Masquée'; } // red
                            ?>
                            <span style="background:<?php echo $badgeColor; ?>; color:#fff; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:700; margin-left:auto;"><?php echo strtoupper($badgeText); ?></span>
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
                             <?php if (($s['status']??'pending') !== 'approved'): ?>
                                <a href="/projetweb/ablelink/success-stories/updateStatus?id=<?php echo (int)$s['id']; ?>&status=approved&q=<?php echo urlencode($q??''); ?>&f_status=<?php echo urlencode($statusFilter??''); ?>" style="color:#10b981; background:rgba(16, 185, 129, 0.1);">✅ Publier</a>
                             <?php else: ?>
                                <a href="/projetweb/ablelink/success-stories/updateStatus?id=<?php echo (int)$s['id']; ?>&status=rejected&q=<?php echo urlencode($q??''); ?>&f_status=<?php echo urlencode($statusFilter??''); ?>" style="color:#ef4444; background:rgba(239, 68, 68, 0.1);">🚫 Masquer</a>
                             <?php endif; ?>
                             
                             <a href="/projetweb/ablelink/success-stories/edit?id=<?php echo (int)$s['id']; ?>">Modifier</a>
                             <a href="/projetweb/ablelink/success-stories/delete?id=<?php echo (int)$s['id']; ?>&return=admin" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette histoire ? Cette action est irréversible.');">Supprimer</a>
                             <a href="/projetweb/ablelink/success-stories/comments?id=<?php echo (int)$s['id']; ?>">Commentaires</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-lg-12">
                <div style="text-align:center; padding:80px 50px; background:linear-gradient(135deg, rgba(30, 41, 59, 0.6), rgba(15, 23, 42, 0.6)); border:1px solid rgba(139, 92, 246, 0.2); border-radius:24px; backdrop-filter:blur(10px);">
                    <div style="font-size:64px; margin-bottom:24px; opacity:0.7;">🔍</div>
                    <h4 style="color:#e0e7ff; margin-bottom:16px; font-size:28px; font-weight:700;">Aucune histoire trouvée</h4>
                    <p style="color:#cbd5e1; margin:0 0 32px; font-size:16px; max-width:500px; margin-left:auto; margin-right:auto;">Essayez de modifier vos critères de recherche ou filtres pour trouver ce que vous cherchez.</p>
                    <a href="/projetweb/ablelink/historique" class="btn-read" style="display:inline-flex; gap:8px; align-items:center;"><i class="fas fa-redo"></i> Réinitialiser les filtres</a>
                </div>
            </div>
        <?php endif; ?>
        </div>
    </div>
</section>
