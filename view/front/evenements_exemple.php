<?php
$pageTitle = "Évaluations & Événements - AbleLink";
include __DIR__ . '/../layout/header.php';
?>

<!-- Hero Banner Section -->
<section class="hero-banner-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="hero-banner-content">
                    <h1>Évaluations & Événements<br>Inclusifs</h1>
                    <p class="hero-subtitle">Découvrez, participez et évaluez les événements inclusifs</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Events Section -->
<section class="events-list-section">
    <div class="container">
        <!-- Section Title -->
        <div class="section-title">
            <h3>Événements disponibles</h3>
        </div>

        <!-- Search and Filters -->
        <div class="filters-container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="search-box">
                        <input type="text" class="form-control search-input" placeholder="Rechercher par titre, lieu...">
                        <button class="btn-search">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-lg-6">
                    <select class="form-control filter-select">
                        <option>Tous les types d'accessibilité</option>
                        <option>Accessibilité physique</option>
                        <option>Accessibilité auditive</option>
                        <option>Accessibilité visuelle</option>
                    </select>
                </div>
                <div class="col-lg-6">
                    <select class="form-control filter-select">
                        <option>Toutes les catégories</option>
                        <option>Conférences</option>
                        <option>Ateliers</option>
                        <option>Formations</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-lg-12 text-center">
                    <button class="btn primary-btn">
                        <i class="fa fa-filter"></i> Filtrer les résultats
                    </button>
                </div>
            </div>
        </div>

        <!-- Events Cards -->
        <div class="events-grid">
            <div class="row">
                <!-- Event Card 1 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="event-card">
                        <div class="event-header">
                            <div class="event-badge">
                                <i class="fa fa-wheelchair"></i> Accessible
                            </div>
                            <h4>Conference Inclusivité</h4>
                        </div>
                        <div class="event-meta">
                            <div class="meta-item">
                                <i class="fa fa-calendar"></i>
                                <span>15 décembre 2025 • 10:00</span>
                            </div>
                            <div class="meta-item">
                                <i class="fa fa-map-marker"></i>
                                <span>Paris, France</span>
                            </div>
                        </div>
                        <div class="event-description">
                            <p>Une conférence dédiée aux bonnes pratiques de l'inclusivité dans le monde professionnel.</p>
                        </div>
                        <div class="event-rating">
                            <div class="stars">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star-o"></i>
                            </div>
                            <span class="participants">150 participants</span>
                        </div>
                        <div class="event-actions">
                            <button class="btn btn-details">Détails</button>
                            <button class="btn btn-modify">Modifier</button>
                        </div>
                    </div>
                </div>

                <!-- Event Card 2 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="event-card">
                        <div class="event-header">
                            <div class="event-badge">
                                <i class="fa fa-wheelchair"></i> Accessible
                            </div>
                            <h4>Atelier Accessibilité Web</h4>
                        </div>
                        <div class="event-meta">
                            <div class="meta-item">
                                <i class="fa fa-calendar"></i>
                                <span>20 décembre 2025 • 14:00</span>
                            </div>
                            <div class="meta-item">
                                <i class="fa fa-map-marker"></i>
                                <span>Lyon, France</span>
                            </div>
                        </div>
                        <div class="event-description">
                            <p>Apprenez à créer des sites web accessibles à tous, conformes aux normes WCAG 2.1.</p>
                        </div>
                        <div class="event-rating">
                            <div class="stars">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <span class="participants">85 participants</span>
                        </div>
                        <div class="event-actions">
                            <button class="btn btn-details">Détails</button>
                            <button class="btn btn-modify">Modifier</button>
                        </div>
                    </div>
                </div>

                <!-- Event Card 3 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="event-card">
                        <div class="event-header">
                            <div class="event-badge">
                                <i class="fa fa-wheelchair"></i> Accessible
                            </div>
                            <h4>Forum Emploi Inclusif</h4>
                        </div>
                        <div class="event-meta">
                            <div class="meta-item">
                                <i class="fa fa-calendar"></i>
                                <span>25 décembre 2025 • 09:00</span>
                            </div>
                            <div class="meta-item">
                                <i class="fa fa-map-marker"></i>
                                <span>Marseille, France</span>
                            </div>
                        </div>
                        <div class="event-description">
                            <p>Rencontrez des employeurs engagés dans une démarche d'inclusion et d'égalité des chances.</p>
                        </div>
                        <div class="event-rating">
                            <div class="stars">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star-o"></i>
                            </div>
                            <span class="participants">200 participants</span>
                        </div>
                        <div class="event-actions">
                            <button class="btn btn-details">Détails</button>
                            <button class="btn btn-modify">Modifier</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../layout/footer.php'; ?>
