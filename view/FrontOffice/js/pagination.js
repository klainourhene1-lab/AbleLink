// Pagination system for event cards
// Handles pagination for multiple grids with 6 cards per page

const CARDS_PER_PAGE = 6;
const paginationState = {};

/**
 * Initialize pagination for a grid
 * @param {string} gridId - ID of the grid container
 * @param {string} paginationId - ID of the pagination controls container
 */
function initializePagination(gridId, paginationId) {
    paginationState[gridId] = {
        currentPage: 1,
        totalPages: 1,
        paginationId: paginationId
    };
}

/**
 * Update pagination for a grid
 * @param {string} gridId - ID of the grid container
 */
function updatePagination(gridId) {
    const grid = document.getElementById(gridId);
    const paginationContainer = document.getElementById(paginationState[gridId].paginationId);
    
    if (!grid || !paginationContainer) return;
    
    const allCards = Array.from(grid.children);
    const totalCards = allCards.length;
    const totalPages = Math.ceil(totalCards / CARDS_PER_PAGE);
    
    paginationState[gridId].totalPages = totalPages;
    
    // Hide pagination if only one page or no cards
    if (totalPages <= 1) {
        paginationContainer.style.display = 'none';
        allCards.forEach(card => card.style.display = '');
        return;
    }
    
    paginationContainer.style.display = 'flex';
    
    // Show only cards for current page
    showPage(gridId, paginationState[gridId].currentPage);
    
    // Render pagination controls
    renderPaginationControls(gridId);
}

/**
 * Show a specific page
 * @param {string} gridId - ID of the grid container
 * @param {number} pageNumber - Page number to show
 */
function showPage(gridId, pageNumber) {
    const grid = document.getElementById(gridId);
    if (!grid) return;
    
    const allCards = Array.from(grid.children);
    const startIndex = (pageNumber - 1) * CARDS_PER_PAGE;
    const endIndex = startIndex + CARDS_PER_PAGE;
    
    allCards.forEach((card, index) => {
        if (index >= startIndex && index < endIndex) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
    
    paginationState[gridId].currentPage = pageNumber;
    
    // Scroll to top of grid
    grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/**
 * Render pagination controls
 * @param {string} gridId - ID of the grid container
 */
function renderPaginationControls(gridId) {
    const paginationContainer = document.getElementById(paginationState[gridId].paginationId);
    if (!paginationContainer) return;
    
    const currentPage = paginationState[gridId].currentPage;
    const totalPages = paginationState[gridId].totalPages;
    
    let html = '';
    
    // Previous button
    html += `
        <button class="pagination-btn" 
                onclick="changePage('${gridId}', ${currentPage - 1})" 
                ${currentPage === 1 ? 'disabled' : ''}>
            ← Précédent
        </button>
    `;
    
    // Page numbers
    const maxButtons = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
    let endPage = Math.min(totalPages, startPage + maxButtons - 1);
    
    if (endPage - startPage < maxButtons - 1) {
        startPage = Math.max(1, endPage - maxButtons + 1);
    }
    
    if (startPage > 1) {
        html += `<button class="pagination-btn" onclick="changePage('${gridId}', 1)">1</button>`;
        if (startPage > 2) {
            html += `<span class="pagination-info">...</span>`;
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        html += `
            <button class="pagination-btn ${i === currentPage ? 'active' : ''}" 
                    onclick="changePage('${gridId}', ${i})">
                ${i}
            </button>
        `;
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<span class="pagination-info">...</span>`;
        }
        html += `<button class="pagination-btn" onclick="changePage('${gridId}', ${totalPages})">${totalPages}</button>`;
    }
    
    // Next button
    html += `
        <button class="pagination-btn" 
                onclick="changePage('${gridId}', ${currentPage + 1})" 
                ${currentPage === totalPages ? 'disabled' : ''}>
            Suivant →
        </button>
    `;
    
    // Page info
    html += `<span class="pagination-info">Page ${currentPage} sur ${totalPages}</span>`;
    
    paginationContainer.innerHTML = html;
}

/**
 * Change to a specific page
 * @param {string} gridId - ID of the grid container
 * @param {number} pageNumber - Page number to navigate to
 */
function changePage(gridId, pageNumber) {
    const totalPages = paginationState[gridId].totalPages;
    
    if (pageNumber < 1 || pageNumber > totalPages) return;
    
    showPage(gridId, pageNumber);
    renderPaginationControls(gridId);
}

// Initialize pagination for all grids when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize pagination for each grid
    initializePagination('eventsGrid', 'eventsPagination');
    initializePagination('eventsToEvaluateGrid', 'eventsToEvaluatePagination');
    initializePagination('myEvaluationsGrid', 'myEvaluationsPagination');
});

// Export for use in other scripts
window.updatePagination = updatePagination;
window.changePage = changePage;
