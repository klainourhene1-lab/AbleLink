<section class="contact spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="section-title"><span>Success Story</span><h2><?php echo isset($story) ? 'Modifier' : 'Créer'; ?></h2></div>
                <form method="post" enctype="multipart/form-data" action="<?php echo isset($story) ? '/projetweb/ablelink/success-stories/update' : '/projetweb/ablelink/success-stories/store'; ?>">
                    <?php if (isset($story)): ?>
                        <input type="hidden" name="id" value="<?php echo (int)$story['id']; ?>">
                    <?php endif; ?>
                    <div class="form-group"><input class="form-control" type="text" name="title" placeholder="Titre" value="<?php echo isset($story) ? htmlspecialchars($story['title']) : ''; ?>" required></div>
                    <div class="form-group"><input class="form-control" type="text" name="author" placeholder="Auteur" value="<?php echo isset($story) ? htmlspecialchars($story['author']) : ''; ?>" required></div>
                    <div class="form-group"><textarea class="form-control" name="content" rows="6" placeholder="Votre histoire" required><?php echo isset($story) ? htmlspecialchars($story['content'] ?? ($story['description'] ?? '')) : ''; ?></textarea></div>
                    <div class="form-group"><input class="form-control" type="text" name="video_url" placeholder="Lien vidéo (optionnel)" value="<?php echo isset($story) ? htmlspecialchars($story['video_url'] ?? '') : ''; ?>"></div>
                    <div class="form-group"><input class="form-control" type="file" name="image" accept="image/*"></div>
                    <button type="submit" class="site-btn"><?php echo isset($story) ? 'Mettre à jour' : 'Publier'; ?></button>
                </form>
            </div>
        </div>
    </div>
</section>
