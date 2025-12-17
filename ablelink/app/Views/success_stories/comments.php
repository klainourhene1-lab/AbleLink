<style>
    @media print {
        @page { size: A4; margin: 20mm; }
        body * { visibility: hidden; }
        .detail-hero, .detail-hero * { visibility: visible; }
        .detail-hero { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; background: none !important; border: none !important; box-shadow: none !important; }
        
        /* Masquer les éléments inutiles pour le PDF */
        .header, .footer, .stories-hero, .share-row, .detail-like, .comments-card, .btn-read, .btn-heart, .read__more, .media-video, .story-actions, form, .site-btn, .comment-form, nav, header, footer { display: none !important; }
        
        /* Styles pour l'impression */
        body { background: #fff !important; color: #000 !important; font-family: 'Times New Roman', serif; }
        .detail-title { color: #000 !important; font-size: 24pt !important; margin-bottom: 0.5cm !important; text-align: center; }
        .detail-meta { color: #555 !important; font-size: 11pt !important; justify-content: center; margin-bottom: 1cm !important; }
        .detail-content { color: #000 !important; background: #fff !important; border: none !important; box-shadow: none !important; padding: 0 !important; font-size: 12pt !important; line-height: 1.6; text-align: justify; }
        .detail-avatar { display: none !important; } /* On cache l'avatar, on garde juste le nom */
        .media-img img { max-width: 100% !important; height: auto !important; border-radius: 4px !important; margin-bottom: 1cm; border: none !important; }
        .tags { justify-content: center; margin-top: 1cm; }
        .tag { border: 1px solid #ccc !important; color: #333 !important; background: #f9f9f9 !important; }
        
        /* Masquer les liens de retour et autres textes */
        a[href*="success-stories"] { display: none !important; }
    }
</style>

<section class="blog spad" style="padding-top: 40px;">
    <div class="container">
        <style>
            .detail-hero { background: linear-gradient(135deg, rgba(69,123,157,0.15), rgba(114,9,183,0.12)); border:1px solid rgba(255,255,255,0.08); border-radius:18px; padding:24px; box-shadow: 0 12px 28px rgba(0,0,0,0.35); }
            .detail-header { display:flex; align-items:center; justify-content:space-between; }
            .detail-brand { display:flex; align-items:center; gap:12px; }
            .detail-avatar { width:48px; height:48px; border-radius:50%; background: var(--gradient-blue-purple); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; }
            .detail-title { font-size:28px; font-weight:800; color:#fff; margin:12px 0 8px; }
            .detail-meta { display:flex; align-items:center; gap:18px; color:#a8dadc; font-size:13px; }
            .detail-like { display:flex; align-items:center; gap:8px; }
            .btn-like { border:1px solid #ef4444; color:#ef4444; background: rgba(239,68,68,0.15); border-radius:12px; padding:8px 12px; text-decoration:none; }
            .btn-like:hover { background:#ef4444; color:#fff; }
            .share-row { display:flex; gap:12px; margin-top:14px; }
            .share-row a { color:#94a3b8; text-decoration:none; font-size:13px; }
            .share-row a:hover { color:#e2e8f0; }
            .detail-content { background: rgba(30,41,59,0.65); border:1px solid rgba(255,255,255,0.06); border-radius:16px; padding:20px; margin-top:18px; color:#cbd5e1; line-height:1.8; }
            .comments-card { background: rgba(30,41,59,0.65); border:1px solid rgba(255,255,255,0.06); border-radius:16px; padding:20px; }
            .comment-item { border-bottom:1px solid rgba(255,255,255,0.06); padding:12px 0; }
            .comment-item:last-child { border-bottom: none; }
            .comment-meta { color:#a8dadc; font-size:12px; margin-bottom:6px; }
            .comment-actions { display:flex; gap:12px; font-size:12px; }
            .media-wrap { display:flex; gap:16px; margin-top:16px; flex-wrap:wrap; }
            .media-img { width:100%; max-width:560px; border-radius:14px; overflow:hidden; border:1px solid rgba(255,255,255,0.06); }
            .media-video { width:100%; max-width:560px; border-radius:14px; overflow:hidden; border:1px solid rgba(255,255,255,0.06); }
            .story-card { background: rgba(30,41,59,0.65); border:1px solid rgba(255,255,255,0.06); border-radius:16px; padding:16px; margin-bottom:16px; }
            .story-header { display:flex; align-items:center; justify-content:space-between; }
            .story-brand { display:flex; align-items:center; gap:10px; }
            .story-avatar { width:36px; height:36px; border-radius:50%; background: var(--gradient-blue-purple); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:14px; }
            .story-brand-name { color:#e2e8f0; font-weight:700; }
            .story-likes { color:#ef4444; }
            .story-title { color:#fff; font-weight:700; margin:8px 0; }
            .story-snippet { color:#cbd5e1; min-height:48px; }
            .story-meta { display:flex; gap:12px; color:#a8dadc; font-size:12px; margin-top:8px; }
            .story-actions { display:flex; gap:10px; margin-top:10px; }
            .btn-read { border:1px solid #38bdf8; color:#38bdf8; padding:6px 10px; border-radius:10px; text-decoration:none; }
            .btn-read:hover { background:#38bdf8; color:#0b1220; }
            .btn-heart { border:1px solid #ef4444; color:#ef4444; padding:6px 10px; border-radius:10px; text-decoration:none; }
            .btn-heart:hover { background:#ef4444; color:#fff; }
            .badge { display:inline-block; padding:4px 8px; border-radius:12px; font-size:11px; font-weight:700; }
            .badge-bronze { background: rgba(205,127,50,0.2); color:#cd7f32; border:1px solid #cd7f32; }
            .badge-silver { background: rgba(192,192,192,0.2); color:#c0c0c0; border:1px solid #c0c0c0; }
            .badge-gold { background: rgba(255,215,0,0.2); color:#ffd700; border:1px solid #ffd700; }
            .badge-diamond { background: rgba(167,139,250,0.2); color:#a78bfa; border:1px solid #a78bfa; }
            .tags { display:flex; gap:6px; flex-wrap:wrap; margin-top:8px; }
            .tag { font-size:11px; color:#a8dadc; background: rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); border-radius:10px; padding:4px 8px; }
            
            /* Enhanced Modern Comment Styles */
            .comments-card {
                background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(15, 23, 42, 0.85));
                border: 1px solid rgba(91, 127, 248, 0.2);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            }
            
            .comments-card h4 {
                color: #fff;
                font-size: 24px;
                font-weight: 700;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .comments-card h4:before {
                content: "💬";
                font-size: 28px;
            }

            
            .comment-item {
                background: rgba(15, 23, 42, 0.5);
                border: 1px solid rgba(255, 255, 255, 0.08);
                border-radius: 12px;
                padding: 18px;
                margin-bottom: 15px;
                transition: all 0.3s;
            }
            
            .comment-item:hover {
                border-color: rgba(91, 127, 248, 0.4);
                box-shadow: 0 5px 15px rgba(91, 127, 248, 0.15);
                transform: translateX(5px);
            }
            
            .comment-meta {
                color: #5b7ff8;
                font-weight: 600;
                font-size: 14px;
                margin-bottom: 10px;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .comment-meta:before {
                content: "👤";
                font-size: 16px;
            }

            
            .comment-item > div:nth-child(2) {
                color: #e2e8f0;
                line-height: 1.6;
                margin: 10px 0;
            }
            
            .comment-actions {
                display: flex;
                gap: 15px;
                margin-top: 12px;
                padding-top: 12px;
                border-top: 1px solid rgba(255, 255, 255, 0.05);
            }
            
            .comment-actions a {
                color: var(--text-secondary);
                text-decoration: none;
                font-size: 13px;
                padding: 6px 12px;
                border-radius: 8px;
                background: rgba(91, 127, 248, 0.1);
                transition: all 0.3s;
                display: flex;
                align-items: center;
                gap: 5px;
            }
            
            .comment-actions a:hover {
                background: rgba(91, 127, 248, 0.3);
                color: #5b7ff8;
                transform: translateY(-2px);
            }
            
            /* Form Styles */
            .form-control {
                background: rgba(15, 23, 42, 0.8);
                border: 1px solid rgba(91, 127, 248, 0.3);
                color: #fff;
                border-radius: 10px;
                padding: 12px 15px;
                transition: all 0.3s;
            }
            
            .form-control:focus {
                border-color: #5b7ff8;
                box-shadow: 0 0 0 3px rgba(91, 127, 248, 0.2);
                outline: none;
            }
            
            textarea.form-control {
                min-height: 100px;
                resize: vertical;
            }
            
            .site-btn {
                background: linear-gradient(90deg, #5b7ff8 0%, #9b59d0 100%);
                color: white;
                border: none;
                border-radius: 10px;
                padding: 12px 30px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            
            .site-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(91, 127, 248, 0.4);
            }
            

            
            .form-inline {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .form-inline select {
                background: rgba(15, 23, 42, 0.8);
                border: 1px solid rgba(91, 127, 248, 0.3);
                color: #fff;
                border-radius: 8px;
                padding: 8px 12px;
                cursor: pointer;
            }
            
            .read__more {
                color: #5b7ff8;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s;
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 10px 20px;
                background: rgba(91, 127, 248, 0.15);
                border-radius: 10px;
                border: 1px solid rgba(91, 127, 248, 0.3);
            }
            
            .read__more:hover {
                background: rgba(91, 127, 248, 0.3);
                transform: translateX(-5px);
            }
            

        </style>
        <?php 
            $text = isset($story['content']) ? $story['content'] : ($story['description'] ?? '');
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $shareUrl = isset($og_url) ? $og_url : ($scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            $titleEnc = urlencode($story['title']);
            $urlEnc = urlencode($shareUrl);
            // Use Helpers for utility methods
            $mins = \App\Core\Helpers::estimateReadingMinutes($text);
            $tags = \App\Core\Helpers::extractKeywords($text, 6);
            
            // Badge logic simplified
            $likesTotal = (int)($story['likes'] ?? 0);
            $badge = '';
            if ($likesTotal >= 100) $badge = 'diamond';
            elseif ($likesTotal >= 50) $badge = 'gold';
            elseif ($likesTotal >= 25) $badge = 'silver';
            elseif ($likesTotal >= 10) $badge = 'bronze';
        ?>
        <div class="detail-hero">
            <div class="detail-header">
                <div class="detail-brand">
                    <div class="detail-avatar"><?php echo strtoupper(substr(($story['author'] ?? 'A'),0,1)); ?></div>
                    <div>
                        <div style="color:#e2e8f0; font-weight:700;">AbleLink</div>
                        <div class="detail-meta"><span><i class="fas fa-user"></i> <?php echo htmlspecialchars($story['author'] ?? ''); ?></span><span><i class="fas fa-calendar"></i> <?php echo date('d F Y', strtotime($story['created_at'])); ?></span><span><i class="fas fa-clock"></i> ~<?php echo (int)$mins; ?> min</span><?php if ($badge): ?><span class="badge badge-<?php echo $badge; ?>"><?php echo strtoupper($badge); ?></span><?php endif; ?></div>
                    </div>
                </div>
                <div class="detail-like">
                    <a class="btn-like" href="/projetweb/ablelink/success-stories/like?id=<?php echo (int)$story['id']; ?>&return=comments"><i class="fas fa-heart"></i></a>
                    <span style="color:#ef4444; font-weight:700;"><?php echo (int)($story['likes'] ?? 0); ?></span>
                </div>
            </div>
            <div class="detail-title"><?php echo htmlspecialchars($story['title']); ?></div>
            <div class="share-row">
                <a href="/projetweb/ablelink/success-stories/share?id=<?php echo (int)$story['id']; ?>&platform=facebook" target="_blank"><i class="fab fa-facebook"></i> Partager</a>
                <a href="/projetweb/ablelink/success-stories/share?id=<?php echo (int)$story['id']; ?>&platform=twitter" target="_blank"><i class="fab fa-twitter"></i> Tweeter</a>
                <a href="/projetweb/ablelink/success-stories/share?id=<?php echo (int)$story['id']; ?>&platform=linkedin" target="_blank"><i class="fab fa-linkedin"></i> LinkedIn</a>
                <a href="/projetweb/ablelink/success-stories/share?id=<?php echo (int)$story['id']; ?>&platform=whatsapp" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                <a href="#" id="btn-speak" style="color:#a78bfa;">Écouter l’histoire</a>
                <a href="#" id="btn-print" style="color:#10b981;">Exporter PDF</a>
            </div>
            <div class="media-wrap">
                <?php if (!empty($story['image'])): ?>
                    <div class="media-img"><img src="<?php echo htmlspecialchars($story['image']); ?>" alt="Image" style="width:100%; display:block;"></div>
                <?php endif; ?>
                <?php if (!empty($story['video_url'])): ?>
                    <div class="media-video">
                        <video controls style="width:100%; display:block; border-radius:14px;">
                            <source src="<?php echo htmlspecialchars($story['video_url']); ?>" type="video/mp4">
                        </video>
                    </div>
                <?php endif; ?>
            </div>
            <div class="detail-content"><?php echo nl2br(htmlspecialchars($text)); ?></div>
            <?php if (!empty($tags)): ?>
                <div class="tags">
                    <?php foreach ($tags as $t): ?><span class="tag">#<?php echo htmlspecialchars($t); ?></span><?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <script>
            (function(){
                var btn = document.getElementById('btn-speak');
                if(!btn) return;
                var speaking = false; var utter;
                btn.addEventListener('click', function(e){
                    e.preventDefault();
                    if(!('speechSynthesis' in window)) return;
                    if(speaking){ window.speechSynthesis.cancel(); speaking = false; btn.textContent = 'Écouter l’histoire'; return; }
                    var text = <?php echo json_encode($text); ?>;
                    utter = new SpeechSynthesisUtterance(text);
                    utter.lang = 'fr-FR';
                    utter.rate = 1;
                    utter.pitch = 1;
                    speaking = true; btn.textContent = 'Arrêter';
                    utter.onend = function(){ speaking = false; btn.textContent = 'Écouter l’histoire'; };
                    window.speechSynthesis.speak(utter);
                });
            })();
            (function(){
                var printBtn = document.getElementById('btn-print');
                if(!printBtn) return;
                printBtn.addEventListener('click', function(e){ e.preventDefault(); window.print(); });
            })();
        </script>
        <div class="row" style="margin-top:20px">
            <div class="col-lg-6">
                <div class="comments-card">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                        <h4 style="color:#fff;">Commentaires</h4>
                        <form method="get" action="/projetweb/ablelink/success-stories/comments" class="form-inline" style="display:flex; gap:8px; align-items:center;">
                            <input type="hidden" name="id" value="<?php echo (int)$story['id']; ?>">
                            <label style="color:#a8dadc; font-size:12px;">Tri</label>
                            <select name="sort" class="form-control" onchange="this.form.submit()">
                                <option value="recent" <?php echo (($sort ?? '')==='recent'?'selected':''); ?>>Plus récents</option>
                                <option value="best" <?php echo (($sort ?? '')==='best'?'selected':''); ?>>Meilleurs</option>
                                <option value="oldest" <?php echo (($sort ?? '')==='oldest'?'selected':''); ?>>Plus anciens</option>
                            </select>
                        </form>
                    </div>
                    <?php foreach (($comments ?? []) as $c): ?>
                        <div class="comment-item">
                            <div class="comment-meta"><?php echo htmlspecialchars($c['author']); ?> — <?php echo date('d M Y', strtotime($c['created_at'])); ?></div>
                            <div><?php echo nl2br(htmlspecialchars($c['content'])); ?></div>
                            <div class="comment-actions">
                                <a href="/projetweb/ablelink/success-stories/comment-like?id=<?php echo (int)$c['id']; ?>&story_id=<?php echo (int)$story['id']; ?>" class="read__more">Like (<?php echo (int)$c['likes']; ?>)</a>
                                <a href="/projetweb/ablelink/success-stories/comment-report?id=<?php echo (int)$c['id']; ?>&story_id=<?php echo (int)$story['id']; ?>" class="read__more">Signaler</a>
                                <a href="/projetweb/ablelink/success-stories/comment-delete?id=<?php echo (int)$c['id']; ?>&story_id=<?php echo (int)$story['id']; ?>" class="read__more">Supprimer</a>
                            </div>
                            <?php if (!empty($c['replies'])): ?>
                                <?php foreach ($c['replies'] as $r): ?>
                                    <div class="comment-item" style="margin-left:20px;">
                                        <div class="comment-meta"><?php echo htmlspecialchars($r['author']); ?> — <?php echo date('d M Y', strtotime($r['created_at'])); ?></div>
                                        <div><?php echo nl2br(htmlspecialchars($r['content'])); ?></div>
                                        <div class="comment-actions">
                                            <a href="/projetweb/ablelink/success-stories/comment-like?id=<?php echo (int)$r['id']; ?>&story_id=<?php echo (int)$story['id']; ?>" class="read__more">Like (<?php echo (int)$r['likes']; ?>)</a>
                                            <a href="/projetweb/ablelink/success-stories/comment-report?id=<?php echo (int)$r['id']; ?>&story_id=<?php echo (int)$story['id']; ?>" class="read__more">Signaler</a>
                                            <a href="/projetweb/ablelink/success-stories/comment-delete?id=<?php echo (int)$r['id']; ?>&story_id=<?php echo (int)$story['id']; ?>" class="read__more">Supprimer</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <div style="margin-left:20px; margin-top:8px;">
                                <form method="post" action="/projetweb/ablelink/success-stories/comment-store">
                                    <input type="hidden" name="story_id" value="<?php echo (int)$story['id']; ?>">
                                    <input type="hidden" name="parent_id" value="<?php echo (int)$c['id']; ?>">
                                    <?php $logged = !empty($_SESSION['user_logged_in']); $uname = $_SESSION['user_name'] ?? ($_SESSION['user_email'] ?? ''); ?>
                                    <?php if ($logged && $uname): ?>
                                        <div class="form-group"><div class="form-control" style="background: rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1);">Connecté: <?php echo htmlspecialchars($uname); ?></div></div>
                                        <input type="hidden" name="author" value="<?php echo htmlspecialchars($uname); ?>">
                                    <?php else: ?>
                                        <div class="form-group"><input class="form-control" type="text" name="author" placeholder="Répondre en tant que" required></div>
                                    <?php endif; ?>
                                    <div class="form-group"><textarea class="form-control" name="content" rows="2" placeholder="Votre réponse" required></textarea></div>
                                    <button type="submit" class="site-btn">Répondre</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="comments-card">
                    <h4 style="color:#fff; margin-bottom:10px;">Ajouter un commentaire</h4>
                    <form method="post" action="/projetweb/ablelink/success-stories/comment-store">
                        <input type="hidden" name="story_id" value="<?php echo (int)$story['id']; ?>">
                        <?php $logged = !empty($_SESSION['user_logged_in']); $uname = $_SESSION['user_name'] ?? ($_SESSION['user_email'] ?? ''); ?>
                        <?php if ($logged && $uname): ?>
                            <div class="form-group"><div class="form-control" style="background: rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1);">Connecté: <?php echo htmlspecialchars($uname); ?></div></div>
                            <input type="hidden" name="author" value="<?php echo htmlspecialchars($uname); ?>">
                        <?php else: ?>
                            <div class="form-group"><input class="form-control" type="text" name="author" placeholder="Auteur" required></div>
                        <?php endif; ?>
                        <div class="form-group"><textarea class="form-control" name="content" rows="4" placeholder="Commentaire" required></textarea></div>
                        <button type="submit" class="site-btn" style="width:100%">Publier</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top:12px"><div class="col-lg-12"><a class="read__more" href="/projetweb/ablelink/success-stories">← Retour aux Success Stories</a></div></div>
        <?php if (!empty($related)): ?>
        <div class="row" style="margin-top:24px">
            <div class="col-lg-12"><h4 style="color:#fff; margin-bottom:10px;">Histoires liées</h4></div>
            <?php foreach ($related as $s): ?>
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
                    <?php 
                        $text = (string)($s['content'] ?? ($s['description'] ?? ''));
                        if (function_exists('mb_strimwidth')) { $snippet = mb_strimwidth($text, 0, 120, '...'); }
                        else { $snippet = strlen($text) > 120 ? substr($text, 0, 120) . '...' : $text; }
                    ?>
                    <div class="story-snippet"><?php echo nl2br(htmlspecialchars($snippet)); ?></div>
                    <div class="story-meta">
                        <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($s['author']); ?></span>
                        <span><i class="fas fa-calendar"></i> <?php echo date('d F Y', strtotime($s['created_at'])); ?></span>
                    </div>
                    <div class="story-actions">
                        <a href="/projetweb/ablelink/success-stories/comments?id=<?php echo (int)$s['id']; ?>" class="btn-read">Lire l'histoire</a>
                        <a href="/projetweb/ablelink/success-stories/like?id=<?php echo (int)$s['id']; ?>" class="btn-heart"><i class="fas fa-heart"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
