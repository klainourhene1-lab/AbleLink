<?php
$pageTitle = "Gestion des offres - AbleLink";
require_once __DIR__ . '/../layout/header.php';

// Calculate statistics for job offers
$total_offres = count($offres ?? []);
$offres_actives = $total_offres; // All are active by default
$offres_cdi = 0;
$offres_recentes = 0;

foreach ($offres ?? [] as $offre_item) {
    if ($offre_item['type_contrat'] === 'CDI') {
        $offres_cdi++;
    }
    // Count offers created in last 7 days
    $date_pub = strtotime($offre_item['date_publication'] ?? 'now');
    if ($date_pub > strtotime('-7 days')) {
        $offres_recentes++;
    }
}
?>

<?php 
$current_action = $_GET['action'] ?? 'gestion';
if ($current_action === 'gestion' || (!isset($offre) && $current_action !== 'add')): 
?>

<!-- Page Header -->
<div class="candidatures-header" style="background: #1a1f3a; padding: 40px 0; margin-bottom: 40px;">
    <div class="container">
        <h1 style="color: #ffffff; font-size: 32px; font-weight: 700; margin: 0 0 10px 0;">Gestion des Offres d'Emploi</h1>
        <p style="color: #a0a0a0; margin: 0; font-size: 14px;">CRUD complet pour gérer toutes vos offres d'emploi inclusives</p>
    </div>
</div>

<!-- Success/Error Messages -->
<?php
$success = $_GET['success'] ?? null;
if ($success == 1) {
    echo '<div class="container"><div class="alert alert-success" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; padding: 18px 25px; border-radius: 12px; margin-bottom: 30px; border: none; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);"><i class="fa fa-check-circle"></i> Offre ajoutée avec succès !</div></div>';
} elseif ($success == 2) {
    echo '<div class="container"><div class="alert alert-success" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #fff; padding: 18px 25px; border-radius: 12px; margin-bottom: 30px; border: none; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);"><i class="fa fa-check-circle"></i> Offre modifiée avec succès !</div></div>';
} elseif ($success == 3) {
    echo '<div class="container"><div class="alert alert-success" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #fff; padding: 18px 25px; border-radius: 12px; margin-bottom: 30px; border: none; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);"><i class="fa fa-trash"></i> Offre supprimée avec succès !</div></div>';
}
?>

<!-- Statistics Cards -->
<section class="statistics-section" style="padding: 0 0 50px 0;">
    <div class="container">
        <div class="row">
            <!-- Total Offres -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 15px; padding: 25px; color: white; box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3); position: relative; overflow: hidden;">
                    <div style="position: relative; z-index: 2;">
                        <p style="margin: 0 0 10px 0; font-size: 14px; opacity: 0.9;">Total des Offres</p>
                        <h2 style="margin: 0; font-size: 42px; font-weight: 700;"><?= $total_offres ?></h2>
                    </div>
                    <i class="fa fa-briefcase" style="position: absolute; right: 20px; bottom: 20px; font-size: 60px; opacity: 0.2;"></i>
                </div>
            </div>

            <!-- Nouvelles (7 jours) -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card" style="background: linear-gradient(135deg, #fb923c 0%, #f97316 100%); border-radius: 15px; padding: 25px; color: white; box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3); position: relative; overflow: hidden;">
                    <div style="position: relative; z-index: 2;">
                        <p style="margin: 0 0 10px 0; font-size: 14px; opacity: 0.9;">Nouvelles (7j)</p>
                        <h2 style="margin: 0; font-size: 42px; font-weight: 700;"><?= $offres_recentes ?></h2>
                    </div>
                    <i class="fa fa-clock-o" style="position: absolute; right: 20px; bottom: 20px; font-size: 60px; opacity: 0.2;"></i>
                </div>
            </div>

            <!-- Offres Actives -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 15px; padding: 25px; color: white; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3); position: relative; overflow: hidden;">
                    <div style="position: relative; z-index: 2;">
                        <p style="margin: 0 0 10px 0; font-size: 14px; opacity: 0.9;">Offres Actives</p>
                        <h2 style="margin: 0; font-size: 42px; font-weight: 700;"><?= $offres_actives ?></h2>
                    </div>
                    <i class="fa fa-check-circle" style="position: absolute; right: 20px; bottom: 20px; font-size: 60px; opacity: 0.2;"></i>
                </div>
            </div>

            <!-- Postes CDI -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 15px; padding: 25px; color: white; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); position: relative; overflow: hidden;">
                    <div style="position: relative; z-index: 2;">
                        <p style="margin: 0 0 10px 0; font-size: 14px; opacity: 0.9;">Postes CDI</p>
                        <h2 style="margin: 0; font-size: 42px; font-weight: 700;"><?= $offres_cdi ?></h2>
                    </div>
                    <i class="fa fa-star" style="position: absolute; right: 20px; bottom: 20px; font-size: 60px; opacity: 0.2;"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Filtres et Actions Rapides Bar -->
<section class="filters-actions-section" style="padding: 0 0 30px 0;">
    <div class="container">
        <!-- Title -->
        <h3 style="color: #ffffff; font-size: 18px; font-weight: 600; margin-bottom: 15px;">Filtres et Actions Rapides</h3>
        
        <!-- Filters Bar -->
        <div class="filters-bar" style="background: rgba(30, 41, 59, 0.7); border: 1px solid rgba(139, 92, 246, 0.3); border-radius: 12px; padding: 15px 20px; margin-bottom: 30px;">
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                
                <!-- Filter Buttons -->
                <button class="filter-btn filter-cdi" data-type="CDI" 
                        style="background: linear-gradient(135deg, #fb923c 0%, #f97316 100%); color: white; border: none; border-radius: 25px; padding: 10px 20px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 8px rgba(249, 115, 22, 0.3); display: inline-flex; align-items: center; gap: 8px;">
                    <span style="width: 18px; height: 18px; background: white; border-radius: 50%; display: inline-block;"></span>
                    CDI
                </button>
                
                <button class="filter-btn filter-cdd" data-type="CDD" 
                        style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; border-radius: 25px; padding: 10px 20px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3); display: inline-flex; align-items: center; gap: 8px;">
                    <span style="width: 18px; height: 18px; background: white; border-radius: 50%; display: inline-block;"></span>
                    CDD
                </button>
                
                <button class="filter-btn filter-stage" data-type="Stage" 
                        style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 25px; padding: 10px 20px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3); display: inline-flex; align-items: center; gap: 8px;">
                    <span style="width: 18px; height: 18px; background: white; border-radius: 50%; display: inline-block;"></span>
                    Stage
                </button>
                
                <button class="filter-btn filter-freelance" data-type="Freelance" 
                        style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none; border-radius: 25px; padding: 10px 20px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3); display: inline-flex; align-items: center; gap: 8px;">
                    <span style="width: 18px; height: 18px; background: white; border-radius: 50%; display: inline-block;"></span>
                    Freelance
                </button>
                
                <!-- Spacer -->
                <div style="flex: 1;"></div>
                
                <!-- Action Buttons -->
                <a href="/projetttwebbbbbbbbb/index.php?controller=offre&action=add" class="action-btn" 
                   style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white; border: none; border-radius: 8px; padding: 10px 20px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3); text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa fa-plus"></i>
                    Ajouter une offre
                </a>
                
                <button class="action-btn" 
                        style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); color: white; border: none; border-radius: 8px; padding: 10px 20px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 8px rgba(6, 182, 212, 0.3); display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa fa-download"></i>
                    Exporter la liste (PDF)
                </button>
            </div>
        </div>
    </div>
</section>

<!-- CSS for Filter and Action Buttons -->
<style>
.filter-btn:hover, .action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
}

.filter-btn:active, .action-btn:active {
    transform: translateY(0);
}

/* Highlight active filter */
.filter-btn.active {
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3) !important;
}

/* Search input focus */
.filter-search-input:focus {
    outline: none;
    border-color: rgba(139, 92, 246, 0.5);
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}
</style>

<!-- JavaScript for Filter Interactions -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let activeFilter = null;
    
    // Filter button functionality
    document.querySelectorAll('.filter-btn').forEach(filterBtn => {
        filterBtn.addEventListener('click', function() {
            const type = this.dataset.type;
            
            // Toggle filter
            if (activeFilter === type) {
                // Deactivate filter
                activeFilter = null;
                document.querySelectorAll('.filter-btn').forEach(b => {
                    b.classList.remove('active');
                    b.style.opacity = '1';
                });
                // Show all rows
                document.querySelectorAll('.modern-table tbody tr').forEach(row => {
                    row.style.display = '';
                });
            } else {
                // Activate filter
                activeFilter = type;
                
                // Visual feedback
                document.querySelectorAll('.filter-btn').forEach(b => {
                    b.classList.remove('active');
                    b.style.opacity = '0.6';
                });
                this.classList.add('active');
                this.style.opacity = '1';
                
                // Filter table rows
                const rows = document.querySelectorAll('.modern-table tbody tr');
                rows.forEach(row => {
                    const typeCell = row.querySelector('td:nth-child(4)');
                    if (typeCell) {
                        const rowType = typeCell.textContent.trim();
                        if (rowType.includes(type)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            }
        });
    });
    
    // Export PDF functionality
    const exportBtn = document.querySelector('.action-btn:last-child');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            // Add title
            doc.setFontSize(18);
            doc.text('Liste des Offres d\'Emploi', 14, 20);
            
            // Add date
            doc.setFontSize(10);
            const today = new Date().toLocaleDateString('fr-FR');
            doc.text('Généré le: ' + today, 14, 28);
            
            // Get visible rows only
            const rows = [];
            document.querySelectorAll('.modern-table tbody tr').forEach(row => {
                if (row.style.display !== 'none') {
                    const cells = row.querySelectorAll('td');
                    rows.push([
                        cells[0]?.textContent.trim() || '', // Titre
                        cells[1]?.textContent.trim() || '', // Entreprise
                        cells[2]?.textContent.trim() || '', // Localisation
                        cells[3]?.textContent.trim() || '', // Type
                        cells[4]?.textContent.trim() || ''  // Salaire
                    ]);
                }
            });
            
            // Add table
            doc.autoTable({
                head: [['Titre', 'Entreprise', 'Localisation', 'Type', 'Salaire']],
                body: rows,
                startY: 35,
                styles: { fontSize: 8 },
                headStyles: { fillColor: [91, 33, 182] }
            });
            
            // Save PDF
            doc.save('offres_emploi_' + new Date().getTime() + '.pdf');
        });
    }
});
</script>

<!-- Table Section -->
<section class="table-section" style="padding: 0 0 60px 0;">
    <div class="container">
        <div class="table-header" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="color: #ffffff; margin: 0; font-size: 20px; font-weight: 600;">Liste des Offres d'Emploi</h3>
            <span style="color: #06b6d4; font-size: 14px; font-weight: 600;">2025+</span>
        </div>

        <div class="table-responsive">
            <table class="table modern-table" style="width: 100%; background: rgba(30, 41, 59, 0.8) !important; border-radius: 15px; overflow: hidden; border-collapse: separate; border-spacing: 0; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);">
                <thead style="background: #5b21b6 !important; border-bottom: none;">
                    <tr>
                        <th style="padding: 18px 20px; color: #ffffff; font-weight: 600; font-size: 14px; text-align: left; border: none;">Titre du Poste</th>
                        <th style="padding: 18px 20px; color: #ffffff; font-weight: 600; font-size: 14px; text-align: left; border: none;">Entreprise</th>
                        <th style="padding: 18px 20px; color: #ffffff; font-weight: 600; font-size: 14px; text-align: left; border: none;">Localisation</th>
                        <th style="padding: 18px 20px; color: #ffffff; font-weight: 600; font-size: 14px; text-align: left; border: none;">Type Contrat</th>
                        <th style="padding: 18px 20px; color: #ffffff; font-weight: 600; font-size: 14px; text-align: left; border: none;">Salaire</th>
                        <th style="padding: 18px 20px; color: #ffffff; font-weight: 600; font-size: 14px; text-align: left; border: none;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($offres)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #a0a0a0;">Aucune offre disponible</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($offres as $offre_item): ?>
                            <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                                <td style="padding: 20px; color: #ffffff !important; font-size: 15px; font-weight: 600; border: none;">
                                    <?= htmlspecialchars($offre_item['titre']) ?>
                                </td>
                                <td style="padding: 20px; color: #ffffff !important; font-size: 14px; font-weight: 500; border: none;">
                                    <?= htmlspecialchars($offre_item['entreprise']) ?>
                                </td>
                                <td style="padding: 20px; color: #ffffff !important; font-size: 14px; font-weight: 500; border: none;">
                                    <i class="fa fa-map-marker" style="color: #06b6d4; margin-right: 6px;"></i>
                                    <?= htmlspecialchars($offre_item['localisation']) ?>
                                </td>
                                <td style="padding: 20px; border: none;">
                                    <?php
                                    $contrat_config = [
                                        'CDI' => ['bg' => '#10b981', 'icon' => 'star'],
                                        'CDD' => ['bg' => '#3b82f6', 'icon' => 'calendar'],
                                        'Stage' => ['bg' => '#fb923c', 'icon' => 'graduation-cap'],
                                        'Freelance' => ['bg' => '#8b5cf6', 'icon' => 'user']
                                    ];
                                    $config = $contrat_config[$offre_item['type_contrat']] ?? ['bg' => '#6b7280', 'icon' => 'briefcase'];
                                    ?>
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: <?= $config['bg'] ?>; color: white; border-radius: 20px; font-size: 12px; font-weight: 600;">
                                        <i class="fa fa-<?= $config['icon'] ?>"></i>
                                        <?= htmlspecialchars($offre_item['type_contrat']) ?>
                                    </span>
                                </td>
                                <td style="padding: 20px; color: #10b981 !important; font-size: 14px; font-weight: 600; border: none;">
                                    <?= $offre_item['salaire'] ? number_format($offre_item['salaire'], 0, ',', ' ') . ' €' : '-' ?>
                                </td>
                                <td style="padding: 20px; border: none;">
                                    <div style="display: flex; gap: 8px;">
                                        <a href="/projetttwebbbbbbbbb/index.php?controller=offre&action=edit&id=<?= $offre_item['id'] ?>" 
                                           style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; padding: 8px 12px; border-radius: 8px; text-decoration: none; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px;">
                                            <i class="fa fa-edit"></i> Modifier
                                        </a>
                                        <a href="/projetttwebbbbbbbbb/index.php?controller=offre&action=delete&id=<?= $offre_item['id'] ?>" 
                                           style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 8px 12px; border-radius: 8px; text-decoration: none; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px;"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette offre ?')">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php else: ?>
    <!-- AI Assistant Section Begin -->
    <section class="ai-assistant-section" style="padding: 40px 0;">
        <div class="container">
            <div class="ai-header" style="text-align: center; margin-bottom: 30px;">
                <h2 style="color: #a78bfa; font-size: 28px; font-weight: 700; margin-bottom: 10px;">
                    ✍️ Assistant IA de Rédaction
                </h2>
                <p style="color: rgba(255, 255, 255, 0.7); font-size: 14px;">
                    Créez des annonces inclusives et optimisées grâce à l'intelligence artificielle
                </p>
            </div>

            <div class="row">
                <!-- Input Area -->
                <div class="col-lg-6 mb-4">
                    <div class="ai-input-card" style="
                        background: rgba(30, 41, 59, 0.8);
                        border: 1px solid rgba(139, 92, 246, 0.3);
                        border-radius: 15px;
                        padding: 25px;
                        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
                    ">
                        <h4 style="color: #a78bfa; margin-bottom: 15px; font-size: 16px; font-weight: 700;">
                            📝 Description de poste
                        </h4>
                        
                        <textarea id="jobDescription" 
                                  placeholder="Décrivez le poste ou collez une description existante..."
                                  style="
                                      width: 100%;
                                      min-height: 200px;
                                      padding: 15px;
                                      background: rgba(15, 23, 42, 0.8);
                                      border: 1px solid rgba(139, 92, 246, 0.3);
                                      border-radius: 12px;
                                      color: #e0e7ff;
                                      font-size: 14px;
                                      line-height: 1.6;
                                      resize: vertical;
                                      font-family: 'Inter', sans-serif;
                                  "></textarea>
                        
                        <div style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
                            <button onclick="analyzeJobOffer()" type="button" class="ai-btn ai-btn-analyze" style="
                                flex: 1;
                                padding: 12px 20px;
                                background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                                color: white;
                                border: none;
                                border-radius: 10px;
                                font-weight: 600;
                                font-size: 13px;
                                cursor: pointer;
                                transition: all 0.3s;
                                box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                gap: 8px;
                            ">
                                <i class="fa fa-search"></i>
                                Analyser
                            </button>
                            
                            <button onclick="optimizeJobOffer()" type="button" class="ai-btn ai-btn-optimize" style="
                                flex: 1;
                                padding: 12px 20px;
                                background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
                                color: white;
                                border: none;
                                border-radius: 10px;
                                font-weight: 600;
                                font-size: 13px;
                                cursor: pointer;
                                transition: all 0.3s;
                                box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                gap: 8px;
                            ">
                                <i class="fa fa-magic"></i>
                                Optimiser
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results Area -->
                <div class="col-lg-6 mb-4">
                    <div class="ai-results-card" style="
                        background: rgba(30, 41, 59, 0.8);
                        border: 1px solid rgba(139, 92, 246, 0.3);
                        border-radius: 15px;
                        padding: 25px;
                        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
                        min-height: 300px;
                    ">
                        <h4 style="color: #a78bfa; margin-bottom: 15px; font-size: 16px; font-weight: 700;">
                            🌟 Résultats
                        </h4>
                        
                        <div id="aiResults" style="color: #e0e7ff; font-size: 14px; line-height: 1.8;">
                            <div style="text-align: center; padding: 30px 20px; color: rgba(224, 231, 255, 0.5);">
                                <i class="fa fa-robot" style="font-size: 40px; margin-bottom: 12px; opacity: 0.3;"></i>
                                <p>Les résultats apparaîtront ici...</p>
                            </div>
                        </div>
                        
                        <div id="loadingSpinner" style="display: none; text-align: center; padding: 30px;">
                            <i class="fa fa-spinner fa-spin" style="font-size: 32px; color: #8b5cf6;"></i>
                            <p style="color: #a78bfa; margin-top: 12px;">Analyse en cours...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- AI Assistant Section End -->

    <!-- Form Section for Add/Edit -->
    <section class="services spad">
        <div class="container">
            <h2 style="color: #ffffff; margin-bottom: 30px;"><?= isset($offre) ? 'Modifier une offre' : 'Ajouter une offre' ?></h2>
            
            <form method="POST" style="max-width: 700px;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="titre" style="display: block; margin-bottom: 8px; color: #fff; font-weight: 600;">Titre *</label>
                            <input type="text" id="titre" name="titre" required 
                                   value="<?= isset($offre) ? htmlspecialchars($offre['titre']) : '' ?>"
                                   style="width: 100%; padding: 12px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 8px; font-size: 14px;">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="entreprise" style="display: block; margin-bottom: 8px; color: #fff; font-weight: 600;">Entreprise *</label>
                            <input type="text" id="entreprise" name="entreprise" required 
                                   value="<?= isset($offre) ? htmlspecialchars($offre['entreprise']) : '' ?>"
                                   style="width: 100%; padding: 12px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 8px; font-size: 14px;">
                        </div>
                    </div>
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="description" style="display: block; margin-bottom: 8px; color: #fff; font-weight: 600;">Description *</label>
                    <textarea id="description" name="description" required 
                              style="width: 100%; padding: 12px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 8px; font-size: 14px; min-height: 120px; resize: vertical;"><?= isset($offre) ? htmlspecialchars($offre['description']) : '' ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="localisation" style="display: block; margin-bottom: 8px; color: #fff; font-weight: 600;">Localisation *</label>
                            <input type="text" id="localisation" name="localisation" required 
                                   value="<?= isset($offre) ? htmlspecialchars($offre['localisation']) : '' ?>"
                                   style="width: 100%; padding: 12px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 8px; font-size: 14px;">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="type_contrat" style="display: block; margin-bottom: 8px; color: #fff; font-weight: 600;">Type de contrat *</label>
                            <select id="type_contrat" name="type_contrat" required 
                                    style="width: 100%; padding: 12px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 8px; font-size: 14px;">
                                <option value="">Sélectionner...</option>
                                <option value="CDI" <?= (isset($offre) && $offre['type_contrat'] === 'CDI') ? 'selected' : '' ?>>CDI</option>
                                <option value="CDD" <?= (isset($offre) && $offre['type_contrat'] === 'CDD') ? 'selected' : '' ?>>CDD</option>
                                <option value="Stage" <?= (isset($offre) && $offre['type_contrat'] === 'Stage') ? 'selected' : '' ?>>Stage</option>
                                <option value="Freelance" <?= (isset($offre) && $offre['type_contrat'] === 'Freelance') ? 'selected' : '' ?>>Freelance</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="salaire" style="display: block; margin-bottom: 8px; color: #fff; font-weight: 600;">Salaire (€)</label>
                            <input type="number" id="salaire" name="salaire" step="0.01" 
                                   value="<?= isset($offre) ? $offre['salaire'] : '' ?>"
                                   style="width: 100%; padding: 12px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 8px; font-size: 14px;">
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 30px;">
                    <button type="submit" style="padding: 14px 36px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600; margin-right: 10px;">
                        <i class="fa fa-<?= isset($offre) ? 'check' : 'plus' ?>"></i> <?= isset($offre) ? 'Modifier' : 'Ajouter' ?>
                    </button>
                    <a href="/projetttwebbbbbbbbb/index.php?controller=offre&action=gestion" style="padding: 14px 36px; background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; text-decoration: none; font-size: 16px; font-weight: 600;">
                        <i class="fa fa-times"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </section>
<?php endif; ?>

<!-- AI Assistant JavaScript -->
<script>
function analyzeJobOffer() {
    const description = document.getElementById('jobDescription').value.trim();
    
    if (!description) {
        alert('Veuillez saisir une description de poste');
        return;
    }
    
    showLoading();
    
    fetch('/projetttwebbbbbbbbb/index.php?controller=ai&action=analyze', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ description: description })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            displayAnalysisResults(data.analysis);
        } else {
            displayError(data.error || 'Une erreur est survenue');
        }
    })
    .catch(error => {
        hideLoading();
        displayError('Erreur de connexion: ' + error.message);
    });
}

function optimizeJobOffer() {
    const description = document.getElementById('jobDescription').value.trim();
    
    if (!description) {
        alert('Veuillez saisir une description de poste');
        return;
    }
    
    showLoading();
    
    fetch('/projetttwebbbbbbbbb/index.php?controller=ai&action=optimize', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ description: description })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            displayOptimizedResults(data.optimized);
        } else {
            displayError(data.error || 'Une erreur est survenue');
        }
    })
    .catch(error => {
        hideLoading();
        displayError('Erreur de connexion: ' + error.message);
    });
}

function showLoading() {
    document.getElementById('aiResults').style.display = 'none';
    document.getElementById('loadingSpinner').style.display = 'block';
}

function hideLoading() {
    document.getElementById('loadingSpinner').style.display = 'none';
    document.getElementById('aiResults').style.display = 'block';
}

function displayAnalysisResults(analysis) {
    const resultsDiv = document.getElementById('aiResults');
    resultsDiv.innerHTML = `
        <div style="margin-bottom: 15px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <span style="font-size: 22px; font-weight: 700; color: ${getScoreColor(analysis.score)}">${analysis.score}/100</span>
                <span style="color: #a78bfa; font-size: 13px;">Score d'inclusivité</span>
            </div>
            <div style="height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; overflow: hidden;">
                <div style="height: 100%; width: ${analysis.score}%; background: linear-gradient(90deg, ${getScoreColor(analysis.score)}, #8b5cf6); border-radius: 3px; transition: width 0.3s;"></div>
            </div>
        </div>
        
        <div style="margin-bottom: 15px;">
            <h5 style="color: #34d399; font-size: 14px; margin-bottom: 8px;">✅ Points positifs</h5>
            <ul style="margin: 0; padding-left: 18px; color: rgba(224, 231, 255, 0.9); font-size: 13px;">
                ${analysis.positives.map(p => `<li style="margin-bottom: 4px;">${p}</li>`).join('')}
            </ul>
        </div>
        
        <div style="margin-bottom: 15px;">
            <h5 style="color: #fb923c; font-size: 14px; margin-bottom: 8px;">⚠️ À améliorer</h5>
            <ul style="margin: 0; padding-left: 18px; color: rgba(224, 231, 255, 0.9); font-size: 13px;">
                ${analysis.improvements.map(i => `<li style="margin-bottom: 4px;">${i}</li>`).join('')}
            </ul>
        </div>
        
        <div>
            <h5 style="color: #a78bfa; font-size: 14px; margin-bottom: 8px;">💡 Suggestions</h5>
            <ul style="margin: 0; padding-left: 18px; color: rgba(224, 231, 255, 0.9); font-size: 13px;">
                ${analysis.suggestions.map(s => `<li style="margin-bottom: 4px;">${s}</li>`).join('')}
            </ul>
        </div>
    `;
}

function displayOptimizedResults(optimized) {
    window.optimizedText = optimized.text; // Store for later use
    const resultsDiv = document.getElementById('aiResults');
    resultsDiv.innerHTML = `
        <div style="margin-bottom: 15px;">
            <h5 style="color: #34d399; font-size: 14px; margin-bottom: 8px;">✨ Version optimisée</h5>
            <div style="
                background: rgba(15, 23, 42, 0.8);
                border: 1px solid rgba(139, 92, 246, 0.3);
                border-radius: 10px;
                padding: 12px;
                color: #e0e7ff;
                line-height: 1.6;
                max-height: 300px;
                overflow-y: auto;
                font-size: 13px;
            ">
                ${optimized.text.replace(/\n/g, '<br>')}
            </div>
        </div>
        
        <div style="margin-top: 12px; display: flex; gap: 8px;">
            <button onclick="insertIntoForm()" style="
                flex: 1;
                padding: 10px;
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                color: white;
                border: none;
                border-radius: 8px;
                font-weight: 600;
                font-size: 12px;
                cursor: pointer;
                transition: all 0.3s;
            ">
                <i class="fa fa-arrow-down"></i> Utiliser dans le formulaire
            </button>
            <button onclick="copyToClipboard()" style="
                flex: 1;
                padding: 10px;
                background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
                color: white;
                border: none;
                border-radius: 8px;
                font-weight: 600;
                font-size: 12px;
                cursor: pointer;
                transition: all 0.3s;
            ">
                <i class="fa fa-copy"></i> Copier
            </button>
        </div>
        
        ${optimized.changes ? `
        <div style="margin-top: 15px;">
            <h5 style="color: #a78bfa; font-size: 14px; margin-bottom: 8px;">📝 Modifications</h5>
            <ul style="margin: 0; padding-left: 18px; color: rgba(224, 231, 255, 0.9); font-size: 12px;">
                ${optimized.changes.map(c => `<li style="margin-bottom: 4px;">${c}</li>`).join('')}
            </ul>
        </div>
        ` : ''}
    `;
}

function insertIntoForm() {
    const descriptionField = document.getElementById('description');
    if (descriptionField && window.optimizedText) {
        descriptionField.value = window.optimizedText;
        descriptionField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        alert('✅ Texte inséré dans le formulaire !');
    }
}

function displayError(message) {
    const resultsDiv = document.getElementById('aiResults');
    resultsDiv.innerHTML = `
        <div style="text-align: center; padding: 30px 15px;">
            <i class="fa fa-exclamation-triangle" style="font-size: 40px; color: #ef4444; margin-bottom: 12px;"></i>
            <p style="color: #fca5a5; font-size: 13px;">${message}</p>
        </div>
    `;
}

function getScoreColor(score) {
    if (score >= 80) return '#10b981';
    if (score >= 60) return '#fb923c';
    return '#ef4444';
}

function copyToClipboard() {
    if (window.optimizedText) {
        navigator.clipboard.writeText(window.optimizedText).then(() => {
            alert('📋 Texte copié dans le presse-papier !');
        });
    }
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>