<?php
$pageTitle = "Postuler - AbleLink";
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-section set-bg" data-setbg="/projetttwebbbbbbbbb/assets/img/breadcrumb-bg.jpg">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="breadcrumb__text">
                    <h2>Postuler à cette offre</h2>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- Contact Section Begin -->
<section class="contact spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="contact__form">
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                            <?= htmlspecialchars($success) ?>
                        </div>
                        <a href="/projetttwebbbbbbbbb/index.php?controller=offre&action=liste" class="primary-btn">Retour aux offres</a>
                    <?php else: ?>
                        <div class="services__item" style="background: #f8f9fa; padding: 25px; border-radius: 8px; margin-bottom: 30px;">
                            <h4 style="color: #111; margin-bottom: 15px;"><?= htmlspecialchars($offre['titre']) ?></h4>
                            <p><strong>Entreprise:</strong> <?= htmlspecialchars($offre['entreprise']) ?></p>
                            <p><strong>Localisation:</strong> <?= htmlspecialchars($offre['localisation']) ?></p>
                            <p><strong>Type de contrat:</strong> <?= htmlspecialchars($offre['type_contrat']) ?></p>
                        </div>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-error" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label for="nom_candidat" style="display: block; margin-bottom: 8px; color: #111; font-weight: 600;">Nom complet *</label>
                                <input type="text" id="nom_candidat" name="nom_candidat" 
                                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            </div>

                            <div class="form-group" style="margin-bottom: 20px;">
                                <label for="email" style="display: block; margin-bottom: 8px; color: #111; font-weight: 600;">Email *</label>
                                <input type="email" id="email" name="email" 
                                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            </div>

                            <div class="form-group" style="margin-bottom: 20px;">
                                <label for="cv" style="display: block; margin-bottom: 8px; color: #111; font-weight: 600;">CV (PDF, DOC, DOCX - Max 5MB) *</label>
                                <input type="file" id="cv" name="cv" 
                                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            </div>

                            <button type="submit" class="primary-btn" style="padding: 14px 36px; background: #3a4ced; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
                                Envoyer ma candidature
                            </button>
                            <a href="/projetttwebbbbbbbbb/index.php?controller=offre&action=liste" class="primary-btn" style="background: #95a5a6; margin-left: 10px;">Annuler</a>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Contact Section End -->

<?php require_once __DIR__ . '/../layout/footer.php'; ?>