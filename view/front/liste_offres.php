<?php
$pageTitle = "Offres d'emploi - AbleLink";
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Purple Banner Section - Matching Colleague's Design -->
<section class="purple-banner-section" style="padding: 80px 0; margin-top: 20px;">
    <div class="container">
        <div class="purple-banner-content" style="background: linear-gradient(135deg, #5b6ff5 0%, #8b5cf6 50%, #a855f7 100%); border-radius: 30px; padding: 50px 60px; text-align: center; box-shadow: 0 20px 60px rgba(124, 58, 237, 0.4); position: relative; overflow: hidden;">
            <h1 style="color: #ffffff; font-size: 40px; font-weight: 800; margin-bottom: 15px; text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3); line-height: 1.2;">
                Offres d'emploi inclusives
            </h1>
            <p style="color: rgba(255, 255, 255, 0.95); font-size: 16px; font-weight: 400; margin: 0; max-width: 600px; margin-left: auto; margin-right: auto;">
                Découvrez des opportunités professionnelles adaptées à tous les profils.
            </p>
        </div>
    </div>
</section>
<!-- Purple Banner Section End -->


<!-- Services Section Begin -->
<section id="offres" class="services spad">
    <div class="container">
        <div class="row">
            <?php 
            if (empty($offres)) {
                echo '<div class="col-12 text-center"><p style="color: #fff;">Aucune offre disponible pour le moment.</p></div>';
            } else {
                $icons = ['fa-cog', 'fa-link', 'fa-desktop', 'fa-code', 'fa-database', 'fa-mobile'];
                $iconColors = ['#8b5cf6', '#06b6d4', '#ec4899', '#f59e0b', '#10b981', '#3b82f6'];
                $index = 0;
                
                foreach ($offres as $offre): 
                    $iconIndex = $index % count($icons);
            ?>
                <div class="col-lg-4 col-md-6 col-sm-12" style="margin-bottom: 30px;">
                    <div class="modern-job-card" style="
                            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
                            border: 1px solid rgba(139, 92, 246, 0.3);
                            border-radius: 20px;
                            padding: 30px;
                            position: relative;
                            overflow: hidden;
                            min-height: 480px;
                            transition: all 0.4s ease;
                            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
                        ">
                            <!-- Decorative Icon -->
                            <div style="position: absolute; top: 25px; right: 25px; width: 50px; height: 50px; background: rgba(139, 92, 246, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa <?= $icons[$iconIndex] ?>" style="color: <?= $iconColors[$iconIndex] ?>; font-size: 24px;"></i>
                            </div>
                            
                            <!-- Job Title -->
                            <h3 style="color: #ffffff; font-size: 24px; font-weight: 700; margin-bottom: 20px; padding-right: 60px;">
                                <?= htmlspecialchars($offre['titre']) ?>
                            </h3>
                            
                            <!-- Company Badge -->
                            <div style="margin-bottom: 15px;">
                                <span style="color: #a78bfa; font-size: 13px; opacity: 0.9;">Réseau tech</span>
                            </div>
                            
                            <!-- Job Details -->
                            <div style="margin-bottom: 20px;">
                                <p style="color: #e0e7ff; margin-bottom: 8px; font-size: 14px;">
                                    <i class="fa fa-map-marker" style="color: #8b5cf6; margin-right: 8px;"></i>
                                    <?= htmlspecialchars($offre['localisation']) ?>
                                </p>
                                <p style="color: #e0e7ff; margin-bottom: 8px; font-size: 14px;">
                                    <i class="fa fa-briefcase" style="color: #8b5cf6; margin-right: 8px;"></i>
                                    <?= htmlspecialchars($offre['type_contrat']) ?>
                                </p>
                                <?php if ($offre['salaire']): ?>
                                    <p style="color: #34d399; font-weight: 600; margin-bottom: 8px; font-size: 16px;">
                                        <i class="fa fa-money" style="margin-right: 8px;"></i>
                                        <?= number_format($offre['salaire'], 0, ',', ' ') ?> €
                                    </p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Description -->
                            <p style="color: rgba(224, 231, 255, 0.7); font-size: 13px; margin-bottom: 25px; line-height: 1.6;">
                                <?= htmlspecialchars(substr($offre['description'], 0, 100)) ?>...
                            </p>
                            
                            <!-- Action Buttons -->
                            <div style="position: absolute; bottom: 30px; left: 30px; right: 30px; display: flex; flex-direction: column; gap: 12px;">
                                <a href="/projetttwebbbbbbbbb/index.php?controller=offre&action=details&id=<?= $offre['id'] ?>" 
                                   class="btn-details-modern" 
                                   style="
                                       display: flex;
                                       align-items: center;
                                       justify-content: center;
                                       gap: 8px;
                                       padding: 12px 24px;
                                       background: transparent;
                                       color: #a78bfa;
                                       text-decoration: none;
                                       border: 2px solid #7c3aed;
                                       border-radius: 12px;
                                       transition: all 0.3s;
                                       font-weight: 600;
                                       font-size: 14px;
                                   ">
                                    <i class="fa fa-info-circle"></i>
                                    Voir détails
                                </a>
                                
                                <div style="display: flex; gap: 10px;">
                                    <button onclick="toggleFavoris(<?= $offre['id'] ?>)" 
                                            id="fav-btn-<?= $offre['id'] ?>"
                                            class="btn-favoris-modern" 
                                            style="
                                                flex: 1;
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                gap: 6px;
                                                padding: 14px 20px;
                                                background: rgba(236, 72, 153, 0.1);
                                                color: #ec4899;
                                                border: 2px solid #ec4899;
                                                border-radius: 12px;
                                                transition: all 0.3s;
                                                font-weight: 600;
                                                font-size: 14px;
                                                cursor: pointer;
                                            ">
                                        <i class="fa fa-heart-o" id="fav-icon-<?= $offre['id'] ?>"></i>
                                        <span id="fav-text-<?= $offre['id'] ?>">Favoris</span>
                                    </button>
                                    
                                    <a href="/projetttwebbbbbbbbb/index.php?controller=candidature&action=postuler&id_offre=<?= $offre['id'] ?>" 
                                       class="btn-apply-modern" 
                                       style="
                                           flex: 2;
                                       align-items: center;
                                       justify-content: center;
                                       gap: 8px;
                                       padding: 14px 24px;
                                       background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
                                       color: #ffffff;
                                       text-decoration: none;
                                       border: none;
                                       border-radius: 12px;
                                       transition: all 0.3s;
                                       font-weight: 700;
                                       font-size: 14px;
                                       text-transform: uppercase;
                                       letter-spacing: 0.5px;
                                       box-shadow: 0 4px 15px rgba(124, 58, 237, 0.4);
                                   ">
                                    POSTULER
                                </a>
                            </div>
                            
                            <!-- Decorative Graph (SVG) -->
                            <svg style="position: absolute; bottom: 0; right: 0; width: 120px; height: 60px; opacity: 0.15;" viewBox="0 0 120 60">
                                <polyline points="0,50 20,35 40,45 60,25 80,30 100,15 120,20" 
                                          fill="none" 
                                          stroke="#8b5cf6" 
                                          stroke-width="2"/>
                                <rect x="10" y="45" width="3" height="15" fill="#8b5cf6" opacity="0.6"/>
                                <rect x="30" y="40" width="3" height="20" fill="#8b5cf6" opacity="0.6"/>
                                <rect x="50" y="35" width="3" height="25" fill="#8b5cf6" opacity="0.6"/>
                                <rect x="70" y="42" width="3" height="18" fill="#8b5cf6" opacity="0.6"/>
                                <rect x="90" y="30" width="3" height="30" fill="#8b5cf6" opacity="0.6"/>
                            </svg>
                        </div>
                    </div>
                </div>
            <?php 
                    $index++;
                endforeach; 
            }
            ?>
        </div>
    </div>
</section>
<!-- Services Section End -->

<!-- Favorites JavaScript -->
<script>
function toggleFavoris(id_offre) {
    const btn = document.getElementById('fav-btn-' + id_offre);
    const icon = document.getElementById('fav-icon-' + id_offre);
    const text = document.getElementById('fav-text-' + id_offre);
    
    // Vérifier si déjà en favoris
    const isFavorite = icon.classList.contains('fa-heart');
    
    const endpoint = isFavorite ? 'supprimer' : 'ajouter';
    
    console.log('Toggle favoris - ID:', id_offre, 'Endpoint:', endpoint);
    
    fetch(`/projetttwebbbbbbbbb/index.php?controller=favoris&action=${endpoint}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id_offre: id_offre })
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.text(); // D'abord récupérer en text pour debug
    })
    .then(text => {
        console.log('Response text:', text);
        try {
            const data = JSON.parse(text);
            console.log('Parsed data:', data);
            
            if (data.success) {
                if (isFavorite) {
                    // Retirer des favoris
                    icon.classList.remove('fa-heart');
                    icon.classList.add('fa-heart-o');
                    text.textContent = 'Favoris';
                    btn.style.background = 'rgba(236, 72, 153, 0.1)';
                    btn.style.color = '#ec4899';
                    btn.style.borderColor = '#ec4899';
                } else {
                    // Ajouter aux favoris
                    icon.classList.remove('fa-heart-o');
                    icon.classList.add('fa-heart');
                    text.textContent = 'Ajouté';
                    btn.style.background = 'linear-gradient(135deg, #ec4899 0%, #db2777 100%)';
                    btn.style.color = '#ffffff';
                    btn.style.borderColor = '#db2777';
                }
                
                // Animation de feedback
                btn.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    btn.style.transform = 'scale(1)';
                }, 200);
            } else {
                alert(data.error || 'Erreur lors de l\'ajout aux favoris');
            }
        } catch (e) {
            console.error('Erreur de parsing JSON:', e);
            console.error('Texte reçu:', text);
            alert('Erreur: La réponse du serveur n\'est pas au format JSON attendu. Consultez la console (F12).');
        }
    })
    .catch(error => {
        console.error('Erreur complète:', error);
        alert('Erreur de connexion: ' + error.message + '\n\nVérifiez:\n1. Êtes-vous connecté?\n2. La table favoris existe-t-elle?\n3. Consultez la console (F12) pour plus de détails');
    });
}

// Vérifier les favoris au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM chargé, vérification des favoris...');
    const favButtons = document.querySelectorAll('[id^="fav-btn-"]');
    console.log('Nombre de boutons favoris trouvés:', favButtons.length);
    favButtons.forEach(btn => {
        const id_offre = btn.id.split('-')[2];
        checkFavorisStatus(id_offre);
    });
});

function checkFavorisStatus(id_offre) {
    fetch(`/projetttwebbbbbbbbb/index.php?controller=favoris&action=verifier&id_offre=${id_offre}`)
    .then(response => response.json())
    .then(data => {
        console.log('Status favori pour offre', id_offre, ':', data);
        if (data.isFavorite) {
            const icon = document.getElementById('fav-icon-' + id_offre);
            const text = document.getElementById('fav-text-' + id_offre);
            const btn = document.getElementById('fav-btn-' + id_offre);
            
            if (icon && text && btn) {
                icon.classList.remove('fa-heart-o');
                icon.classList.add('fa-heart');
                text.textContent = 'Ajouté';
                btn.style.background = 'linear-gradient(135deg, #ec4899 0%, #db2777 100%)';
                btn.style.color = '#ffffff';
                btn.style.borderColor = '#db2777';
            }
        }
    })
    .catch(error => {
        console.error('Erreur lors de la vérification du favori', id_offre, ':', error);
    });
}
</script>

<!-- Modern Job Card Styles -->
<style>
.modern-job-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(124, 58, 237, 0.5);
    border-color: rgba(139, 92, 246, 0.6);
}

.btn-details-modern:hover {
    background: rgba(124, 58, 237, 0.1);
    border-color: #a78bfa;
    color: #c4b5fd;
    transform: scale(1.02);
}

.btn-apply-modern:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(124, 58, 237, 0.6);
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
}

.modern-job-card {
    cursor: pointer;
}
</style>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>