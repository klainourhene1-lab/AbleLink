<?php
$pageTitle = "Mes Favoris - AbleLink";
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Purple Banner Section -->
<section class="purple-banner-section" style="padding: 80px 0; margin-top: 20px;">
    <div class="container">
        <div class="purple-banner-content" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 50%, #be185d 100%); border-radius: 30px; padding: 50px 60px; text-align: center; box-shadow: 0 20px 60px rgba(236, 72, 153, 0.4); position: relative; overflow: hidden;">
            <h1 style="color: #ffffff; font-size: 40px; font-weight: 800; margin-bottom: 15px; text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3); line-height: 1.2;">
                <i class="fa fa-heart"></i> Mes Offres Favorites
            </h1>
            <p style="color: rgba(255, 255, 255, 0.95); font-size: 16px; font-weight: 400; margin: 0; max-width: 600px; margin-left: auto; margin-right: auto;">
                Retrouvez toutes les offres que vous avez sauvegardées
            </p>
        </div>
    </div>
</section>

<!-- Favoris Section -->
<section id="favoris" class="services spad">
    <div class="container">
        <?php if (empty($favoris)): ?>
            <div class="row">
                <div class="col-lg-12 text-center" style="padding: 60px 20px;">
                    <i class="fa fa-heart-o" style="font-size: 100px; color: #ec4899; opacity: 0.3; margin-bottom: 20px;"></i>
                    <h3 style="color: #ffffff; margin-bottom: 15px;">Aucun favori pour le moment</h3>
                    <p style="color: rgba(255, 255, 255, 0.7); margin-bottom: 30px;">
                        Parcourez nos offres et ajoutez celles qui vous intéressent à vos favoris
                    </p>
                    <a href="/projetttwebbbbbbbbb/index.php?controller=offre&action=liste" 
                       style="display: inline-block; padding: 15px 40px; background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); color: #fff; text-decoration: none; border-radius: 25px; font-weight: 600; transition: all 0.3s;">
                        <i class="fa fa-search"></i> Voir les offres
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <?php 
                $icons = ['fa-cog', 'fa-link', 'fa-desktop', 'fa-code', 'fa-database', 'fa-mobile'];
                $iconColors = ['#8b5cf6', '#06b6d4', '#ec4899', '#f59e0b', '#10b981', '#3b82f6'];
                $index = 0;
                foreach ($favoris as $offre): 
                    $iconIndex = $index % count($icons);
                ?>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="modern-job-card" style="
                            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
                            border: 1px solid rgba(236, 72, 153, 0.3);
                            border-radius: 20px;
                            padding: 30px;
                            position: relative;
                            overflow: hidden;
                            min-height: 480px;
                            transition: all 0.4s ease;
                            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
                        ">
                            <!-- Badge Favori -->
                            <div style="position: absolute; top: 15px; left: 15px; background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); color: white; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; display: flex; align-items: center; gap: 5px;">
                                <i class="fa fa-heart"></i> FAVORI
                            </div>
                            
                            <!-- Decorative Icon -->
                            <div style="position: absolute; top: 25px; right: 25px; width: 50px; height: 50px; background: rgba(236, 72, 153, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa <?= $icons[$iconIndex] ?>" style="color: <?= $iconColors[$iconIndex] ?>; font-size: 24px;"></i>
                            </div>
                            
                            <!-- Job Title -->
                            <h3 style="color: #ffffff; font-size: 24px; font-weight: 700; margin-bottom: 20px; padding-right: 60px; margin-top: 30px;">
                                <?= htmlspecialchars($offre['titre']) ?>
                            </h3>
                            
                            <!-- Job Details -->
                            <div style="margin-bottom: 20px;">
                                <p style="color: #e0e7ff; margin-bottom: 8px; font-size: 14px;">
                                    <i class="fa fa-map-marker" style="color: #ec4899; margin-right: 8px;"></i>
                                    <?= htmlspecialchars($offre['localisation']) ?>
                                </p>
                                <p style="color: #e0e7ff; margin-bottom: 8px; font-size: 14px;">
                                    <i class="fa fa-briefcase" style="color: #ec4899; margin-right: 8px;"></i>
                                    <?= htmlspecialchars($offre['type_contrat']) ?>
                                </p>
                                <?php if ($offre['salaire']): ?>
                                    <p style="color: #34d399; font-weight: 600; margin-bottom: 8px; font-size: 16px;">
                                        <i class="fa fa-money" style="margin-right: 8px;"></i>
                                        <?= number_format($offre['salaire'], 0, ',', ' ') ?> €
                                    </p>
                                <?php endif; ?>
                                <p style="color: rgba(236, 72, 153, 0.8); margin-top: 12px; font-size: 12px;">
                                    <i class="fa fa-calendar" style="margin-right: 6px;"></i>
                                    Ajouté le <?= date('d/m/Y', strtotime($offre['date_ajout'])) ?>
                                </p>
                            </div>
                            
                            <!-- Description -->
                            <p style="color: rgba(224, 231, 255, 0.7); font-size: 13px; margin-bottom: 25px; line-height: 1.6;">
                                <?= htmlspecialchars(substr($offre['description'], 0, 100)) ?>...
                            </p>
                            
                            <!-- Action Buttons -->
                            <div style="position: absolute; bottom: 30px; left: 30px; right: 30px; display: flex; gap: 10px;">
                                <button onclick="toggleFavoris(<?= $offre['id'] ?>)" 
                                        id="fav-btn-<?= $offre['id'] ?>"
                                        style="
                                            flex: 1;
                                            padding: 12px;
                                            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
                                            color: white;
                                            border: none;
                                            border-radius: 10px;
                                            font-weight: 600;
                                            font-size: 13px;
                                            cursor: pointer;
                                            transition: all 0.3s;
                                        ">
                                    <i class="fa fa-heart" id="fav-icon-<?= $offre['id'] ?>"></i>
                                </button>
                                <a href="/projetttwebbbbbbbbb/index.php?controller=candidature&action=postuler&id_offre=<?= $offre['id'] ?>" 
                                   style="
                                       flex: 3;
                                       padding: 12px;
                                       background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
                                       color: white;
                                       text-decoration: none;
                                       border-radius: 10px;
                                       font-weight: 700;
                                       font-size: 13px;
                                       text-align: center;
                                       transition: all 0.3s;
                                   ">
                                    POSTULER
                                </a>
                            </div>
                        </div>
                    </div>
                <?php 
                    $index++;
                endforeach; 
                ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script src="/projetttwebbbbbbbbb/view/front/liste_offres.php"></script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
