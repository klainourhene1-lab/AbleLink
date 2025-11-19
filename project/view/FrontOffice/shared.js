const STORAGE_KEY = 'abelink_evenements_v4';
const USER_ROLE_KEY = 'abelink_user_role';
const COMPANIES_KEY = 'abelink_companies';

function initializeSampleData() {
    const sampleCompanies = [
        {
            id_entreprise: 'company_123',
            nom: 'Tech Inclusive',
            secteur: 'Technologie',
            description: 'Entreprise spécialisée dans les solutions numériques accessibles',
            email_contact: 'contact@techinclusive.fr',
            logo_url: '',
            valide: true
        },
        {
            id_entreprise: 'company_456',
            nom: 'Diversité & Co',
            secteur: 'Consulting',
            description: 'Cabinet de conseil en diversité et inclusion',
            email_contact: 'info@diversiteco.fr',
            logo_url: '',
            valide: true
        },
        {
            id_entreprise: 'company_789',
            nom: 'Access Solutions',
            secteur: 'Services',
            description: 'Prestataire de services en accessibilité',
            email_contact: 'contact@accesssolutions.fr',
            logo_url: '',
            valide: false
        }
    ];

    const sampleEvents = [
        {
            id_evenement: makeId(),
            titre: "Atelier Accessibilité Numérique",
            description: "Atelier pratique sur l'accessibilité web et outils inclusifs pour les personnes en situation de handicap. Découvrez les bonnes pratiques et outils pour rendre vos interfaces accessibles à tous.",
            date_evenement: addDaysISO(7),
            lieu: "Maison de la Paix, 123 Avenue de l'Inclusion, Paris",
            createur_type: 'entreprise',
            createur_id: 'company_123',
            accessibilite: ["Langue des signes", "Accès PMR"],
            statut: "Publié",
            participants_max: 30,
            inscrits: 15,
            participations: [
                { id_participation: makeId(), utilisateur_id: "user_1", evenement_id: "", date_inscription: todayISO(-2), presence: false, etat_inscription: "Confirmée" },
                { id_participation: makeId(), utilisateur_id: "user_2", evenement_id: "", date_inscription: todayISO(-1), presence: false, etat_inscription: "Confirmée" }
            ],
            evaluations: [
                { 
                    id_evaluation: makeId(), 
                    evenement_id: "", 
                    utilisateur_id: "user_1", 
                    note_accessibilite: 5, 
                    note_inclusion: 4, 
                    commentaire: "Interprète LSF excellent, très bonne organisation. Le contenu était adapté et les supports accessibles.", 
                    date_evaluation: todayISO(-1),
                    signalee: false,
                    etat_moderation: "Visible"
                }
            ]
        },
        {
            id_evenement: makeId(),
            titre: "Conférence : Diversité en entreprise",
            description: "Table-ronde sur les bonnes pratiques pour l'intégration professionnelle des personnes en situation de handicap. Témoignages et retours d'expérience.",
            date_evenement: addDaysISO(14),
            lieu: "En ligne (Zoom)",
            createur_type: 'entreprise',
            createur_id: 'company_456',
            accessibilite: ["Sous-titrage", "Visio (en ligne)"],
            statut: "Publié",
            participants_max: 100,
            inscrits: 8,
            participations: [
                { id_participation: makeId(), utilisateur_id: "user_3", evenement_id: "", date_inscription: todayISO(-3), presence: false, etat_inscription: "Confirmée" }
            ],
            evaluations: []
        },
        {
            id_evenement: makeId(),
            titre: "Séminaire Accessibilité Universelle",
            description: "Formation approfondie sur les principes de conception universelle et leur application pratique dans différents contextes.",
            date_evenement: addDaysISO(-5),
            lieu: "Centre des Congrès, Lyon",
            createur_type: 'admin',
            createur_id: 'admin_1',
            accessibilite: ["Langue des signes", "Accès PMR", "Sous-titrage"],
            statut: "Archivé",
            participants_max: 50,
            inscrits: 22,
            participations: [
                { id_participation: makeId(), utilisateur_id: "user_1", evenement_id: "", date_inscription: todayISO(-10), presence: true, etat_inscription: "Confirmée" },
                { id_participation: makeId(), utilisateur_id: "user_2", evenement_id: "", date_inscription: todayISO(-9), presence: true, etat_inscription: "Confirmée" }
            ],
            evaluations: [
                { 
                    id_evaluation: makeId(), 
                    evenement_id: "", 
                    utilisateur_id: "user_1", 
                    note_accessibilite: 4, 
                    note_inclusion: 5, 
                    commentaire: "Très bon séminaire, contenu riche et accessible. L'équipe était très à l'écoute.", 
                    date_evaluation: todayISO(-4),
                    signalee: false,
                    etat_moderation: "Visible"
                },
                { 
                    id_evaluation: makeId(), 
                    evenement_id: "", 
                    utilisateur_id: "user_2", 
                    note_accessibilite: 3, 
                    note_inclusion: 4, 
                    commentaire: "Bonne initiative mais certains ateliers manquaient d'accessibilité.", 
                    date_evaluation: todayISO(-3),
                    signalee: true,
                    etat_moderation: "En attente"
                }
            ]
        },
        {
            id_evenement: makeId(),
            titre: "Forum Innovation Inclusive",
            description: "Événement de networking et démonstrations de technologies innovantes pour l'inclusion.",
            date_evenement: addDaysISO(21),
            lieu: "Espace Innovation, Toulouse",
            createur_type: 'entreprise',
            createur_id: 'company_789',
            accessibilite: ["Accès PMR"],
            statut: "Brouillon",
            participants_max: 80,
            inscrits: 0,
            participations: [],
            evaluations: []
        }
    ];

    sampleEvents.forEach(event => {
        event.participations.forEach(p => p.evenement_id = event.id_evenement);
        event.evaluations.forEach(e => e.evenement_id = event.id_evenement);
    });

    return {
        companies: sampleCompanies,
        events: sampleEvents
    };
}

function loadStore() {
    const eventsRaw = localStorage.getItem(STORAGE_KEY);
    const companiesRaw = localStorage.getItem(COMPANIES_KEY);
    
    if (!eventsRaw || !companiesRaw) {
        const data = initializeSampleData();
        localStorage.setItem(STORAGE_KEY, JSON.stringify(data.events));
        localStorage.setItem(COMPANIES_KEY, JSON.stringify(data.companies));
        return data;
    }
    
    try {
        return {
            events: JSON.parse(eventsRaw),
            companies: JSON.parse(companiesRaw)
        };
    } catch (e) {
        console.error(e);
        const data = initializeSampleData();
        localStorage.setItem(STORAGE_KEY, JSON.stringify(data.events));
        localStorage.setItem(COMPANIES_KEY, JSON.stringify(data.companies));
        return data;
    }
}

function saveStore() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(window.EVENTS));
    localStorage.setItem(COMPANIES_KEY, JSON.stringify(window.COMPANIES));
}

function loadUserRole() {
    return localStorage.getItem(USER_ROLE_KEY) || 'user';
}

function saveUserRole(role) {
    localStorage.setItem(USER_ROLE_KEY, role);
}

function makeId() { return 'id_' + Math.random().toString(36).slice(2, 10); }

function todayISO(offsetDays = 0) {
    const d = new Date(); 
    d.setDate(d.getDate() + offsetDays);
    return d.toISOString().split('T')[0];
}

function addDaysISO(days) { 
    const d = new Date(); 
    d.setDate(d.getDate() + days); 
    return d.toISOString().split('T')[0]; 
}

function formatDate(dISO) {
    if (!dISO) return '—';
    const d = new Date(dISO + 'T00:00:00');
    return d.toLocaleDateString('fr-FR', { year: 'numeric', month: 'short', day: 'numeric' });
}

function isEventPassed(event) {
    if (!event.date_evenement) return false;
    return new Date(event.date_evenement) < new Date();
}

function escapeHtml(s) { 
    return (s + '').replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;'); 
}

function truncate(s, n) { 
    if (s.length <= n) return s; 
    return s.slice(0, n - 1) + '…'; 
}

function renderStars(avg) {
    const full = Math.floor(avg);
    const half = (avg - full) >= 0.5 ? 1 : 0;
    let html = '';
    for (let i = 0; i < 5; i++) {
        if (i < full) html += '<span class="star">★</span>';
        else if (i === full && half) html += '<span class="star">☆</span>';
        else html += '<span class="star" style="color:rgba(255,255,255,0.12)">★</span>';
    }
    return html;
}

function calculateEventRating(event) {
    if (!event.evaluations || event.evaluations.length === 0) return { access: 0, inclusion: 0 };
    
    const visibleEvals = event.evaluations.filter(e => e.etat_moderation === 'Visible');
    if (visibleEvals.length === 0) return { access: 0, inclusion: 0 };
    
    const sumAccess = visibleEvals.reduce((sum, evaluation) => sum + evaluation.note_accessibilite, 0);
    const sumInclusion = visibleEvals.reduce((sum, evaluation) => sum + evaluation.note_inclusion, 0);
    
    return {
        access: Math.round((sumAccess / visibleEvals.length) * 10) / 10,
        inclusion: Math.round((sumInclusion / visibleEvals.length) * 10) / 10
    };
}

function getCompanyById(companyId) {
    return window.COMPANIES.find(c => c.id_entreprise === companyId);
}

function getCompanyName(companyId) {
    const company = getCompanyById(companyId);
    return company ? company.nom : 'Entreprise inconnue';
}

function changeUserRole() {
    const roleSelect = document.getElementById('roleSelect');
    const newRole = roleSelect.value;
    window.CURRENT_USER_ROLE = newRole;
    saveUserRole(newRole);
    
    updateUIForRole();
    
    window.location.reload();
}
function updateUIForRole() {
    const roleBadge = document.getElementById('roleBadge');
    const createEventBtn = document.getElementById('createEventBtn');
    const moderationLink = document.getElementById('moderationLink');
    const dashboardLink = document.getElementById('dashboardLink');
    
    roleBadge.className = 'role-badge ' + window.CURRENT_USER_ROLE;
    roleBadge.textContent = 
        window.CURRENT_USER_ROLE === 'user' ? 'Utilisateur' :
        window.CURRENT_USER_ROLE === 'company' ? 'Entreprise' :
        window.CURRENT_USER_ROLE === 'inclusion' ? 'Responsable Inclusion' : 'Administrateur';
    
    if (createEventBtn) {
        if (window.CURRENT_USER_ROLE === 'user') {
            createEventBtn.style.display = 'none';
        } else {
            createEventBtn.style.display = 'inline-block';
        }
    }
    
    if (moderationLink) {
        if (window.CURRENT_USER_ROLE === 'inclusion' || window.CURRENT_USER_ROLE === 'admin') {
            moderationLink.style.display = 'inline-block';
        } else {
            moderationLink.style.display = 'none';
        }
    }
    
    if (dashboardLink) {
        if (window.CURRENT_USER_ROLE === 'admin') {
            dashboardLink.style.display = 'inline-block';
        } else {
            dashboardLink.style.display = 'none';
        }
    }
}

function canCreateEvents() {
    return window.CURRENT_USER_ROLE === 'company' || window.CURRENT_USER_ROLE === 'admin' || window.CURRENT_USER_ROLE === 'inclusion';
}

function openEventModal() {
    if (!canCreateEvents()) {
        alert('Seules les entreprises vérifiées et l\'équipe AbeLink peuvent créer des événements.');
        return;
    }
    
    document.getElementById('eventForm').reset();
    document.getElementById('eventId').value = '';
    document.getElementById('statut').value = 'Brouillon';
    document.getElementById('participants_max').value = '50';
    showModal('eventModal');
    focusFirst('#eventModal');
}

function editEvent(eventId) {
    const event = window.EVENTS.find(e => e.id_evenement === eventId);
    if (!event) return;
    
    if (window.CURRENT_USER_ROLE === 'company' && event.createur_id !== window.CURRENT_USER_ID) {
        alert('Vous ne pouvez modifier que vos propres événements.');
        return;
    }
    
    document.getElementById('eventId').value = event.id_evenement;
    document.getElementById('titre').value = event.titre;
    document.getElementById('description').value = event.description || '';
    document.getElementById('date_evenement').value = event.date_evenement;
    document.getElementById('lieu').value = event.lieu;
    document.getElementById('participants_max').value = event.participants_max;
    document.getElementById('statut').value = event.statut;
    
    document.querySelectorAll('.checkbox-item input').forEach(cb => {
        cb.checked = false;
    });
    
    if (event.accessibilite) {
        event.accessibilite.forEach(acc => {
            const checkbox = document.querySelector(`input[value="${acc}"]`);
            if (checkbox) checkbox.checked = true;
        });
    }
    
    showModal('eventModal');
    focusFirst('#eventModal');
}

function registerForEvent(eventId) {
    const event = window.EVENTS.find(e => e.id_evenement === eventId);
    if (!event) return;
    
    if (event.participations.some(p => p.utilisateur_id === window.CURRENT_USER_ID && p.etat_inscription === 'Confirmée')) {
        alert('Vous êtes déjà inscrit à cet événement.');
        return;
    }
    
    if (event.inscrits >= event.participants_max) {
        alert('Désolé, cet événement est complet.');
        return;
    }
    
    event.participations.push({
        id_participation: makeId(),
        utilisateur_id: window.CURRENT_USER_ID,
        evenement_id: eventId,
        date_inscription: todayISO(),
        presence: false,
        etat_inscription: "Confirmée"
    });
    
    event.inscrits = event.participations.filter(p => p.etat_inscription === 'Confirmée').length;
    
    saveStore();
    
    window.location.reload();
    
    alert('Inscription réussie ! Vous recevrez les informations pratiques par email.');
}

function openEventDetails(eventId) {
    const event = window.EVENTS.find(e => e.id_evenement === eventId);
    if (!event) return;
    
    const rating = calculateEventRating(event);
    const isUserParticipating = event.participations && 
        event.participations.some(p => p.utilisateur_id === window.CURRENT_USER_ID && p.etat_inscription === 'Confirmée');
    const isUserCreator = event.createur_id === window.CURRENT_USER_ID || 
        (window.CURRENT_USER_ROLE === 'admin') ||
        (window.CURRENT_USER_ROLE === 'inclusion');
    const company = getCompanyById(event.createur_id);
    const isFull = event.inscrits >= event.participants_max;
    
    let participationsHTML = '';
    if (isUserCreator || window.CURRENT_USER_ROLE === 'admin') {
        participationsHTML = `
            <h4>Participants (${event.inscrits}/${event.participants_max})</h4>
            <div style="max-height: 200px; overflow-y: auto; margin-top: 10px;">
                ${event.participations.length > 0 ? 
                    event.participations.filter(p => p.etat_inscription === 'Confirmée').map(p => `
                        <div style="display: flex; justify-content: space-between; padding: 8px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <span>Utilisateur ${p.utilisateur_id.substr(5)}</span>
                            <span>
                                ${p.presence ? 
                                    '<span style="color: var(--success);">Présent</span>' : 
                                    `<button class="cta-button" onclick="markPresence('${event.id_evenement}', '${p.id_participation}')" style="padding: 4px 8px; font-size: 12px;">Marquer présent</button>`
                                }
                            </span>
                        </div>
                    `).join('') : 
                    '<div class="muted">Aucun participant pour le moment.</div>'
                }
            </div>
        `;
    }
    
    const evaluationsHTML = `
        <h4>Évaluations (${event.evaluations ? event.evaluations.filter(e => e.etat_moderation === 'Visible').length : 0})</h4>
        <div style="max-height: 300px; overflow-y: auto; margin-top: 10px;">
            ${event.evaluations && event.evaluations.filter(e => e.etat_moderation === 'Visible').length > 0 ? 
                event.evaluations.filter(e => e.etat_moderation === 'Visible').map(evaluation => `
                    <div class="glass" style="padding: 12px; margin-bottom: 10px;">
                        <div style="display: flex; justify-content: space-between;">
                            <div>
                                <strong>Utilisateur ${evaluation.utilisateur_id.substr(5)}</strong>
                                <span class="small muted"> • ${formatDate(evaluation.date_evaluation)}</span>
                            </div>
                            <div>
                                <span class="small">Accès: ${evaluation.note_accessibilite}/5</span>
                                <span class="small" style="margin-left: 10px;">Incl: ${evaluation.note_inclusion}/5</span>
                            </div>
                        </div>
                        <div style="margin-top: 8px;">${escapeHtml(evaluation.commentaire)}</div>
                        ${(window.CURRENT_USER_ROLE === 'inclusion' || window.CURRENT_USER_ROLE === 'admin') ? 
                            `<div style="margin-top: 8px;">
                                <button class="cta-button" onclick="reportEvaluation('${event.id_evenement}', '${evaluation.id_evaluation}')" style="padding: 4px 8px; font-size: 12px;">Signaler</button>
                            </div>` : ''
                        }
                    </div>
                `).join('') : 
                '<div class="muted">Aucune évaluation visible pour le moment.</div>'
            }
        </div>
    `;
    
    document.getElementById('detailsContent').innerHTML = `
        <div style="display: grid; gap: 20px;">
            <div>
                <h4 style="margin: 0 0 10px;">${escapeHtml(event.titre)}</h4>
                <div class="company-info" style="margin-bottom: 10px;">
                    <div class="company-logo">${company ? company.nom.charAt(0) : 'E'}</div>
                    <div class="company-name">${company ? company.nom : 'AbeLink'}</div>
                </div>
                <div class="meta">${formatDate(event.date_evenement)} • ${escapeHtml(event.lieu)}</div>
                <div style="margin-top: 10px;">${escapeHtml(event.description)}</div>
                
                <div class="tags" style="margin-top: 10px;">
                    ${event.accessibilite.map(acc => `<span class="tag">${escapeHtml(acc)}</span>`).join('')}
                    <span class="tag status ${event.statut.toLowerCase()}">${event.statut}</span>
                    ${isFull ? '<span class="tag" style="background: rgba(255, 107, 107, 0.2); color: var(--danger);">Complet</span>' : ''}
                </div>
                
                <div style="display: flex; gap: 20px; margin-top: 15px;">
                    <div>
                        <div class="small">Note d'accessibilité</div>
                        <div class="stars">${renderStars(rating.access)} <span class="avg">${rating.access || '—'}/5</span></div>
                    </div>
                    <div>
                        <div class="small">Note d'inclusion</div>
                        <div class="stars">${renderStars(rating.inclusion)} <span class="avg">${rating.inclusion || '—'}/5</span></div>
                    </div>
                </div>
            </div>
            
            ${participationsHTML}
            
            ${evaluationsHTML}
            
            <div class="card-actions">
                ${!isUserParticipating && event.statut === 'Publié' && !isEventPassed(event) && !isFull ? 
                    `<button class="cta-button primary" onclick="registerForEvent('${event.id_evenement}'); closeDetailsModal();">S'inscrire</button>` : ''}
                    
                ${isUserParticipating && isEventPassed(event) ? 
                    `<button class="cta-button primary" onclick="openEvaluationModal('${event.id_evenement}'); closeDetailsModal();">Évaluer cet événement</button>` : ''}
                    
                ${isUserCreator ? 
                    `<button class="cta-button" onclick="editEvent('${event.id_evenement}'); closeDetailsModal();">Modifier l'événement</button>` : ''}
                    
                ${canModerateEvents() && event.statut === 'Brouillon' && event.createur_type === 'entreprise' ? 
                    `<button class="cta-button primary" onclick="validateEvent('${event.id_evenement}'); closeDetailsModal();">Valider la publication</button>` : ''}
            </div>
        </div>
    `;
    
    showModal('detailsModal');
}

function markPresence(eventId, participationId) {
    const event = window.EVENTS.find(e => e.id_evenement === eventId);
    if (!event) return;
    
    const participation = event.participations.find(p => p.id_participation === participationId);
    if (participation) {
        participation.presence = true;
        saveStore();
        openEventDetails(eventId); 
    }
}

function validateEvent(eventId) {
    const event = window.EVENTS.find(e => e.id_evenement === eventId);
    if (event) {
        event.statut = 'Publié';
        saveStore();
        
        window.location.reload();
        
        alert('Événement validé et publié avec succès !');
    }
}

function canModerateEvents() {
    return window.CURRENT_USER_ROLE === 'inclusion' || window.CURRENT_USER_ROLE === 'admin';
}

function openEvaluationModal(eventId) {
    document.getElementById('evaluationForm').reset();
    document.getElementById('evaluationEventId').value = eventId;
    showModal('evaluationModal');
    focusFirst('#evaluationModal');
}

function reportEvaluation(eventId, evaluationId) {
    const event = window.EVENTS.find(e => e.id_evenement === eventId);
    if (!event) return;
    
    const evaluation = event.evaluations.find(e => e.id_evaluation === evaluationId);
    if (evaluation) {
        evaluation.signalee = true;
        evaluation.etat_moderation = 'En attente';
        saveStore();
        
        openEventDetails(eventId); 
        
        alert('Évaluation signalée pour modération.');
    }
}

function setupFilters() {
    const searchInput = document.getElementById('searchInput');
    const filterAccess = document.getElementById('filterAccess');
    const filterCompany = document.getElementById('filterCompany');
    
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            window.CURRENT_FILTER.search = searchInput.value;
            if (typeof renderEvents === 'function') {
                renderEvents();
            }
        });
    }
    
    if (filterAccess) {
        filterAccess.addEventListener('change', () => {
            window.CURRENT_FILTER.access = filterAccess.value;
            if (typeof renderEvents === 'function') {
                renderEvents();
            }
        });
    }
    
    if (filterCompany) {
        const validCompanies = window.COMPANIES.filter(c => c.valide);
        filterCompany.innerHTML = '<option value="">Toutes les entreprises</option>' +
            validCompanies.map(c => `<option value="${c.id_entreprise}">${c.nom}</option>`).join('');
        
        filterCompany.addEventListener('change', () => {
            window.CURRENT_FILTER.company = filterCompany.value;
            if (typeof renderEvents === 'function') {
                renderEvents();
            }
        });
    }
}

function filterByDate(type) {
    window.CURRENT_FILTER.date = type;
    if (typeof renderEvents === 'function') {
        renderEvents();
    }
}

function showModal(id) { 
    document.getElementById(id).classList.add('open'); 
    document.getElementById(id).setAttribute('aria-hidden', 'false'); 
}

function closeModal(id) { 
    document.getElementById(id).classList.remove('open'); 
    document.getElementById(id).setAttribute('aria-hidden', 'true'); 
}

function closeEventModal() { closeModal('eventModal'); }
function closeEvaluationModal() { closeModal('evaluationModal'); }
function closeDetailsModal() { closeModal('detailsModal'); }

function focusFirst(selector) {
    const modal = document.querySelector(selector);
    if (!modal) return;
    const f = modal.querySelector('input,textarea,select,button');
    if (f) f.focus();
}

document.addEventListener('DOMContentLoaded', function() {
    const eventModal = document.getElementById('eventModal');
    const evaluationModal = document.getElementById('evaluationModal');
    const detailsModal = document.getElementById('detailsModal');
    
    if (eventModal) {
        eventModal.addEventListener('click', (e) => {
            if (e.target === eventModal) closeEventModal();
        });
    }
    
    if (evaluationModal) {
        evaluationModal.addEventListener('click', (e) => {
            if (e.target === evaluationModal) closeEvaluationModal();
        });
    }
    
    if (detailsModal) {
        detailsModal.addEventListener('click', (e) => {
            if (e.target === detailsModal) closeDetailsModal();
        });
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeEventModal();
        closeEvaluationModal();
        closeDetailsModal();
    }
});

function canAccessDashboard() {
    return window.CURRENT_USER_ROLE === 'admin';
}
function canAccessModeration() {
    return window.CURRENT_USER_ROLE === 'inclusion' || window.CURRENT_USER_ROLE === 'admin';
}
// Database export functions
function exportEventsToDB() {
    const resultDiv = document.getElementById('exportResult');
    resultDiv.innerHTML = '<p style="color: #3498db;">Export en cours...</p>';
    
    // Create a FormData object to send the events data
    const formData = new FormData();
    formData.append('action', 'export_events');
    formData.append('events', JSON.stringify(window.EVENTS));
    
    // Send the data to the server via AJAX
    fetch('export_events.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
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
    .then(response => response.json())
    .then(data => {
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