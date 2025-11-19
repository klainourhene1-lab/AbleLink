<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Évaluations & Événements - AbeLink</title>
    <link rel="stylesheet" href="shared.css">
    <style>
        /* Role badges */
        .role-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }

        .role-badge.user {
            background: rgba(52, 152, 219, 0.2);
            color: #3498db;
        }

        .role-badge.company {
            background: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
        }

        .role-badge.inclusion {
            background: rgba(155, 89, 182, 0.2);
            color: #9b59b6;
        }

        .role-badge.admin {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }

        /* Role selector */
        .role-selector {
            display: flex;
            align-items: center;
            margin-left: 20px;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
        }

        .panel {
            background: rgba(255,255,255,0.1);
            margin: 5% auto;
            padding: 20px;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            position: relative;
        }

        /* Form styles */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .checkbox-group {
            display: grid;
            gap: 10px;
            margin-top: 8px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.1);
            color: white;
            font-family: inherit;
            resize: vertical;
            min-height: 100px;
        }

        textarea:focus {
            outline: none;
            border-color: #3498db;
        }

        .small {
            font-size: 12px;
            color: #ccc;
        }

        /* Event card styles */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }

        .event-card {
            padding: 20px;
            border-radius: 12px;
            transition: transform 0.2s ease;
        }

        .event-card:hover {
            transform: translateY(-2px);
        }

        .event-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: white;
        }

        .company-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .company-logo {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #3498db;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }

        .company-name {
            color: #ccc;
            font-size: 14px;
        }

        .meta {
            color: #ccc;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin: 10px 0;
        }

        .tag {
            padding: 4px 8px;
            background: rgba(255,255,255,0.1);
            border-radius: 12px;
            font-size: 12px;
            color: #ccc;
        }

        .tag.status {
            background: rgba(52, 152, 219, 0.2);
            color: #3498db;
        }

        .tag.status.publie {
            background: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
        }

        .tag.status.brouillon {
            background: rgba(241, 196, 15, 0.2);
            color: #f1c40f;
        }

        .stars {
            color: #f1c40f;
            font-size: 14px;
        }

        .avg {
            color: #ccc;
            font-size: 12px;
            margin-left: 5px;
        }

        .card-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        /* Button styles */
        .cta-button {
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            background: rgba(255,255,255,0.1);
            color: white;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .cta-button:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-1px);
        }

        .cta-button.primary {
            background: #3498db;
        }

        .cta-button.primary:hover {
            background: #2980b9;
        }

        /* Input styles */
        .input {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.1);
            color: white;
            font-family: inherit;
        }

        .input:focus {
            outline: none;
            border-color: #3498db;
        }

        /* Utility classes */
        .muted {
            color: #666;
            font-style: italic;
        }

        .glass {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        /* Loading indicator */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Combined admin button */
        .admin-combined-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .admin-combined-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
    </style>
</head>
<body>
    <div class="bg-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <header>
        <div class="container">
            <nav class="glass">
                <div class="logo" onclick="window.location.href='index.php'">
                    <div class="logo-icon">AL</div>
                    <span>AbeLink — Événements Inclusifs</span>
                </div>
                <div style="display: flex; align-items: center;">
                    <div class="nav-links">
                        <a href="../FrontOffice/index.html">Accueil</a>
                        <a href="../FrontOffice/evaluations-evenements.php">Évaluations & Événements</a>
                        
                       <!-- Combined Admin Button -->
<button class="admin-combined-btn" id="adminCombinedBtn" style="display: none;" onclick="handleAdminButtonClick()">
    Modération/Tableau
</button>
                    </div>
                    <div class="role-selector">
                        <select id="roleSelect" class="input" style="width: 150px;" onchange="changeUserRole()">
                            <option value="user">Utilisateur</option>
                            <option value="company">Entreprise</option>
                            <option value="inclusion">Responsable Inclusion</option>
                            <option value="admin">Administrateur</option>
                        </select>
                        <div id="roleBadge" class="role-badge user">Utilisateur</div>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="content-wrapper">
            <section class="hero glass">
                <h1>Évaluations & Événements Inclusifs</h1>
                <p>Découvrez et participez aux événements organisés par les entreprises partenaires et AbeLink, et évaluez ceux auxquels vous avez participé.</p>
            </section>

            
            </section>

            <section style="margin-top: 20px;">
                <h2 style="margin: 6px 0 12px; color: white;">Événements disponibles</h2>
                
                <div class="filters glass" style="padding: 20px; margin-bottom: 20px;">
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <input id="searchInput" class="input" type="search" placeholder="Rechercher par titre, lieu..." aria-label="Rechercher événements" style="flex: 1; min-width: 200px;"/>
                        <select id="filterAccess" class="input" aria-label="Filtrer par accessibilité" style="flex: 1; min-width: 200px;">
                            <option value="">Tous les types d'accessibilité</option>
                            <option>Langue des signes</option>
                            <option>Accès PMR</option>
                            <option>Sous-titrage</option>
                            <option>Interprète LSF</option>
                            <option>Visio (en ligne)</option>
                        </select>
                        <select id="filterCompany" class="input" aria-label="Filtrer par entreprise" style="flex: 1; min-width: 200px;">
                            <option value="">Toutes les entreprises</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 8px; margin-top: 12px; flex-wrap: wrap;">
                        <button class="cta-button" onclick="filterByDate('all')">Tous</button>
                        <button class="cta-button" onclick="filterByDate('upcoming')">À venir</button>
                        <button class="cta-button" onclick="filterByDate('past')">Passés</button>
                        <button class="cta-button primary" onclick="openEventModal()" id="createEventBtn" style="margin-left: auto; display: none;">+ Créer un événement</button>
                    </div>
                </div>
                
                <div id="eventsGrid" class="grid" role="list" aria-live="polite"></div>
                <div id="noEvents" class="muted" style="display: none; margin-top: 12px; text-align: center;">Aucun événement trouvé.</div>
            </section>

            <section style="margin-top: 40px;">
                <h2 style="margin: 6px 0 12px; color: white;">Événements à évaluer</h2>
                <p class="muted" style="margin-bottom: 15px;">Évaluez les événements passés auxquels vous avez participé.</p>
                <div id="eventsToEvaluateGrid" class="grid" role="list" aria-live="polite"></div>
                <div id="noEventsToEvaluate" class="muted" style="display: none; margin-top: 12px; text-align: center;">Aucun événement à évaluer pour le moment.</div>
            </section>

            <section style="margin-top: 40px;">
                <h2 style="margin: 6px 0 12px; color: white;">Mes évaluations</h2>
                <p class="muted" style="margin-bottom: 15px;">Consultez les évaluations que vous avez déjà soumises.</p>
                <div id="myEvaluationsGrid" class="grid" role="list" aria-live="polite"></div>
                <div id="noMyEvaluations" class="muted" style="display: none; margin-top: 12px; text-align: center;">Vous n'avez pas encore évalué d'événements.</div>
            </section>
        </div>
    </div>

    <!-- Rest of your modals remain the same -->
    <div id="eventModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="eventModalTitle">
        <div class="panel glass" role="document">
            <button class="cta-button" aria-label="Fermer" style="float: right; padding: 8px 12px;" onclick="closeEventModal()">✕</button>
            <h3 id="eventModalTitle">Créer un événement inclusif</h3>
            <form id="eventForm">
                <input type="hidden" id="eventId">
                <div style="display: grid; gap: 15px;">
                    <div>
                        <label for="titre" style="color: white;">Titre de l'événement *</label>
                        <input id="titre" class="input" required placeholder="Ex: Atelier Accessibilité Numérique">
                    </div>
                    <div>
                        <label for="description" style="color: white;">Description détaillée *</label>
                        <textarea id="description" required placeholder="Décrivez votre événement, son objectif, son public cible..."></textarea>
                    </div>
                    <div class="form-row">
                        <div class="col">
                            <label for="date_evenement" style="color: white;">Date de l'événement *</label>
                            <input id="date_evenement" type="datetime-local" class="input" required>
                        </div>
                        <div class="col">
                            <label for="participants_max" style="color: white;">Nombre maximum de participants</label>
                            <input id="participants_max" type="number" class="input" min="1" value="50">
                        </div>
                    </div>
                    <div>
                        <label for="lieu" style="color: white;">Lieu ou lien de participation *</label>
                        <input id="lieu" class="input" required placeholder="Adresse physique ou lien de visioconférence">
                    </div>
                    <div>
                        <label style="color: white;">Mesures d'accessibilité proposées *</label>
                        <div class="checkbox-group">
                            <div class="checkbox-item">
                                <input type="checkbox" id="access_lsf" value="Langue des signes">
                                <label for="access_lsf" style="color: white;">Langue des signes (LSF)</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="access_pmr" value="Accès PMR">
                                <label for="access_pmr" style="color: white;">Accès PMR</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="access_sous_titres" value="Sous-titrage">
                                <label for="access_sous_titres" style="color: white;">Sous-titrage</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="access_visio" value="Visio (en ligne)">
                                <label for="access_visio" style="color: white;">Participation en ligne</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="access_autre" value="Autre">
                                <label for="access_autre" style="color: white;">Autre mesure d'accessibilité</label>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="statut" style="color: white;">Statut de publication</label>
                        <select id="statut" class="input">
                            <option value="Brouillon">Brouillon (non visible)</option>
                            <option value="Publié">Publié (visible par tous)</option>
                        </select>
                        <small class="small">Les événements des entreprises doivent être validés par un modérateur avant publication.</small>
                    </div>

                    <div style="display: flex; gap: 8px; justify-content: flex-end; margin-top: 6px;">
                        <button type="button" class="cta-button" onclick="closeEventModal()">Annuler</button>
                        <button type="submit" class="cta-button primary" id="submitEventBtn">Enregistrer l'événement</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="evaluationModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="evaluationModalTitle">
        <div class="panel glass" role="document">
            <button class="cta-button" aria-label="Fermer" style="float: right; padding: 8px 12px;" onclick="closeEvaluationModal()">✕</button>
            <h3 id="evaluationModalTitle">Évaluer un événement</h3>
            <form id="evaluationForm">
                <input type="hidden" id="evaluationEventId">
                <div style="display: grid; gap: 15px;">
                    <div>
                        <label for="note_accessibilite" style="color: white;">Note d'accessibilité (1-5) *</label>
                        <select id="note_accessibilite" class="input" required>
                            <option value="">Sélectionnez une note</option>
                            <option value="1">1 - Très mauvais</option>
                            <option value="2">2 - Mauvais</option>
                            <option value="3">3 - Moyen</option>
                            <option value="4">4 - Bon</option>
                            <option value="5">5 - Excellent</option>
                        </select>
                        <small class="small">Évaluez les mesures d'accessibilité (LSF, PMR, sous-titrage...)</small>
                    </div>
                    <div>
                        <label for="note_inclusion" style="color: white;">Note d'inclusion (1-5) *</label>
                        <select id="note_inclusion" class="input" required>
                            <option value="">Sélectionnez une note</option>
                            <option value="1">1 - Très mauvais</option>
                            <option value="2">2 - Mauvais</option>
                            <option value="3">3 - Moyen</option>
                            <option value="4">4 - Bon</option>
                            <option value="5">5 - Excellent</option>
                        </select>
                        <small class="small">Évaluez l'ambiance inclusive et l'accueil des diversités</small>
                    </div>
                    <div>
                        <label for="commentaire" style="color: white;">Commentaire détaillé</label>
                        <textarea id="commentaire" placeholder="Partagez votre expérience, vos suggestions d'amélioration..."></textarea>
                    </div>

                    <div style="display: flex; gap: 8px; justify-content: flex-end; margin-top: 6px;">
                        <button type="button" class="cta-button" onclick="closeEvaluationModal()">Annuler</button>
                        <button type="submit" class="cta-button primary">Soumettre l'évaluation</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="detailsModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="detailsTitle">
        <div class="panel glass" role="document" style="max-width: 900px;">
            <button class="cta-button" aria-label="Fermer" style="float: right; padding: 8px 12px;" onclick="closeDetailsModal()">✕</button>
            <h3 id="detailsTitle">Détails de l'événement</h3>
            <div id="detailsContent"></div>
        </div>
    </div>

    <script>
        // User roles and permissions
        const USER_ROLES = {
            USER: 'user',
            COMPANY: 'company',
            INCLUSION: 'inclusion',
            ADMIN: 'admin'
        };

        // Store management
        function loadStore() {
            const defaultData = {
                events: [],
                companies: [],
                users: [],
                evaluations: []
            };
            
            try {
                const stored = localStorage.getItem('abelink_data');
                return stored ? JSON.parse(stored) : defaultData;
            } catch (e) {
                console.error('Error loading store:', e);
                return defaultData;
            }
        }

        function saveStore() {
            try {
                const data = {
                    events: window.EVENTS || [],
                    companies: window.COMPANIES || [],
                    users: window.USERS || [],
                    evaluations: window.EVALUATIONS || []
                };
                localStorage.setItem('abelink_data', JSON.stringify(data));
            } catch (e) {
                console.error('Error saving store:', e);
            }
        }

        // User role management
        function loadUserRole() {
            return localStorage.getItem('abelink_user_role') || USER_ROLES.USER;
        }

        function saveUserRole(role) {
            localStorage.setItem('abelink_user_role', role);
        }

        function changeUserRole() {
            const roleSelect = document.getElementById('roleSelect');
            const newRole = roleSelect.value;
            
            if (newRole && Object.values(USER_ROLES).includes(newRole)) {
                window.CURRENT_USER_ROLE = newRole;
                saveUserRole(newRole);
                updateUIForRole();
                
                // Re-render content based on new role
                if (typeof renderEvents === 'function') renderEvents();
                if (typeof renderEventsToEvaluate === 'function') renderEventsToEvaluate();
                if (typeof renderMyEvaluations === 'function') renderMyEvaluations();
                
                console.log('User role changed to:', newRole);
            }
        }

        function updateUIForRole() {
    const role = window.CURRENT_USER_ROLE || USER_ROLES.USER;
    const roleBadge = document.getElementById('roleBadge');
    const createEventBtn = document.getElementById('createEventBtn');
    const adminCombinedBtn = document.getElementById('adminCombinedBtn');
    
    // Update role badge
    if (roleBadge) {
        roleBadge.textContent = getRoleDisplayName(role);
        roleBadge.className = `role-badge ${role}`;
    }
    
    // Update create event button visibility
    if (createEventBtn) {
        createEventBtn.style.display = canCreateEvents() ? 'block' : 'none';
    }
    
    // Update combined admin button visibility
    if (adminCombinedBtn) {
        // Show for Inclusion and Admin roles
        adminCombinedBtn.style.display = (role === USER_ROLES.INCLUSION || role === USER_ROLES.ADMIN) ? 'block' : 'none';
    }
    
    // Update role selector value
    const roleSelect = document.getElementById('roleSelect');
    if (roleSelect) {
        roleSelect.value = role;
    }
}

// Handle admin button click
function handleAdminButtonClick() {
    // Redirect to the combined admin dashboard
    window.location.href = 'admin_dashboard.php';
}

        function getRoleDisplayName(role) {
            const displayNames = {
                [USER_ROLES.USER]: 'Utilisateur',
                [USER_ROLES.COMPANY]: 'Entreprise',
                [USER_ROLES.INCLUSION]: 'Responsable Inclusion',
                [USER_ROLES.ADMIN]: 'Administrateur'
            };
            return displayNames[role] || 'Utilisateur';
        }

        function canCreateEvents() {
            const role = window.CURRENT_USER_ROLE || USER_ROLES.USER;
            return role === USER_ROLES.COMPANY || role === USER_ROLES.ADMIN || role === USER_ROLES.INCLUSION;
        }

        // Utility functions
        function makeId() {
            return 'id_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now().toString(36);
        }

        function todayISO() {
            return new Date().toISOString().split('T')[0];
        }

        function formatDate(dateString) {
            if (!dateString) return '';
            const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            return new Date(dateString).toLocaleDateString('fr-FR', options);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function truncate(text, length) {
            return text.length > length ? text.substring(0, length) + '...' : text;
        }

        function isEventPassed(event) {
            if (!event.date) return false;
            return new Date(event.date) < new Date();
        }

        function calculateEventRating(event) {
            if (!event.evaluations || event.evaluations.length === 0) {
                return { access: 0, inclusion: 0, count: 0 };
            }
            
            const accessSum = event.evaluations.reduce((sum, evaluation) => sum + (evaluation.note || 0), 0);
            const inclusionSum = event.evaluations.reduce((sum, evaluation) => sum + (evaluation.note || 0), 0);
            const count = event.evaluations.length;
            
            return {
                access: (accessSum / count).toFixed(1),
                inclusion: (inclusionSum / count).toFixed(1),
                count: count
            };
        }

        function renderStars(rating) {
            const numRating = parseFloat(rating) || 0;
            const fullStars = Math.floor(numRating);
            const hasHalfStar = numRating % 1 >= 0.5;
            const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
            
            let stars = '';
            for (let i = 0; i < fullStars; i++) {
                stars += '★';
            }
            if (hasHalfStar) {
                stars += '½';
            }
            for (let i = 0; i < emptyStars; i++) {
                stars += '☆';
            }
            
            return stars;
        }

        function getCompanyById(companyId) {
            return (window.COMPANIES || []).find(company => company.id === companyId);
        }

        function getCompanyName(companyId) {
            const company = getCompanyById(companyId);
            return company ? company.nom : 'AbeLink';
        }

        // Filter functions
        function setupFilters() {
            const searchInput = document.getElementById('searchInput');
            const filterAccess = document.getElementById('filterAccess');
            const filterCompany = document.getElementById('filterCompany');
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    window.CURRENT_FILTER.search = this.value;
                    if (typeof renderEvents === 'function') renderEvents();
                });
            }
            
            if (filterAccess) {
                filterAccess.addEventListener('change', function() {
                    window.CURRENT_FILTER.access = this.value;
                    if (typeof renderEvents === 'function') renderEvents();
                });
            }
            
            if (filterCompany) {
                filterCompany.addEventListener('change', function() {
                    window.CURRENT_FILTER.company = this.value;
                    if (typeof renderEvents === 'function') renderEvents();
                });
                
                // Populate company filter
                populateCompanyFilter();
            }
        }

        function populateCompanyFilter() {
            const filterCompany = document.getElementById('filterCompany');
            if (!filterCompany) return;
            
            // Clear existing options except the first one
            while (filterCompany.options.length > 1) {
                filterCompany.remove(1);
            }
            
            // Add companies
            (window.COMPANIES || []).forEach(company => {
                const option = document.createElement('option');
                option.value = company.id;
                option.textContent = company.nom;
                filterCompany.appendChild(option);
            });
        }

        function filterByDate(dateType) {
            window.CURRENT_FILTER.date = dateType;
            if (typeof renderEvents === 'function') renderEvents();
        }

        // Modal functions
        function openEventModal(eventId = null) {
            const modal = document.getElementById('eventModal');
            const title = document.getElementById('eventModalTitle');
            const form = document.getElementById('eventForm');
            
            if (eventId) {
                // Edit mode
                title.textContent = 'Modifier l\'événement';
                const event = window.EVENTS.find(e => e.id === eventId);
                if (event) {
                    document.getElementById('eventId').value = event.id;
                    document.getElementById('titre').value = event.titre || '';
                    document.getElementById('description').value = event.description || '';
                    document.getElementById('date_evenement').value = event.date ? event.date.replace(' ', 'T') : '';
                    document.getElementById('lieu').value = event.lieu || '';
                    document.getElementById('participants_max').value = event.participants_max || 50;
                    document.getElementById('statut').value = event.statut || 'Brouillon';
                    
                    // Reset checkboxes
                    document.querySelectorAll('.checkbox-item input').forEach(cb => {
                        cb.checked = false;
                    });
                    
                    // Set accessibility checkboxes
                    (event.accessibilite || []).forEach(acc => {
                        const checkbox = document.querySelector(`input[value="${acc}"]`);
                        if (checkbox) checkbox.checked = true;
                    });
                }
            } else {
                // Create mode
                title.textContent = 'Créer un événement inclusif';
                form.reset();
                document.getElementById('eventId').value = '';
                document.getElementById('statut').value = 'Brouillon';
                // Set default date to tomorrow
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                document.getElementById('date_evenement').value = tomorrow.toISOString().slice(0, 16);
            }
            
            modal.style.display = 'block';
        }

        function closeEventModal() {
            document.getElementById('eventModal').style.display = 'none';
        }

        function openEvaluationModal(eventId) {
            const modal = document.getElementById('evaluationModal');
            document.getElementById('evaluationEventId').value = eventId;
            
            const event = window.EVENTS.find(e => e.id === eventId);
            if (event) {
                document.getElementById('evaluationModalTitle').textContent = `Évaluer: ${event.titre}`;
            }
            
            modal.style.display = 'block';
        }

        function closeEvaluationModal() {
            document.getElementById('evaluationModal').style.display = 'none';
            document.getElementById('evaluationForm').reset();
        }

   function openEventDetails(eventId) {
    const modal = document.getElementById('detailsModal');
    const content = document.getElementById('detailsContent');
    const title = document.getElementById('detailsTitle');
    
    const event = window.EVENTS.find(e => e.id === eventId);
    if (event) {
        title.textContent = event.titre;
        
        const company = getCompanyById(event.idUtilisateur);
        const rating = calculateEventRating(event);
        const isPastEvent = isEventPassed(event);
        const isAdmin = window.CURRENT_USER_ROLE === 'admin';
        const isInclusion = window.CURRENT_USER_ROLE === 'inclusion';
        
        // Base event info
        let eventHTML = `
            <div style="display: grid; gap: 20px;">
                <div>
                    <h4 style="margin: 0 0 10px 0; color: white;">Description</h4>
                    <p style="color: #ccc;">${escapeHtml(event.description || 'Aucune description disponible.')}</p>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <h4 style="margin: 0 0 10px 0; color: white;">Informations</h4>
                        <p style="color: #ccc;"><strong>Date:</strong> ${formatDate(event.date)}</p>
                        <p style="color: #ccc;"><strong>Lieu:</strong> ${escapeHtml(event.lieu)}</p>
                        <p style="color: #ccc;"><strong>Organisateur:</strong> ${company ? company.nom : 'AbeLink'}</p>
                        <p style="color: #ccc;"><strong>Participants:</strong> ${event.inscrits || 0}/${event.participants_max || 50}</p>
                        <p style="color: #ccc;"><strong>Statut:</strong> ${event.statut}</p>
                    </div>
                    
                    <div>
                        <h4 style="margin: 0 0 10px 0; color: white;">Accessibilité</h4>
                        <div class="tags">
                            ${(typeof event.accessibilite === 'string' ? event.accessibilite.split(', ') : event.accessibilite || []).map(acc => `<span class="tag">${escapeHtml(acc)}</span>`).join('')}
                            ${event.accessibilite && event.accessibilite.length === 0 ? '<span class="muted">Aucune mesure spécifiée</span>' : ''}
                        </div>
                    </div>
                </div>
                
                ${event.evaluations && event.evaluations.length > 0 ? `
                    <div>
                        <h4 style="margin: 0 0 10px 0; color: white;">Notes moyennes</h4>
                        <div style="display: flex; gap: 30px; margin-bottom: 15px;">
                            <div>
                                <div class="small">Accessibilité</div>
                                <div class="stars">${renderStars(rating.access)} <span class="avg">${rating.access}/5</span></div>
                            </div>
                            <div>
                                <div class="small">Inclusion</div>
                                <div class="stars">${renderStars(rating.inclusion)} <span class="avg">${rating.inclusion}/5</span></div>
                            </div>
                        </div>
                    </div>
                ` : ''}
                
                <div id="evaluationsContainer">
                    <div style="text-align: center; padding: 20px;">
                        <div class="loading"></div>
                        <p>Chargement des évaluations...</p>
                    </div>
                </div>
                
                <div class="card-actions">
                    ${!isPastEvent && event.statut === 'Publié' && !isFull(event) ? 
                        `<button class="cta-button primary" onclick="registerForEvent('${event.id}'); closeDetailsModal();">S'inscrire</button>` : ''}
                        
                    ${isPastEvent ? 
                        `<button class="cta-button primary" onclick="openEvaluationModal('${event.id}'); closeDetailsModal();">Évaluer cet événement</button>` : ''}
                        
                    ${(window.CURRENT_USER_ROLE === 'company' && event.idUtilisateur === window.CURRENT_USER_ID) || isAdmin || isInclusion ? 
                        `<button class="cta-button" onclick="editEvent('${event.id}'); closeDetailsModal();">Modifier l'événement</button>` : ''}
                        
                    ${isAdmin ? 
                        `<button class="cta-button danger" onclick="deleteEventFromDetails('${event.id}')" style="background: rgba(255, 107, 107, 0.2); color: var(--danger);">Supprimer l'événement</button>` : ''}
                </div>
            </div>
        `;
        
        content.innerHTML = eventHTML;
        
        // Load evaluations from database
        loadEventEvaluations(eventId, isPastEvent, isAdmin || isInclusion);
        
        modal.style.display = 'block';
    }
}

function loadEventEvaluations(eventId, showReportButtons = false, showModerationButtons = false) {
    fetch(`admin_moderation.php?action=get_event_evaluations&eventId=${eventId}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('evaluationsContainer');
            
            if (data.success && data.evaluations && data.evaluations.length > 0) {
                container.innerHTML = `
                    <h4 style="margin: 20px 0 10px 0; color: white;">Évaluations (${data.evaluations.length})</h4>
                    <div style="max-height: 400px; overflow-y: auto;">
                        ${data.evaluations.map(evaluation => `
                            <div class="glass" style="padding: 15px; margin-bottom: 10px; border-left: 4px solid ${evaluation.signalee ? '#e74c3c' : '#2ecc71'}">
                                <div style="display: flex; justify-content: space-between; align-items: start;">
                                    <div>
                                        <strong>${evaluation.prenom} ${evaluation.nom}</strong>
                                        <span class="small muted"> • ${formatDate(evaluation.dateEvaluation)}</span>
                                        ${evaluation.signalee ? '<span style="color: #e74c3c; margin-left: 10px;">🚩 Signalée</span>' : ''}
                                    </div>
                                    <div style="text-align: right;">
                                        <div class="small">Accès: ${evaluation.note_accessibilite}/5</div>
                                        <div class="small">Inclusion: ${evaluation.note_inclusion}/5</div>
                                    </div>
                                </div>
                                <div style="margin-top: 8px; color: #ccc;">${escapeHtml(evaluation.commentaire || 'Aucun commentaire')}</div>
                                <div style="margin-top: 10px; display: flex; gap: 10px;">
                                    ${showReportButtons && !evaluation.signalee ? `
                                        <button class="cta-button" onclick="reportEvaluation(${evaluation.id})" style="padding: 6px 12px; font-size: 12px; background: rgba(231, 76, 60, 0.2); color: #e74c3c;">
                                            🚩 Signaler
                                        </button>
                                    ` : ''}
                                    
                                    ${showModerationButtons && evaluation.signalee ? `
                                        <button class="cta-button primary" onclick="approveEvaluationFromDetails(${evaluation.id}, '${eventId}')" style="padding: 6px 12px; font-size: 12px;">
                                            ✅ Approuver
                                        </button>
                                        <button class="cta-button" onclick="rejectEvaluationFromDetails(${evaluation.id}, '${eventId}')" style="padding: 6px 12px; font-size: 12px; background: rgba(231, 76, 60, 0.2); color: #e74c3c;">
                                            ❌ Supprimer
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
            } else {
                container.innerHTML = '<p class="muted">Aucune évaluation pour le moment.</p>';
            }
        })
        .catch(error => {
            console.error('Error loading evaluations:', error);
            document.getElementById('evaluationsContainer').innerHTML = '<p class="muted">Erreur lors du chargement des évaluations.</p>';
        });
}

// New functions for evaluation reporting and moderation
function reportEvaluation(evaluationId) {
    if (confirm('Signaler cette évaluation pour modération ?')) {
        fetch('manage_evaluation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'report',
                evaluationId: evaluationId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Évaluation signalée pour modération.');
                // Reload the evaluations
                const eventId = document.getElementById('evaluationEventId')?.value;
                if (eventId) {
                    loadEventEvaluations(eventId, true, false);
                }
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors du signalement');
        });
    }
}

function approveEvaluationFromDetails(evaluationId, eventId) {
    if (confirm('Approuver cette évaluation ?')) {
        fetch('admin_moderation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'approve_evaluation',
                evaluationId: evaluationId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Évaluation approuvée.');
                loadEventEvaluations(eventId, false, true);
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de l\'approbation');
        });
    }
}

function rejectEvaluationFromDetails(evaluationId, eventId) {
    if (confirm('Supprimer cette évaluation ?')) {
        fetch('admin_moderation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'reject_evaluation',
                evaluationId: evaluationId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Évaluation supprimée.');
                loadEventEvaluations(eventId, false, true);
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la suppression');
        });
    }
}

function deleteEventFromDetails(eventId) {
    if (confirm('Supprimer définitivement cet événement ? Toutes les évaluations et participations associées seront également supprimées.')) {
        fetch('admin_moderation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'delete_event',
                eventId: eventId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Événement supprimé avec succès.');
                closeDetailsModal();
                // Refresh events list
                if (typeof renderEvents === 'function') renderEvents();
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la suppression');
        });
    }
}

// Add this helper function
function isFull(event) {
    return (event.inscrits || 0) >= (event.participants_max || 50);
}

        function closeDetailsModal() {
            document.getElementById('detailsModal').style.display = 'none';
        }

        // Event participation
        function registerForEvent(eventId) {
            const event = window.EVENTS.find(e => e.id === eventId);
            if (!event) return;
            
            // Check if already registered
            const isAlreadyRegistered = event.participations && 
                event.participations.some(p => p.idUtilisateur === window.CURRENT_USER_ID);
            
            if (isAlreadyRegistered) {
                alert('Vous êtes déjà inscrit à cet événement.');
                return;
            }
            
            // Check if event is full
            if (event.inscrits >= event.participants_max) {
                alert('Désolé, cet événement est complet.');
                return;
            }
            
            // Register user
            if (!event.participations) event.participations = [];
            
            event.participations.push({
                id: makeId(),
                idEvenement: eventId,
                idUtilisateur: window.CURRENT_USER_ID,
                statut: 'Confirmée',
                dateInscription: new Date().toISOString()
            });
            
            event.inscrits = (event.inscrits || 0) + 1;
            
            saveStore();
            renderEvents();
            
            alert('Inscription confirmée ! Nous avons hâte de vous voir.');
        }

        function editEvent(eventId) {
            openEventModal(eventId);
        }

        // Database export functions
        function exportEventsToDB() {
            const resultDiv = document.getElementById('exportResult');
            resultDiv.innerHTML = '<p style="color: #3498db;">Export en cours...</p>';
            
            // Prepare the data
            const exportData = {
                action: 'export_events',
                events: window.EVENTS || []
            };
            
            console.log('Exporting events:', window.EVENTS);
            
            // Send the data to the server via AJAX
            fetch('export_events.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(exportData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Export response:', data);
                if (data.success) {
                    resultDiv.innerHTML = `<p style="color: #2ecc71;">${data.message}</p>`;
                    // Show statistics
                    if (data.stats) {
                        document.getElementById('exportStats').style.display = 'block';
                        document.getElementById('eventsCount').textContent = data.stats.events;
                        document.getElementById('evaluationsCount').textContent = data.stats.evaluations;
                        document.getElementById('participationsCount').textContent = data.stats.participations;
                    }
                } else {
                    resultDiv.innerHTML = `<p style="color: #e74c3c;">Erreur: ${data.message}</p>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultDiv.innerHTML = `<p style="color: #e74c3c;">Erreur lors de l'export: ${error.message}</p>`;
            });
        }

        function checkDBConnection() {
            const resultDiv = document.getElementById('exportResult');
            resultDiv.innerHTML = '<p style="color: #3498db;">Test de connexion en cours...</p>';
            
            fetch('export_events.php?action=test_connection')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Connection test response:', data);
                if (data.success) {
                    resultDiv.innerHTML = `<p style="color: #2ecc71;">${data.message}</p>`;
                } else {
                    resultDiv.innerHTML = `<p style="color: #e74c3c;">Erreur: ${data.message}</p>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultDiv.innerHTML = `<p style="color: #e74c3c;">Erreur lors du test: ${error.message}</p>`;
            });
        }

        function viewExportedEvents() {
            // This would typically redirect to a page showing events from the database
            alert('Cette fonctionnalité afficherait les événements exportés depuis la base de données.');
        }

        function updateDBConfig() {
            const host = document.getElementById('dbHost').value;
            const dbName = document.getElementById('dbName').value;
            const user = document.getElementById('dbUser').value;
            const password = document.getElementById('dbPass').value;
            
            // In a real application, you'd send this to a configuration file
            // For this demo, we'll just show a confirmation
            alert(`Configuration mise à jour:\nHôte: ${host}\nBase: ${dbName}\nUtilisateur: ${user}`);
        }

        // Event form handling
        function handleEventFormSubmit(e) {
            e.preventDefault();
            console.log('Event form submitted');
            
            if (!canCreateEvents()) {
                alert('Seules les entreprises vérifiées et l\'équipe AbeLink peuvent créer des événements.');
                return;
            }
            
            const eventId = document.getElementById('eventId').value;
            const titre = document.getElementById('titre').value.trim();
            const description = document.getElementById('description').value.trim();
            const date_evenement = document.getElementById('date_evenement').value;
            const lieu = document.getElementById('lieu').value.trim();
            const participants_max = parseInt(document.getElementById('participants_max').value) || 50;
            const statut = document.getElementById('statut').value;
            
            const accessibilite = [];
            document.querySelectorAll('.checkbox-item input:checked').forEach(cb => {
                accessibilite.push(cb.value);
            });
            
            console.log('Form data:', { titre, date_evenement, lieu, description, accessibilite });
            
            if (!titre || !date_evenement || !lieu || !description) {
                alert('Veuillez remplir tous les champs obligatoires.');
                return;
            }
            
            // Show loading state
            const submitBtn = document.getElementById('submitEventBtn');
            const originalText = submitBtn.textContent;
            submitBtn.innerHTML = '<span class="loading"></span> Enregistrement...';
            submitBtn.disabled = true;
            
            // Prepare data for database
            const eventData = {
                titre,
                description,
                date: date_evenement.replace('T', ' '),
                lieu,
                participants_max,
                accessibilite: accessibilite.join(', '),
                statut,
                idUtilisateur: 1, // Use valid user ID
                theme: 'Inclusion' // Default theme
            };
            
            // If editing, add the event ID
            if (eventId) {
                eventData.id = eventId;
            }
            
            // Send to server
            fetch('save_event.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(eventData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Save event response:', data);
                
                if (data.success) {
                    // Update local storage with the new/updated event
                    if (eventId) {
                        // Edit existing event
                        const index = window.EVENTS.findIndex(e => e.id === eventId);
                        if (index !== -1) {
                            window.EVENTS[index] = {
                                ...window.EVENTS[index],
                                ...eventData,
                                id: eventId
                            };
                            console.log('Event updated:', window.EVENTS[index]);
                        }
                    } else {
                        // Create new event
                        const newEvent = {
                            ...eventData,
                            id: data.eventId || makeId(),
                            inscrits: 0,
                            participations: [],
                            evaluations: []
                        };
                        window.EVENTS.unshift(newEvent);
                        console.log('New event created:', newEvent);
                    }
                    
                    saveStore();
                    renderEvents();
                    closeEventModal();
                    
                    alert('Événement enregistré avec succès !');
                } else {
                    alert('Erreur lors de l\'enregistrement: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de l\'enregistrement: ' + error.message);
            })
            .finally(() => {
                // Reset button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        }

        function handleEvaluationFormSubmit(e) {
            e.preventDefault();
            
            const eventId = document.getElementById('evaluationEventId').value;
            const note_accessibilite = parseInt(document.getElementById('note_accessibilite').value);
            const note_inclusion = parseInt(document.getElementById('note_inclusion').value);
            const commentaire = document.getElementById('commentaire').value.trim();
            
            if (!note_accessibilite || !note_inclusion) {
                alert('Veuillez donner une note pour l\'accessibilité et l\'inclusion.');
                return;
            }
            
            // Use valid user ID that exists in database
            const validUserId = 1;
            
            // Extract numeric event ID if it has a prefix
            let numericEventId = eventId;
            if (eventId.includes('_')) {
                numericEventId = eventId.split('_')[1];
            }
            
            // Prepare data for database submission
            const evaluationData = {
                action: 'submit',
                idUtilisateur: validUserId,
                idEvenement: parseInt(numericEventId),
                note_accessibilite: note_accessibilite,
                note_inclusion: note_inclusion,
                commentaire: commentaire
            };
            
            console.log('Submitting evaluation:', evaluationData);
            
            // Send to server
            fetch('manage_evaluation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(evaluationData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Evaluation response:', data);
                
                if (data.success) {
                    // Update local storage with the new evaluation
                    const event = window.EVENTS.find(e => e.id === eventId);
                    if (event) {
                        if (!event.evaluations) event.evaluations = [];
                        
                        const newEvaluation = {
                            id: data.evaluationId || makeId(),
                            idEvenement: eventId,
                            idUtilisateur: window.CURRENT_USER_ID,
                            note_accessibilite: note_accessibilite,
                            note_inclusion: note_inclusion,
                            commentaire: commentaire,
                            dateEvaluation: new Date().toISOString()
                        };
                        
                        event.evaluations.push(newEvaluation);
                        saveStore();
                        
                        closeEvaluationModal();
                        renderEventsToEvaluate();
                        renderMyEvaluations();
                        renderEvents(); // Refresh events to show updated ratings
                        
                        alert('Merci pour votre évaluation ! Votre retour aide à améliorer l\'inclusion.');
                    }
                } else {
                    alert('Erreur lors de l\'envoi de l\'évaluation: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de l\'envoi de l\'évaluation: ' + error.message);
            });
        }

        function renderEvents() {
            const eventsGrid = document.getElementById('eventsGrid');
            const noEvents = document.getElementById('noEvents');
            
            const filteredEvents = window.EVENTS.filter(event => {
                const searchTerm = window.CURRENT_FILTER.search.toLowerCase();
                if (searchTerm && 
                    !event.titre.toLowerCase().includes(searchTerm) && 
                    !event.lieu.toLowerCase().includes(searchTerm) &&
                    !event.description.toLowerCase().includes(searchTerm)) {
                    return false;
                }
                
                if (window.CURRENT_FILTER.access && !event.accessibilite.includes(window.CURRENT_FILTER.access)) {
                    return false;
                }
                
                if (window.CURRENT_FILTER.company && event.idUtilisateur !== window.CURRENT_FILTER.company) {
                    return false;
                }
                
                if (window.CURRENT_FILTER.date === 'upcoming' && isEventPassed(event)) {
                    return false;
                }
                if (window.CURRENT_FILTER.date === 'past' && !isEventPassed(event)) {
                    return false;
                }
                
                if (window.CURRENT_USER_ROLE === 'user' && event.statut !== 'Publié') {
                    return false;
                }
                
                if (window.CURRENT_USER_ROLE === 'company' && event.statut === 'Brouillon' && event.idUtilisateur !== window.CURRENT_USER_ID) {
                    return false;
                }
                
                return true;
            });
            
            eventsGrid.innerHTML = '';
            
            if (filteredEvents.length === 0) {
                noEvents.style.display = 'block';
                return;
            }
            
            noEvents.style.display = 'none';
            
            filteredEvents.forEach(event => {
                const rating = calculateEventRating(event);
                const isUserParticipating = event.participations && 
                    event.participations.some(p => p.idUtilisateur === window.CURRENT_USER_ID && p.statut === 'Confirmée');
                const isUserCreator = event.idUtilisateur === window.CURRENT_USER_ID || 
                    (window.CURRENT_USER_ROLE === 'admin') ||
                    (window.CURRENT_USER_ROLE === 'inclusion');
                const company = getCompanyById(event.idUtilisateur);
                const isFull = event.inscrits >= event.participants_max;
                
                const div = document.createElement('div');
                div.className = 'event-card glass';
                div.setAttribute('role', 'listitem');
                
                div.innerHTML = `
                    <div class="event-title">${escapeHtml(event.titre)}</div>
                    <div class="company-info">
                        <div class="company-logo">${company ? company.nom.charAt(0) : 'E'}</div>
                        <div class="company-name">${company ? company.nom : 'AbeLink'}</div>
                    </div>
                    <div class="meta">${formatDate(event.date)} • ${escapeHtml(event.lieu)}</div>
                    <div class="small">${truncate(escapeHtml(event.description || ''), 120)}</div>
                    
                    <div class="tags">
                        ${(typeof event.accessibilite === 'string' ? event.accessibilite.split(', ') : event.accessibilite || []).map(acc => `<span class="tag">${escapeHtml(acc)}</span>`).join('')}
                        <span class="tag status ${event.statut.toLowerCase()}">${event.statut}</span>
                        ${isFull ? '<span class="tag" style="background: rgba(255, 107, 107, 0.2); color: var(--danger);">Complet</span>' : ''}
                    </div>
                    
                    <div style="margin-top: 10px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div class="stars">${renderStars(rating.access)} <span class="avg">${rating.access || '—'}</span></div>
                            <div class="small">${event.inscrits}/${event.participants_max} participants</div>
                        </div>
                    </div>
                    
                    <div class="card-actions">
                        ${isUserParticipating ? 
                            '<span class="tag" style="background: rgba(46, 204, 113, 0.2); color: #2ecc71;">Inscrit</span>' : 
                            (event.statut === 'Publié' && !isEventPassed(event) && !isFull ? 
                                `<button class="cta-button" onclick="registerForEvent('${event.id}')">S'inscrire</button>` : '')
                        }
                        
                        ${!isUserParticipating && isEventPassed(event) && event.statut === 'Publié' ? 
                            `<button class="cta-button" onclick="openEvaluationModal('${event.id}')">Évaluer</button>` : ''}
                        
                        <button class="cta-button" onclick="openEventDetails('${event.id}')">Détails</button>
                        
                        ${isUserCreator ? 
                            `<button class="cta-button" onclick="editEvent('${event.id}')">Modifier</button>` : ''}
                    </div>
                `;
                
                eventsGrid.appendChild(div);
            });
        }

        function renderEventsToEvaluate() {
            const eventsToEvaluateGrid = document.getElementById('eventsToEvaluateGrid');
            const noEventsToEvaluate = document.getElementById('noEventsToEvaluate');
            
            const eventsToEvaluate = window.EVENTS.filter(event => {
                const userParticipated = event.participations && 
                    event.participations.some(p => p.idUtilisateur === window.CURRENT_USER_ID);
                
                const isPast = isEventPassed(event);
                
                const userEvaluated = event.evaluations && 
                    event.evaluations.some(e => e.idUtilisateur === window.CURRENT_USER_ID);
                
                return userParticipated && isPast && !userEvaluated;
            });
            
            eventsToEvaluateGrid.innerHTML = '';
            
            if (eventsToEvaluate.length === 0) {
                noEventsToEvaluate.style.display = 'block';
            } else {
                noEventsToEvaluate.style.display = 'none';
                
                eventsToEvaluate.forEach(event => {
                    const company = getCompanyById(event.idUtilisateur);
                    
                    const div = document.createElement('div');
                    div.className = 'event-card glass';
                    
                    div.innerHTML = `
                        <div class="event-title">${escapeHtml(event.titre)}</div>
                        <div class="company-info">
                            <div class="company-logo">${company ? company.nom.charAt(0) : 'E'}</div>
                            <div class="company-name">${company ? company.nom : 'AbeLink'}</div>
                        </div>
                        <div class="meta">${formatDate(event.date)} • ${escapeHtml(event.lieu)}</div>
                        <div class="small">${truncate(escapeHtml(event.description || ''), 120)}</div>
                        
                        <div class="card-actions">
                            <button class="cta-button primary" onclick="openEvaluationModal('${event.id}')">Évaluer cet événement</button>
                        </div>
                    `;
                    
                    eventsToEvaluateGrid.appendChild(div);
                });
            }
        }

        function renderMyEvaluations() {
            const myEvaluationsGrid = document.getElementById('myEvaluationsGrid');
            const noMyEvaluations = document.getElementById('noMyEvaluations');
            
            const myEvaluations = [];
            window.EVENTS.forEach(event => {
                if (event.evaluations) {
                    event.evaluations.filter(e => e.idUtilisateur === window.CURRENT_USER_ID)
                        .forEach(evaluationObj => {
                            myEvaluations.push({
                                ...evaluationObj,
                                eventTitre: event.titre,
                                eventDate: event.date,
                                companyName: getCompanyName(event.idUtilisateur)
                            });
                        });
                }
            });
            
            myEvaluationsGrid.innerHTML = '';
            
            if (myEvaluations.length === 0) {
                noMyEvaluations.style.display = 'block';
            } else {
                noMyEvaluations.style.display = 'none';
                
                myEvaluations.forEach(evaluationObj => {
                    const div = document.createElement('div');
                    div.className = 'event-card glass';
                    
                    div.innerHTML = `
                        <div class="event-title">${escapeHtml(evaluationObj.eventTitre)}</div>
                        <div class="company-info">
                            <div class="company-logo">${evaluationObj.companyName.charAt(0)}</div>
                            <div class="company-name">${evaluationObj.companyName}</div>
                        </div>
                        <div class="meta">${formatDate(evaluationObj.eventDate)} • Évalué le ${formatDate(evaluationObj.dateEvaluation)}</div>
                        
                        <div style="display: flex; gap: 15px; margin-top: 10px;">
                            <div>
                                <div class="small">Note</div>
                                <div class="stars">${renderStars(evaluationObj.note)} <span class="avg">${evaluationObj.note}/5</span></div>
                            </div>
                        </div>
                        
                        <div style="margin-top: 10px;">${escapeHtml(evaluationObj.commentaire)}</div>
                    `;
                    
                    myEvaluationsGrid.appendChild(div);
                });
            }
        }

        // Initialize sample data if empty
        function initializeSampleData() {
            const data = loadStore();
            
            if (data.events.length === 0) {
                data.events = [
                    {
                        id: 'event_1',
                        titre: 'Atelier Accessibilité Numérique',
                        description: 'Un atelier pratique pour découvrir les bonnes pratiques en matière d\'accessibilité numérique.',
                        date: '2025-12-15 14:00:00',
                        lieu: 'Paris, France',
                        idUtilisateur: 'admin_1',
                        accessibilite: ['Langue des signes', 'Accès PMR', 'Sous-titrage'],
                        statut: 'Publié',
                        participants_max: 30,
                        inscrits: 15,
                        participations: [],
                        evaluations: []
                    }
                ];
            }
            
            if (data.companies.length === 0) {
                data.companies = [
                    {
                        id: 'comp_1',
                        nom: 'Tech Inclusive',
                        verifiee: true
                    },
                    {
                        id: 'comp_2', 
                        nom: 'Accessibilité Pro',
                        verifiee: true
                    }
                ];
            }
            
            saveStore();
            return data;
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(event) {
            const modals = ['eventModal', 'evaluationModal', 'detailsModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal && event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing...');
            
            const data = initializeSampleData();
            window.EVENTS = data.events;
            window.COMPANIES = data.companies;
            window.CURRENT_USER_ROLE = loadUserRole();
            window.CURRENT_USER_ID = 'user_' + Math.random().toString(36).substr(2, 9);
            window.CURRENT_FILTER = { search: '', access: '', date: 'all', company: '' };
            
            console.log('Initial data:', {
                events: window.EVENTS,
                role: window.CURRENT_USER_ROLE,
                userId: window.CURRENT_USER_ID
            });
            
            document.getElementById('roleSelect').value = window.CURRENT_USER_ROLE;
            updateUIForRole();
            
            setupFilters();
            
            renderEvents();
            renderEventsToEvaluate();
            renderMyEvaluations();
            
            // Connect event listeners
            const eventForm = document.getElementById('eventForm');
            const evaluationForm = document.getElementById('evaluationForm');
            
            if (eventForm) {
                eventForm.removeEventListener('submit', handleEventFormSubmit);
                eventForm.addEventListener('submit', handleEventFormSubmit);
                console.log('Event form listener attached');
            } else {
                console.error('Event form not found!');
            }
            
            if (evaluationForm) {
                evaluationForm.removeEventListener('submit', handleEvaluationFormSubmit);
                evaluationForm.addEventListener('submit', handleEvaluationFormSubmit);
                console.log('Evaluation form listener attached');
            } else {
                console.error('Evaluation form not found!');
            }
            
            console.log('AbeLink initialized with role:', window.CURRENT_USER_ROLE);
        });

        // Make functions globally available
        window.openEventModal = openEventModal;
        window.editEvent = editEvent;
        window.openEventDetails = openEventDetails;
        window.closeDetailsModal = closeDetailsModal;
        window.registerForEvent = registerForEvent;
        window.openEvaluationModal = openEvaluationModal;
        window.closeEvaluationModal = closeEvaluationModal;
        window.filterByDate = filterByDate;
        window.changeUserRole = changeUserRole;
        window.exportEventsToDB = exportEventsToDB;
        window.checkDBConnection = checkDBConnection;
        window.handleAdminButtonClick = handleAdminButtonClick;
    </script>
</body>
</html>