<?php
$pageTitle = "Gestion des candidatures - AbleLink";
require_once __DIR__ . '/../layout/header.php';

// Calculate statistics
$total_candidatures = count($candidatures);
$nouveau = 0;
$en_cours = 0;
$embauches = 0;
$refuses = 0;

foreach ($candidatures as $candidature) {
    if ($candidature['statut'] === 'en_attente') {
        $nouveau++;
    } elseif ($candidature['statut'] === 'acceptee') {
        $embauches++;
    } elseif ($candidature['statut'] === 'refusee') {
        $refuses++;
    }
}
$en_cours = $total_candidatures - $nouveau - $embauches - $refuses;
?>

<!-- Page Header -->
<div class="candidatures-header" style="background: #1a1f3a; padding: 40px 0; margin-bottom: 40px;">
    <div class="container">
        <h1 style="color: #ffffff; font-size: 32px; font-weight: 700; margin: 0 0 10px 0;">Gestion des Candidatures</h1>
        <p style="color: #a0a0a0; margin: 0; font-size: 14px;">Gestion des offres d'emploi et des candidatures</p>
    </div>
</div>

<!-- Statistics Cards -->
<section class="statistics-section" style="padding: 0 0 50px 0;">
    <div class="container">
        <div class="row">
            <!-- Total Candidatures -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card stat-card-purple" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 15px; padding: 25px; color: white; box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3); position: relative; overflow: hidden;">
                    <div style="position: relative; z-index: 2;">
                        <p style="margin: 0 0 10px 0; font-size: 14px; opacity: 0.9;">Candidatures Totales</p>
                        <h2 style="margin: 0; font-size: 42px; font-weight: 700;"><?= $total_candidatures ?></h2>
                    </div>
                    <i class="fa fa-users" style="position: absolute; right: 20px; bottom: 20px; font-size: 60px; opacity: 0.2;"></i>
                </div>
            </div>

            <!-- Nouveau -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card stat-card-orange" style="background: linear-gradient(135deg, #fb923c 0%, #f97316 100%); border-radius: 15px; padding: 25px; color: white; box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3); position: relative; overflow: hidden;">
                    <div style="position: relative; z-index: 2;">
                        <p style="margin: 0 0 10px 0; font-size: 14px; opacity: 0.9;">Nouveau</p>
                        <h2 style="margin: 0; font-size: 42px; font-weight: 700;"><?= $nouveau ?></h2>
                    </div>
                    <i class="fa fa-file-text" style="position: absolute; right: 20px; bottom: 20px; font-size: 60px; opacity: 0.2;"></i>
                </div>
            </div>

            <!-- En Cours -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card stat-card-blue" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 15px; padding: 25px; color: white; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3); position: relative; overflow: hidden;">
                    <div style="position: relative; z-index: 2;">
                        <p style="margin: 0 0 10px 0; font-size: 14px; opacity: 0.9;">En Cours</p>
                        <h2 style="margin: 0; font-size: 42px; font-weight: 700;"><?= $en_cours ?></h2>
                    </div>
                    <i class="fa fa-refresh" style="position: absolute; right: 20px; bottom: 20px; font-size: 60px; opacity: 0.2;"></i>
                </div>
            </div>

            <!-- Embauchés -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card stat-card-green" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 15px; padding: 25px; color: white; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); position: relative; overflow: hidden;">
                    <div style="position: relative; z-index: 2;">
                        <p style="margin: 0 0 10px 0; font-size: 14px; opacity: 0.9;">Embauchés (Mois)</p>
                        <h2 style="margin: 0; font-size: 42px; font-weight: 700;"><?= $embauches ?></h2>
                    </div>
                    <i class="fa fa-check-circle" style="position: absolute; right: 20px; bottom: 20px; font-size: 60px; opacity: 0.2;"></i>
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
                <button class="filter-btn filter-nouveau" data-status="en_attente" 
                        style="background: linear-gradient(135deg, #fb923c 0%, #f97316 100%); color: white; border: none; border-radius: 25px; padding: 10px 20px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 8px rgba(249, 115, 22, 0.3); display: inline-flex; align-items: center; gap: 8px;">
                    <span style="width: 18px; height: 18px; background: white; border-radius: 50%; display: inline-block;"></span>
                    En attente (<?= $nouveau ?>)
                </button>
                
                <button class="filter-btn filter-entretien" data-status="acceptee" 
                        style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 25px; padding: 10px 20px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3); display: inline-flex; align-items: center; gap: 8px;">
                    <span style="width: 18px; height: 18px; background: white; border-radius: 50%; display: inline-block;"></span>
                    Embauché (<?= $embauches ?>)
                </button>
                
                <button class="filter-btn filter-refuse" data-status="refusee" 
                        style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); color: white; border: none; border-radius: 25px; padding: 10px 20px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 8px rgba(107, 114, 128, 0.3); display: inline-flex; align-items: center; gap: 8px;">
                    <span style="width: 18px; height: 18px; background: white; border-radius: 50%; display: inline-block;"></span>
                    Refusés (<?= $refuses ?>)
                </button>
                
                <!-- Spacer -->
                <div style="flex: 1;"></div>
                
                <!-- Action Buttons -->
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
            const status = this.dataset.status;
            
            // Toggle filter
            if (activeFilter === status) {
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
                activeFilter = status;
                
                // Visual feedback
                document.querySelectorAll('.filter-btn').forEach(b => {
                    b.classList.remove('active');
                    b.style.opacity = '0.6';
                });
                this.classList.add('active');
                this.style.opacity = '1';
                
                // Filter table rows based on status
                const rows = document.querySelectorAll('.modern-table tbody tr');
                rows.forEach(row => {
                    const statusCell = row.querySelector('td:nth-child(3)');
                    if (statusCell) {
                        const rowStatus = statusCell.textContent.trim().toLowerCase();
                        let shouldShow = false;
                        
                        // Match status
                        if (status === 'en_attente' && rowStatus.includes('nouveau')) {
                            shouldShow = true;
                        } else if (status === 'en_cours' && rowStatus.includes('entretien')) {
                            shouldShow = true;
                        } else if (status === 'acceptee' && rowStatus.includes('embauché')) {
                            shouldShow = true;
                        } else if (status === 'refusee' && rowStatus.includes('refusé')) {
                            shouldShow = true;
                        }
                        
                        row.style.display = shouldShow ? '' : 'none';
                    }
                });
            }
        });
    });
    
    // Export PDF functionality
    const exportBtn = document.querySelector('.action-btn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            // Add title
            doc.setFontSize(18);
            doc.text('Liste des Candidatures', 14, 20);
            
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
                        cells[0]?.textContent.trim() || '', // Nom
                        cells[1]?.textContent.trim() || '', // Poste
                        cells[2]?.textContent.trim() || '', // Statut
                        cells[3]?.textContent.trim() || ''  // Date
                    ]);
                }
            });
            
            // Add table
            doc.autoTable({
                head: [['Nom du Candidat', 'Poste Appliqué', 'Statut', 'Reçu le']],
                body: rows,
                startY: 35,
                styles: { fontSize: 8 },
                headStyles: { fillColor: [91, 33, 182] }
            });
            
            // Save PDF
            doc.save('candidatures_' + new Date().getTime() + '.pdf');
        });
    }
});
</script>



<!-- Table Section -->
<section class="table-section" style="padding: 0 0 60px 0;">
    <div class="container">
        <div class="table-header" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="color: #ffffff; margin: 0; font-size: 20px; font-weight: 600;">Liste Détaillée des Candidatures</h3>
            <span style="color: #06b6d4; font-size: 14px; font-weight: 600;">2025+</span>
        </div>

        <div class="table-responsive">
            <table class="table modern-table" style="width: 100%; background: rgba(30, 41, 59, 0.8) !important; border-radius: 15px; overflow: hidden; border-collapse: separate; border-spacing: 0; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);">
                <thead style="background: #5b21b6 !important; border-bottom: none;">
                    <tr>
                        <th style="padding: 18px 20px; color: #ffffff; font-weight: 600; font-size: 14px; text-align: left; border: none;">Nom du Candidat</th>
                        <th style="padding: 18px 20px; color: #ffffff; font-weight: 600; font-size: 14px; text-align: left; border: none;">Poste Appliqué</th>
                        <th style="padding: 18px 20px; color: #ffffff; font-weight: 600; font-size: 14px; text-align: left; border: none;">Statut Actuel</th>
                        <th style="padding: 18px 20px; color: #ffffff; font-weight: 600; font-size: 14px; text-align: left; border: none;">Reçu le</th>
                        <th style="padding: 18px 20px; color: #ffffff; font-weight: 600; font-size: 14px; text-align: left; border: none;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($candidatures)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: #a0a0a0;">Aucune candidature disponible</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($candidatures as $candidature): ?>
                            <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                                <td style="padding: 20px; color: #ffffff !important; font-size: 15px; font-weight: 600; border: none;">
                                    <?= htmlspecialchars($candidature['nom_candidat']) ?>
                                </td>
                                <td style="padding: 20px; color: #ffffff !important; font-size: 14px; font-weight: 500; border: none;">
                                    <?= htmlspecialchars($candidature['offre_titre']) ?>
                                </td>
                                <td style="padding: 20px; border: none;">
                                    <?php
                                    $statut_config = [
                                        'en_attente' => ['label' => 'Nouveau', 'bg' => '#fb923c', 'icon' => 'file-text'],
                                        'acceptee' => ['label' => 'Embauché', 'bg' => '#10b981', 'icon' => 'check'],
                                        'refusee' => ['label' => 'Refusé', 'bg' => '#6b7280', 'icon' => 'times']
                                    ];
                                    $config = $statut_config[$candidature['statut']] ?? $statut_config['en_attente'];
                                    ?>
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: <?= $config['bg'] ?>; color: white; border-radius: 20px; font-size: 12px; font-weight: 600;">
                                        <i class="fa fa-<?= $config['icon'] ?>"></i>
                                        <?= $config['label'] ?>
                                    </span>
                                </td>
                                <td style="padding: 20px; color: #ffffff !important; font-size: 14px; font-weight: 500; border: none;">
                                    <i class="fa fa-arrow-right" style="color: #06b6d4; margin-right: 8px;"></i>
                                    <?= date('d déc. Y', strtotime($candidature['date_candidature'])) ?>
                                </td>
                                <td style="padding: 20px; border: none;">
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <!-- Status Change Dropdown -->
                                        <form method="POST" action="/projetttwebbbbbbbbb/index.php?controller=candidature&action=update_statut&id=<?= $candidature['id'] ?><?= isset($id_offre) ? '&id_offre=' . $id_offre : '' ?>" style="display: inline;">
                                            <select name="statut" onchange="this.form.submit()" 
                                                    style="background: rgba(139, 92, 246, 0.2); border: 1px solid rgba(139, 92, 246, 0.4); color: #fff; border-radius: 8px; padding: 8px 12px; font-size: 12px; cursor: pointer;">
                                                <option value="en_attente" <?= $candidature['statut'] === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                                                <option value="acceptee" <?= $candidature['statut'] === 'acceptee' ? 'selected' : '' ?>>Acceptée</option>
                                                <option value="refusee" <?= $candidature['statut'] === 'refusee' ? 'selected' : '' ?>>Refusée</option>
                                            </select>
                                        </form>
                                        
                                        <!-- Delete Button -->
                                        <a href="/projetttwebbbbbbbbb/index.php?controller=candidature&action=delete&id=<?= $candidature['id'] ?><?= isset($id_offre) ? '&id_offre=' . $id_offre : '' ?>" 
                                           style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 8px 12px; border-radius: 8px; text-decoration: none; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px;"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette candidature ?')">
                                            <i class="fa fa-trash"></i> Supprimer
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

<?php require_once __DIR__ . '/../layout/footer.php'; ?>