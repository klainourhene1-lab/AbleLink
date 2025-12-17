<?php
$pageTitle = "Détails de l'offre - AbleLink";
require_once __DIR__ . '/../layout/header.php';
?>


<!-- Hero Section avec titre de l'offre -->
<section class="hero__item set-bg" data-setbg="/projetttwebbbbbbbbb/assets/img/breadcrumb-bg.jpg" style="background-image: url('/projetttwebbbbbbbbb/assets/img/breadcrumb-bg.jpg'); height: 40vh; min-height: 300px;">
    <div class="hero__text">
        <span><?= htmlspecialchars($offre['entreprise']) ?></span>
        <h2><?= htmlspecialchars($offre['titre']) ?></h2>
    </div>
</section>

<!-- Section Détails -->
<section class="services spad" style="padding: 60px 0;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- Card principale avec détails -->
                <div class="services__item" style="padding: 40px; margin-bottom: 30px;">
                    <h3 style="color: #e9d5ff; margin-bottom: 20px; font-size: 28px;">Description du poste</h3>
                    <p style="color: #d8b4fe; line-height: 1.8; font-size: 16px;">
                        <?= nl2br(htmlspecialchars($offre['description'])) ?>
                    </p>
                </div>

                <!-- Boutons d'action -->
                <div style="display: flex; gap: 15px; margin-bottom: 30px;">
                    <a href="/projetttwebbbbbbbbb/index.php?controller=candidature&action=postuler&id_offre=<?= $offre['id'] ?>" 
                       class="primary-btn" 
                       style="display: inline-flex; align-items: center; gap: 8px; padding: 15px 40px; background: #6366f1; color: #fff; text-decoration: none; border-radius: 10px; font-weight: 600;">
                        <i class="fa fa-paper-plane"></i> Postuler maintenant
                    </a>
                    <a href="/projetttwebbbbbbbbb/index.php?controller=offre&action=liste" 
                       class="btn-details" 
                       style="display: inline-flex; align-items: center; gap: 8px; padding: 15px 40px; background: transparent; color: #8b5cf6; text-decoration: none; border: 2px solid #8b5cf6; border-radius: 10px; font-weight: 600;">
                        <i class="fa fa-arrow-left"></i> Retour aux offres
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Card informations -->
                <div class="services__item" style="padding: 30px; margin-bottom: 20px;">
                    <h4 style="color: #e9d5ff; margin-bottom: 20px; font-size: 22px;">
                        <i class="fa fa-info-circle"></i> Informations
                    </h4>
                    
                    <div style="margin-bottom: 15px;">
                        <strong style="color: #c4b5fd; display: block; margin-bottom: 5px;">
                            <i class="fa fa-building"></i> Entreprise
                        </strong>
                        <p style="color: #a78bfa; margin: 0;">
                            <?= htmlspecialchars($offre['entreprise']) ?>
                        </p>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <strong style="color: #c4b5fd; display: block; margin-bottom: 5px;">
                            <i class="fa fa-map-marker"></i> Localisation
                        </strong>
                        <p style="color: #d8b4fe; margin: 0;">
                            <?= htmlspecialchars($offre['localisation']) ?>
                        </p>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <strong style="color: #c4b5fd; display: block; margin-bottom: 5px;">
                            <i class="fa fa-briefcase"></i> Type de contrat
                        </strong>
                        <p style="color: #d8b4fe; margin: 0;">
                            <?= htmlspecialchars($offre['type_contrat']) ?>
                        </p>
                    </div>

                    <?php if ($offre['salaire']): ?>
                        <div style="margin-bottom: 15px;">
                            <strong style="color: #c4b5fd; display: block; margin-bottom: 5px;">
                                <i class="fa fa-money"></i> Salaire
                            </strong>
                            <p style="color: #34d399; margin: 0; font-weight: 600; font-size: 18px;">
                                <?= number_format($offre['salaire'], 2, ',', ' ') ?> €
                            </p>
                        </div>
                    <?php endif; ?>

                    <div style="margin-bottom: 0;">
                        <strong style="color: #c4b5fd; display: block; margin-bottom: 5px;">
                            <i class="fa fa-calendar"></i> Date de publication
                        </strong>
                        <p style="color: #d8b4fe; margin: 0;">
                            <?= date('d/m/Y', strtotime($offre['date_publication'])) ?>
                        </p>
                    </div>
                </div>

                <!-- Card partage -->
                <div class="services__item" style="padding: 25px;">
                    <h5 style="color: #e9d5ff; margin-bottom: 15px;">
                        <i class="fa fa-share-alt"></i> Partager cette offre
                    </h5>
                    <div style="display: flex; gap: 10px;">
                        <a href="#" class="btn-details" style="padding: 10px 15px; flex: 1; text-align: center;">
                            <i class="fa fa-facebook"></i>
                        </a>
                        <a href="#" class="btn-details" style="padding: 10px 15px; flex: 1; text-align: center;">
                            <i class="fa fa-twitter"></i>
                        </a>
                        <a href="#" class="btn-details" style="padding: 10px 15px; flex: 1; text-align: center;">
                            <i class="fa fa-linkedin"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
