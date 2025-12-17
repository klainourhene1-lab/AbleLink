<?php
use App\Core\Helpers;
?>
<!-- Hero Banner Section -->
<section class="hero-banner">
    <div class="container-fluid">
        <div class="banner-card">
            <h1 class="banner-title">Bienvenue sur AbleLink</h1>
        </div>
        <p class="banner-description">Découvrez les histoires inspirantes de notre communauté et partagez la vôtre.</p>
    </div>
</section>

<!-- Success Stories Section -->
<section class="stories-section">
    <div class="container-fluid">
        <div class="section-header">
            <h2 class="section-title-large">Dernières Success Stories</h2>
            <a href="/projetweb/ablelink/success-stories" class="btn-view-all">
                Voir toutes les histoires
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <!-- Stories Grid -->
        <div class="stories-grid">
            <?php if (!empty($stories)): ?>
                <?php foreach ($stories as $index => $story): ?>
                    <?php
                        $title = htmlspecialchars($story['title'] ?? 'Sans titre');
                        $author = htmlspecialchars($story['author'] ?? $story['author_name'] ?? 'Anonyme');
                        $content = $story['content'] ?? $story['description'] ?? '';
                        $fullContent = nl2br(htmlspecialchars(strip_tags($content)));
                        $excerpt = mb_substr(strip_tags($content), 0, 150) . '...';
                        $likes = (int)($story['likes'] ?? 0);
                        $created = $story['created_at'] ?? '';
                        $storyId = (int)($story['id'] ?? 0);
                        $authorInitial = mb_substr($author, 0, 1);
                        $readingTime = Helpers::estimateReadingMinutes($content);
                        
                        // Get comments for this story
                        $storyComments = $commentsByStory[$storyId] ?? [];
                        $commentCount = count($storyComments);
                    ?>
                    <div class="story-card" id="story-card-<?= $index ?>">
                        <div class="story-header">
                            <div class="story-author">
                                <div class="author-icon"><?= strtoupper($authorInitial) ?></div>
                                <span><?= $author ?></span>
                            </div>
                            <div class="story-likes">
                                <i class="fas fa-heart"></i>
                                <span><?= $likes ?></span>
                            </div>
                        </div>
                        <h3 class="story-title"><?= $title ?></h3>
                        
                        <!-- Excerpt (visible par défaut) -->
                        <p class="story-excerpt" id="excerpt-<?= $index ?>"><?= htmlspecialchars($excerpt) ?></p>
                        
                        <!-- Full content (caché par défaut) -->
                        <div class="story-full-content" id="full-content-<?= $index ?>" style="display: none;">
                            <p style="color: var(--text-secondary); font-size: 15px; line-height: 1.6; margin-bottom: 15px;">
                                <?= $fullContent ?>
                            </p>
                        </div>
                        
                        <div class="story-meta">
                            <span class="meta-item">
                                <i class="fas fa-clock"></i>
                                <?= $readingTime ?> min de lecture
                            </span>
                            <?php if ($created): ?>
                                <span class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <?= date('d/m/Y', strtotime($created)) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="story-actions">
                            <button class="btn-story-detail" onclick="toggleStory(<?= $index ?>)" id="btn-toggle-<?= $index ?>">
                                Lire la suite
                            </button>
                            <button class="btn-comments" onclick="toggleComments(<?= $index ?>)" id="btn-comments-<?= $index ?>">
                                <i class="fas fa-comments"></i> Commentaires (<?= $commentCount ?>)
                            </button>
                        </div>
                        
                        <!-- Comments Section (caché par défaut) -->
                        <div class="comments-section" id="comments-section-<?= $index ?>" style="display: none;">
                            <h4 style="color: var(--text-primary); margin-bottom: 15px;">
                                <i class="fas fa-comments"></i> Commentaires (<?= $commentCount ?>)
                            </h4>
                            
                            <?php if (!empty($storyComments)): ?>
                                <div class="comments-list">
                                    <?php foreach ($storyComments as $comment): ?>
                                        <div class="comment-item">
                                            <div class="comment-header">
                                                <strong><?= htmlspecialchars($comment['author'] ?? 'Anonyme') ?></strong>
                                                <span class="comment-date">
                                                    <?= date('d/m/Y', strtotime($comment['created_at'])) ?>
                                                </span>
                                            </div>
                                            <div class="comment-content">
                                                <?= nl2br(htmlspecialchars($comment['content'] ?? '')) ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p style="color: var(--text-secondary); font-style: italic;">Aucun commentaire pour le moment.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-stories">
                    <i class="fas fa-book-open"></i>
                    <p>Aucune histoire disponible pour le moment.</p>
                    <a href="/projetweb/ablelink/success-stories/create" class="btn-create-first">
                        Soyez le premier à partager votre histoire!
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Recent Comments Section -->
<?php if (!empty($recentComments)): ?>
<section class="comments-section">
    <div class="container-fluid">
        <h2 class="section-title">Commentaires récents</h2>
        <div class="comments-preview">
            <?php foreach (array_slice($recentComments, 0, 3) as $comment): ?>
                <?php
                    $commentAuthor = htmlspecialchars($comment['author'] ?? 'Anonyme');
                    $commentContent = htmlspecialchars($comment['content'] ?? '');
                    $commentExcerpt = mb_substr($commentContent, 0, 100) . '...';
                    $storyTitle = htmlspecialchars($comment['story_title'] ?? '');
                    $commentStoryId = (int)($comment['story_id'] ?? 0);
                    $commentDate = $comment['created_at'] ?? '';
                ?>
                <div class="comment-card">
                    <div class="comment-header">
                        <div class="comment-author">
                            <div class="author-icon-small"><?= strtoupper(mb_substr($commentAuthor, 0, 1)) ?></div>
                            <span><?= $commentAuthor ?></span>
                        </div>
                        <?php if ($commentDate): ?>
                            <span class="comment-date">
                                <i class="fas fa-clock"></i>
                                <?= date('d/m/Y', strtotime($commentDate)) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <p class="comment-text">"<?= $commentExcerpt ?>"</p>
                    <div class="comment-story">
                        <span>Sur l'histoire:</span>
                        <a href="/projetweb/ablelink/success-stories/comments?id=<?= $commentStoryId ?>">
                            <?= $storyTitle ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Call to Action Section -->
<section class="cta-section">
    <div class="container-fluid">
        <div class="cta-card">
            <i class="fas fa-pen-fancy"></i>
            <h2>Partagez votre histoire de succès</h2>
            <p>Inspirez les autres en partageant votre parcours et vos réussites avec la communauté AbleLink.</p>
            <a href="/projetweb/ablelink/success-stories/create" class="btn-cta">
                Partager mon histoire
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<style>
    .container-fluid {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 30px;
    }
    
    /* Hero Banner */
    .hero-banner {
        padding: 60px 0 40px;
        text-align: center;
    }
    
    .banner-card {
        background: linear-gradient(135deg, #457B9D 0%, #7209B7 100%);
        border-radius: 20px;
        padding: 50px 60px;
        margin-bottom: 20px;
        box-shadow: 0 10px 30px rgba(69, 123, 157, 0.3);
    }
    
    .banner-title {
        font-size: 48px;
        font-weight: 700;
        color: white;
        margin: 0;
    }
    
    .banner-description {
        font-size: 20px;
        color: var(--text-secondary);
        margin-top: 15px;
    }
    
    /* Success Stories Section */
    .stories-section {
        padding: 40px 0 60px;
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .section-title-large {
        font-size: 36px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }
    
    .section-title {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 30px;
    }
    
    .btn-view-all {
        padding: 12px 25px;
        background: linear-gradient(135deg, #457B9D 0%, #7209B7 100%);
        color: white;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
    }
    
    .btn-view-all:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(69, 123, 157, 0.4);
    }
    
    /* Stories Grid */
    .stories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 25px;
    }
    
    .story-card {
        background: rgba(30, 41, 59, 0.8);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 25px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s;
    }
    
    .story-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(69, 123, 157, 0.2);
        border-color: rgba(69, 123, 157, 0.3);
    }
    
    .story-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .story-author {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .author-icon {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, #457B9D 0%, #7209B7 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 20px;
    }
    
    .story-author span {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 15px;
    }
    
    .story-likes {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #E63946;
        font-weight: 600;
    }
    
    .story-title {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 15px;
        line-height: 1.3;
    }
    
    .story-excerpt {
        color: var(--text-secondary);
        font-size: 15px;
        line-height: 1.6;
        margin-bottom: 15px;
    }
    
    .story-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
        color: var(--text-secondary);
        font-size: 13px;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .meta-item i {
        color: #457B9D;
    }
    
    .story-actions {
        display: flex;
        gap: 10px;
    }
    
    .btn-story-detail {
        padding: 12px 24px;
        background: rgba(69, 123, 157, 0.2);
        border: 1px solid #457B9D;
        border-radius: 10px;
        color: #457B9D;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s;
        flex: 1;
        text-align: center;
    }
    
    .btn-story-detail:hover {
        background: #457B9D;
        color: white;
        transform: translateY(-2px);
    }
    
    /* No Stories */
    .no-stories {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        color: var(--text-secondary);
    }
    
    .no-stories i {
        font-size: 64px;
        color: #457B9D;
        margin-bottom: 20px;
    }
    
    .no-stories p {
        font-size: 18px;
        margin-bottom: 25px;
    }
    
    .btn-create-first {
        display: inline-block;
        padding: 14px 30px;
        background: linear-gradient(135deg, #457B9D 0%, #7209B7 100%);
        color: white;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-create-first:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(69, 123, 157, 0.4);
    }
    
    /* Comments Section */
    .comments-section {
        padding: 40px 0 60px;
        background: rgba(15, 23, 42, 0.3);
    }
    
    .comments-preview {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
    }
    
    .comment-card {
        background: rgba(30, 41, 59, 0.8);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 20px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s;
    }
    
    .comment-card:hover {
        border-color: rgba(69, 123, 157, 0.3);
        box-shadow: 0 5px 15px rgba(69, 123, 157, 0.15);
    }
    
    .comment-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }
    
    .comment-author {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .author-icon-small {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, #457B9D 0%, #7209B7 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 16px;
    }
    
    .comment-author span {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 14px;
    }
    
    .comment-date {
        font-size: 12px;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .comment-text {
        color: var(--text-secondary);
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 12px;
        font-style: italic;
    }
    
    .comment-story {
        font-size: 13px;
        color: var(--text-secondary);
    }
    
    .comment-story span {
        margin-right: 5px;
    }
    
    .comment-story a {
        color: #457B9D;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .comment-story a:hover {
        color: #7209B7;
    }
    
    /* CTA Section */
    .cta-section {
        padding: 60px 0 80px;
    }
    
    .cta-card {
        background: linear-gradient(135deg, #457B9D 0%, #7209B7 100%);
        border-radius: 20px;
        padding: 60px 40px;
        text-align: center;
        box-shadow: 0 15px 40px rgba(69, 123, 157, 0.3);
    }
    
    .cta-card i {
        font-size: 48px;
        color: white;
        margin-bottom: 20px;
    }
    
    .cta-card h2 {
        font-size: 36px;
        font-weight: 700;
        color: white;
        margin-bottom: 15px;
    }
    
    .cta-card p {
        font-size: 18px;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 30px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .btn-cta {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 16px 35px;
        background: white;
        color: #457B9D;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        font-size: 16px;
        transition: all 0.3s;
    }
    
    .btn-cta:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    
    /* Responsive */
    @media (max-width: 1024px) {
        .stories-grid,
        .comments-preview {
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        }
    }
    
    @media (max-width: 768px) {
        .banner-title {
            font-size: 32px;
        }
        
        .banner-card {
            padding: 35px 25px;
        }
        
        .banner-description {
            font-size: 16px;
        }
        
        .section-title-large {
            font-size: 28px;
        }
        
        .section-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .stories-grid,
        .comments-preview {
            grid-template-columns: 1fr;
        }
        
        .cta-card {
            padding: 40px 25px;
        }
        
        .cta-card h2 {
            font-size: 28px;
        }
    }
    
    /* Button styles */
    .story-actions {
        display: flex;
        gap: 10px;
    }
    
    .btn-story-detail {
        padding: 12px 24px;
        background: rgba(69, 123, 157, 0.2);
        border: 1px solid #457B9D;
        border-radius: 10px;
        color: #457B9D;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s;
        flex: 1;
        text-align: center;
        cursor: pointer;
    }
    
    .btn-story-detail:hover {
        background: #457B9D;
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-comments {
        padding: 12px 20px;
        background: rgba(230, 57, 70, 0.2);
        border: 1px solid #E63946;
        border-radius: 10px;
        color: #E63946;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        cursor: pointer;
    }
    
    .btn-comments:hover {
        background: #E63946;
        color: white;
        transform: translateY(-2px);
    }
    
    /* Comments Section */
    .comments-section {
        margin-top: 20px;
        padding: 20px;
        background: rgba(15, 23, 42, 0.6);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .comments-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .comment-item {
        padding: 15px;
        background: rgba(30, 41, 59, 0.6);
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .comment-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .comment-header strong {
        color: var(--text-primary);
        font-size: 14px;
    }
    
    .comment-date {
        color: var(--text-secondary);
        font-size: 12px;
    }
    
    .comment-content {
        color: var(--text-secondary);
        font-size: 14px;
        line-height: 1.6;
    }
</style>

<script>
function toggleStory(index) {
    const excerpt = document.getElementById('excerpt-' + index);
    const fullContent = document.getElementById('full-content-' + index);
    const btn = document.getElementById('btn-toggle-' + index);
    
    if (fullContent.style.display === 'none') {
        // Montrer le contenu complet
        excerpt.style.display = 'none';
        fullContent.style.display = 'block';
        btn.textContent = 'Réduire';
    } else {
        // Montrer l'extrait
        excerpt.style.display = 'block';
        fullContent.style.display = 'none';
        btn.textContent = 'Lire la suite';
    }
}

function toggleComments(index) {
    const commentsSection = document.getElementById('comments-section-' + index);
    const btn = document.getElementById('btn-comments-' + index);
    
    if (commentsSection.style.display === 'none') {
        // Montrer les commentaires
        commentsSection.style.display = 'block';
        btn.innerHTML = '<i class="fas fa-times"></i> Masquer les commentaires';
    } else {
        // Cacher les commentaires
        commentsSection.style.display = 'none';
        const commentCount = commentsSection.querySelector('h4').textContent.match(/\d+/)[0];
        btn.innerHTML = '<i class="fas fa-comments"></i> Commentaires (' + commentCount + ')';
    }
}
</script>
```
